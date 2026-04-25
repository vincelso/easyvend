<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'price',
        'stock',
        'expiry_date',
        'image',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    // ── Image helpers ─────────────────────────────
    public function imageUrl(): string
    {
        if ($this->image && file_exists(storage_path('app/public/' . $this->image))) {
            return asset('storage/' . $this->image);
        }
        return '';
    }

    public function hasImage(): bool
    {
        return !empty($this->image) && file_exists(storage_path('app/public/' . $this->image));
    }

    // ── Expiry helpers ────────────────────────────
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date
            && !$this->isExpired()
            && $this->expiry_date->lte(now()->addDays($days));
    }

    public function daysUntilExpiry(): ?int
    {
        if (!$this->expiry_date) return null;
        return (int) now()->startOfDay()->diffInDays($this->expiry_date->startOfDay(), false);
    }

    public function expiryStatus(): string
    {
        if (!$this->expiry_date)       return 'none';
        if ($this->isExpired())        return 'expired';
        if ($this->isExpiringSoon(7))  return 'critical';
        if ($this->isExpiringSoon(30)) return 'warning';
        return 'good';
    }

    // ── Relationships ─────────────────────────────
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}