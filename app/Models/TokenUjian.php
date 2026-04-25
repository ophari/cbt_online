<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenUjian extends Model
{
    protected $fillable = [
        'token',
        'is_active',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Generate token acak 5 karakter huruf besar & angka.
     */
    public static function generateToken(): self
    {
        // Nonaktifkan semua token lama
        self::where('is_active', true)->update(['is_active' => false]);

        // Buat token baru, aktif selama 15 menit
        return self::create([
            'token' => strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5)),
            'is_active' => true,
            'expired_at' => now()->addMinutes(15),
        ]);
    }

    /**
     * Ambil token yang aktif saat ini.
     */
    public static function getActiveToken(): ?self
    {
        $token = self::where('is_active', true)->latest()->first();

        // Jika token sudah expired, nonaktifkan
        if ($token && $token->expired_at && now()->greaterThan($token->expired_at)) {
            $token->update(['is_active' => false]);

            return null;
        }

        return $token;
    }
}
