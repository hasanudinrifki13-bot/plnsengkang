<?php

namespace Database\Seeders;

use App\Models\OrganizationHierarchy;
use Illuminate\Database\Seeder;

class OrganizationHierarchySeeder extends Seeder
{
    public function run(): void
    {
        $hierarchies = [
            // Level Asman to MUP
            ['incumbent' => 'ASMAN OP', 'parent' => 'MUP', 'incumbent_title' => 'Asisten Manajer Operasi', 'parent_title' => 'Manager UP Sengkang', 'person_name' => 'H. MUHAMMAD ISMAIL', 'sort_order' => 1],
            ['incumbent' => 'ASMAN HAR', 'parent' => 'MUP', 'incumbent_title' => 'Asisten Manajer Pemeliharaan', 'parent_title' => 'Manager UP Sengkang', 'person_name' => 'CAHYA PATRIA C.T', 'sort_order' => 2],
            ['incumbent' => 'ASMAN ENG', 'parent' => 'MUP', 'incumbent_title' => 'Asisten Manajer Engineering', 'parent_title' => 'Manager UP Sengkang', 'person_name' => 'FUAD ANWAR', 'sort_order' => 3],
            ['incumbent' => 'ASMAN BS', 'parent' => 'MUP', 'incumbent_title' => 'Asisten Manajer Business Support', 'parent_title' => 'Manager UP Sengkang', 'person_name' => 'PRAN MOCH SOLEH', 'sort_order' => 4],
            ['incumbent' => 'TL K3', 'parent' => 'MUP', 'incumbent_title' => 'Team Leader K3 & Keamanan', 'parent_title' => 'Manager UP Sengkang', 'person_name' => 'YOGA KURNIAWAN', 'sort_order' => 5],
            ['incumbent' => 'TL LINGKUNGAN', 'parent' => 'MUP', 'incumbent_title' => 'Team Leader Lingkungan', 'parent_title' => 'Manager UP Sengkang', 'person_name' => 'H. ABDUL MALIK', 'sort_order' => 6],

            // Level TL to ASMAN OP (Operasi)
            ['incumbent' => 'TL RENDALOP', 'parent' => 'ASMAN OP', 'incumbent_title' => 'Team Leader Rendal Operasi', 'parent_title' => 'Asisten Manajer Operasi', 'person_name' => 'SINUNG RAHARJO', 'sort_order' => 7],
            ['incumbent' => 'TL EP', 'parent' => 'ASMAN OP', 'incumbent_title' => 'Team Leader Evaluasi Kinerja & Kimia', 'parent_title' => 'Asisten Manajer Operasi', 'person_name' => 'ANDI ANANDA MAGFIRAH', 'sort_order' => 8],
            ['incumbent' => 'TL PROD 1A', 'parent' => 'ASMAN OP', 'incumbent_title' => 'Team Leader Produksi Blok 1A', 'parent_title' => 'Asisten Manajer Operasi', 'person_name' => 'MURSALAM', 'sort_order' => 9],
            ['incumbent' => 'TL PROD 1B', 'parent' => 'ASMAN OP', 'incumbent_title' => 'Team Leader Produksi Blok 1B', 'parent_title' => 'Asisten Manajer Operasi', 'person_name' => 'MEDY IVANTRY TANDI BUA', 'sort_order' => 10],
            ['incumbent' => 'TL PROD 1C', 'parent' => 'ASMAN OP', 'incumbent_title' => 'Team Leader Produksi Blok 1C', 'parent_title' => 'Asisten Manajer Operasi', 'person_name' => 'H. HERMAN JAYA, ST', 'sort_order' => 11],
            ['incumbent' => 'TL PROD 1D', 'parent' => 'ASMAN OP', 'incumbent_title' => 'Team Leader Produksi Blok 1D', 'parent_title' => 'Asisten Manajer Operasi', 'person_name' => 'MUH NASAR D', 'sort_order' => 12],
            ['incumbent' => 'TL PROD 2B', 'parent' => 'ASMAN OP', 'incumbent_title' => 'Team Leader Produksi Blok 2B', 'parent_title' => 'Asisten Manajer Operasi', 'person_name' => 'NADAR', 'sort_order' => 13],
            ['incumbent' => 'TL PROD 2C', 'parent' => 'ASMAN OP', 'incumbent_title' => 'Team Leader Produksi Blok 2C', 'parent_title' => 'Asisten Manajer Operasi', 'person_name' => 'AGUSNAWAN', 'sort_order' => 14],
            ['incumbent' => 'TL PROD 2D', 'parent' => 'ASMAN OP', 'incumbent_title' => 'Team Leader Produksi Blok 2D', 'parent_title' => 'Asisten Manajer Operasi', 'person_name' => 'SUNARDI SM', 'sort_order' => 15],

            // Level TL to ASMAN HAR (Pemeliharaan)
            ['incumbent' => 'TL HAR MESIN', 'parent' => 'ASMAN HAR', 'incumbent_title' => 'Team Leader Pemeliharaan Mesin & Sipil', 'parent_title' => 'Asisten Manajer Pemeliharaan', 'person_name' => 'SAINAL TAJANG', 'sort_order' => 16],
            ['incumbent' => 'TL RENDAL HAR', 'parent' => 'ASMAN HAR', 'incumbent_title' => 'Team Leader Rendal Pemeliharaan', 'parent_title' => 'Asisten Manajer Pemeliharaan', 'person_name' => 'YERSON', 'sort_order' => 17],
            ['incumbent' => 'TL HAR LISTRIK', 'parent' => 'ASMAN HAR', 'incumbent_title' => 'Team Leader Pemeliharaan Listrik', 'parent_title' => 'Asisten Manajer Pemeliharaan', 'person_name' => 'SUHAIMI', 'sort_order' => 18],
            ['incumbent' => 'TL HAR KONIN', 'parent' => 'ASMAN HAR', 'incumbent_title' => 'Team Leader Pemeliharaan Kontrol & Instrumen', 'parent_title' => 'Asisten Manajer Pemeliharaan', 'person_name' => 'HENDRA GULANG', 'sort_order' => 19],
            ['incumbent' => 'TL INVENTORY', 'parent' => 'ASMAN HAR', 'incumbent_title' => 'Team Leader Inventory & Gudang', 'parent_title' => 'Asisten Manajer Pemeliharaan', 'person_name' => 'NURHALIM', 'sort_order' => 20],
            ['incumbent' => 'TL OUTAGE', 'parent' => 'ASMAN HAR', 'incumbent_title' => 'Team Leader Outage Management', 'parent_title' => 'Asisten Manajer Pemeliharaan', 'person_name' => 'MUHAMMAD SIDDIQ B.', 'sort_order' => 21],

            // Level TL to ASMAN ENG (Engineering)
            ['incumbent' => 'TL CBM', 'parent' => 'ASMAN ENG', 'incumbent_title' => 'Team Leader Condition Based Maintenance', 'parent_title' => 'Asisten Manajer Engineering', 'person_name' => 'ARMAN ARIFIN', 'sort_order' => 22],
            ['incumbent' => 'TL SO & CA', 'parent' => 'ASMAN ENG', 'incumbent_title' => 'Team Leader System Owner & Component Analyst', 'parent_title' => 'Asisten Manajer Engineering', 'person_name' => 'HARI AKBAR SYUAIB', 'sort_order' => 23],
            ['incumbent' => 'TL MMRK', 'parent' => 'ASMAN ENG', 'incumbent_title' => 'Team Leader Manajemen Mutu, Risiko & Kinerja', 'parent_title' => 'Asisten Manajer Engineering', 'person_name' => 'AFFANDY HERMAWAN', 'sort_order' => 24],

            // Level TL to ASMAN BS (Business Support)
            ['incumbent' => 'TL KEUANGAN', 'parent' => 'ASMAN BS', 'incumbent_title' => 'Team Leader Keuangan', 'parent_title' => 'Asisten Manajer Business Support', 'person_name' => 'ZUL QADRI A. SADIK', 'sort_order' => 25],
            ['incumbent' => 'TL SDM & UMUM', 'parent' => 'ASMAN BS', 'incumbent_title' => 'Team Leader SDM, Umum & CSR', 'parent_title' => 'Asisten Manajer Business Support', 'person_name' => 'ANDI RIDWANSYAH B.P', 'sort_order' => 26],
            ['incumbent' => 'TL PENGADAAN', 'parent' => 'ASMAN BS', 'incumbent_title' => 'Team Leader Pengadaan & Logistik', 'parent_title' => 'Asisten Manajer Business Support', 'person_name' => 'AGUS SALIM HANAFI', 'sort_order' => 27],
        ];

        foreach ($hierarchies as $row) {
            OrganizationHierarchy::updateOrCreate(
                ['incumbent' => $row['incumbent']],
                $row
            );
        }
    }
}
