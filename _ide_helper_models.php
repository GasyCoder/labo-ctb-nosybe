<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property string $level
 * @property int|null $parent_id
 * @property string|null $designation
 * @property string|null $description
 * @property numeric|null $prix
 * @property bool $is_bold
 * @property int|null $examen_id
 * @property int|null $type_id
 * @property string|null $valeur_ref
 * @property string|null $unite
 * @property string|null $suffixe
 * @property array<array-key, mixed>|null $valeurs_predefinies
 * @property int|null $ordre
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Analyse> $enfants
 * @property-read int|null $enfants_count
 * @property-read \App\Models\Examen|null $examen
 * @property-read mixed $a_des_enfants
 * @property-read mixed $est_parent
 * @property-read mixed $formatted_results
 * @property-read mixed $result_disponible
 * @property-read mixed $valeur_complete
 * @property-read Analyse|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Resultat> $resultats
 * @property-read int|null $resultats_count
 * @property-read \App\Models\Type|null $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse actives()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse enfants()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse normales()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse parents()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse racines()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereDesignation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereExamenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereIsBold($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereOrdre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse wherePrix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereSuffixe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereUnite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereValeurRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse whereValeursPredefinies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analyse withoutTrashed()
 */
	class Analyse extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $prescription_id
 * @property int $analyse_id
 * @property string|null $valeur_min
 * @property string|null $valeur_max
 * @property string|null $valeur_normal
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Analyse $analyse
 * @property-read mixed $est_paye
 * @property-read mixed $est_termine
 * @property-read mixed $est_valide
 * @property-read mixed $statut_couleur
 * @property-read \App\Models\Prescription $prescription
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription aRefaire()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription enAttente()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription enCours()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription nonPaye()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription paye()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription status($status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription termine()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription valide()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription whereAnalyseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription wherePrescriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription whereValeurMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription whereValeurMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AnalysePrescription whereValeurNormal($value)
 */
	class AnalysePrescription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $prescription_id
 * @property int $analyse_id
 * @property int $bacterie_id
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Analyse $analyse
 * @property-read \App\Models\Bacterie $bacterie
 * @property-read \App\Models\Prescription $prescription
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ResultatAntibiotique> $resultatsAntibiotiques
 * @property-read int|null $resultats_antibiotiques_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiogramme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiogramme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiogramme query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiogramme whereAnalyseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiogramme whereBacterieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiogramme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiogramme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiogramme whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiogramme wherePrescriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiogramme whereUpdatedAt($value)
 */
	class Antibiogramme extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $famille_id
 * @property string $designation
 * @property string|null $commentaire
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bacterie> $bacteries
 * @property-read int|null $bacteries_count
 * @property-read \App\Models\BacterieFamille $famille
 * @property-read mixed $famille_name
 * @property-read mixed $full_name
 * @property-read mixed $status_badge
 * @property-read mixed $status_text
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique actifs()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique actives()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique byFamille($familleId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique inactives()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique search($term)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique whereCommentaire($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique whereDesignation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique whereFamilleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique withBacteries()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Antibiotique withoutTrashed()
 */
	class Antibiotique extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $famille_id
 * @property string $designation
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Antibiotique> $antibiotiques
 * @property-read int|null $antibiotiques_count
 * @property-read \App\Models\BacterieFamille $famille
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bacterie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bacterie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bacterie query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bacterie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bacterie whereDesignation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bacterie whereFamilleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bacterie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bacterie whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bacterie whereUpdatedAt($value)
 */
	class Bacterie extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $designation
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Antibiotique> $antibiotiques
 * @property-read int|null $antibiotiques_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bacterie> $bacteries
 * @property-read int|null $bacteries_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BacterieFamille actives()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BacterieFamille newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BacterieFamille newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BacterieFamille query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BacterieFamille whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BacterieFamille whereDesignation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BacterieFamille whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BacterieFamille whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BacterieFamille whereUpdatedAt($value)
 */
	class BacterieFamille extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $abr
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Analyse> $analyses
 * @property-read int|null $analyses_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Examen actifs()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Examen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Examen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Examen onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Examen query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Examen whereAbr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Examen whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Examen whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Examen whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Examen whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Examen whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Examen whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Examen withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Examen withoutTrashed()
 */
	class Examen extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $prescription_id
 * @property numeric $montant
 * @property numeric $commission_prescripteur
 * @property int|null $payment_method_id
 * @property int $recu_par
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $methode_paiement_label
 * @property-read mixed $modee_paiement
 * @property-read mixed $status_badge_class
 * @property-read mixed $status_color
 * @property-read mixed $status_label
 * @property-read \App\Models\PaymentMethod|null $paymentMethod
 * @property-read \App\Models\Prescription $prescription
 * @property-read \App\Models\User $utilisateur
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement nonPayés()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement payés()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereCommissionPrescripteur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereMontant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement wherePrescriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereRecuPar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Paiement withoutTrashed()
 */
	class Paiement extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $numero_dossier
 * @property string $nom
 * @property string|null $prenom
 * @property string $civilite
 * @property string|null $date_naissance
 * @property string|null $telephone
 * @property string|null $email
 * @property string $statut
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Prescription|null $dernierePrescription
 * @property-read mixed $derniere_visite
 * @property-read mixed $genre
 * @property-read mixed $is_enfant
 * @property-read mixed $latest_age
 * @property-read mixed $latest_unite_age
 * @property-read mixed $montant_total_paye
 * @property-read mixed $statut_automatique
 * @property-read mixed $total_analyses
 * @property-read mixed $total_paiements
 * @property-read mixed $total_prescriptions
 * @property-read \App\Models\Prescription|null $premierePrescription
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Prescription> $prescriptions
 * @property-read int|null $prescriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient actifs($jours = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient adultes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient enfants()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient fideles()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient nouveaux()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient rechercher($terme)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient vip()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereCivilite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereDateNaissance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereNumeroDossier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient wherePrenom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patient withoutTrashed()
 */
	class Patient extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code Code unique de la méthode (ex: ESPECES, CARTE)
 * @property string $label Libellé affiché (ex: Espèces, Carte bancaire)
 * @property bool $is_active Méthode active ou non
 * @property int $display_order Ordre d'affichage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Paiement> $paiements
 * @property-read int|null $paiements_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod activeOrdered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentMethod whereUpdatedAt($value)
 */
	class PaymentMethod extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nom Nom du prélèvement
 * @property string|null $description Description du prélèvement
 * @property string $prix Prix du prélèvement
 * @property int $quantite Quantité disponible
 * @property int $is_active Indique si le prélèvement est actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Prescription> $prescriptions
 * @property-read int|null $prescriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement wherePrix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement whereQuantite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prelevement withoutTrashed()
 */
	class Prelevement extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $prescription_id
 * @property int $prelevement_id
 * @property string $prix_unitaire Prix unitaire du prélèvement dans la prescription
 * @property int $quantite Quantité commandée dans la prescription
 * @property string $type_tube_requis
 * @property string $volume_requis_ml
 * @property int $tubes_generes
 * @property string|null $tubes_generes_at
 * @property string $is_payer Statut de paiement
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Prelevement $prelevement
 * @property-read \App\Models\Prescription $prescription
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription whereIsPayer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription wherePrelevementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription wherePrescriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription wherePrixUnitaire($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription whereQuantite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription whereTubesGeneres($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription whereTubesGeneresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription whereTypeTubeRequis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrelevementPrescription whereVolumeRequisMl($value)
 */
	class PrelevementPrescription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $grade
 * @property string $nom
 * @property string|null $prenom
 * @property string $status
 * @property string|null $specialite
 * @property string|null $telephone
 * @property string|null $email
 * @property bool $is_active
 * @property string|null $adresse
 * @property string|null $ville
 * @property string|null $code_postal
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $est_commissionnable
 * @property-read mixed $nom_complet
 * @property-read mixed $nom_simple
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Prescription> $prescriptions
 * @property-read int|null $prescriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur actifs()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur commissionnables()
 * @method static \Database\Factories\PrescripteurFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereCodePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur wherePrenom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereSpecialite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur whereVille($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescripteur withoutTrashed()
 */
	class Prescripteur extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $reference
 * @property int $secretaire_id
 * @property int $patient_id
 * @property int|null $prescripteur_id
 * @property string $patient_type
 * @property int $age
 * @property string $unite_age
 * @property numeric|null $poids
 * @property string|null $renseignement_clinique
 * @property numeric $remise
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Analyse> $analyses
 * @property-read int|null $analyses_count
 * @property-read mixed $commission_prescripteur
 * @property-read mixed $est_payee
 * @property-read mixed $est_payee_completement
 * @property-read mixed $montant_total
 * @property-read mixed $progres_analyses
 * @property-read mixed $status_label
 * @property-read mixed $tubes_par_statut
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Paiement> $paiements
 * @property-read int|null $paiements_count
 * @property-read \App\Models\Patient $patient
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Prelevement> $prelevements
 * @property-read int|null $prelevements_count
 * @property-read \App\Models\Prescripteur|null $prescripteur
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Resultat> $resultats
 * @property-read int|null $resultats_count
 * @property-read \App\Models\User $secretaire
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tube> $tubes
 * @property-read int|null $tubes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription actives()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription archivees()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription parPeriode($dateDebut, $dateFin)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription parPrescripteur($prescripteurId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription payees()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription wherePatientType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription wherePoids($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription wherePrescripteurId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereRemise($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereRenseignementClinique($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereSecretaireId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereUniteAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prescription withoutTrashed()
 */
	class Prescription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $prescription_id
 * @property int $analyse_id
 * @property string|null $resultats Résultats sous forme de texte ou JSON
 * @property string|null $valeur Valeur numérique ou texte simple
 * @property string|null $interpretation
 * @property string|null $conclusion
 * @property string $status
 * @property int|null $tube_id
 * @property int|null $famille_id
 * @property int|null $bacterie_id
 * @property int|null $validated_by
 * @property \Illuminate\Support\Carbon|null $validated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Analyse $analyse
 * @property-read \App\Models\Bacterie|null $bacterie
 * @property-read \App\Models\BacterieFamille|null $famille
 * @property-read mixed $display_value_pdf
 * @property-read mixed $est_pathologique
 * @property-read mixed $est_valide
 * @property-read mixed $germe_data
 * @property-read mixed $interpretation_couleur
 * @property-read mixed $leucocytes_data
 * @property-read mixed $resultats_pdf
 * @property-read mixed $statut_couleur
 * @property-read mixed $valeur_formatee
 * @property-read mixed $valeur_pdf
 * @property-read \App\Models\Prescription $prescription
 * @property-read \App\Models\Tube|null $tube
 * @property-read \App\Models\User|null $validatedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat enAttente()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat enCours()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat normaux()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat pathologiques()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat status($s)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat termines()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat valides()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereAnalyseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereBacterieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereConclusion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereFamilleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereInterpretation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat wherePrescriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereResultats($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereTubeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereValeur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereValidatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat whereValidatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resultat withoutTrashed()
 */
	class Resultat extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $antibiogramme_id
 * @property int $antibiotique_id
 * @property string $interpretation
 * @property numeric|null $diametre_mm
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Antibiogramme $antibiogramme
 * @property-read \App\Models\Antibiotique $antibiotique
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatAntibiotique newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatAntibiotique newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatAntibiotique query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatAntibiotique whereAntibiogrammeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatAntibiotique whereAntibiotiqueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatAntibiotique whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatAntibiotique whereDiametreMm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatAntibiotique whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatAntibiotique whereInterpretation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultatAntibiotique whereUpdatedAt($value)
 */
	class ResultatAntibiotique extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nom_entreprise
 * @property string|null $nif
 * @property string|null $statut
 * @property float $remise_pourcentage
 * @property bool $activer_remise
 * @property string $format_unite_argent
 * @property bool $commission_prescripteur
 * @property float $commission_prescripteur_pourcentage
 * @property string|null $logo Chemin vers le logo de l'entreprise
 * @property string|null $favicon Chemin vers le favicon du site
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PaymentMethod|null $defaultPaymentMethod
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereActiverRemise($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCommissionPrescripteur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCommissionPrescripteurPourcentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereFavicon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereFormatUniteArgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereNif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereNomEntreprise($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereRemisePourcentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $prescription_id
 * @property int $patient_id
 * @property int $prelevement_id
 * @property string|null $code_barre Code-barre unique du tube
 * @property string|null $numero_tube Numéro séquentiel lisible (ex: T-2024-001234)
 * @property string $statut
 * @property string|null $type_tube Type de tube (ex: EDTA, Héparine, Sec)
 * @property numeric|null $volume_ml Volume en ml
 * @property string|null $couleur_bouchon Couleur du bouchon
 * @property \Illuminate\Support\Carbon|null $genere_at
 * @property \Illuminate\Support\Carbon|null $preleve_at
 * @property \Illuminate\Support\Carbon|null $receptionne_at
 * @property \Illuminate\Support\Carbon|null $archive_at
 * @property int|null $preleve_par
 * @property int|null $receptionne_par
 * @property string|null $observations
 * @property array<array-key, mixed>|null $metadata Données techniques supplémentaires
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Analyse> $analyses
 * @property-read int|null $analyses_count
 * @property-read mixed $statut_couleur
 * @property-read \App\Models\Patient $patient
 * @property-read \App\Models\Prelevement $prelevement
 * @property-read \App\Models\Prescription $prescription
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Resultat> $resultats
 * @property-read int|null $resultats_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube enAttentePrelvement()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube generes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube pourPrescription($prescriptionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube prelevesNonReceptionnes()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube statut($statut)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereArchiveAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereCodeBarre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereCouleurBouchon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereGenereAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereNumeroTube($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube wherePreleveAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube wherePrelevePar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube wherePrelevementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube wherePrescriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereReceptionneAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereReceptionnePar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereTypeTube($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube whereVolumeMl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tube withoutTrashed()
 */
	class Tube extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tube_id
 * @property string $ancien_statut
 * @property string $nouveau_statut
 * @property int $modifie_par
 * @property string|null $commentaire
 * @property \Illuminate\Support\Carbon $modifie_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $statut_changement
 * @property-read \App\Models\Tube $tube
 * @property-read \App\Models\User $utilisateur
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TubeHistoriqueStatut newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TubeHistoriqueStatut newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TubeHistoriqueStatut pourTube($tubeId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TubeHistoriqueStatut query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TubeHistoriqueStatut recentsPremiers()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TubeHistoriqueStatut whereAncienStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TubeHistoriqueStatut whereCommentaire($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TubeHistoriqueStatut whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TubeHistoriqueStatut whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TubeHistoriqueStatut whereModifieAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TubeHistoriqueStatut whereModifiePar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TubeHistoriqueStatut whereNouveauStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TubeHistoriqueStatut whereTubeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TubeHistoriqueStatut whereUpdatedAt($value)
 */
	class TubeHistoriqueStatut extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $libelle
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Analyse> $analyses
 * @property-read int|null $analyses_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type actifs()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereLibelle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type withoutTrashed()
 */
	class Type extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string|null $email
 * @property string $type
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Analyse> $analyses
 * @property-read int|null $analyses_count
 * @property-read string $avatar
 * @property-read string $full_name
 * @property-read string $initials
 * @property-read mixed $type_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Prescription> $prescriptions
 * @property-read int|null $prescriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Resultat> $validatedResults
 * @property-read int|null $validated_results_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User admins()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User biologistes()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User search(string $search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User secretaires()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User techniciens()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 */
	class User extends \Eloquent {}
}

