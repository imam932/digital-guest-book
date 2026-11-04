<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateEventSettingsRequest;
use App\Models\EventSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventSettingsController extends Controller
{
    public function index(): View
    {
        $settings = EventSetting::query()->first() ?? EventSetting::query()->create([
            'event_name' => 'Digital Guest Book',
            'event_description' => 'A memorable guest experience for your event.',
            'is_active' => true,
        ]);

        return view('admin.settings', compact('settings'));
    }

    public function update(UpdateEventSettingsRequest $request): RedirectResponse
    {
        $settings = EventSetting::query()->first() ?? new EventSetting();
        $data = $request->validated();

        if ($request->hasFile('main_background')) {
            $data['main_background'] = $this->storeUploadedBackground(
                $request->file('main_background'),
                $settings->main_background
            );
        } else {
            $data['main_background'] = $settings->main_background;
        }

        if ($request->hasFile('form_background')) {
            $data['form_background'] = $this->storeUploadedBackground(
                $request->file('form_background'),
                $settings->form_background
            );
        } else {
            $data['form_background'] = $settings->form_background;
        }

        $settings->fill($data);
        $settings->save();

        return redirect()->route('admin.settings')->with('success', 'Event settings updated successfully.');
    }

    protected function storeUploadedBackground($file, ?string $existingPath = null): string
    {
        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $filename = sprintf('%s.%s', Str::uuid()->toString(), $extension);
        $path = $file->storeAs('event-settings', $filename, 'public');

        return $path;
    }
}
