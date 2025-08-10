<?php
// ✅ SOLUTION 1 : Custom Cast (Recommandée)
// Créez le fichier : app/Casts/JsonUnicode.php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class JsonUnicode implements CastsAttributes
{
    /**
     * Cast the given value (from database to PHP)
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }
        
        return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Prepare the given value for storage (from PHP to database)
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }
        
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}