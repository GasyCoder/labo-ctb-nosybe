{{-- resources/views/livewire/secretaire/prescription/facture-impression.blade.php --}}
<div class="facture-container" style="width: 210mm; min-height: 297mm; background: white; font-family: Arial, sans-serif; font-size: 12px; margin: 0 auto; padding: 15mm;">
    
    {{-- EN-TÊTE LABORATOIRE --}}
    <div class="header-section" style="border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 70%;">
                    <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">
                        GCARE
                    </div>
                    <div style="font-size: 11px; line-height: 1.4;">
                        Galana-Galana, RC 2010 B00465, STAT 72102 1 2010 010 482
                        <br>Lot II S 24 Galana-Galana, Toliariada, TS: (020) 94 521 71
                        <br>Cll: 032 48 482 49, Email: info@gcare.mg
                        <br>Compte Bancaire: MCB TANA - 00000 00003 00045593 48 - BOA TANA - 00001 02799 01 202 1 450 16 Em
                        <br>Compte: BIC MG IFT - 78 60 61 202 1 1-450 16 Em
                    </div>
                </td>
                <td style="width: 30%; text-align: right; vertical-align: top;">
                    {{-- Logo/Cachet zone --}}
                    <div style="border: 1px solid #ccc; width: 80px; height: 80px; margin: 0 0 0 auto; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                        <span style="font-size: 10px; color: #666;">LOGO</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- INFORMATIONS FACTURE --}}
    <div class="facture-info" style="margin-bottom: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%;">
                    <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">FACTURE</div>
                    <div style="font-size: 11px;">
                        <strong>Facture N°:</strong> {{ $prescription->reference ?? $reference ?? 'PRES-2025-XXXXX' }}<br>
                        <strong>Date:</strong> {{ $prescription ? $prescription->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}<br>
                        <strong>Type Patient:</strong> {{ $patientType ?? 'EXTERNE' }}
                    </div>
                </td>
                <td style="width: 50%; text-align: right;">
                    <div style="font-size: 16px; font-weight: bold; color: #d32f2f;">
                        Arrête la présente facture à la somme de :
                        <br>{{ number_format($total, 0, ',', ' ') }} Ar
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- INFORMATIONS CLIENT --}}
    <div class="client-info" style="border: 1px solid #000; padding: 10px; margin-bottom: 15px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%;">
                    <div style="font-weight: bold; margin-bottom: 8px;">PATIENT</div>
                    <div style="font-size: 11px; line-height: 1.4;">
                        <strong>Nom:</strong> {{ $patient->nom ?? '' }} {{ $patient->prenom ?? '' }}<br>
                        @if($patient?->telephone)
                            <strong>Tél:</strong> {{ $patient->telephone }}<br>
                        @endif
                        @if($patient?->email)
                            <strong>Email:</strong> {{ $patient->email }}<br>
                        @endif
                        <strong>Âge:</strong> {{ $age ?? 0 }} {{ $uniteAge ?? 'ans' }}
                        @if($poids)
                            | <strong>Poids:</strong> {{ $poids }} kg
                        @endif
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <div style="font-weight: bold; margin-bottom: 8px;">PRESCRIPTEUR</div>
                    <div style="font-size: 11px; line-height: 1.4;">
                        @php
                            $prescripteur = null;
                            if($prescripteurId) {
                                $prescripteur = \App\Models\Prescripteur::find($prescripteurId);
                            }
                        @endphp
                        @if($prescripteur)
                            <strong>Dr:</strong> {{ $prescripteur->nom }} {{ $prescripteur->prenom }}<br>
                            @if($prescripteur->specialite)
                                <strong>Spécialité:</strong> {{ $prescripteur->specialite }}<br>
                            @endif
                            @if($prescripteur->telephone)
                                <strong>Tél:</strong> {{ $prescripteur->telephone }}<br>
                            @endif
                        @else
                            Dr. [Prescripteur non défini]
                        @endif
                    </div>
                </td>
            </tr>
        </table>
        
        @if($renseignementClinique)
            <div style="margin-top: 10px; font-size: 11px;">
                <strong>Renseignements cliniques:</strong> {{ $renseignementClinique }}
            </div>
        @endif
    </div>

    {{-- TABLEAU DES ANALYSES --}}
    <div class="analyses-table" style="margin-bottom: 15px;">
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; font-size: 11px;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 5%;">S</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 45%;">Libellé</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 10%;">Code</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 8%;">Qté</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%;">Unité</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: right; width: 20%;">Montant (MGA)</th>
                </tr>
            </thead>
            <tbody>
                @php $numeroLigne = 1; @endphp
                
                {{-- ANALYSES --}}
                @foreach($analysesPanier as $analyse)
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $numeroLigne++ }}</td>
                        <td style="border: 1px solid #000; padding: 4px;">
                            {{ $analyse['designation'] }}
                            @if(isset($analyse['is_parent']) && $analyse['is_parent'])
                                <br><em style="font-size: 10px; color: #666;">(Panel complet)</em>
                            @elseif($analyse['parent_nom'] !== 'Analyse individuelle')
                                <br><em style="font-size: 10px; color: #666;">{{ $analyse['parent_nom'] }}</em>
                            @endif
                        </td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center; font-family: monospace;">{{ $analyse['code'] ?? 'N/A' }}</td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center;">1</td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center;">Test</td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: right; font-weight: bold;">
                            {{ number_format($analyse['prix_effectif'], 0, ',', ' ') }}
                        </td>
                    </tr>
                @endforeach

                {{-- PRÉLÈVEMENTS (si présents) --}}
                @foreach($prelevementsSelectionnes as $prelevement)
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $numeroLigne++ }}</td>
                        <td style="border: 1px solid #000; padding: 4px;">
                            {{ $prelevement['nom'] }}
                            @if($prelevement['description'])
                                <br><em style="font-size: 10px; color: #666;">{{ $prelevement['description'] }}</em>
                            @endif
                        </td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center; font-family: monospace;">PREL</td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $prelevement['quantite'] }}</td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center;">{{ $prelevement['type_tube_requis'] ?? 'SEC' }}</td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: right; font-weight: bold;">
                            {{ number_format(($prelevement['prix'] * $prelevement['quantite']), 0, ',', ' ') }}
                        </td>
                    </tr>
                @endforeach

                {{-- LIGNE VIDE si besoin --}}
                @for($i = $numeroLigne; $i <= 8; $i++)
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $i }}</td>
                        <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
                        <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
                        <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
                        <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
                        <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    {{-- SECTION TOTAUX --}}
    <div class="totaux-section" style="margin-bottom: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 60%;">
                    {{-- Informations de paiement --}}
                    <div style="font-size: 11px;">
                        <strong>Mode de paiement:</strong> 
                        @php
                            $paymentMethod = \App\Models\PaymentMethod::where('code', $modePaiement)->first();
                        @endphp
                        {{ $paymentMethod?->name ?? $modePaiement }}<br>
                        
                        @if($prescription)
                            <strong>Secrétaire:</strong> {{ $prescription->secretaire->name ?? Auth::user()->name }}<br>
                        @endif
                        
                        <strong>Statut:</strong> 
                        @if($prescription)
                            {{ $prescription->status == 'EN_ATTENTE' ? 'En attente d\'analyse' : $prescription->status }}
                        @else
                            Payé
                        @endif
                    </div>
                </td>
                <td style="width: 40%; text-align: right;">
                    <table style="border: 1px solid #000; width: 100%; border-collapse: collapse; font-size: 11px;">
                        <tr>
                            <td style="border: 1px solid #000; padding: 4px; font-weight: bold; background-color: #f5f5f5;">SOUS-TOTAL</td>
                            <td style="border: 1px solid #000; padding: 4px; text-align: right; font-weight: bold;">
                                @php
                                    $sousTotal = 0;
                                    foreach($analysesPanier as $analyse) {
                                        $sousTotal += $analyse['prix_effectif'];
                                    }
                                    foreach($prelevementsSelectionnes as $prelevement) {
                                        $sousTotal += ($prelevement['prix'] * $prelevement['quantite']);
                                    }
                                @endphp
                                {{ number_format($sousTotal, 0, ',', ' ') }} Ar
                            </td>
                        </tr>
                        @if($remise > 0)
                            <tr>
                                <td style="border: 1px solid #000; padding: 4px; font-weight: bold;">REMISE</td>
                                <td style="border: 1px solid #000; padding: 4px; text-align: right; font-weight: bold; color: #d32f2f;">
                                    -{{ number_format($remise, 0, ',', ' ') }} Ar
                                </td>
                            </tr>
                        @endif
                        <tr style="background-color: #f0f0f0;">
                            <td style="border: 1px solid #000; padding: 6px; font-weight: bold; font-size: 12px;">TOTAL</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: right; font-weight: bold; font-size: 12px;">
                                {{ number_format($total, 0, ',', ' ') }} Ar
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- INFORMATIONS TUBES (si générés) --}}
    @if(!empty($tubesGeneres))
        <div class="tubes-info" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 15px; background-color: #f9f9f9;">
            <div style="font-weight: bold; font-size: 12px; margin-bottom: 8px;">TUBES GÉNÉRÉS</div>
            <div style="font-size: 10px; display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach($tubesGeneres as $tube)
                    <span style="background: white; border: 1px solid #999; padding: 3px 6px; border-radius: 3px; font-family: monospace;">
                        {{ $tube['numero_tube'] ?? $tube['code_barre'] }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- PIED DE PAGE --}}
    <div class="footer-section" style="border-top: 1px solid #ccc; padding-top: 10px; margin-top: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div style="line-height: 1.4;">
                        <strong>Conditions:</strong><br>
                        - Résultats disponibles sous 24-48h<br>
                        - Retrait sur présentation de cette facture<br>
                        - Conservation des échantillons: 7 jours<br>
                        - Horaires: Lun-Ven 7h-17h, Sam 7h-12h
                    </div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: bottom;">
                    {{-- Code-barres simulé --}}
                    <div style="border: 1px solid #000; padding: 5px; background: white; display: inline-block; font-family: monospace; font-size: 8px;">
                        <div style="background: repeating-linear-gradient(90deg, #000 0px, #000 1px, #fff 1px, #fff 2px); height: 30px; width: 150px; margin-bottom: 2px;"></div>
                        {{ $prescription->reference ?? $reference ?? 'PRES-2025-XXXXX' }}
                    </div>
                </td>
            </tr>
        </table>
        
        <div style="text-align: center; margin-top: 15px; font-size: 9px; color: #666;">
            Laboratoire GCARE - Galana-Galana, Toliariada - Tél: (020) 94 521 71
        </div>
    </div>
</div>

{{-- STYLES D'IMPRESSION --}}
<style>
    @media print {
        body { margin: 0; padding: 0; }
        .facture-container { 
            margin: 0 !important; 
            box-shadow: none !important;
            max-width: none !important;
        }
        .no-print { display: none !important; }
    }
    
    .facture-container {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        page-break-inside: avoid;
    }
    
    table { 
        page-break-inside: avoid; 
    }
    
    tr { 
        page-break-inside: avoid; 
    }
</style>

{{-- SCRIPT POUR L'IMPRESSION --}}
<script>
    window.imprimerFacture = function() {
        window.print();
    };
    
    // Auto-impression si paramètre présent
    @if(request()->has('print'))
        window.addEventListener('load', function() {
            setTimeout(() => window.print(), 500);
        });
    @endif
</script>