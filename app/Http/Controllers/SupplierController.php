<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Services\ImportExportService;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(
        private SupplierService $supplierService,
        private ImportExportService $importExportService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Supplier::class);

        $search = $request->input('search');
        $suppliers = $this->supplierService->paginate(10, $search);
        return view('suppliers.index', compact('suppliers', 'search'));
    }

    public function create()
    {
        $this->authorize('create', Supplier::class);

        return view('suppliers.create');
    }

    public function store(SupplierRequest $request)
    {
        $this->authorize('create', Supplier::class);

        $this->supplierService->create($request->validated());
        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        $this->authorize('view', $supplier);

        $supplier->load('layups.layers');
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        $this->authorize('update', $supplier);

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $this->authorize('update', $supplier);

        $this->supplierService->update($supplier, $request->validated());
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $this->authorize('delete', $supplier);

        $this->supplierService->delete($supplier);
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }

    public function export(Supplier $supplier)
    {
        $this->authorize('export', $supplier);

        $data = $this->importExportService->export($supplier);
        $filename = 'supplier_' . $supplier->id . '_' . now()->format('Ymd_His') . '.json';

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }

    public function importForm(Supplier $supplier)
    {
        $this->authorize('import', $supplier);

        return view('suppliers.import', compact('supplier'));
    }

    public function importAnalyze(ImportRequest $request, Supplier $supplier)
    {
        $this->authorize('import', $supplier);

        // If confirmed, use data from session instead of requiring re-upload
        if ($request->has('confirmed')) {
            $data = session('import_data');
            $strategy = session('import_strategy', 'skip');
            $dryRun = session('import_dry_run', false);

            if (!$data) {
                return redirect()->route('suppliers.import', $supplier)
                    ->with('error', 'Import session expired. Please upload the file again.');
            }

            if ($dryRun) {
                $analysis = $this->importExportService->analyzeImport($supplier, $data);
                $conflictCount = collect($analysis['conflicts'])->sum(fn($c) => count($c['layer_conflicts']));
                $newCount = collect($analysis['new_layups'])->sum(fn($l) => count($l['layers'] ?? []));
                session()->forget(['import_analysis', 'import_data', 'import_supplier_id', 'import_strategy', 'import_dry_run']);
                return redirect()->route('suppliers.show', $supplier)
                    ->with('success', "[Dry Run] Simulation complete. Conflicts: {$conflictCount}, New layers: {$newCount}. No changes made.");
            }

            $result = $this->importExportService->executeImport($supplier, $data, $strategy);
            session()->forget(['import_analysis', 'import_data', 'import_supplier_id', 'import_strategy', 'import_dry_run']);

            if (!empty($result['rejected'])) {
                $details = implode(', ', $result['conflict_details'] ?? ['Unknown conflict']);
                return redirect()->route('suppliers.import', $supplier)
                    ->with('error', "Import rejected due to conflicts in: {$details}");
            }

            return redirect()->route('suppliers.show', $supplier)
                ->with('success', "Import completed. Created: {$result['created']}, Updated: {$result['updated']}, Skipped: {$result['skipped']}");
        }

        try {
            $content = file_get_contents($request->file('file')->getRealPath());
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return back()->with('error', 'Invalid JSON file structure: ' . $e->getMessage());
        }

        if (!isset($data['layups']) || !is_array($data['layups'])) {
            return back()->with('error', 'Invalid format: The file must contain a "layups" array.');
        }

        // Deep validation of the incoming data structure
        foreach ($data['layups'] as $layup) {
            if (empty($layup['name'])) {
                return back()->with('error', 'Data Anomaly: found a layup without a name.');
            }
            if (!isset($layup['layers']) || !is_array($layup['layers'])) {
                return back()->with('error', "Data Anomaly: layup '{$layup['name']}' has no layers.");
            }
            foreach ($layup['layers'] as $layer) {
                if (!isset($layer['layer_order'], $layer['thickness'], $layer['width'], $layer['angle'])) {
                    return back()->with('error', "Data Anomaly: layup '{$layup['name']}' has incomplete layer data.");
                }
                if (!is_numeric($layer['thickness']) || !is_numeric($layer['width']) || !is_numeric($layer['angle'])) {
                    return back()->with('error', "Data Anomaly: non-numeric values found in layers for '{$layup['name']}'.");
                }
            }
        }

        $strategy = $request->input('strategy', 'skip');
        $dryRun = $request->boolean('dry_run');
        $analysis = $this->importExportService->analyzeImport($supplier, $data);

        // Store analysis and data in session for sophisticated UI feedback
        session([
            'import_analysis' => $analysis,
            'import_data' => $data,
            'import_supplier_id' => $supplier->id,
            'import_strategy' => $strategy,
            'import_dry_run' => $dryRun
        ]);

        // If conflicts exist, show the warning on the import page
        if (!empty($analysis['conflicts'])) {
            // If manual strategy is selected, redirect to the review UI
            if ($strategy === 'manual') {
                return redirect()->route('suppliers.import.review', $supplier);
            }

            return redirect()->route('suppliers.import', $supplier)
                ->with('conflicts_detected', true);
        }

        // Execute if no conflicts
        if ($dryRun) {
            $conflictCount = 0;
            $newCount = collect($analysis['new_layups'])->sum(fn($l) => count($l['layers'] ?? []));
            session()->forget(['import_analysis', 'import_data', 'import_supplier_id', 'import_strategy', 'import_dry_run']);
            return redirect()->route('suppliers.show', $supplier)
                ->with('success', "[Dry Run] Simulation complete. Conflicts: {$conflictCount}, New layers: {$newCount}. No changes made.");
        }

        $result = $this->importExportService->executeImport($supplier, $data, $strategy);
        session()->forget(['import_analysis', 'import_data', 'import_supplier_id', 'import_strategy', 'import_dry_run']);

        if (!empty($result['rejected'])) {
            $details = implode(', ', $result['conflict_details'] ?? ['Unknown conflict']);
            return redirect()->route('suppliers.import', $supplier)
                ->with('error', "Import rejected due to conflicts in: {$details}");
        }

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', "Import completed. Created: {$result['created']}, Updated: {$result['updated']}, Skipped: {$result['skipped']}");
    }

    public function importReview(Supplier $supplier)
    {
        $this->authorize('import', $supplier);

        $analysis = session('import_analysis');
        $data = session('import_data');

        if (!$analysis || !$data) {
            return redirect()->route('suppliers.import', $supplier)
                ->with('error', 'Import session expired. Please upload the file again.');
        }

        return view('suppliers.conflicts', [
            'supplier' => $supplier,
            'conflicts' => $analysis['conflicts'],
            'newLayups' => $analysis['new_layups'],
        ]);
    }

    public function importExecute(Request $request, Supplier $supplier)
    {
        $this->authorize('import', $supplier);

        $data = session('import_data');
        $strategy = $request->input('strategy', 'overwrite');
        $resolutions = $request->input('resolutions', []);

        $result = $this->importExportService->executeImport($supplier, $data, $strategy, $resolutions);
        
        // Clean up session
        session()->forget(['import_analysis', 'import_data', 'import_supplier_id', 'import_strategy', 'import_dry_run']);

        if (!empty($result['rejected'])) {
            $details = implode(', ', $result['conflict_details'] ?? ['Unknown conflict']);
            return redirect()->route('suppliers.import', $supplier)
                ->with('error', "Import rejected due to conflicts in: {$details}");
        }

        $summary = "Import matched your manual decisions. ";
        $summary .= "Created: {$result['created']} layers, ";
        $summary .= "Updated: {$result['updated']} layers, ";
        $summary .= "Skipped: {$result['skipped']} conflicts.";

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', $summary);
    }
}
