<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TubeHistoriqueStatut extends Model
{
    protected $fillable = [
        'tube_id',
        'ancien_statut',
        'nouveau_statut',
        'modifie_par',
        'modifie_at',
    ];

    protected $casts = [
        'modifie_at' => 'datetime',
    ];

    // Relations
    public function tube(): BelongsTo
    {
        return $this->belongsTo(Tube::class);
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modifie_par');
    }

    // Scopes
    public function scopePourTube($query, $tubeId)
    {
        return $query->where('tube_id', $tubeId);
    }

    public function scopeRecentsPremiers($query)
    {
        return $query->orderBy('modifie_at', 'desc');
    }

    // Accesseurs
    public function getStatutChangementAttribute()
    {
        return "{$this->ancien_statut} → {$this->nouveau_statut}";
    }
}