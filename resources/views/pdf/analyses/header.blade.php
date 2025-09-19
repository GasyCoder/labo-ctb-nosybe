{{-- resources/views/pdf/analyses/header.blade.php --}}
@php
    $patientFullName = trim(
        ($prescription->patient->civilite ?? '') .
            ' ' .
            ($prescription->patient->nom ?? 'N/A') .
            ' ' .
            ($prescription->patient->prenom ?? ''),
    );
@endphp

{{-- Informations patient --}}
<div class="patient-info">
    <div class="patient-info-row">
        <div class="patient-info-left">
            <div class="info-value">
                <span class="info-label">Résultats de :</span><br>
               <span class="text-fine patient-name" style="font-size: 11.5px;">{{ $patientFullName }}</span>

            </div>

            <div class="info-value">
                <span class="info-label">Âge :</span>
                <span class="text-fine">{{ $prescription->age ?? 'N/A' }} {{ $prescription->unite_age ?? '' }}</span><br>

                <span class="info-label">Dossier n° :</span>
                <span class="text-fine">
                    {{ $prescription->patient->numero_dossier ?? $prescription->reference }}
                    du {{ $prescription->patient->created_at->format('d/m/Y') ?? 'N/A' }}
                </span><br>

                <span class="info-label">Prescription :</span>
                <span class="text-fine">
                    du {{ $prescription->created_at->format('d/m/Y') }}
                </span>
            </div>

            <div class="info-value">
                <span class="text-fine medecin-name" style="font-size: 11.5px;">
                    {{ trim(
                        ($prescription->prescripteur->grade ?? '') .
                            ' ' .
                            ($prescription->prescripteur->prenom ?? '') .
                            ' ' .
                            ($prescription->prescripteur->nom ?? 'Non assigné'),
                    ) }}
                </span>
            </div>
        </div>

        <div class="patient-info-right">


            <div class="info-value">
                <span class="text-fine patient-name" style="font-size: 11.5px;">{{ $patientFullName }}</span><br>
                <span class="info-label">Dossier n° :</span>
                <span class="text-fine">
                    {{ $prescription->patient->numero_dossier ?? $prescription->reference }}
                    du {{ $prescription->patient->created_at->format('d/m/Y') ?? 'N/A' }}
                </span>
            </div>

            @if (!empty($prescription->renseignement_clinique))
                <div class="info-value">
                    {{-- <span class="info-label">Renseignement clinique :</span><br> --}}
                    <span class="text-fine">{{ $prescription->renseignement_clinique }}</span>
                </div>
            @endif
        </div>
    </div>
</div>
