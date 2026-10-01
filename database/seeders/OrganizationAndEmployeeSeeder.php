<?php

namespace Database\Seeders;

use App\Models\OrganizationUnit;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganizationAndEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Inisialisasi Peran (Role) Sesuai BSD
        $roleMup   = Role::firstOrCreate(['name' => 'MUP']);
        $roleAsman = Role::firstOrCreate(['name' => 'ASMAN']);
        $roleTl    = Role::firstOrCreate(['name' => 'TL']);
        $roleStaff = Role::firstOrCreate(['name' => 'STAFF']);

        // 2. Inisialisasi Unit Organisasi Sesuai Gambar Struktur Organisasi UP Sengkang
        // Root: UP Sengkang
        $root = OrganizationUnit::firstOrCreate(['name' => 'UP Sengkang'], ['parent_unit_id' => null]);

        // Level 1 Departments
        $deptK3          = OrganizationUnit::firstOrCreate(['name' => 'K3 & Keamanan'], ['parent_unit_id' => $root->id]);
        $deptLingkungan  = OrganizationUnit::firstOrCreate(['name' => 'Lingkungan'], ['parent_unit_id' => $root->id]);
        $deptOperasi     = OrganizationUnit::firstOrCreate(['name' => 'Bagian Operasi'], ['parent_unit_id' => $root->id]);
        $deptHar         = OrganizationUnit::firstOrCreate(['name' => 'Bagian Pemeliharaan'], ['parent_unit_id' => $root->id]);
        $deptEngineering = OrganizationUnit::firstOrCreate(['name' => 'Bagian Engineering & QA'], ['parent_unit_id' => $root->id]);
        $deptBs          = OrganizationUnit::firstOrCreate(['name' => 'Bagian Business Support'], ['parent_unit_id' => $root->id]);

        // Sub-units / Seksi Mapping
        $unitsMap = [
            'MUP SENGKANG'       => $root,
            'K3 & KEAMANAN'      => $deptK3,
            'LINGKUNGAN'         => $deptLingkungan,
            'OPERASI'            => $deptOperasi,
            'RENDAL OPERASI'     => OrganizationUnit::firstOrCreate(['name' => 'Rendal Operasi'], ['parent_unit_id' => $deptOperasi->id]),
            'KIMIA'              => OrganizationUnit::firstOrCreate(['name' => 'Kimia'], ['parent_unit_id' => $deptOperasi->id]),
            'PRODUKSI BLOK 1 A'  => OrganizationUnit::firstOrCreate(['name' => 'Produksi Blok 1 A'], ['parent_unit_id' => $deptOperasi->id]),
            'PRODUKSI BLOK 1 B'  => OrganizationUnit::firstOrCreate(['name' => 'Produksi Blok 1 B'], ['parent_unit_id' => $deptOperasi->id]),
            'PRODUKSI BLOK 1 C'  => OrganizationUnit::firstOrCreate(['name' => 'Produksi Blok 1 C'], ['parent_unit_id' => $deptOperasi->id]),
            'PRODUKSI BLOK 1 D'  => OrganizationUnit::firstOrCreate(['name' => 'Produksi Blok 1 D'], ['parent_unit_id' => $deptOperasi->id]),
            'PRODUKSI BLOK 2 A'  => OrganizationUnit::firstOrCreate(['name' => 'Produksi Blok 2 A'], ['parent_unit_id' => $deptOperasi->id]),
            'PRODUKSI BLOK 2 B'  => OrganizationUnit::firstOrCreate(['name' => 'Produksi Blok 2 B'], ['parent_unit_id' => $deptOperasi->id]),
            'PRODUKSI BLOK 2 C'  => OrganizationUnit::firstOrCreate(['name' => 'Produksi Blok 2 C'], ['parent_unit_id' => $deptOperasi->id]),
            'PRODUKSI BLOK 2 D'  => OrganizationUnit::firstOrCreate(['name' => 'Produksi Blok 2 D'], ['parent_unit_id' => $deptOperasi->id]),
            'PEMELIHARAAN'       => $deptHar,
            'RENDAL PEMELIHARAAN'=> OrganizationUnit::firstOrCreate(['name' => 'Rendal Pemeliharaan'], ['parent_unit_id' => $deptHar->id]),
            'HAR LISTRIK'        => OrganizationUnit::firstOrCreate(['name' => 'Pemeliharaan Listrik'], ['parent_unit_id' => $deptHar->id]),
            'HAR KONIN'          => OrganizationUnit::firstOrCreate(['name' => 'Pemeliharaan Kontrol & Instrumen'], ['parent_unit_id' => $deptHar->id]),
            'HAR MESIN'          => OrganizationUnit::firstOrCreate(['name' => 'Pemeliharaan Mesin & Sipil'], ['parent_unit_id' => $deptHar->id]),
            'OUTAGE MANAGEMENT'  => OrganizationUnit::firstOrCreate(['name' => 'Outage Management'], ['parent_unit_id' => $deptHar->id]),
            'INVENTORY & GUDANG' => OrganizationUnit::firstOrCreate(['name' => 'Inventory Control & Gudang'], ['parent_unit_id' => $deptHar->id]),
            'ENGINEERING'        => $deptEngineering,
            'SO & CA'            => OrganizationUnit::firstOrCreate(['name' => 'System Owner & Component Analyst'], ['parent_unit_id' => $deptEngineering->id]),
            'CBM'                => OrganizationUnit::firstOrCreate(['name' => 'Condition Based Maintenance'], ['parent_unit_id' => $deptEngineering->id]),
            'MMRK'               => OrganizationUnit::firstOrCreate(['name' => 'Manajemen Mutu, Risiko & Kinerja'], ['parent_unit_id' => $deptEngineering->id]),
            'ADMINISTRASI'       => $deptBs,
            'SDM & UMUM'         => OrganizationUnit::firstOrCreate(['name' => 'SDM, Umum & CSR'], ['parent_unit_id' => $deptBs->id]),
            'SEKERTARIS'         => OrganizationUnit::firstOrCreate(['name' => 'Sekretaris'], ['parent_unit_id' => $deptBs->id]),
            'LOGISTIK & PENGADAAN'=> OrganizationUnit::firstOrCreate(['name' => 'Logistik & Pengadaan'], ['parent_unit_id' => $deptBs->id]),
            'KEUANGAN'           => OrganizationUnit::firstOrCreate(['name' => 'Keuangan'], ['parent_unit_id' => $deptBs->id]),
        ];

        // 3. Raw Data Karyawan Nyata dari User
        $rawEmployees = [
            ['8108050JA', 'MOH. FURQON AKHSANI', 'MANAGER UNIT', 'MUP SENGKANG'],
            ['9315231TA', 'YOGA KURNIAWAN', 'TEAM LEADER K3 DAN KEAMANAN', 'K3 & KEAMANAN'],
            ['8723023PKW', 'RUSDI ALFRIYANTO RIWALDI RAHMA', 'TECHNICIAN K3 & KEAMANAN', 'K3 & KEAMANAN'],
            ['9623022WGK', 'IRIANTI APRILLA', 'JUNIOR K3 & KEAMANAN', 'K3 & KEAMANAN'],
            ['8023020WGK', 'SYAFRUDDIN', 'TENAGA PENDUKUNG K3 & KEAMANAN', 'K3 & KEAMANAN'],
            ['8424056WGK', 'BASO RIDWAN', 'TENAGA PENDUKUNG K3 & KEAMANAN', 'K3 & KEAMANAN'],
            ['0525096WGK', 'MUH. AL AQSHAL KHIDAYATULLAH S.', 'TENAGA PENDUKUNG K3 & KEAMANAN', 'K3 & KEAMANAN'],
            ['7223005PKW', 'H. ABDUL MALIK', 'TEAM LEADER LINGKUNGAN', 'LINGKUNGAN'],
            ['8123003WGK', 'MUH YUSUF', 'TENAGA PENDUKUNG LINGKUNGAN', 'LINGKUNGAN'],
            ['7823006WGK', 'ISMAIL', 'TENAGA PENDUKUNG LINGKUNGAN', 'LINGKUNGAN'],
            ['9624065WGK', 'NURUL FAHMIAH', 'JUNIOR OFFICER LINGKUNGAN', 'LINGKUNGAN'],
            ['9825097WGK', 'BASO AWALUDDIN', 'TENAGA PENDUKUNG LINGKUNGAN', 'LINGKUNGAN'],
            ['9825092WGK', 'ULVANI JULFIRA', 'TENAGA PENDUKUNG LINGKUNGAN', 'LINGKUNGAN'],
            ['8609070JA', 'FUAD ANWAR', 'ASMAN ENGINEERING & QA', 'ENGINEERING'],
            ['9417078BL', 'HARI AKBAR SYUAIB', 'TEAM LEADER SYSTEM OWNER & COMPONENT ANALYST', 'SO & CA'],
            ['9017082BL', 'M TAUFIK HASRIANSYAH ASIARI', 'TECHNICIAN SYSTEM OWNER', 'SO & CA'],
            ['8423028PKW', 'ANDI IMRAN MUSTIKA', 'TECHNICIAN COMPONENT ANALYST', 'SO & CA'],
            ['9824059WGK', 'IRFAN', 'TENAGA PENDUKUNG SYSTEM OWNER & COMPONENT ANALYST', 'SO & CA'],
            ['8312589KP', 'ARMAN ARIFIN', 'TEAM LEADER CONDITION BASED MAINTENANCE', 'CBM'],
            ['9217026AM', 'DAUD MALOMO', 'TECHNICIAN CONDITION BASED MAINTENANCE', 'CBM'],
            ['9923039WGK', 'NUARDI', 'JUNIOR TECHNICIAN CONDITION BASED MAINTENANCE', 'CBM'],
            ['8424086WGK', 'ABDUL RAHIM', 'TENAGA PENDUKUNG TEKNIK CONDITION BASED MAINTENANCE', 'CBM'],
            ['7423008PKW', 'AFFANDY HERMAWAN', 'TEAM LEADER MANAJEMEN MUTU, RISIKO & KINERJA', 'MMRK'],
            ['7123016PKW', 'H. NASRUL MARZUKI', 'OFFICER MANAJEMEN MUTU, RISIKO & KEPATUHAN', 'MMRK'],
            ['9523023WGK', 'TIARA SUHARYATI', 'JUNIOR OFFICER MANAJEMEN MUTU, RISIKO & KINERJA', 'MMRK'],
            ['9924077WGK', 'HERI ANUGRAH PRATAMA', 'JUNIOR OFFICER MANAJEMEN MUTU, RISIKO & KINERJA', 'MMRK'],
            ['9424069WGK', 'WAHYU HIDAYAT', 'TENAGA PENDUKUNG MANAJEMEN MUTU, RISIKO & KINERJA', 'MMRK'],
            ['7323002PKW', 'H. MUHAMMAD ISMAIL', 'ASMAN OPERASI', 'OPERASI'],
            ['8812558PC', 'SINUNG RAHARJO', 'TEAM LEADER RENDAL OPERASI', 'RENDAL OPERASI'],
            ['9115083ID', 'MUH. ASPAR ARIFIN', 'OFFICER RENDAL OPERASI', 'RENDAL OPERASI'],
            ['0424049WGK', 'ALGAZALI', 'TENAGA PENDUKUNG PERENCANAAN & PENGENDALIAN OPERASI', 'RENDAL OPERASI'],
            ['9923028WGK', 'ANDI ANANDA MAGFIRAH', 'JUNIOR OFFICER KIMIA', 'KIMIA'],
            ['9724066WGK', 'RIYAN ADRIANSYAH AMRI', 'JUNIOR OFFICER KIMIA', 'KIMIA'],
            ['9925088WGK', 'BISMI RATU FORTUNA N', 'TENAGA PENDUKUNG KIMIA', 'KIMIA'],
            ['0124068WGK', 'SITTI MUNAWARAH SYAM', 'TENAGA PENDUKUNG KIMIA', 'KIMIA'],
            ['0324064WGK', 'MUHAMMAD ABDIL MUKMIN SYAH', 'TENAGA PENDUKUNG KIMIA', 'KIMIA'],
            ['8915056TD', 'MURSALAM', 'TEAM LEADER PRODUKSI BLOK 1 A', 'PRODUKSI BLOK 1 A'],
            ['9123038PKW', 'SATRIAWAN', 'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 A', 'PRODUKSI BLOK 1 A'],
            ['9324067WGK', 'RUDI', 'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 1 A', 'PRODUKSI BLOK 1 A'],
            ['9723011WGK', 'ADRIAN', 'JUNIOR TECHNICIAN PRODUKSI BLOK-1 A', 'PRODUKSI BLOK 1 A'],
            ['8817083BL', 'MUSRIYADI', 'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 A', 'PRODUKSI BLOK 1 A'],
            ['8917074BL', 'MEDY IVANTRY TANDI BUA', 'TEAM LEADER PRODUKSI BLOK 1 B', 'PRODUKSI BLOK 1 B'],
            ['8515058TD', 'AHMAD BAYU PERMADI', 'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 B', 'PRODUKSI BLOK 1 B'],
            ['9117084BL', 'ONESIMUS', 'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 B', 'PRODUKSI BLOK 1 B'],
            ['9923008WGK', 'MUH. ASHAR', 'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 1 B', 'PRODUKSI BLOK 1 B'],
            ['9824079WGK', 'MOCH. ALIF AFDILAH SALDI', 'JUNIOR TECHNICIAN PRODUKSI BLOK-1 B', 'PRODUKSI BLOK 1 B'],
            ['7423003PKW', 'H.HERMAN JAYA.ST', 'TEAM LEADER PRODUKSI BLOK 1 C', 'PRODUKSI BLOK 1 C'],
            ['7323020PKW', 'BERFRY', 'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 C', 'PRODUKSI BLOK 1 C'],
            ['9723031WGK', 'AYU', 'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 1 C', 'PRODUKSI BLOK 1 C'],
            ['9723015WGK', 'ANDI DEDI PRABOWO', 'JUNIOR TECHNICIAN PRODUKSI BLOK 1 C', 'PRODUKSI BLOK 1 C'],
            ['0224071WGK', 'FIRMAN', 'JUNIOR TECHNICIAN PRODUKSI BLOK 1 C', 'PRODUKSI BLOK 1 C'],
            ['7223009PKW', 'MUH NASAR D', 'TEAM LEADER PRODUKSI BLOK 1 D', 'PRODUKSI BLOK 1 D'],
            ['9023025PKW', 'ASRIANTO', 'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-1 D', 'PRODUKSI BLOK 1 D'],
            ['0023027WGK', 'ANDI ALIEM ADRIAN ASRUL', 'JUNIOR TECHNICIAN PRODUKSI BLOK 1 D', 'PRODUKSI BLOK 1 D'],
            ['9924063WGK', 'MUH. SOFYAN', 'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK1 D', 'PRODUKSI BLOK 1 D'],
            ['9023039PKW', 'MUH. TAKDIR T', 'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 A', 'PRODUKSI BLOK 2 A'],
            ['8915057TD', 'BAHARUDDIN', 'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 A', 'PRODUKSI BLOK 2 A'],
            ['0125098WGK', 'MUHAMMAD FAJAR', 'JUNIOR TECHNICIAN PRODUKSI BLOK 2 A', 'PRODUKSI BLOK 2 A'],
            ['0423036WGK', 'MUH. AKBAR', 'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 2 A', 'PRODUKSI BLOK 2 A'],
            ['8223015PKW', 'NADAR', 'TEAM LEADER PRODUKSI BLOK 2 B', 'PRODUKSI BLOK 2 B'],
            ['8715065KD', 'ARIFUDDIN NURDIN', 'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 B', 'PRODUKSI BLOK 2 B'],
            ['8923031PKW', 'SURYA DARMAN SAPUTRA', 'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 B', 'PRODUKSI BLOK 2 B'],
            ['9924081WGK', 'MUH. HIDAYAT ARAS', 'JUNIOR TECHNICIAN PRODUKSI BLOK 2 B', 'PRODUKSI BLOK 2 B'],
            ['9323041WGK', 'RIAHASTAMAN', 'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 2 B', 'PRODUKSI BLOK 2 B'],
            ['0225089WGK', 'ALIF IQBAL MAULANA', 'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 2 B', 'PRODUKSI BLOK 2 B'],
            ['8323018PKW', 'AGUSNAWAN', 'TEAM LEADER PRODUKSI BLOK 2 C', 'PRODUKSI BLOK 2 C'],
            ['8423029PKW', 'ADY GUNA', 'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 C', 'PRODUKSI BLOK 2 C'],
            ['9123041PKW', 'NURJUMAEDI', 'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 C', 'PRODUKSI BLOK 2 C'],
            ['9723038WGK', 'MUKMIN', 'JUNIOR TECHNICIAN PRODUKSI BLOK 2 C', 'PRODUKSI BLOK 2 C'],
            ['8623017PKW', 'SUNARDI SM', 'TEAM LEADER PRODUKSI BLOK 2 D', 'PRODUKSI BLOK 2 D'],
            ['8223033PKW', 'ABD MUSAWWIR AMFAR', 'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 D', 'PRODUKSI BLOK 2 D'],
            ['8223032PKW', 'MOH FIQRY', 'TECHNICIAN CCR, DCS & ELEKTRIKAL BLOK-2 D', 'PRODUKSI BLOK 2 D'],
            ['9924075WGK', 'ANUGRAH', 'JUNIOR TECHNICIAN PRODUKSI BLOK 2 D', 'PRODUKSI BLOK 2 D'],
            ['9223044WGK', 'SUPRIADI', 'TENAGA PENDUKUNG TEKNIK PRODUKSI BLOK 2 D', 'PRODUKSI BLOK 2 D'],
            ['8208036JA', 'CAHYA PATRIA CITRA TARUNA', 'ASMAN PEMELIHARAAN', 'PEMELIHARAAN'],
            ['9015144ZJY', 'YERSON', 'TEAM LEADER RENDAL PEMELIHARAAN', 'RENDAL PEMELIHARAAN'],
            ['9317123KD', 'AZWAR RASYID', 'OFFICER RENDAL PEMELIHARAAN', 'RENDAL PEMELIHARAAN'],
            ['0023021WGK', 'FILLIA', 'JUNIOR OFFICER RENDAL PEMELIHARAAN', 'RENDAL PEMELIHARAAN'],
            ['0023026WGK', 'NAFTALIA SAMPE RANDAN', 'JUNIOR OFFICER RENDAL PEMELIHARAAN', 'RENDAL PEMELIHARAAN'],
            ['0424048WGK', 'AHMAD RESKY', 'TENAGA PENDUKUNG TEKNIK PERENCANAAN & PENGENDALIAN PEMELIHARAAN', 'RENDAL PEMELIHARAAN'],
            ['9115227KP', 'SUHAIMI', 'TEAM LEADER PEMELIHARAAN LISTRIK', 'HAR LISTRIK'],
            ['7323030PKW', 'SUPRIYADI LAMMA', 'JUNIOR TECHNICIAN PEMELIHARAAN LISTRIK', 'HAR LISTRIK'],
            ['8023024PKW', 'USMAN', 'JUNIOR TECHNICIAN PEMELIHARAAN LISTRIK', 'HAR LISTRIK'],
            ['8023017WGK', 'WAHYUDDIN', 'TENAGA PENDUKUNG TEKNIK TECHNICIAN PEMELIHARAAN LISTRIK', 'HAR LISTRIK'],
            ['0124072WGK', 'ABDI RAHABUDDIN', 'JUNIOR TECHNICIAN PEMELIHARAAN LISTRIK', 'HAR LISTRIK'],
            ['0124084WGK', 'ADITYA WIRATAMA PRAYOGA', 'JUNIOR TECHNICIAN PEMELIHARAAN LISTRIK', 'HAR LISTRIK'],
            ['0124083WGK', 'SUTRISNO.BP, S.T', 'JUNIOR TECHNICIAN PEMELIHARAAN LISTRIK', 'HAR LISTRIK'],
            ['9724074WGK', 'ANDI WAHYUDI WIJAYANTO', 'JUNIOR TECHNICIAN PEMELIHARAAN LISTRIK', 'HAR LISTRIK'],
            ['9625094WGK', 'AGUS SALIM', 'TENAGA PENDUKUNG TEKNIK TECHNICIAN PEMELIHARAAN LISTRIK', 'HAR LISTRIK'],
            ['9424060WGK', 'IRFANDI', 'TENAGA PENDUKUNG TEKNIK TECHNICIAN PEMELIHARAAN LISTRIK', 'HAR LISTRIK'],
            ['9723034WGK', 'IRWANSYAH', 'TENAGA PENDUKUNG TEKNIK TECHNICIAN PEMELIHARAAN LISTRIK', 'HAR LISTRIK'],
            ['8323007PKW', 'HENDRA GULANG', 'TEAM LEADER PEMELIHARAAN KONIN', 'HAR KONIN'],
            ['8723040PKW', 'DWI SUPAJAR MUCHTAR SIAJENG', 'JUNIOR TECHNICIAN PEMELIHARAAN KONTROL & INSTRUMEN', 'HAR KONIN'],
            ['9923033WGK', 'FHARIS IRHAM MULLIAH', 'JUNIOR TECHNICIAN PEMELIHARAAN KONTROL & INSTRUMEN', 'HAR KONIN'],
            ['9023035WGK', 'KAHARDIMAN', 'TENAGA PENDUKUNG TEKNIK PEMELIHARAAN KONTROL & INSTRUMEN', 'HAR KONIN'],
            ['9923043WGK', 'SAHRUL KHAN', 'TENAGA PENDUKUNG TEKNIK PEMELIHARAAN KONTROL & INSTRUMEN', 'HAR KONIN'],
            ['8524061WGK', 'JUMARDI', 'TENAGA PENDUKUNG TEKNIK PEMELIHARAAN KONTROL & INSTRUMEN', 'HAR KONIN'],
            ['0025099WGK', 'AHMAD REZKY NASRUDDIN', 'JUNIOR TECHNICIAN PEMELIHARAAN KONTROL & INSTRUMEN', 'HAR KONIN'],
            ['7823006PKW', 'SAINAL TAJANG', 'TEAM LEADER PEMELIHARAAN MESIN & SIPIL', 'HAR MESIN'],
            ['8423037PKW', 'ASRIYADI', 'JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL', 'HAR MESIN'],
            ['9014036ID', 'MELKI VANDI RANTE', 'JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL', 'HAR MESIN'],
            ['8623027PKW', 'MUH. AKBAL', 'JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL', 'HAR MESIN'],
            ['9523010WGK', 'DEDI SETIADI', 'JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL', 'HAR MESIN'],
            ['9123016WGK', 'MASDAR', 'TENAGA PENDUKUNG TEKNIK PEMELIHARAAN MESIN', 'HAR MESIN'],
            ['8923013WGK', 'MUH.ARAFA', 'TENAGA PENDUKUNG TEKNIK PEMELIHARAAN MESIN', 'HAR MESIN'],
            ['9123012WGK', 'SADDAN HUSEN', 'JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL', 'HAR MESIN'],
            ['8023009WGK', 'SUDARTO', 'TENAGA PENDUKUNG TEKNIK PEMELIHARAAN MESIN', 'HAR MESIN'],
            ['9624053WGK', 'ARISMAN', 'TENAGA PENDUKUNG TEKNIK PEMELIHARAAN MESIN', 'HAR MESIN'],
            ['9123045WGK', 'YUSRAN JAYA', 'TENAGA PENDUKUNG TEKNIK PEMELIHARAAN MESIN', 'HAR MESIN'],
            ['0124078WGK', 'LUCKY EDY PRAKOSO SULASTIYO', 'JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL', 'HAR MESIN'],
            ['9924076WGK', 'BASO MUHAMMAD AGUNG ANUGERAH', 'JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL', 'HAR MESIN'],
            ['0124080WGK', 'MUH. ADRIANSYAH', 'JUNIOR TECHNICIAN PEMELIHARAAN MESIN & SIPIL', 'HAR MESIN'],
            ['9424046WGK', 'A.M.ISHAQ HAQUE AB', 'TENAGA PENDUKUNG TEKNIK PEMELIHARAAN MESIN', 'HAR MESIN'],
            ['9015132ZJY', 'MUHAMMAD SIDDIQ B.', 'TEAM LEADER OUTAGE MANAGEMENT', 'OUTAGE MANAGEMENT'],
            ['9216020KA', 'ASRIEL BASTIAD', 'JUNIOR OFFICER OUTAGE MANAGEMENT', 'OUTAGE MANAGEMENT'],
            ['0023019WGK', 'PUTRI FATMASARI', 'JUNIOR OFFICER OUTAGE MANAGEMENT', 'OUTAGE MANAGEMENT'],
            ['9623029WGK', 'ANDI RYAN ADRIAN', 'JUNIOR OFFICER OUTAGE MANAGEMENT', 'OUTAGE MANAGEMENT'],
            ['9624050WGK', 'ANDI HIDAYAT', 'TENAGA PENDUKUNG OUTAGE MANAGEMENT', 'OUTAGE MANAGEMENT'],
            ['8716092PC', 'NURHALIM', 'TEAM LEADER INVENTORY CONTROL & GUDANG', 'INVENTORY & GUDANG'],
            ['8523042PKW', 'MUH. ARCANG', 'JUNIOR OFFICER INVENTORI KONTROL & GUDANG', 'INVENTORY & GUDANG'],
            ['8623018WGK', 'ANDI BASO MUNAWIR', 'JUNIOR OFFICER INVENTORI KONTROL & GUDANG', 'INVENTORY & GUDANG'],
            ['9923032WGK', 'FERDI AGUS', 'JUNIOR OFFICER INVENTORI KONTROL & GUDANG', 'INVENTORY & GUDANG'],
            ['0024047WGK', 'AFRENGKI SAPUTRA', 'TENAGA PENDUKUNG OFFICER INVENTORY & GUDANG', 'INVENTORY & GUDANG'],
            ['0224055WGK', 'ARWAN', 'TENAGA PENDUKUNG OFFICER INVENTORY & GUDANG', 'INVENTORY & GUDANG'],
            ['9724062WGK', 'MUH. IQBAL RAWANDI, S.KEL', 'TENAGA PENDUKUNG OFFICER INVENTORY & GUDANG', 'INVENTORY & GUDANG'],
            ['8713108ZJY', 'PRAN MOCH SOLEH', 'ASMAN BUSINESS SUPPORT', 'ADMINISTRASI'],
            ['9117374KP', 'ANDI RIDWANSYAH BAHAR PUTRA', 'TEAM LEADER SDM, UMUM & CSR', 'SDM & UMUM'],
            ['8923024WGK', 'ANDI AISYAH NINGRATI ASHARIANA', 'JUNIOR OFFICER SDM, UMUM & CSR', 'SDM & UMUM'],
            ['7823025WGK', 'MURLINA', 'JUNIOR OFFICER SDM, UMUM & CSR', 'SDM & UMUM'],
            ['0024054WGK', 'ARUN SATRIA PUTRA', 'TENAGA PENDUKUNG SDM & UMUM', 'SDM & UMUM'],
            ['9624057WGK', 'DIRGA FAJAR MAULANA', 'TENAGA PENDUKUNG SDM & UMUM', 'SDM & UMUM'],
            ['8524070WGK', 'YAZID RIZAL', 'TENAGA PENDUKUNG SDM & UMUM', 'SDM & UMUM'],
            ['0124058WGK', 'HILDA RIANYSKA', 'SEKERTARIS', 'SEKERTARIS'],
            ['7423002WGK', 'ERNA S', 'HOUSE KEEPER', 'SDM & UMUM'],
            ['9123001WGK', 'HERNILA', 'HOUSE KEEPER', 'SDM & UMUM'],
            ['7923005WGK', 'NANNA TRIANA', 'HOUSE KEEPER', 'SDM & UMUM'],
            ['7823014PKW', 'AGUS SALIM', 'TEAM LEADER LOGISTIK & PENGADAAN', 'LOGISTIK & PENGADAAN'],
            ['9923042WGK', 'RIZAL HAMZAH', 'JUNIOR OFFICER LOGISTIK & PENGADAAN', 'LOGISTIK & PENGADAAN'],
            ['9223040WGK', 'NURDEDI', 'JUNIOR OFFICER LOGISTIK & PENGADAAN', 'LOGISTIK & PENGADAAN'],
            ['0024051WGK', 'ANNISA MAULIANA AKBAR', 'JUNIOR OFFICER LOGISTIK & PENGADAAN', 'LOGISTIK & PENGADAAN'],
            ['0425095WGK', 'ANDI ALYA AJRIN ASRUL', 'TENAGA PENDUKUNG OFFICER LOGISTIK & PENGADAAN', 'LOGISTIK & PENGADAAN'],
            ['9925091WGK', 'MUHAMMAD RAYHAN SYAKIL', 'TENAGA PENDUKUNG OFFICER LOGISTIK & PENGADAAN', 'LOGISTIK & PENGADAAN'],
            ['0124087WGK', 'SANDRI KURNIAWAN', 'TENAGA PENDUKUNG OFFICER LOGISTIK & PENGADAAN', 'LOGISTIK & PENGADAAN'],
            ['8915104ZJY', 'ZUL QADRI A. SADIK', 'TEAM LEADER KEUANGAN', 'KEUANGAN'],
            ['8923019PKW', 'NURDIANSAR ARFAH', 'JUNIOR OFFICER KEUANGAN', 'KEUANGAN'],
            ['8623011PKW', 'WARDA', 'JUNIOR OFFICER KEUANGAN', 'KEUANGAN'],
            ['9923037WGK', 'MUHAMMAD ZAKIR KAMARUDDIN', 'TENAGA PENDUKUNG OFFICER KEUANGAN', 'KEUANGAN'],
            ['9625090WGK', 'MUHAMMAD ARFAN SANJAYA', 'TENAGA PENDUKUNG OFFICER KEUANGAN', 'KEUANGAN'],
            ['7523004WGK', 'ANSAR ANAS', 'KOORDINATOR MKP', 'ADMINISTRASI'],
            ['8814113KP', 'ANDIK YULIANTO', 'KOORDINATOR NPS', 'ADMINISTRASI'],
            ['9124085WGK', 'NELLA SUSANTY PINERASMA', 'KOORDINATOR NPS', 'ADMINISTRASI'],
        ];

        $defaultPassword = Hash::make('password123');

        foreach ($rawEmployees as $item) {
            $rawNid = trim($item[0]);
            // Bersihkan jika terduplikasi seperti 8108050JA8108050JA
            if (strlen($rawNid) > 10 && substr($rawNid, 0, strlen($rawNid)/2) === substr($rawNid, strlen($rawNid)/2)) {
                $rawNid = substr($rawNid, 0, strlen($rawNid)/2);
            }

            $nama = trim($item[1]);
            $jabatan = trim($item[2]);
            $bidang = trim($item[3]);

            // 1. Position
            $pos = Position::firstOrCreate(['name' => $jabatan]);

            // 2. Unit Organisasi
            $unit = $unitsMap[$bidang] ?? $root;

            // 3. Tentukan Peran (Role) Sesuai BSD
            $upperJabatan = strtoupper($jabatan);
            if (str_contains($upperJabatan, 'MANAGER UNIT')) {
                $role = $roleMup;
            } elseif (str_contains($upperJabatan, 'ASMAN') || str_contains($upperJabatan, 'ASSISTANT MANAGER')) {
                $role = $roleAsman;
            } elseif (str_contains($upperJabatan, 'TEAM LEADER')) {
                $role = $roleTl;
            } else {
                $role = $roleStaff;
            }

            // 4. Generate Email
            // Berikan alias email penting untuk login cepat
            if ($rawNid === '8108050JA') {
                $email = 'mup@plnnp.co.id';
            } elseif ($rawNid === '7323002PKW') {
                $email = 'asman@plnnp.co.id'; // Asman Operasi
            } elseif ($rawNid === '8208036JA') {
                $email = 'asman.pemeliharaan@plnnp.co.id';
            } elseif ($rawNid === '8609070JA') {
                $email = 'asman.engineering@plnnp.co.id';
            } elseif ($rawNid === '8713108ZJY') {
                $email = 'asman.bs@plnnp.co.id';
            } elseif ($rawNid === '8915056TD') {
                $email = 'tl@plnnp.co.id'; // TL Produksi Blok 1A
            } elseif ($rawNid === '9315231TA') {
                $email = 'tl.k3@plnnp.co.id';
            } else {
                $email = strtolower($rawNid) . '@plnnp.co.id';
            }

            // Update or Create User
            $user = User::where('employee_no', $rawNid)->orWhere('email', $email)->first();
            if ($user) {
                $user->update([
                    'employee_no' => $rawNid,
                    'name' => $nama,
                    'email' => $email,
                    'password' => $defaultPassword,
                    'position_id' => $pos->id,
                    'unit_id' => $unit->id,
                    'role_id' => $role->id,
                    'is_active' => true,
                ]);
            } else {
                User::create([
                    'employee_no' => $rawNid,
                    'name' => $nama,
                    'email' => $email,
                    'password' => $defaultPassword,
                    'position_id' => $pos->id,
                    'unit_id' => $unit->id,
                    'role_id' => $role->id,
                    'is_active' => true,
                ]);
            }
        }
    }
}
