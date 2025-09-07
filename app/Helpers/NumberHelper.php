<?php

if (!function_exists('nombreEnLettres')) {
    function nombreEnLettres($nombre) {
        // Tableaux des nombres en lettres
        $unites = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
        $dizaines = ['', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];
        $exceptions = [
            11 => 'onze', 12 => 'douze', 13 => 'treize', 14 => 'quatorze', 15 => 'quinze',
            16 => 'seize', 17 => 'dix-sept', 18 => 'dix-huit', 19 => 'dix-neuf'
        ];
        
        // Cas particulier pour zéro
        if ($nombre == 0) {
            return 'zéro';
        }
        
        // Conversion du nombre
        $resultat = '';
        
        // Millions
        if ($nombre >= 1000000) {
            $millions = floor($nombre / 1000000);
            $resultat .= nombreEnLettres($millions) . ' million' . ($millions > 1 ? 's' : '') . ' ';
            $nombre %= 1000000;
        }
        
        // Milliers
        if ($nombre >= 1000) {
            $milliers = floor($nombre / 1000);
            if ($milliers == 1) {
                $resultat .= 'mille ';
            } else {
                $resultat .= nombreEnLettres($milliers) . ' mille ';
            }
            $nombre %= 1000;
        }
        
        // Centaines
        if ($nombre >= 100) {
            $centaines = floor($nombre / 100);
            if ($centaines == 1) {
                $resultat .= 'cent ';
            } else {
                $resultat .= $unites[$centaines] . ' cent ';
            }
            $nombre %= 100;
        }
        
        // Dizaines et unités
        if ($nombre > 0) {
            if (isset($exceptions[$nombre])) {
                $resultat .= $exceptions[$nombre];
            } else {
                $dizaine = floor($nombre / 10);
                $unite = $nombre % 10;
                
                if ($dizaine > 0) {
                    $resultat .= $dizaines[$dizaine];
                    if ($unite > 0) {
                        $resultat .= '-' . $unites[$unite];
                    }
                } else {
                    $resultat .= $unites[$unite];
                }
            }
        }
        
        return trim($resultat);
    }
}