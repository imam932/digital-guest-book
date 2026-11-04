<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EventSetting extends Model
{
    use HasFactory;

    protected $table = 'event_settings';

    protected $fillable = [
        'event_name',
        'event_description',
        'main_background',
        'form_background',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function current(): ?self
    {
        return static::query()->where('is_active', true)->first() ?? static::query()->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function mainBackgroundUrl(): ?string
    {
        if (! $this->main_background) {
            return null;
        }

        return Storage::disk('public')->url($this->main_background);
    }

    public function formBackgroundUrl(): ?string
    {
        if (! $this->form_background) {
            return null;
        }

        return Storage::disk('public')->url($this->form_background);
    }
}
