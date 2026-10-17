<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EventSettingController extends Controller
{
    public function show()
    {
        $info = Cache::get('event_info', [
            'title' => 'Gen Z Indonesia 2026',
            'theme' => 'The Nusantara Legends Go to the World',
            'location' => 'Platinum Cineplex Milenial Hub Metland Cibitung',
            'event_date' => '2026-10-24'
        ]);

        return response()->json(['success' => true, 'data' => $info]);
    }

    public function update(Request $request)
    {
        $data = $request->only(['title', 'theme', 'location', 'event_date']);
        Cache::put('event_info', $data);

        return response()->json(['success' => true, 'message' => 'Informasi acara diperbarui', 'data' => $data]);
    }
}