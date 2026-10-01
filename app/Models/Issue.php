<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['sla_info'];

    /**
     * Accessor untuk menghitung durasi batas penanganan (SLA) berdasarkan level eskalasi (TL: 24h, Asman: 48h, MUP: 72h).
     */
    public function getSlaInfoAttribute(): array
    {
        $level = strtoupper($this->current_level ?: 'TL');
        $targetHours = match ($level) {
            'MUP' => 72,
            'ASMAN' => 48,
            default => 24,
        };

        // Gunakan waktu saat eskalasi terakhir jika ada, atau created_at
        $latestEscalation = $this->escalations()->first();
        $startTime = $latestEscalation ? $latestEscalation->created_at : ($this->created_at ? Carbon::parse($this->created_at) : Carbon::now());
        $deadline = (clone $startTime)->addHours($targetHours);
        $now = Carbon::now();

        $isClosed = in_array($this->status, ['Resolved', 'Closed', 'Decided']);
        if ($isClosed) {
            return [
                'target_hours' => $targetHours,
                'deadline' => $deadline,
                'remaining_hours' => 0,
                'status' => 'Closed',
                'label' => 'Tuntas dalam SLA',
                'badge_class' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                'icon' => 'fas fa-check-circle',
                'percent_elapsed' => 100,
            ];
        }

        $remainingHours = $now->diffInHours($deadline, false);
        $elapsedHours = $startTime->diffInHours($now);
        $percentElapsed = min(100, max(0, round(($elapsedHours / $targetHours) * 100)));

        if ($remainingHours < 0) {
            $overdueHours = abs((int) $remainingHours);
            $dayStr = $overdueHours >= 24 ? floor($overdueHours / 24) . 'h ' : '';
            return [
                'target_hours' => $targetHours,
                'deadline' => $deadline,
                'remaining_hours' => $remainingHours,
                'status' => 'Breached',
                'label' => 'Lewat SLA (' . $dayStr . ($overdueHours % 24) . ' jam)',
                'badge_class' => 'bg-rose-50 text-rose-600 border border-rose-200 animate-pulse font-bold',
                'icon' => 'fas fa-exclamation-triangle',
                'percent_elapsed' => 100,
            ];
        }

        if ($remainingHours <= 6) {
            return [
                'target_hours' => $targetHours,
                'deadline' => $deadline,
                'remaining_hours' => $remainingHours,
                'status' => 'Warning',
                'label' => 'Sisa ' . (int) $remainingHours . ' Jam (Kritis)',
                'badge_class' => 'bg-amber-50 text-amber-800 border border-amber-300 font-bold',
                'icon' => 'fas fa-stopwatch',
                'percent_elapsed' => $percentElapsed,
            ];
        }

        return [
            'target_hours' => $targetHours,
            'deadline' => $deadline,
            'remaining_hours' => $remainingHours,
            'status' => 'Safe',
            'label' => 'Sisa ' . (int) $remainingHours . ' Jam',
            'badge_class' => 'bg-blue-50 text-blue-700 border border-blue-200 font-semibold',
            'icon' => 'fas fa-clock',
            'percent_elapsed' => $percentElapsed,
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function escalations(): HasMany
    {
        return $this->hasMany(Escalation::class);
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(Decision::class)->latest();
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(IssueDiscussion::class)->oldest();
    }

    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_issues')->withPivot(['status', 'notes'])->withTimestamps();
    }
}
