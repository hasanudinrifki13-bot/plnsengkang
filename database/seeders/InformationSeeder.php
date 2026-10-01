<?php

namespace Database\Seeders;

use App\Models\Information;
use App\Models\OrganizationUnit;
use App\Models\User;
use Illuminate\Database\Seeder;

class InformationSeeder extends Seeder
{
    public function run(): void
    {
        $mup = User::whereHas('role', fn($q) => $q->where('name', 'MUP'))->first() ?? User::first();
        $asman = User::whereHas('role', fn($q) => $q->where('name', 'ASMAN'))->first() ?? $mup;
        $tl = User::whereHas('role', fn($q) => $q->where('name', 'TL'))->first() ?? $asman;

        $opUnit = OrganizationUnit::where('name', 'like', '%Operasi%')->first();
        $harUnit = OrganizationUnit::where('name', 'like', '%Pemeliharaan%')->first();

        $items = [
            [
                'title' => 'Kesiapan Siaga Beban Puncak Sistem Kelistrikan Sulselrabar',
                'content' => 'Seluruh seksi Operasi dan Pemeliharaan diinstruksikan menjaga keandalan pembangkit Blok 1 dan Blok 2. Pastikan jadwal patroli inspeksi harian tercatat di sistem koordinasi dan tidak ada pekerjaan berisiko tinggi tanpa approval.',
                'category' => 'ARAHAN',
                'target_level' => 'ALL',
                'target_unit_id' => null,
                'publisher_id' => $mup->id,
                'published_at' => now()->subDays(2),
                'expires_at' => now()->addDays(14),
                'is_active' => true,
            ],
            [
                'title' => 'Peningkatan Disiplin K3 & Keselamatan Operasi Unit Sengkang',
                'content' => 'Wajib menggunakan APD lengkap di area turbin hall, HRSG, dan switchyard 150kV. Pastikan JSA dan Working Permit sudah diverifikasi sebelum pekerjaan dimulai. Target Zero Accident tanpa kompromi.',
                'category' => 'KEBIJAKAN',
                'target_level' => 'ALL',
                'target_unit_id' => null,
                'publisher_id' => $mup->id,
                'published_at' => now()->subDays(4),
                'expires_at' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'title' => 'Optimalisasi Pengawasan Heat Rate Blok 1 & Pemantauan Gas Flow',
                'content' => 'Diharapkan Team Leader Operasi dan staf operator meningkatkan monitoring efisiensi heat rate serta meminimalkan gas venting saat proses start-up & shut-down turbin.',
                'category' => 'ARAHAN',
                'target_level' => 'TL',
                'target_unit_id' => $opUnit?->id,
                'publisher_id' => $asman->id,
                'published_at' => now()->subDay(),
                'expires_at' => now()->addDays(7),
                'is_active' => true,
            ],
            [
                'title' => 'Jadwal Preventive Maintenance Mingguan Turbin Gas Unit 1.2',
                'content' => 'Pelaksanaan pemeriksaan filter udara intake, lube oil system, dan kalibrasi sensor temperatur TG 1.2 dijadwalkan hari Rabu pukul 08:30 WITA.',
                'category' => 'PENGUMUMAN',
                'target_level' => 'STAFF',
                'target_unit_id' => $harUnit?->id,
                'publisher_id' => $tl->id,
                'published_at' => now(),
                'expires_at' => now()->addDays(5),
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            Information::firstOrCreate(
                ['title' => $item['title']],
                $item
            );
        }
    }
}
