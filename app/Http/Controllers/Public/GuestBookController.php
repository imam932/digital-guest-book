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
        $rules = [
            'guest_name' => ['required', 'string', 'max:255'],
            'company_id' => ['required'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];

        $messages = [
            'guest_name.required' => 'Please enter your name.',
            'company_id.required' => 'Please select a company.',
            'message.required' => 'Please add a message.',
            'message.min' => 'Your message must be at least 10 characters long.',
            'message.max' => 'Your message exceeds the maximum allowed length.',
        ];

        if ($request->input('company_id') === 'other') {
            $rules['company_id'] = ['required', 'in:other'];
            $rules['other_company'] = ['required', 'string', 'max:150'];
            $messages['other_company.required'] = 'Please enter your company name.';
            $messages['other_company.max'] = 'Company name must not exceed 150 characters.';
        } else {
            $rules['company_id'] = ['required', 'integer', 'exists:companies,id'];
            $rules['other_company'] = ['nullable', 'prohibited'];
            $messages['company_id.integer'] = 'Please select a valid company.';
            $messages['company_id.exists'] = 'The selected company is invalid or unavailable.';
        }

        $validated = $request->validate($rules, $messages);

        if ($validated['company_id'] === 'other') {
            $validated['other_company'] = trim((string) $validated['other_company']);
            $validated['company_id'] = null;
            $company = null;
        } else {
            $company = Company::active()->find($validated['company_id']);

            if (! $company) {
                throw ValidationException::withMessages([
                    'company_id' => ['Please select an active company.'],
                ]);
            }

            $validated['other_company'] = null;
        }

        $companyKey = $company?->id ?? 'other:' . strtolower($validated['other_company']);
        $ip = $request->ip();
        $messageHash = hash('sha256', strtolower(trim($validated['guest_name'])) . '|' . strtolower(trim($validated['message'])) . '|' . $companyKey . '|' . ($ip ?? 'unknown'));

        $recentSubmission = DB::table('guest_books')
            ->when($company, fn ($query) => $query->where('company_id', $company->id), fn ($query) => $query->where('other_company', trim((string) $validated['other_company'])))
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
            'company_id' => $company?->id,
            'other_company' => $company ? null : $validated['other_company'],
            'guest_name' => trim($validated['guest_name']),
            'message' => trim(strip_tags($validated['message'])),
            'display_status' => 'displayed',
        ]);

        return response()->json([
            'message' => 'Thank you! Your message has been sent. Please check the screen.',
            'guestBook' => [
                'id' => $guestBook->id,
                'guest_name' => $guestBook->guest_name,
                'company_id' => $guestBook->company_id,
                'other_company' => $guestBook->other_company,
            ],
        ], 201);
    }
}
