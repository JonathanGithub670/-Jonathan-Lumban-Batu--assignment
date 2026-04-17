<?php

namespace App\Http\Controllers;

use App\Http\Requests\CltLayupRequest;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\CltLayupService;
use Illuminate\Http\Request;

class CltLayupController extends Controller
{
    public function __construct(
        private CltLayupService $layupService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', CltLayup::class);

        $search = $request->input('search');
        $layups = $this->layupService->paginate(10, $search);
        $suppliers = Supplier::orderBy('name')->get();
        return view('layups.index', compact('layups', 'search', 'suppliers'));
    }

    public function create(Supplier $supplier)
    {
        $this->authorize('create', CltLayup::class);

        return view('layups.create', compact('supplier'));
    }

    public function store(CltLayupRequest $request, Supplier $supplier)
    {
        $this->authorize('create', CltLayup::class);

        $this->layupService->create([
            'supplier_id' => $supplier->id,
            ...$request->validated(),
        ]);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Layup created successfully.');
    }

    public function show(Supplier $supplier, CltLayup $layup)
    {
        $this->authorize('view', $layup);

        $layup->load('layers');
        return view('layups.show', compact('supplier', 'layup'));
    }

    public function edit(Supplier $supplier, CltLayup $layup)
    {
        $this->authorize('update', $layup);

        return view('layups.edit', compact('supplier', 'layup'));
    }

    public function update(CltLayupRequest $request, Supplier $supplier, CltLayup $layup)
    {
        $this->authorize('update', $layup);

        $this->layupService->update($layup, $request->validated());

        return redirect()->route('suppliers.layups.show', [$supplier, $layup])
            ->with('success', 'Layup updated successfully.');
    }

    public function duplicate(Supplier $supplier, CltLayup $layup)
    {
        $this->authorize('create', CltLayup::class);

        $newLayup = $this->layupService->duplicate($layup);

        return redirect()->route('suppliers.layups.show', [$supplier, $newLayup])
            ->with('success', 'Layup duplicated successfully.');
    }

    public function destroy(Supplier $supplier, CltLayup $layup)
    {
        $this->authorize('delete', $layup);

        $this->layupService->delete($layup);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Layup deleted successfully.');
    }
}
