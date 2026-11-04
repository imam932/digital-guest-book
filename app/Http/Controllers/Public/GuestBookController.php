<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\GuestBook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GuestBookController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'guest_name' => ['required', 'string', 'max:255'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'guest_name.required' => 'Please enter your name.',
            'company_id.required' => 'Please select a company.',
            'company_id.exists' => 'The selected company is invalid or unavailable.',
            'message.required' => 'Please add a message.',
            'message.min' => 'Your message must be at least 10 characters long.',
            'message.max' => 'Your message exceeds the maximum allowed length.',
        ]);

        $company = Company::active()->find($validated['company_id']);

        if (! $company) {
            throw ValidationException::withMessages([
                'company_id' => ['Please select an active company.'],
            ]);
        }

        $ip = $request->ip();
        $messageHash = hash('sha256', strtolower(trim($validated['guest_name'])) . '|' . strtolower(trim($validated['message'])) . '|' . $company->id . '|' . ($ip ?? 'unknown'));

        $recentSubmission = DB::table('guest_books')
            ->where('company_id', $company->id)
            ->where('guest_name', trim($validated['guest_name']))
            ->where('message', trim($validated['message']))
            ->where('created_at', '>=', now()->subMinutes(10))
            ->exists();

        if ($recentSubmission) {
            return response()->json([
                'message' => 'A very similar submission was already received recently. Please wait a moment and try again.',
            ], 429);
        }

        $guestBook = GuestBook::create([
            'company_id' => $company->id,
            'guest_name' => trim($validated['guest_name']),
            'message' => trim(strip_tags($validated['message'])),
            'display_status' => 'displayed',
        ]);

        return response()->json([
            'message' => 'Terima Kasih! Pesan Anda telah terkirim, lihat di laman utama.',
            'guestBook' => [
                'id' => $guestBook->id,
                'guest_name' => $guestBook->guest_name,
                'company_id' => $guestBook->company_id,
            ],
        ], 201);
    }
}
