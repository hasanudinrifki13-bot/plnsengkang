<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Atribut dinamis yang otomatis disertakan dalam serialisasi model Task.
     */
    protected $appends = ['calculated_status'];

    /**
     * Accessor untuk menghitung status keterlambatan (Overdue) dan penyelesaian secara otomatis.
     */
    public function getCalculatedStatusAttribute(): string
    {
        // 1. Jika progress sudah 100%, status mutlak Selesai / Closed
        if ((int) $this->progress_percent >= 100 || in_array($this->status, ['Selesai', 'Closed', 'Resolved'])) {
            return 'Selesai';
        }

        // 2. Jika due_date lewat hari ini dan progres belum 100%, otomatis Overdue / Terlambat
        if ($this->due_date && Carbon::parse($this->due_date)->startOfDay()->lt(Carbon::now()->startOfDay())) {
            return 'Terlambat';
        }

        // 3. Jika belum lewat, kembalikan nilai status asli dari database
        return $this->status ?: 'In Progress';
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function decision(): BelongsTo
    {
        return $this->belongsTo(Decision::class);
    }

    public function getCompletionAttachmentUrlAttribute(): ?string
    {
        if ($this->completion_attachment && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->completion_attachment)) {
            return asset('storage/' . $this->completion_attachment);
        }
        return null;
    }
}
