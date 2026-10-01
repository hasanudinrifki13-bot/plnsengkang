@extends('layouts.app')

@section('title', 'Manajemen Profil & Keamanan Akun')
@section('page_title', 'Profil Pengguna & Keamanan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Card Identitas Ringkas Karyawan -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col md:flex-row items-center gap-6">
        <div class="w-20 h-20 rounded-full bg-slate-600 text-white text-2xl font-bold flex items-center justify-center shadow-lg ring-4 ring-slate-100 shrink-0 overflow-hidden">
            @if($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar))
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=64748b&color=fff&size=160" alt="Avatar" class="w-full h-full object-cover">
            @endif
        </div>
        <div class="flex-1 text-center md:text-left space-y-1">
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                    {{ $user->role->name ?? 'STAFF' }}
                </span>
            </div>
            <p class="text-xs text-gray-500 font-medium">NID: <span class="font-bold text-gray-700">{{ $user->employee_no }}</span> | Posisi: <span class="font-semibold text-gray-700">{{ $user->position->name ?? '-' }}</span></p>
            <p class="text-xs text-gray-500">Unit Organisasi: <span class="font-semibold text-gray-700">{{ $user->organizationUnit->name ?? 'UP Sengkang' }}</span></p>
        </div>
        <div class="text-right shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700' }}">
                <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                {{ $user->is_active ? 'Status Akun Aktif' : 'Non-Aktif' }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Form 1: Ubah Data Profil -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-5">
            <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-user-edit text-blue-600"></i> Informasi Data Diri & Foto
                    </h3>
                    <p class="text-[11px] text-gray-400">Perbarui foto profil, informasi kontak, dan nama Anda</p>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Foto Profil / Avatar</label>
                    <input type="file" name="avatar" accept="image/*" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 2MB). Kosongkan jika tidak ingin mengubah foto.</p>
                    @error('avatar')
                        <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Induk Dokumen / NID</label>
                    <input type="text" value="{{ $user->employee_no }}" disabled class="w-full bg-slate-100 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-500 cursor-not-allowed">
                    <p class="text-[10px] text-gray-400 mt-1">NID terikat pada data kepegawaian resmi dan tidak dapat diubah.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('email')
                        <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor HP / WhatsApp (Koordinasi Cepat)</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('phone')
                        <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg text-xs shadow-md transition flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- Form 2: Ubah Kata Sandi / Password -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-5">
            <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-amber-500"></i> Ubah Kata Sandi Akun
                    </h3>
                    <p class="text-[11px] text-gray-400">Gunakan kombinasi minimal 6 karakter demi keamanan akun</p>
                </div>
            </div>

            <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kata Sandi Saat Ini <span class="text-rose-500">*</span></label>
                    <input type="password" name="current_password" required placeholder="Masukkan kata sandi lama Anda" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    @error('current_password')
                        <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kata Sandi Baru <span class="text-rose-500">*</span></label>
                    <input type="password" name="new_password" required placeholder="Minimal 6 karakter" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    @error('new_password')
                        <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span></label>
                    <input type="password" name="new_password_confirmation" required placeholder="Ulangi kata sandi baru" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2.5 px-4 rounded-lg text-xs shadow-md transition flex items-center justify-center gap-2">
                        <i class="fas fa-key"></i> Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
