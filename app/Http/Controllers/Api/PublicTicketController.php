<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Support\Facades\Cache;

class PublicTicketController extends Controller
{
    public function show($ticket_code)
    {
        $invitation = Invitation::where('ticket_code', $ticket_code)->firstOrFail();

        $eventInfo = Cache::get('event_info', [
            'title' => 'Gen Z Indonesia 2026',
            'theme' => 'The Nusantara Legends Go to the World',
            'location' => 'Platinum Cineplex Milenial Hub Metland Cibitung',
            'event_date' => '2026-10-24',
        ]);

        // Jika tiket punya tanggal khusus, ganti tanggal di event_info
        if (!empty($invitation->event_date)) {
            $eventInfo['event_date'] = $invitation->event_date;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'ticket_code' => $invitation->ticket_code,
                'type' => $invitation->type,
                'school_name' => $invitation->school_name,
                'teacher_names' => $invitation->teacher_names,
                'guest_name' => $invitation->guest_name,
                'parent_1_name' => $invitation->parent_1_name,
                'parent_2_name' => $invitation->parent_2_name,
                'seat_area' => $invitation->seat_area,
                'event_date' => $invitation->event_date,
                'scan_count' => $invitation->scan_count,
                'max_scans' => $invitation->max_scans,
                'remaining_scans' => max(0, $invitation->max_scans - $invitation->scan_count),
                'event'           => $eventInfo,
                'event_info' => $eventInfo
            ]
        ]);
    }
}