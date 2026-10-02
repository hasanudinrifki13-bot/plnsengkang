<template>
  <AppLayout title="Profil Pengguna & Keamanan">
    <div class="max-w-4xl mx-auto space-y-6">

      <!-- Card Identitas Ringkas Karyawan -->
      <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs flex flex-col md:flex-row items-center gap-6">
        <div class="w-20 h-20 rounded-full bg-slate-600 text-white text-2xl font-bold flex items-center justify-center shadow-md ring-4 ring-slate-100 shrink-0 overflow-hidden">
          <img v-if="user.avatar_url" :src="user.avatar_url" alt="Avatar" class="w-full h-full object-cover">
          <span v-else>{{ (user.name || 'U').substring(0, 2).toUpperCase() }}</span>
        </div>
        <div class="flex-1 text-center md:text-left space-y-1">
          <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
            <h2 class="text-xl font-bold text-gray-800">{{ user.name }}</h2>
            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
              {{ user.role?.name || 'STAFF' }}
            </span>
          </div>
          <p class="text-xs text-gray-500 font-medium">
            NID: <span class="font-bold text-gray-700">{{ user.employee_no || '-' }}</span> | Posisi: <span class="font-semibold text-gray-700">{{ user.position?.name || '-' }}</span>
          </p>
          <p class="text-xs text-gray-500">
            Unit Organisasi: <span class="font-semibold text-gray-700">{{ user.organization_unit?.name || 'UP Sengkang' }}</span>
          </p>
        </div>
        <div class="text-right shrink-0">
          <span :class="user.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700'" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold">
            <span :class="user.is_active ? 'bg-emerald-500' : 'bg-rose-500'" class="w-2 h-2 rounded-full"></span>
            {{ user.is_active ? 'Status Akun Aktif' : 'Non-Aktif' }}
          </span>
        </div>
      </div>

      <!-- Card Struktur Jalur Pimpinan & Hierarki Pelaporan Saya -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 p-5 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-500/20 border border-blue-400/30 flex items-center justify-center text-blue-400 text-base shadow-inner shrink-0">
              <i class="fas fa-sitemap"></i>
            </div>
            <div>
              <h3 class="text-sm font-bold text-white tracking-wide flex items-center gap-2">
                Jalur Komando & Pimpinan Saya
                <span class="text-[10px] font-normal px-2 py-0.5 rounded-full bg-blue-500/30 text-blue-200 border border-blue-400/30">
                  Hierarki Pelaporan
                </span>
              </h3>
              <p class="text-[11px] text-slate-300">
                Pemetaan rantai komando struktural: Anda ➔ Team Leader ➔ Asisten Manajer ➔ Manajer Unit
              </p>
            </div>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <span class="text-xs text-slate-300">Peran Sistem:</span>
            <span class="px-2.5 py-1 bg-amber-400 text-slate-900 rounded-lg text-xs font-black shadow-xs">
              {{ user.role?.name || 'STAFF' }}
            </span>
          </div>
        </div>

        <div class="p-6 bg-slate-50/50">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- 1. TEAM LEADER (TL) -->
            <div class="bg-white rounded-xl border border-blue-200/80 p-4 shadow-2xs flex flex-col justify-between relative overflow-hidden">
              <div class="absolute top-0 right-0 w-16 h-16 bg-blue-50 rounded-bl-full pointer-events-none -mr-4 -mt-4 opacity-50"></div>
              <div>
                <div class="flex items-center justify-between mb-3">
                  <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-blue-100 text-blue-800 border border-blue-200">
                    <i class="fas fa-users-cog mr-1"></i> Team Leader (TL)
                  </span>
                  <span v-if="user.hierarchy_info?.tl?.is_self" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                    Posisi Anda
                  </span>
                  <span v-else class="text-[10px] text-gray-400 font-medium">Rapat Pagi</span>
                </div>

                <div v-if="user.hierarchy_info?.tl" class="flex items-center gap-3">
                  <img 
                    :src="user.hierarchy_info.tl.avatar" 
                    alt="TL" 
                    class="w-12 h-12 rounded-full object-cover border-2 border-blue-400 shadow-xs shrink-0" 
                  />
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-gray-900 truncate" :title="user.hierarchy_info.tl.name">
                      {{ user.hierarchy_info.tl.name }}
                      <span v-if="user.hierarchy_info.tl.is_self" class="text-[10px] text-blue-600 font-bold block">(Anda Sendiri)</span>
                    </p>
                    <p class="text-[11px] text-gray-600 truncate mt-0.5" :title="user.hierarchy_info.tl.position">
                      {{ user.hierarchy_info.tl.position }}
                    </p>
                    <p class="text-[10px] text-blue-700 font-medium truncate mt-0.5">
                      <i class="fas fa-building text-[9px] mr-1"></i>{{ user.hierarchy_info.tl.unit }}
                    </p>
                  </div>
                </div>

                <div v-else class="text-center py-3 text-gray-400 text-xs">
                  <p class="italic">- Bukan bawahan seksi TL -</p>
                  <p class="text-[10px] mt-0.5">Tingkat Asman / MUP</p>
                </div>
              </div>

              <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-500 flex items-center justify-between">
                <span>Pimpinan Tingkat 1</span>
                <span class="text-blue-700 font-semibold">Morning Talk</span>
              </div>
            </div>

            <!-- 2. ASISTEN MANAJER (ASMAN) -->
            <div class="bg-white rounded-xl border border-indigo-200/80 p-4 shadow-2xs flex flex-col justify-between relative overflow-hidden">
              <div class="absolute top-0 right-0 w-16 h-16 bg-indigo-50 rounded-bl-full pointer-events-none -mr-4 -mt-4 opacity-50"></div>
              <div>
                <div class="flex items-center justify-between mb-3">
                  <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-indigo-100 text-indigo-800 border border-indigo-200">
                    <i class="fas fa-user-tie mr-1"></i> Asisten Manajer
                  </span>
                  <span v-if="user.hierarchy_info?.asman?.is_self" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                    Posisi Anda
                  </span>
                  <span v-else class="text-[10px] text-gray-400 font-medium">Rapat Siang</span>
                </div>

                <!-- Jika Ada Asman Bidang -->
                <div v-if="user.hierarchy_info?.asman" class="flex items-center gap-3">
                  <img 
                    :src="user.hierarchy_info.asman.avatar" 
                    alt="Asman" 
                    class="w-12 h-12 rounded-full object-cover border-2 border-indigo-400 shadow-xs shrink-0" 
                  />
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-gray-900 truncate" :title="user.hierarchy_info.asman.name">
                      {{ user.hierarchy_info.asman.name }}
                      <span v-if="user.hierarchy_info.asman.is_self" class="text-[10px] text-indigo-600 font-bold block">(Anda Sendiri)</span>
                    </p>
                    <p class="text-[11px] text-gray-600 truncate mt-0.5" :title="user.hierarchy_info.asman.position">
                      {{ user.hierarchy_info.asman.position }}
                    </p>
                    <p class="text-[10px] text-indigo-700 font-medium truncate mt-0.5">
                      <i class="fas fa-briefcase text-[9px] mr-1"></i>{{ user.hierarchy_info.asman.unit }}
                    </p>
                  </div>
                </div>

                <!-- Jika Langsung ke MUP (Unit Khusus) -->
                <div v-else-if="user.hierarchy_info?.is_direct_to_mup" class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded-full bg-slate-100 border-2 border-slate-300 flex items-center justify-center text-slate-500 shrink-0 font-bold text-sm">
                    <i class="fas fa-level-up-alt"></i>
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-gray-800 truncate">Langsung ke MUP</p>
                    <p class="text-[11px] text-gray-500 truncate mt-0.5">Unit Mandiri Non-Bidang</p>
                    <p class="text-[10px] text-emerald-600 font-bold truncate mt-0.5">K3 / Lingkungan</p>
                  </div>
                </div>

                <div v-else class="text-center py-3 text-gray-400 text-xs">
                  <p class="italic">- Pimpinan Tertinggi -</p>
                  <p class="text-[10px] mt-0.5">Tingkat Manajer Unit</p>
                </div>
              </div>

              <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-500 flex items-center justify-between">
                <span>Pimpinan Tingkat 2</span>
                <span class="text-indigo-700 font-semibold">Noon Meeting</span>
              </div>
            </div>

            <!-- 3. MANAJER UNIT (MUP) -->
            <div class="bg-white rounded-xl border border-purple-200/80 p-4 shadow-2xs flex flex-col justify-between relative overflow-hidden">
              <div class="absolute top-0 right-0 w-16 h-16 bg-purple-50 rounded-bl-full pointer-events-none -mr-4 -mt-4 opacity-50"></div>
              <div>
                <div class="flex items-center justify-between mb-3">
                  <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-purple-100 text-purple-800 border border-purple-200">
                    <i class="fas fa-landmark mr-1"></i> Manajer Unit (MUP)
                  </span>
                  <span v-if="user.hierarchy_info?.mup?.is_self" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                    Posisi Anda
                  </span>
                  <span v-else class="text-[10px] text-gray-400 font-medium">Puncak</span>
                </div>

                <div v-if="user.hierarchy_info?.mup" class="flex items-center gap-3">
                  <img 
                    :src="user.hierarchy_info.mup.avatar" 
                    alt="MUP" 
                    class="w-12 h-12 rounded-full object-cover border-2 border-purple-400 shadow-xs shrink-0" 
                  />
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-gray-900 truncate" :title="user.hierarchy_info.mup.name">
                      {{ user.hierarchy_info.mup.name }}
                      <span v-if="user.hierarchy_info.mup.is_self" class="text-[10px] text-purple-600 font-bold block">(Anda Sendiri)</span>
                    </p>
                    <p class="text-[11px] text-gray-600 truncate mt-0.5" :title="user.hierarchy_info.mup.position">
                      {{ user.hierarchy_info.mup.position }}
                    </p>
                    <p class="text-[10px] text-purple-700 font-medium truncate mt-0.5">
                      <i class="fas fa-building text-[9px] mr-1"></i>{{ user.hierarchy_info.mup.unit }}
                    </p>
                  </div>
                </div>

                <div v-else class="text-center py-3 text-gray-400 text-xs">
                  <p class="italic">- Data MUP -</p>
                </div>
              </div>

              <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-500 flex items-center justify-between">
                <span>Pimpinan Unit</span>
                <span class="text-purple-700 font-semibold">UP Sengkang</span>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Form 1: Ubah Data Profil -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs space-y-5">
          <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
            <div>
              <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-user-edit text-blue-600"></i> Informasi Data Diri & Foto
              </h3>
              <p class="text-[11px] text-gray-400">Perbarui foto profil, informasi kontak, dan nama Anda</p>
            </div>
          </div>

          <form @submit.prevent="submitProfile" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Foto Profil / Avatar</label>
              <input 
                type="file" 
                accept="image/*" 
                @change="onAvatarChange"
                class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer"
              />
              <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 2MB). Kosongkan jika tidak ingin mengubah foto.</p>
              <p v-if="profileForm.errors.avatar" class="text-rose-500 text-[10px] mt-1">{{ profileForm.errors.avatar }}</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Induk Dokumen / NID</label>
              <input 
                type="text" 
                :value="user.employee_no" 
                disabled 
                class="w-full bg-slate-100 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-500 cursor-not-allowed"
              />
              <p class="text-[10px] text-gray-400 mt-1">NID terikat pada data kepegawaian resmi dan tidak dapat diubah.</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
              <input 
                type="text" 
                v-model="profileForm.name" 
                required 
                class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              <p v-if="profileForm.errors.name" class="text-rose-500 text-[10px] mt-1">{{ profileForm.errors.name }}</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
              <input 
                type="email" 
                v-model="profileForm.email" 
                required 
                class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              <p v-if="profileForm.errors.email" class="text-rose-500 text-[10px] mt-1">{{ profileForm.errors.email }}</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor HP / WhatsApp (Koordinasi Cepat)</label>
              <input 
                type="text" 
                v-model="profileForm.phone" 
                placeholder="Contoh: 081234567890" 
                class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              <p v-if="profileForm.errors.phone" class="text-rose-500 text-[10px] mt-1">{{ profileForm.errors.phone }}</p>
            </div>

            <div class="pt-2">
              <button 
                type="submit" 
                :disabled="profileForm.processing"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg text-xs shadow-sm transition flex items-center justify-center gap-2 cursor-pointer"
              >
                <i class="fas fa-save"></i> 
                {{ profileForm.processing ? 'Menyimpan...' : 'Simpan Perubahan Profil' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Form 2: Ubah Kata Sandi / Password -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs space-y-5">
          <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
            <div>
              <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-shield-alt text-amber-500"></i> Ubah Kata Sandi Akun
              </h3>
              <p class="text-[11px] text-gray-400">Gunakan kombinasi minimal 6 karakter demi keamanan akun</p>
            </div>
          </div>

          <form @submit.prevent="submitPassword" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Kata Sandi Saat Ini <span class="text-rose-500">*</span></label>
              <input 
                type="password" 
                v-model="passwordForm.current_password" 
                required 
                placeholder="Masukkan kata sandi lama Anda" 
                class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
              />
              <p v-if="passwordForm.errors.current_password" class="text-rose-500 text-[10px] mt-1">{{ passwordForm.errors.current_password }}</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Kata Sandi Baru <span class="text-rose-500">*</span></label>
              <input 
                type="password" 
                v-model="passwordForm.new_password" 
                required 
                placeholder="Minimal 6 karakter" 
                class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
              />
              <p v-if="passwordForm.errors.new_password" class="text-rose-500 text-[10px] mt-1">{{ passwordForm.errors.new_password }}</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span></label>
              <input 
                type="password" 
                v-model="passwordForm.new_password_confirmation" 
                required 
                placeholder="Ulangi kata sandi baru" 
                class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
              />
            </div>

            <div class="pt-2">
              <button 
                type="submit" 
                :disabled="passwordForm.processing"
                class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2.5 px-4 rounded-lg text-xs shadow-sm transition flex items-center justify-center gap-2 cursor-pointer"
              >
                <i class="fas fa-key"></i> 
                {{ passwordForm.processing ? 'Menyimpan...' : 'Perbarui Kata Sandi' }}
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  user: Object,
});

const profileForm = useForm({
  _method: 'PUT',
  name: props.user?.name || '',
  email: props.user?.email || '',
  phone: props.user?.phone || '',
  avatar: null,
});

function onAvatarChange(e) {
  if (e.target.files && e.target.files[0]) {
    profileForm.avatar = e.target.files[0];
  }
}

function submitProfile() {
  profileForm.post('/profil', {
    preserveScroll: true,
    onSuccess: () => {
      profileForm.avatar = null;
    },
  });
}

const passwordForm = useForm({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
});

function submitPassword() {
  passwordForm.put('/profil/password', {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset();
    },
  });
}
</script>
