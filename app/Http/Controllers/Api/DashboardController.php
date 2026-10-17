<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;

class DashboardController extends Controller
{
    public function overview()
    {
        $totalTickets = Invitation::count();
        $usedScans = Invitation::sum('scan_count');
        $totalCapacity = Invitation::sum('max_scans');
        $fullTickets = Invitation::whereColumn('scan_count', '>=', 'max_scans')->count();

        $byType = ['school', 'vip', 'parent'];
        $categories = [];

        foreach ($byType as $type) {
            $items = Invitation::where('type', $type);
            $count = $items->count();
            $used = Invitation::where('type', $type)->sum('scan_count');
            $cap = Invitation::where('type', $type)->sum('max_scans');

            $categories[] = [
                'type' => $type,
                'count' => $count,
                'used' => (int)$used,
                'cap' => (int)$cap,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_tickets' => $totalTickets,
                'used_scans' => (int)$usedScans,
                'total_capacity' => (int)$totalCapacity,
                'full_tickets' => $fullTickets,
                'remaining_capacity' => max(0, $totalCapacity - $usedScans),
                'by_category' => $categories
            ]
        ]);
    }
}