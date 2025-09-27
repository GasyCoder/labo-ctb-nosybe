<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysePrescription extends Model
{
    protected $table = 'prescription_analyse';

    public $incrementing = true;

    protected $fillable = [
        'prescription_id',
        'analyse_id',
        'prix',
        'status',
        'is_payer',
    ];

    // CONSTANTES DE STATUT
    const STATUS_EN_ATTENTE = 'EN_ATTENTE';
    const STATUS_EN_COURS = 'EN_COURS';
    const STATUS_TERMINE = 'TERMINE';
    const STATUS_VALIDE = 'VALIDE';
    const STATUS_ARCHIVE = 'ARCHIVE';
    const STATUS_PAYE = 'PAYE';
    const STATUS_NON_PAYE = 'NON_PAYE';
    const STATUS_A_REFAIRE = 'A_REFAIRE';

    // RELATIONS
    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function analyse(): BelongsTo
    {
        return $this->belongsTo(Analyse::class);
    }

    // SCOPES
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeEnAttente($query)
    {
        return $query->where('status', self::STATUS_EN_ATTENTE);
    }

    public function scopeEnCours($query)
    {
        return $query->where('status', self::STATUS_EN_COURS);
    }

    public function scopeTermine($query)
    {
        return $query->where('status', self::STATUS_TERMINE);
    }

    public function scopeValide($query)
    {
        return $query->where('status', self::STATUS_VALIDE);
    }

    public function scopeARefaire($query)
    {
        return $query->where('status', self::STATUS_A_REFAIRE);
    }

    public function scopePaye($query)
    {
        return $query->where('is_payer', self::STATUS_PAYE);
    }

    public function scopeNonPaye($query)
    {
        return $query->where('is_payer', self::STATUS_NON_PAYE);
    }

    // MÉTHODES MÉTIER
    public function updateStatus($newStatus)
    {
        if (
            !in_array($newStatus, [
                self::STATUS_EN_ATTENTE,
                self::STATUS_EN_COURS,
                self::STATUS_TERMINE,
                self::STATUS_VALIDE,
                self::STATUS_A_REFAIRE
            ])
        ) {
            throw new \InvalidArgumentException("Statut invalide: {$newStatus}");
        }

        $this->status = $newStatus;
        $this->save();
    }

    public function marquerTermine()
    {
        $this->updateStatus(self::STATUS_TERMINE);
    }

    public function marquerValide()
    {
        $this->updateStatus(self::STATUS_VALIDE);
    }

    public function marquerARefaire()
    {
        $this->updateStatus(self::STATUS_A_REFAIRE);
    }

    public function marquerPaye()
    {
        $this->update(['is_payer' => self::STATUS_PAYE]);
    }

    // ACCESSEURS
    public function getEstTermineAttribute()
    {
        return $this->status === self::STATUS_TERMINE;
    }

    public function getEstValideAttribute()
    {
        return $this->status === self::STATUS_VALIDE;
    }

    public function getEstPayeAttribute()
    {
        return $this->is_payer === self::STATUS_PAYE;
    }

    public function getStatutCouleurAttribute()
    {
        return match ($this->status) {
            self::STATUS_EN_ATTENTE => 'gray',
            self::STATUS_EN_COURS => 'blue',
            self::STATUS_TERMINE => 'orange',
            self::STATUS_VALIDE => 'green',
            self::STATUS_A_REFAIRE => 'red',
            self::STATUS_ARCHIVE => 'gray',
            default => 'gray'
        };
    }
}