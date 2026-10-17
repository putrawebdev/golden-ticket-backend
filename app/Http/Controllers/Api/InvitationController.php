<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function index(Request $request)
    {
        $query = Invitation::query();

        // Search Filter
        if ($search = $request->input('query')) {
            $query->where(function ($q) use ($search) {
                    $q->where('ticket_code', 'like', "%{$search}%")
                    ->orWhere('school_name', 'like', "%{$search}%")
                    ->orWhere('guest_name', 'like', "%{$search}%")
                    ->orWhere('parent_1_name', 'like', "%{$search}%")
                    ->orWhere('parent_2_name', 'like', "%{$search}%")
                    ->orWhere('seat_area', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($type = $request->input('type')) {
            if (in_array($type, ['school', 'vip', 'parent'])) {
                $query->where('type', $type);
            }
        }

        // Status Filter
        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->whereColumn('scan_count', '<', 'max_scans');
            } elseif ($status === 'full') {
                $query->whereColumn('scan_count', '>=', 'max_scans');
            }
        }

        $invitations = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $invitations
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:school,vip,parent',
            'seat_area' => 'required|string|max:100',
            'school_name' => 'required_if:type,school|nullable|string',
            'teacher_names' => 'required_if:type,school|nullable|array',
            'guest_name' => 'required_if:type,vip|nullable|string',
            'parent_1_name' => 'required_if:type,parent|nullable|string',
            'parent_2_name' => 'nullable|string',
        ]);

        // Tentukan Max Scans berdasarkan PRD
        $maxScans = 1;
        if ($validated['type'] === 'school') {
            $maxScans = 10;
        } elseif ($validated['type'] === 'parent') {
            $maxScans = !empty($validated['parent_2_name']) ? 2 : 1;
        }

        // Auto Generate Ticket Code jika tidak disediakan
        $prefix = strtoupper(substr($validated['type'], 0, 3));
        $code = 'GZ26-' . $prefix . '-' . strtoupper(Str::random(4));

        $invitation = Invitation::create([
            'ticket_code' => $code,
            'type' => $validated['type'],
            'school_name' => $validated['type'] === 'school' ? $validated['school_name'] : null,
            'teacher_names' => $validated['type'] === 'school' ? ($validated['teacher_names'] ?? []) : null,
            'guest_name' => $validated['type'] === 'vip' ? $validated['guest_name'] : null,
            'parent_1_name' => $validated['type'] === 'parent' ? $validated['parent_1_name'] : null,
            'parent_2_name' => $validated['type'] === 'parent' ? $validated['parent_2_name'] : null,
            'seat_area' => $validated['seat_area'],
            'scan_count' => 0,
            'max_scans' => $maxScans,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil dibuat.',
            'data' => $invitation
        ], 201);
    }

    public function show($id)
    {
        $invitation = Invitation::findOrFail($id);
        return response()->json(['success' => true, 'data' => $invitation]);
    }

    public function update(Request $request, $id)
    {
        $invitation = Invitation::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:school,vip,parent',
            'school_name' => 'nullable|string',
            'teacher_names' => 'nullable|array',
            'guest_name' => 'nullable|string',
            'parent_1_name' => 'nullable|string',
            'parent_2_name' => 'nullable|string',
            'seat_area' => 'required|string',
            'event_date' => 'nullable|date',
        ]);

        // Mengubah string kosong "" menjadi null agar tidak crash di MySQL
        $validated['event_date'] = $request->filled('event_date') ? $request->event_date : null;

        // Hitung ulang max_scans otomatis
        if ($validated['type'] === 'school') {
            $validated['max_scans'] = 10;
        } elseif ($validated['type'] === 'vip') {
            $validated['max_scans'] = 1;
        } else {
            $validated['max_scans'] = !empty($validated['parent_2_name']) ? 2 : 1;
        }

        $invitation->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data undangan berhasil diperbarui.',
            'data' => $invitation
        ]);
    }

    public function destroy($id)
    {
        $invitation = Invitation::findOrFail($id);
        $invitation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil dihapus.'
        ]);
    }
}