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
        'email', // ✅ AJOUT
        'password',
        'type',
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
        ];
    }

    // ✅ MÉTHODES D'AUTHENTIFICATION IMPORTANTES
    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'id'; 
    }
    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the "remember me" token value.
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the "remember me" token value.
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    // ========== ACCESSEURS ==========
    public function getTypeNameAttribute()
    {
        return [
            'admin' => 'Administrateur',
            'secretaire' => 'Secrétaire',
            'technicien' => 'Technicien',
            'biologiste' => 'Biologiste',
        ][$this->type] ?? 'Inconnu';
    }

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
    public function isAdmin(): bool
    {
        return $this->type === self::TYPE_ADMIN;
    }

    public function isSecretaire(): bool
    {
        return $this->type === self::TYPE_SECRETAIRE;
    }

    public function isTechnicien(): bool
    {
        return $this->type === self::TYPE_TECHNICIEN;
    }

    public function isBiologiste(): bool
    {
        return $this->type === self::TYPE_BIOLOGISTE;
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->type === $roles;
        }

        return in_array($this->type, $roles);
    }

    public function canAccessAdmin(): bool
    {
        return $this->isAdmin();
    }

    public function canManagePrescriptions(): bool
    {
        return $this->hasRole([self::TYPE_ADMIN, self::TYPE_SECRETAIRE]);
    }

    public function canPerformAnalyses(): bool
    {
        return $this->hasRole([self::TYPE_ADMIN, self::TYPE_TECHNICIEN, self::TYPE_BIOLOGISTE]);
    }

    public function canValidateResults(): bool
    {
        return $this->hasRole([self::TYPE_ADMIN, self::TYPE_BIOLOGISTE]);
    }

    // ========== SCOPES ==========
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

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%");
        });
    }

    // ========== RELATIONS ==========
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'secretaire_id');
    }

    public function analyses()
    {
        return $this->hasMany(Analyse::class, 'technicien_id');
    }

    public function validatedResults()
    {
        return $this->hasMany(Resultat::class, 'biologiste_id');
    }

    // ========== MÉTHODES UTILITAIRES ==========
    public static function getAvailableTypes(): array
    {
        return self::TYPES;
    }

    public static function isValidType(string $type): bool
    {
        return array_key_exists($type, self::TYPES);
    }

    public static function getCountByType(): array
    {
        $counts = [];
        foreach (self::TYPES as $type => $label) {
            $counts[$type] = self::where('type', $type)->count();
        }
        return $counts;
    }

    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getAvatarAttribute(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}