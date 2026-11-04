<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\GuestBook;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessageWallController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $lastId = $request->query('last_id', 0);

        $messages = GuestBook::query()
            ->with('company:id,name')
            ->where('display_status', 'displayed')
            ->where('id', '>', (int) $lastId)
            ->orderBy('created_at', 'asc')
            ->limit(25)
            ->get([
                'id',
                'guest_name',
                'company_id',
                'message',
                'created_at',
            ]);

        return response()->json([
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => (int) $message->id,
                    'name' => $message->guest_name,
                    'company' => $message->company?->name ?? 'Guest',
                    'message' => $message->message,
                    'created_at' => $message->created_at?->toISOString(),
                ];
            })->values(),
            'last_id' => $messages->last()?->id ?? (int) $lastId,
        ]);
    }
}
