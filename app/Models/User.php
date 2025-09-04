<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ========== CONSTANTES POUR LES TYPES D'UTILISATEURS ==========
    public const TYPE_ADMIN = 'admin';
    public const TYPE_SECRETAIRE = 'secretaire';
    public const TYPE_TECHNICIEN = 'technicien';
    public const TYPE_BIOLOGISTE = 'biologiste';

    public const TYPES = [
        self::TYPE_ADMIN => 'Administrateur',
        self::TYPE_SECRETAIRE => 'Secrétaire',
        self::TYPE_TECHNICIEN => 'Technicien',
        self::TYPE_BIOLOGISTE => 'Biologiste',
    ];

    // ========== ATTRIBUTS ==========
    protected $fillable = [
        'name',
        'username',
        'password',
        'type',
    ];

    // ✅ IMPORTANT: Spécifier que l'authentification se fait par username
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ Correction des casts
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
    
    // ========== ACCESSEURS ==========
    /**
     * Obtenir le nom du type d'utilisateur
     */
    public function getTypeNameAttribute()
    {
        return [
            'admin' => 'Administrateur',
            'secretaire' => 'Secrétaire',
            'technicien' => 'Technicien',
            'biologiste' => 'Biologiste',
        ][$this->type] ?? 'Inconnu';
    }

    /**
     * Obtenir les initiales de l'utilisateur
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return substr($initials, 0, 2);
    }

    // ========== MÉTHODES DE VÉRIFICATION DES RÔLES ==========
    /**
     * Vérifier si l'utilisateur est admin
     */
    public function isAdmin(): bool
    {
        return $this->type === self::TYPE_ADMIN;
    }

    /**
     * Vérifier si l'utilisateur est secrétaire
     */
    public function isSecretaire(): bool
    {
        return $this->type === self::TYPE_SECRETAIRE;
    }

    /**
     * Vérifier si l'utilisateur est technicien
     */
    public function isTechnicien(): bool
    {
        return $this->type === self::TYPE_TECHNICIEN;
    }

    /**
     * Vérifier si l'utilisateur est biologiste
     */
    public function isBiologiste(): bool
    {
        return $this->type === self::TYPE_BIOLOGISTE;
    }

    /**
     * Vérifier si l'utilisateur a un des rôles spécifiés
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->type === $roles;
        }

        return in_array($this->type, $roles);
    }

    /**
     * Vérifier si l'utilisateur peut accéder à l'administration
     */
    public function canAccessAdmin(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Vérifier si l'utilisateur peut gérer les prescriptions
     */
    public function canManagePrescriptions(): bool
    {
        return $this->hasRole([self::TYPE_ADMIN, self::TYPE_SECRETAIRE]);
    }

    /**
     * Vérifier si l'utilisateur peut effectuer des analyses
     */
    public function canPerformAnalyses(): bool
    {
        return $this->hasRole([self::TYPE_ADMIN, self::TYPE_TECHNICIEN, self::TYPE_BIOLOGISTE]);
    }

    /**
     * Vérifier si l'utilisateur peut valider les résultats
     */
    public function canValidateResults(): bool
    {
        return $this->hasRole([self::TYPE_ADMIN, self::TYPE_BIOLOGISTE]);
    }

    // ========== SCOPES ==========
    /**
     * Filtrer par type d'utilisateur
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSecretaires($query)
    {
        return $query->where('type', self::TYPE_SECRETAIRE);
    }

    public function scopeTechniciens($query)
    {
        return $query->where('type', self::TYPE_TECHNICIEN);
    }

    public function scopeBiologistes($query)
    {
        return $query->where('type', self::TYPE_BIOLOGISTE);
    }

    public function scopeAdmins($query)
    {
        return $query->where('type', self::TYPE_ADMIN);
    }

    /**
     * Recherche par nom ou username
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%");
        });
    }

    // ========== RELATIONS ==========
    /**
     * Prescriptions créées par ce secrétaire
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'secretaire_id');
    }

    /**
     * Analyses effectuées par ce technicien
     */
    public function analyses()
    {
        return $this->hasMany(Analyse::class, 'technicien_id');
    }

    /**
     * Résultats validés par ce biologiste
     */
    public function validatedResults()
    {
        return $this->hasMany(Resultat::class, 'biologiste_id');
    }

    // ========== MÉTHODES UTILITAIRES ==========
    /**
     * Obtenir la liste des types disponibles
     */
    public static function getAvailableTypes(): array
    {
        return self::TYPES;
    }

    /**
     * Vérifier si un type est valide
     */
    public static function isValidType(string $type): bool
    {
        return array_key_exists($type, self::TYPES);
    }

    /**
     * Obtenir le nombre d'utilisateurs par type
     */
    public static function getCountByType(): array
    {
        $counts = [];
        foreach (self::TYPES as $type => $label) {
            $counts[$type] = self::where('type', $type)->count();
        }
        return $counts;
    }

    /**
     * Formater le nom complet
     */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Obtenir l'avatar par défaut basé sur les initiales
     */
    public function getAvatarAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}