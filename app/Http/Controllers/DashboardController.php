<?php

namespace App\Http\Controllers;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $layupsCount = CltLayup::count();
        $layersCount = CltLayer::count();

        $stats = [
            'suppliers_count' => Supplier::count(),
            'layups_count' => $layupsCount,
            'layers_count' => $layersCount,
            'avg_layers' => $layupsCount > 0 ? round($layersCount / $layupsCount, 1) : 0,
            'recent_suppliers' => Supplier::withCount('layups')->latest()->take(5)->get(),
        ];

        return view('dashboard', compact('stats'));
    }
}
