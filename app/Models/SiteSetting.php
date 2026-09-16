<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_name',
        'brand_logo_url',
        'brand_logo_path',
        'developer_title',
        'developer_name',
        'developer_tagline',
        'phone',
        'whatsapp_url',
        'facebook_url',
        'avatar_url',
        'avatar_path',
    ];

    public static function defaults(): array
    {
        return [
            'brand_name' => 'Mess Manager',
            'brand_logo_url' => null,
            'brand_logo_path' => null,
            'developer_title' => 'Sponsored & Developed By',
            'developer_name' => 'Md. Mithu Rahman',
            'developer_tagline' => 'Smart Soft X InterX · Mess Manager',
            'phone' => '01875960149',
            'whatsapp_url' => 'https://wa.me/8801875960149',
            'facebook_url' => 'https://www.facebook.com/Try.to.be.a.rainbow.in.someones.cloud.M2',
            'avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=600&q=80',
            'avatar_path' => null,
        ];
    }

    public static function getCurrent(): self
    {
        return self::query()->firstOrCreate([], self::defaults());
    }
}
