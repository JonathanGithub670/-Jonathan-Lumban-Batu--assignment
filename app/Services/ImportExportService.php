<?php

namespace App\Services;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class ImportExportService
{
    public function export(Supplier $supplier): array
    {
        $supplier->load('layups.layers');

        return [
            'supplier' => [
                'name' => $supplier->name,
                'identifier' => $supplier->identifier,
            ],
            'layups' => $supplier->layups->map(function (CltLayup $layup) {
                return [
                    'name' => $layup->name,
                    'identifier' => $layup->identifier,
                    'layers' => $layup->layers->map(function (CltLayer $layer) {
                        return [
                            'layer_order' => $layer->layer_order,
                            'thickness' => (float) $layer->thickness,
                            'width' => (float) $layer->width,
                            'angle' => (float) $layer->angle,
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ];
    }

    public function analyzeImport(Supplier $supplier, array $data): array
    {
        $conflicts = [];
        $newLayups = [];
        $updatableLayups = [];

        foreach ($data['layups'] ?? [] as $index => $layupData) {
            // Priority 1: Match by Identifier (Global check)
            $existingLayup = null;
            if (!empty($layupData['identifier'])) {
                // We check globally to avoid UNIQUE constraint violations
                $existingLayup = CltLayup::where('identifier', $layupData['identifier'])->first();
                
                // If it belongs to a different supplier, we cannot overwrite or update it
                if ($existingLayup && $existingLayup->supplier_id !== $supplier->id) {
                    $existingLayup = null;
                    $layupData['identifier_conflict'] = true; // Mark to strip identifier later
                }
            }

            // Priority 2: Fallback to Name matching (Scoped to current supplier)
            if (!$existingLayup) {
                $existingLayup = $supplier->layups()
                    ->where('name', $layupData['name'])
                    ->first();
            }

            if (!$existingLayup) {
                $newLayups[] = $layupData;
                continue;
            }

            $layerConflicts = [];
            $newLayers = [];

            foreach ($layupData['layers'] ?? [] as $layerData) {
                $existingLayer = $existingLayup->layers()
                    ->where('layer_order', $layerData['layer_order'])
                    ->first();

                if (!$existingLayer) {
                    $newLayers[] = $layerData;
                    continue;
                }

                $hasDiff = (float) $existingLayer->thickness !== (float) $layerData['thickness']
                    || (float) $existingLayer->width !== (float) $layerData['width']
                    || (float) $existingLayer->angle !== (float) $layerData['angle'];

                if ($hasDiff) {
                    $layerConflicts[] = [
                        'layer_order' => $layerData['layer_order'],
                        'existing' => [
                            'thickness' => (float) $existingLayer->thickness,
                            'width' => (float) $existingLayer->width,
                            'angle' => (float) $existingLayer->angle,
                        ],
                        'incoming' => [
                            'thickness' => (float) $layerData['thickness'],
                            'width' => (float) $layerData['width'],
                            'angle' => (float) $layerData['angle'],
                        ],
                        'existing_layer_id' => $existingLayer->id,
                    ];
                }
            }

            if (!empty($layerConflicts)) {
                // Generate a full comparison map for the UI side-by-side table
                $fullLayers = [];
                $allOrders = collect($existingLayup->layers->pluck('layer_order'))
                    ->merge(collect($layupData['layers'] ?? [])->pluck('layer_order'))
                    ->unique()
                    ->sort();

                foreach ($allOrders as $order) {
                    $existing = $existingLayup->layers->where('layer_order', $order)->first();
                    $incoming = collect($layupData['layers'] ?? [])->where('layer_order', $order)->first();
                    
                    $isConflict = false;
                    if ($existing && $incoming) {
                        $isConflict = (float) $existing->thickness !== (float) $incoming['thickness']
                            || (float) $existing->width !== (float) $incoming['width']
                            || (float) $existing->angle !== (float) $incoming['angle'];
                    } else {
                        $isConflict = true; // New or deleted layer is also a structural change
                    }

                    $fullLayers[] = [
                        'order' => $order,
                        'existing' => $existing ? [
                            'thickness' => (float) $existing->thickness,
                            'width' => (float) $existing->width,
                            'angle' => (float) $existing->angle,
                        ] : null,
                        'incoming' => $incoming ? [
                            'thickness' => (float) $incoming['thickness'],
                            'width' => (float) $incoming['width'],
                            'angle' => (float) $incoming['angle'],
                        ] : null,
                        'has_conflict' => $isConflict
                    ];
                }

                $conflicts[] = [
                    'layup_name' => $layupData['name'],
                    'layup_id' => $existingLayup->id,
                    'layer_conflicts' => $layerConflicts,
                    'full_layers' => $fullLayers,
                    'new_layers' => $newLayers,
                    'all_incoming_layers' => $layupData['layers'] ?? [],
                ];
            } else {
                $updatableLayups[] = [
                    'layup' => $existingLayup,
                    'new_layers' => $newLayers,
                ];
            }
        }

        return [
            'conflicts' => $conflicts,
            'new_layups' => $newLayups,
            'updatable_layups' => $updatableLayups,
        ];
    }

    public function executeImport(Supplier $supplier, array $data, string $strategy = 'overwrite', array $resolutions = []): array
    {
        return DB::transaction(function () use ($supplier, $data, $strategy, $resolutions) {
            $result = ['created' => 0, 'updated' => 0, 'skipped' => 0];
            $analysis = $this->analyzeImport($supplier, $data);

            // Reject strategy: abort entire import BEFORE any writes
            if ($strategy === 'reject' && !empty($analysis['conflicts'])) {
                $conflictDetails = collect($analysis['conflicts'])->map(function ($c) {
                    return $c['layup_name'] . ' (' . count($c['layer_conflicts']) . ' layer conflicts)';
                })->toArray();

                return [
                    'created' => 0,
                    'updated' => 0,
                    'skipped' => 0,
                    'rejected' => true,
                    'conflict_details' => $conflictDetails,
                ];
            }

            // Create new layups
            foreach ($analysis['new_layups'] as $layupData) {
                // Strip identifier if it conflicts with another supplier
                $identifier = ($layupData['identifier_conflict'] ?? false) ? null : ($layupData['identifier'] ?? null);

                $layup = $supplier->layups()->create([
                    'name' => $layupData['name'],
                    'identifier' => $identifier,
                ]);
                foreach ($layupData['layers'] ?? [] as $layerData) {
                    $layup->layers()->create($layerData);
                    $result['created']++;
                }
            }

            // Handle updatable layups (no conflicts, just new layers)
            foreach ($analysis['updatable_layups'] as $item) {
                foreach ($item['new_layers'] as $layerData) {
                    $item['layup']->layers()->create($layerData);
                    $result['created']++;
                }
            }

            // Handle conflicts based on strategy
            foreach ($analysis['conflicts'] as $conflict) {
                // Create new layers that don't conflict
                $layup = CltLayup::find($conflict['layup_id']);

                foreach ($conflict['new_layers'] as $layerData) {
                    $layup->layers()->create($layerData);
                    $result['created']++;
                }

                switch ($strategy) {
                    case 'overwrite':
                        foreach ($conflict['layer_conflicts'] as $lc) {
                            $layer = CltLayer::find($lc['existing_layer_id']);
                            $layer->update($lc['incoming']);
                            $result['updated']++;
                        }
                        break;

                    case 'skip':
                        $result['skipped'] += count($conflict['layer_conflicts']);
                        break;

                    case 'duplicate':
                        $newLayup = $supplier->layups()->create([
                            'name' => $conflict['layup_name'] . ' (imported)',
                        ]);
                        foreach ($conflict['all_incoming_layers'] as $layerData) {
                            $newLayup->layers()->create($layerData);
                            $result['created']++;
                        }
                        break;

                    case 'manual':
                        foreach ($conflict['layer_conflicts'] as $lc) {
                            $key = $conflict['layup_id'] . '_' . $lc['layer_order'];
                            $resolution = $resolutions[$key] ?? 'skip';
                            if ($resolution === 'incoming') {
                                $layer = CltLayer::find($lc['existing_layer_id']);
                                $layer->update($lc['incoming']);
                                $result['updated']++;
                            } else {
                                $result['skipped']++;
                            }
                        }
                        break;
                }
            }

            return $result;
        });
    }
}
