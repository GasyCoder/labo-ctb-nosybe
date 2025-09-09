{{-- resources/views/pdf/analyses/repeated-header.blade.php - NOUVEAU FICHIER --}}
{{-- En-tête répété pour les pages suivantes --}}
<div class="repeated-header">
    {{-- Logo simplifié --}}
    <img src="{{ public_path('assets/images/logo.png') }}" alt="LABORATOIRE LA REFERENCE" class="repeated-header-logo">
    
    {{-- Texte "En collaboration technique avec CTB MADAGASCAR" --}}
    <div style="text-align: center; font-size: 8pt; color: #0b48eeff; margin-bottom: 8px; font-style: italic;">
        En collaboration technique avec CTB MADAGASCAR
    </div>
    
    {{-- Informations patient simplifiées --}}
    <div class="repeated-patient-info">
        <div class="repeated-patient-name">
            Résultats de {{ $patientFullName }}
        </div>
        <div class="repeated-dossier">
            Dossier n° {{ $prescription->patient->numero_dossier ?? $prescription->reference }} du {{ $prescription->created_at->format('d/m/Y') }}
        </div>
    </div>
</div>