<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\DeviceInstance;
use App\Models\Inventory;
use App\Models\Material;
use App\Models\NfrProtocolItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMaterials = Material::where('is_active', true)->count();
        $lowStockMaterials = Material::where('is_active', true)->get()
            ->filter(fn ($m) => $m->isBelowMinStock() && $m->min_stock > 0);

        $expiredItems = Inventory::with('material', 'location')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->where('quantity', '>', 0)
            ->get();

        $expiringSoon = Inventory::with('material', 'location')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(30)])
            ->where('quantity', '>', 0)
            ->get();

        $recentCheckouts = Checkout::with('material', 'user', 'location')
            ->latest()
            ->take(10)
            ->get();

        $openCheckouts = Checkout::with('material', 'user')
            ->where('type', 'checkout')
            ->whereNull('returned_at')
            ->count();

        $deviceWarnings = DeviceInstance::with('material')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('mtk_ablauf', '<=', now()->addDays(30))
                  ->orWhere('stk_ablauf', '<=', now()->addDays(30));
            })
            ->get()
            ->sortBy(fn ($d) => min(
                $d->mtk_ablauf?->timestamp ?? PHP_INT_MAX,
                $d->stk_ablauf?->timestamp ?? PHP_INT_MAX,
            ));

        $openDefects = NfrProtocolItem::with([
                'protocol.equipment.vehicle',
                'material',
                'deviceInstance.material',
            ])
            ->where('is_ok', false)
            ->whereNull('resolved_at')
            ->latest()
            ->get();

        return view('dashboard', compact(
            'totalMaterials', 'lowStockMaterials', 'expiredItems',
            'expiringSoon', 'recentCheckouts', 'openCheckouts',
            'deviceWarnings', 'openDefects'
        ));
    }
}
