<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Prevent deleting a company that is still referenced by guest book entries.
     */
    protected static function booted(): void
    {
        static::deleting(function (self $company) {
            if ($company->guestBooks()->exists()) {
                throw new \RuntimeException('Cannot delete company while guest book references exist.');
            }
        });
    }

    /**
     * Get the guest books for the company.
     */
    public function guestBooks(): HasMany
    {
        return $this->hasMany(GuestBook::class);
    }
}
