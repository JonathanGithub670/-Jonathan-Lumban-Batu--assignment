<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayerRequest;
use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\CltLayerService;
use Illuminate\Http\Request;

class CltLayerController extends Controller
{
    public function __construct(
        private CltLayerService $layerService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', CltLayer::class);

        $search = $request->input('search');
        $layers = $this->layerService->paginate(15, $search);
        return view('layers.index', compact('layers', 'search'));
    }

    public function create(Supplier $supplier, CltLayup $layup)
    {
        $this->authorize('create', CltLayer::class);

        return view('layers.create', compact('supplier', 'layup'));
    }

    public function store(CltLayerRequest $request, Supplier $supplier, CltLayup $layup)
    {
        $this->authorize('create', CltLayer::class);

        $this->layerService->create([
            'layup_id' => $layup->id,
            ...$request->validated(),
        ]);

        return redirect()->route('suppliers.layups.show', [$supplier, $layup])
            ->with('success', 'Layer created successfully.');
    }

    public function edit(Supplier $supplier, CltLayup $layup, CltLayer $layer)
    {
        $this->authorize('update', $layer);

        return view('layers.edit', compact('supplier', 'layup', 'layer'));
    }

    public function update(CltLayerRequest $request, Supplier $supplier, CltLayup $layup, CltLayer $layer)
    {
        $this->authorize('update', $layer);

        $this->layerService->update($layer, $request->validated());

        return redirect()->route('suppliers.layups.show', [$supplier, $layup])
            ->with('success', 'Layer updated successfully.');
    }

    public function destroy(Supplier $supplier, CltLayup $layup, CltLayer $layer)
    {
        $this->authorize('delete', $layer);

        $this->layerService->delete($layer);

        return redirect()->route('suppliers.layups.show', [$supplier, $layup])
            ->with('success', 'Layer deleted successfully.');
    }
}
