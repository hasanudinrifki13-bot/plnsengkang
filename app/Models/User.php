<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $appends = [
        'avatar_url',
        'hierarchy_info',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Accessor dinamis avatar URL (Mendukung Hosting & Local Storage serta Fallback).
     */
    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->avatar)) {
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }

            $cleanPath = ltrim($this->avatar, '/');

            // Cek ketersediaan file di storage atau public
            if (file_exists(storage_path('app/public/' . $cleanPath)) || file_exists(public_path('storage/' . $cleanPath))) {
                return asset('storage/' . $cleanPath);
            }
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'User') . '&background=0284c7&color=fff&size=120';
    }

    /**
     * Cek apakah user memiliki avatar kustom yang ada fisiknya di storage.
     */
    public function getHasCustomAvatarAttribute(): bool
    {
        if (empty($this->avatar)) {
            return false;
        }

        $cleanPath = ltrim($this->avatar, '/');
        return file_exists(storage_path('app/public/' . $cleanPath)) || file_exists(public_path('storage/' . $cleanPath));
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function organizationUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationUnit::class, 'unit_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function issuesReported(): HasMany
    {
        return $this->hasMany(Issue::class, 'reported_by');
    }

    public function issuesOwned(): HasMany
    {
        return $this->hasMany(Issue::class, 'owner_id');
    }

    public function tasksAssigned(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function tasksCreated(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }

    public function teamLeader(): BelongsTo
    {
        return $this->belongsTo(self::class, 'tl_id');
    }

    public function asistenManajer(): BelongsTo
    {
        return $this->belongsTo(self::class, 'asman_id');
    }

    /**
     * Dapatkan daftar ID unit kerja yang berada di dalam lingkup wewenang user ini.
     * - MUP & ADMIN: Seluruh unit UP Sengkang
     * - ASMAN: Unit bidangnya beserta seluruh sub-unit/seksi (child units) di bawahnya
     * - TL & STAFF: Unit seksinya sendiri
     */
    public function getAuthorizedUnitIds(): array
    {
        $roleName = strtoupper($this->role->name ?? '');

        if ($roleName === 'MUP' || $roleName === 'ADMIN') {
            return OrganizationUnit::pluck('id')->toArray();
        }

        if ($roleName === 'ASMAN') {
            $myUnitId = $this->unit_id;
            if (!$myUnitId) {
                return [];
            }

            // Ambil unit bidang ini dan seluruh unit anak (seksi-seksi) di bawahnya
            return OrganizationUnit::where('parent_unit_id', $myUnitId)
                ->orWhere('id', $myUnitId)
                ->pluck('id')
                ->toArray();
        }

        // Untuk TL dan STAFF: hanya unit seksinya sendiri
        return $this->unit_id ? [$this->unit_id] : [];
    }

    /**
     * Dapatkan daftar ID user bawahan (dan diri sendiri) dalam lingkup kerja.
     */
    public function getSubordinateUserIds(): array
    {
        $roleName = strtoupper($this->role->name ?? '');

        if ($roleName === 'MUP' || $roleName === 'ADMIN') {
            return self::pluck('id')->toArray();
        }

        $unitIds = $this->getAuthorizedUnitIds();
        if (empty($unitIds)) {
            return [$this->id];
        }

        return self::whereIn('unit_id', $unitIds)->pluck('id')->toArray();
    }

    /**
     * Jalur hierarki komando struktural: Staff -> TL -> ASMAN -> MUP
     */
    public function getHierarchyInfoAttribute(): array
    {
        $roleName = strtoupper($this->role->name ?? 'STAFF');

        // 1. Cari TL
        $tl = null;
        if ($roleName === 'TL') {
            $tl = $this;
        } elseif ($this->tl_id) {
            $tl = $this->teamLeader ?? self::with(['position', 'organizationUnit'])->find($this->tl_id);
        } else {
            $tl = self::with(['position', 'organizationUnit'])
                ->where('unit_id', $this->unit_id)
                ->whereHas('role', fn($q) => $q->where('name', 'TL'))
                ->first();
        }

        // 2. Cari ASMAN
        $asman = null;
        if ($roleName === 'ASMAN') {
            $asman = $this;
        } elseif ($this->asman_id) {
            $asman = $this->asistenManajer ?? self::with(['position', 'organizationUnit'])->find($this->asman_id);
        } else {
            $unit = $this->organizationUnit;
            if ($unit && $unit->parent_unit_id) {
                $asman = self::with(['position', 'organizationUnit'])
                    ->where('unit_id', $unit->parent_unit_id)
                    ->whereHas('role', fn($q) => $q->where('name', 'ASMAN'))
                    ->first();
            }
        }

        // 3. Cari MUP
        $mup = self::with(['position', 'organizationUnit'])
            ->whereHas('role', fn($q) => $q->where('name', 'MUP'))
            ->first();

        return [
            'staff' => [
                'id' => $this->id,
                'name' => $this->name,
                'position' => $this->position?->name ?? ($this->role?->name ?? 'Staff'),
                'unit' => $this->organizationUnit?->name ?? '-',
                'avatar' => $this->avatar_url,
            ],
            'tl' => $tl ? [
                'id' => $tl->id,
                'name' => $tl->name,
                'position' => $tl->position?->name ?? 'Team Leader',
                'unit' => $tl->organizationUnit?->name ?? '-',
                'avatar' => $tl->avatar_url,
                'is_self' => $tl->id === $this->id,
            ] : null,
            'asman' => $asman ? [
                'id' => $asman->id,
                'name' => $asman->name,
                'position' => $asman->position?->name ?? 'Asisten Manajer',
                'unit' => $asman->organizationUnit?->name ?? '-',
                'avatar' => $asman->avatar_url,
                'is_self' => $asman->id === $this->id,
            ] : null,
            'mup' => $mup ? [
                'id' => $mup->id,
                'name' => $mup->name,
                'position' => $mup->position?->name ?? 'Manager Unit',
                'unit' => $mup->organizationUnit?->name ?? 'UP Sengkang',
                'avatar' => $mup->avatar_url,
                'is_self' => $mup->id === $this->id,
            ] : null,
            'is_direct_to_mup' => !$asman && $this->organizationUnit && ($this->organizationUnit->parent_unit_id == 1 || str_contains(strtolower($this->organizationUnit->name ?? ''), 'k3') || str_contains(strtolower($this->organizationUnit->name ?? ''), 'lingkungan')),
        ];
    }
}
