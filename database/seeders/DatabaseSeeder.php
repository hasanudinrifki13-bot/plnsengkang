<?php

namespace Database\Seeders;

use App\Models\Decision;
use App\Models\Escalation;
use App\Models\Issue;
use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roleMup = Role::firstOrCreate(['name' => 'MUP']);
        $roleAsman = Role::firstOrCreate(['name' => 'ASMAN']);
        $roleTl = Role::firstOrCreate(['name' => 'TL']);
        $roleStaff = Role::firstOrCreate(['name' => 'STAFF']);

        // 2. Positions
        $posMup = Position::firstOrCreate(['name' => 'Manajer Unit Pelaksana']);
        $posAsman = Position::firstOrCreate(['name' => 'Asisten Manajer Operasi']);
        $posTl = Position::firstOrCreate(['name' => 'Team Leader Operasi 1A']);
        $posStaff = Position::firstOrCreate(['name' => 'Operator & Teknisi Pembangkit']);

        // 3. Organization Units
        $unitUp = OrganizationUnit::firstOrCreate(['name' => 'UP Sengkang', 'parent_unit_id' => null]);
        $unitBagian = OrganizationUnit::firstOrCreate(['name' => 'Bagian Operasi', 'parent_unit_id' => $unitUp->id]);
        $unitSeksi = OrganizationUnit::firstOrCreate(['name' => 'Seksi Operasi 1A', 'parent_unit_id' => $unitBagian->id]);

        // 4. Users (Manajemen & Seluruh PIC di Mockup)
        $pass = Hash::make('password123');

        $userMup = User::updateOrCreate(
            ['email' => 'mup@plnnp.co.id'],
            ['name' => 'Ir. Bambang Wijaya (MUP)', 'employee_no' => 'PLN-001', 'password' => $pass, 'position_id' => $posMup->id, 'unit_id' => $unitUp->id, 'role_id' => $roleMup->id, 'is_active' => true]
        );

        $userAsman = User::updateOrCreate(
            ['email' => 'asman@plnnp.co.id'],
            ['name' => 'Ahmad Fauzi, ST (ASMAN)', 'employee_no' => 'PLN-002', 'password' => $pass, 'position_id' => $posAsman->id, 'unit_id' => $unitBagian->id, 'role_id' => $roleAsman->id, 'is_active' => true]
        );

        $userTl = User::updateOrCreate(
            ['email' => 'tl@plnnp.co.id'],
            ['name' => 'Andi Pratama, ST (TL)', 'employee_no' => 'PLN-003', 'password' => $pass, 'position_id' => $posTl->id, 'unit_id' => $unitSeksi->id, 'role_id' => $roleTl->id, 'is_active' => true]
        );

        $userBudi = User::updateOrCreate(
            ['email' => 'budi@plnnp.co.id'],
            ['name' => 'Budi Santoso', 'employee_no' => 'PLN-004', 'password' => $pass, 'position_id' => $posStaff->id, 'unit_id' => $unitSeksi->id, 'role_id' => $roleStaff->id, 'is_active' => true]
        );

        $userSiti = User::updateOrCreate(
            ['email' => 'siti@plnnp.co.id'],
            ['name' => 'Siti Rahma', 'employee_no' => 'PLN-005', 'password' => $pass, 'position_id' => $posStaff->id, 'unit_id' => $unitSeksi->id, 'role_id' => $roleStaff->id, 'is_active' => true]
        );

        $userAgus = User::updateOrCreate(
            ['email' => 'agus@plnnp.co.id'],
            ['name' => 'Agus Kurniawan', 'employee_no' => 'PLN-006', 'password' => $pass, 'position_id' => $posStaff->id, 'unit_id' => $unitSeksi->id, 'role_id' => $roleStaff->id, 'is_active' => true]
        );

        $userDewi = User::updateOrCreate(
            ['email' => 'dewi@plnnp.co.id'],
            ['name' => 'Dewi Lestari', 'employee_no' => 'PLN-007', 'password' => $pass, 'position_id' => $posStaff->id, 'unit_id' => $unitSeksi->id, 'role_id' => $roleStaff->id, 'is_active' => true]
        );

        $userRizki = User::updateOrCreate(
            ['email' => 'rizki@plnnp.co.id'],
            ['name' => 'Rizki Maulana', 'employee_no' => 'PLN-008', 'password' => $pass, 'position_id' => $posStaff->id, 'unit_id' => $unitSeksi->id, 'role_id' => $roleStaff->id, 'is_active' => true]
        );

        // Akun Staff Umum
        $userStaff = User::updateOrCreate(
            ['email' => 'staff@plnnp.co.id'],
            ['name' => 'Rian Saputra (STAFF)', 'employee_no' => 'PLN-009', 'password' => $pass, 'position_id' => $posStaff->id, 'unit_id' => $unitSeksi->id, 'role_id' => $roleStaff->id, 'is_active' => true]
        );

        // 5. SAMPLE ISSUES PERSIS PANEL 6 MOCKUP
        $issue1 = Issue::updateOrCreate(
            ['issue_no' => 'ISS-2026-0012'],
            [
                'title' => 'Kebocoran pada sistem seal pompa air laut',
                'description' => 'Terjadi kebocoran rembesan pelumas pada seal pompa air laut SWP-01B yang berpotensi menurunkan debit pendinginan kondensor.',
                'issue_type' => 'PROBLEM',
                'reported_by' => $userBudi->id,
                'owner_id' => $userTl->id,
                'current_level' => 'TL',
                'priority' => 'HIGH',
                'status' => 'Open',
                'due_date' => Carbon::now()->addDays(2)->toDateString(),
                'created_at' => Carbon::now()->subDays(2),
            ]
        );

        $issue2 = Issue::updateOrCreate(
            ['issue_no' => 'ISS-2026-0011'],
            [
                'title' => 'Pengadaan spare part tidak sesuai spesifikasi',
                'description' => 'Material gasket dan seal kit yang diterima dari vendor tidak sesuai dengan nomor part catalog turbin pabrikan. Memerlukan arahan bidang untuk klaim garansi.',
                'issue_type' => 'DECISION_REQUIRED',
                'reported_by' => $userSiti->id,
                'owner_id' => $userAsman->id,
                'current_level' => 'ASMAN',
                'priority' => 'MEDIUM',
                'status' => 'Escalated',
                'due_date' => Carbon::now()->addDays(5)->toDateString(),
                'created_at' => Carbon::now()->subDays(4),
            ]
        );

        $issue3 = Issue::updateOrCreate(
            ['issue_no' => 'ISS-2026-0010'],
            [
                'title' => 'Kapasitas cooling tower menurun',
                'description' => 'Efisiensi pendinginan cooling tower unit 2 drop sebesar 15% akibat penyumbatan pada fill pack. Diperlukan keputusan shut-down terencana dari Manajer Unit.',
                'issue_type' => 'PROBLEM',
                'reported_by' => $userAgus->id,
                'owner_id' => $userMup->id,
                'current_level' => 'MUP',
                'priority' => 'HIGH',
                'status' => 'Waiting Decision',
                'due_date' => Carbon::now()->addDays(1)->toDateString(),
                'created_at' => Carbon::now()->subDays(1),
            ]
        );

        $issue4 = Issue::updateOrCreate(
            ['issue_no' => 'ISS-2026-0009'],
            [
                'title' => 'Keterlambatan material project upgrade',
                'description' => 'Pengiriman panel kontrol DSC terlambat 1 minggu dari jadwal kontraktor dikarenakan kendala pengiriman logistik pelabuhan.',
                'issue_type' => 'ACTION',
                'reported_by' => $userDewi->id,
                'owner_id' => $userAsman->id,
                'current_level' => 'ASMAN',
                'priority' => 'MEDIUM',
                'status' => 'Under Review',
                'due_date' => Carbon::now()->addDays(7)->toDateString(),
                'created_at' => Carbon::now()->subDays(3),
            ]
        );

        $issue5 = Issue::updateOrCreate(
            ['issue_no' => 'ISS-2026-0008'],
            [
                'title' => 'Gangguan komunikasi sistem SCADA',
                'description' => 'Indikasi link loss pada switch RTU gardu hubung 150kV. Telah dilakukan restart modul komunikasi dan koneksi kembali normal.',
                'issue_type' => 'INFORMATION',
                'reported_by' => $userRizki->id,
                'owner_id' => $userTl->id,
                'current_level' => 'TL',
                'priority' => 'LOW',
                'status' => 'Closed',
                'due_date' => Carbon::now()->subDays(1)->toDateString(),
                'created_at' => Carbon::now()->subDays(1),
            ]
        );

        // Riwayat Eskalasi untuk Issue 2 & 3
        Escalation::firstOrCreate(
            ['issue_id' => $issue2->id, 'to_level' => 'ASMAN'],
            [
                'from_level' => 'TL',
                'from_user_id' => $userTl->id,
                'reason' => 'Perbedaan spesifikasi barang vendor berada di luar kewenangan TL, eskalasi ke Asman Pemeliharaan.',
                'status' => 'Accepted'
            ]
        );

        Escalation::firstOrCreate(
            ['issue_id' => $issue3->id, 'to_level' => 'MUP'],
            [
                'from_level' => 'ASMAN',
                'from_user_id' => $userAsman->id,
                'reason' => 'Penurunan performa berdampak pada daya mampu netto pembangkit, memerlukan izin persetujuan MUP.',
                'status' => 'Pending'
            ]
        );

        // Riwayat Keputusan untuk Issue 5
        Decision::firstOrCreate(
            ['issue_id' => $issue5->id],
            [
                'decision_by' => $userTl->id,
                'decision_level' => 'TL',
                'decision' => 'Penggantian kabel patch cord cadangan dan monitoring berkala 1x24 jam.',
                'rationale' => 'Komunikasi telah stabil dan data telemetri pulih 100%.',
                'follow_up' => 'Pengecekan ground loop saat jadwal pemeliharaan bulanan.'
            ]
        );

        // 6. SAMPLE TASKS PERSIS PANEL 5 MOCKUP
        Task::updateOrCreate(
            ['title' => 'Pemeliharaan Pompa Air Laut'],
            [
                'issue_id' => $issue1->id,
                'description' => 'Penggantian mechanical seal dan pengecekan bearing pompa.',
                'assigned_to' => $userBudi->id,
                'assigned_by' => $userTl->id,
                'start_date' => Carbon::now()->subDays(1)->toDateString(),
                'due_date' => Carbon::create(2026, 9, 15)->toDateString(),
                'progress_percent' => 80,
                'status' => 'In Progress',
            ]
        );

        Task::updateOrCreate(
            ['title' => 'Pemeriksaan Sistem Kontrol TG'],
            [
                'issue_id' => $issue2->id,
                'description' => 'Kalibrasi input analog dan output actuator pada turbin gas.',
                'assigned_to' => $userSiti->id,
                'assigned_by' => $userTl->id,
                'start_date' => Carbon::now()->subDays(2)->toDateString(),
                'due_date' => Carbon::create(2026, 9, 16)->toDateString(),
                'progress_percent' => 30,
                'status' => 'In Progress',
            ]
        );

        Task::updateOrCreate(
            ['title' => 'Penggantian Filter Udara'],
            [
                'issue_id' => null,
                'description' => 'Penggantian cartridge pre-filter udara intake kompresor.',
                'assigned_to' => $userAgus->id,
                'assigned_by' => $userTl->id,
                'start_date' => Carbon::now()->subDays(3)->toDateString(),
                'due_date' => Carbon::create(2026, 9, 14)->toDateString(),
                'progress_percent' => 100,
                'status' => 'Selesai',
            ]
        );

        Task::updateOrCreate(
            ['title' => 'Perbaikan Valve CV-101'],
            [
                'issue_id' => null,
                'description' => 'Overhaul packing stem dan uji kedap pneumatic positioner.',
                'assigned_to' => $userDewi->id,
                'assigned_by' => $userTl->id,
                'start_date' => Carbon::now()->subDays(1)->toDateString(),
                'due_date' => Carbon::create(2026, 9, 18)->toDateString(),
                'progress_percent' => 20,
                'status' => 'In Progress',
            ]
        );

        Task::updateOrCreate(
            ['title' => 'Pembersihan Area Kerja'],
            [
                'issue_id' => null,
                'description' => 'Housekeeping 5S area turbin deck lantai 2.',
                'assigned_to' => $userRizki->id,
                'assigned_by' => $userTl->id,
                'start_date' => Carbon::now()->subDays(4)->toDateString(),
                'due_date' => Carbon::create(2026, 9, 14)->toDateString(),
                'progress_percent' => 0,
                'status' => 'Terlambat',
            ]
        );
    }
}
