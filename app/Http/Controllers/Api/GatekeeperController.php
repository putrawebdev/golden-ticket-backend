<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\ScanLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GatekeeperController extends Controller
{
    public function validateScan(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string'
        ]);

        $code = trim($request->input('ticket_code'));
        $userId = auth('sanctum')->id();

        // Menggunakan DB Transaction & lockForUpdate() untuk mencegah double-scan ganda serentak (Race Condition)
        return DB::transaction(function () use ($code, $userId) {
            $invitation = Invitation::where('ticket_code', $code)
                ->lockForUpdate()
                ->first();

            if (!$invitation) {
                ScanLog::create([
                    'scanned_code' => $code,
                    'scanned_by_user_id' => $userId,
                    'status' => 'invalid_code',
                    'message' => 'Kode tiket tidak ditemukan',
                    'scanned_at' => now(),
                ]);

                return response()->json([
                    'ok' => false,
                    'reason' => 'Kode tiket tidak ditemukan',
                    'detail' => 'Pastikan QR Code berasal dari undangan resmi Gen Z Indonesia 2026.',
                    'item' => null
                ], 404);
            }

            if ($invitation->scan_count >= $invitation->max_scans) {
                ScanLog::create([
                    'invitation_id' => $invitation->id,
                    'scanned_code' => $code,
                    'scanned_by_user_id' => $userId,
                    'status' => 'quota_full',
                    'message' => 'Kuota tiket sudah penuh',
                    'scanned_at' => now(),
                ]);

                return response()->json([
                    'ok' => false,
                    'reason' => 'Kuota tiket sudah penuh',
                    'detail' => "Batas {$invitation->max_scans} orang telah tercapai. Akses ditolak.",
                    'item' => $invitation
                ], 422);
            }

            // Increment scan_count secara atomic
            $invitation->increment('scan_count');
            $invitation->refresh();

            ScanLog::create([
                'invitation_id' => $invitation->id,
                'scanned_code' => $code,
                'scanned_by_user_id' => $userId,
                'status' => 'success',
                'message' => 'Pemindaian berhasil',
                'scanned_at' => now(),
            ]);

            return response()->json([
                'ok' => true,
                'reason' => 'Akses diterima',
                'detail' => 'Tiket valid. Pemindaian berhasil dicatat.',
                'item' => $invitation
            ]);
        });
    }
}