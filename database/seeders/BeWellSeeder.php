<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\ClinicSetting;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BeWellSeeder extends Seeder
{
    public function run(): void
    {
        // Clinic
        $clinic = Clinic::create([
            'name' => 'Be Well Sağlıklı Yaşam Merkezi',
            'slug' => 'bewellklinik',
            'domain' => 'bewellklinik.com',
            'is_active' => true,
        ]);

        // Settings
        $settings = [
            // General
            ['key' => 'owner_name', 'value' => 'Uzm. Dyt. Mualla Seray Birikim', 'group' => 'general'],
            ['key' => 'email', 'value' => 'info@bewellklinik.com', 'group' => 'general'],
            ['key' => 'phone', 'value' => '+90 (392) ___ __ __', 'group' => 'general'],
            ['key' => 'address', 'value' => 'Mağusa, Kuzey Kıbrıs', 'group' => 'general'],
            ['key' => 'working_hours', 'value' => 'Pzt-Cmt 09:00-18:00', 'group' => 'general'],

            // Branding
            ['key' => 'primary_color', 'value' => '#8E9E82', 'group' => 'branding'],
            ['key' => 'secondary_color', 'value' => '#6B7F5E', 'group' => 'branding'],
            ['key' => 'logo_url', 'value' => null, 'group' => 'branding'],

            // Social
            ['key' => 'instagram', 'value' => '@bewellklinik', 'group' => 'social'],
            ['key' => 'whatsapp', 'value' => null, 'group' => 'social'],
        ];

        foreach ($settings as $setting) {
            ClinicSetting::create([
                'clinic_id' => $clinic->id,
                ...$setting,
            ]);
        }

        // Admin user
        User::create([
            'clinic_id' => $clinic->id,
            'name' => 'Mualla Seray Birikim',
            'email' => 'admin@bewellklinik.com',
            'password' => Hash::make('bewell2025'),
            'role' => 'owner',
            'is_active' => true,
        ]);

        // Programs
        Program::create([
            'clinic_id' => $clinic->id,
            'title' => 'Sağlıklı Başlangıç Programı',
            'price' => 149.00,
            'currency' => 'EUR',
            'duration' => '4 Hafta',
            'description' => 'Sağlıklı beslenme yolculuğunuza ilk adımı atın. 4 haftalık temel beslenme programı ile alışkanlıklarınızı dönüştürmeye başlayın.',
            'features' => [
                '4 haftalık detaylı beslenme planı',
                'Haftalık alışveriş listeleri',
                '50+ sağlıklı ve kolay tarif',
                'Besin takip rehberi',
                'Porsiyon kontrol kılavuzu',
            ],
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 1,
        ]);

        Program::create([
            'clinic_id' => $clinic->id,
            'title' => 'Premium Dönüşüm Programı',
            'price' => 249.00,
            'currency' => 'EUR',
            'duration' => '8 Hafta',
            'description' => 'Kapsamlı ve kişiselleştirilmiş 8 haftalık program ile hedeflerinize ulaşın. Uzman takibi dahil, tam dönüşüm paketi.',
            'features' => [
                '8 haftalık özelleştirilmiş plan',
                'Haftalık uzman diyetisyen takibi',
                '100+ sağlıklı tarif koleksiyonu',
                'Egzersiz ve hareket rehberi',
                'E-posta ile birebir soru-cevap desteği',
                'Motivasyon ve alışkanlık koçluğu',
            ],
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);
    }
}
