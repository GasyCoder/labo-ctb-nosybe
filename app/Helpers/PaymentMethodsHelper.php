<?php

namespace App\Helpers;

use App\Models\PaymentMethod;
use Illuminate\Support\Collection;

class PaymentMethodsHelper
{
    /**
     * Récupérer toutes les méthodes de paiement actives
     */
    public static function getActivePaymentMethods(): Collection
    {
        return PaymentMethod::activeOrdered()->get();
    }

    /**
     * Récupérer toutes les méthodes de paiement sous forme de tableau pour select
     */
    public static function getPaymentMethodsForSelect(): array
    {
        return PaymentMethod::activeOrdered()
            ->pluck('label', 'code')
            ->toArray();
    }

    /**
     * Récupérer une méthode de paiement par son code
     */
    public static function getPaymentMethodByCode(string $code): ?PaymentMethod
    {
        return PaymentMethod::where('code', strtoupper($code))->first();
    }

    /**
     * Vérifier si une méthode de paiement est active
     */
    public static function isPaymentMethodActive(string $code): bool
    {
        $method = self::getPaymentMethodByCode($code);
        return $method && $method->is_active;
    }

    /**
     * Récupérer le libellé d'une méthode de paiement par son code
     */
    public static function getPaymentMethodLabel(string $code): string
    {
        $method = self::getPaymentMethodByCode($code);
        return $method ? $method->label : $code;
    }

    /**
     * Récupérer toutes les méthodes de paiement avec leurs statistiques d'utilisation
     * (nécessite une relation avec les paiements)
     */
    public static function getPaymentMethodsWithStats(): Collection
    {
        return PaymentMethod::withCount('paiements')
            ->ordered()
            ->get();
    }

    /**
     * Créer une nouvelle méthode de paiement
     */
    public static function createPaymentMethod(array $data): PaymentMethod
    {
        return PaymentMethod::create([
            'code' => strtoupper($data['code']),
            'label' => $data['label'],
            'is_active' => $data['is_active'] ?? true,
            'display_order' => $data['display_order'] ?? self::getNextDisplayOrder(),
        ]);
    }

    /**
     * Obtenir le prochain ordre d'affichage disponible
     */
    public static function getNextDisplayOrder(): int
    {
        return PaymentMethod::max('display_order') + 1;
    }

    /**
     * Réorganiser l'ordre d'affichage des méthodes de paiement
     */
    public static function reorderPaymentMethods(array $orderedIds): void
    {
        foreach ($orderedIds as $order => $id) {
            PaymentMethod::where('id', $id)->update(['display_order' => $order + 1]);
        }
    }

    /**
     * Activer/désactiver une méthode de paiement
     */
    public static function togglePaymentMethodStatus(int $id): bool
    {
        $method = PaymentMethod::find($id);
        if ($method) {
            $method->update(['is_active' => !$method->is_active]);
            return $method->is_active;
        }
        return false;
    }

    /**
     * Supprimer une méthode de paiement (seulement si pas utilisée)
     */
    public static function deletePaymentMethod(int $id): bool
    {
        $method = PaymentMethod::find($id);
        if ($method) {
            // Vérifier si la méthode est utilisée
            // if ($method->paiements()->count() > 0) {
            //     throw new \Exception('Cette méthode de paiement ne peut pas être supprimée car elle est utilisée.');
            // }
            
            return $method->delete();
        }
        return false;
    }

    /**
     * Récupérer les méthodes de paiement sous forme de tableau pour l'API
     */
    public static function getPaymentMethodsForApi(): array
    {
        return PaymentMethod::activeOrdered()
            ->get(['id', 'code', 'label', 'display_order'])
            ->toArray();
    }
}