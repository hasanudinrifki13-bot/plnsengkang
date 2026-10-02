<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user()->load(['role', 'position', 'organizationUnit']);
        return Inertia::render('Profile/Index', [
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        if ($request->hasFile('avatar')) {
            // Hapus file avatar lama jika ada
            if ($user->avatar) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
                }
                if (file_exists(public_path('storage/' . $user->avatar))) {
                    @unlink(public_path('storage/' . $user->avatar));
                }
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;

            // Mirror file ke public/storage untuk kompatibilitas hosting cPanel tanpa symlink
            try {
                $targetDir = public_path('storage/avatars');
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0755, true);
                }
                @copy(storage_path('app/public/' . $path), public_path('storage/' . $path));
            } catch (\Throwable $e) {
                // Abaikan jika folder permission hosting membaca via route fallback
            }
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('success', 'Profil dan foto akun Anda berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak cocok.'])->with('error', 'Kata sandi saat ini tidak valid.');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Kata sandi akun Anda berhasil diubah. Gunakan kata sandi baru untuk login berikutnya.');
    }
}
