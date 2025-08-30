<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Facture {{ $prescription->reference ?? 'N/A' }} - C-Lab/C-Care</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    @media print {
      .no-print {
        display: none !important;
      }
      body {
        padding: 0;
        margin: 0;
        background: white;
        font-size: 12pt;
      }
      .invoice-container {
        box-shadow: none;
        margin: 0;
        padding: 15px;
        width: 100%;
      }
      .break-before {
        page-break-before: always;
      }
      .break-after {
        page-break-after: always;
      }
      .avoid-break {
        page-break-inside: avoid;
      }
    }
    
    body {
      font-family: 'DejaVu Sans', Arial, sans-serif;
      background-color: #f3f4f6;
      padding: 20px;
    }
    
    .invoice-container {
      max-width: 800px;
      margin: 0 auto;
      background-color: white;
      padding: 25px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .header-table {
      width: 100%;
      margin-bottom: 15px;
      border-collapse: collapse;
    }
    
    .header-table td {
      padding: 3px 5px;
      vertical-align: top;
      border: none;
    }
    
    .divider {
      border-top: 1px solid #000;
      margin: 10px 0;
    }
    
    .footer {
      margin-top: 20px;
      font-size: 0.75rem;
      text-align: center;
      color: #555;
    }
    
    .stamp {
      position: absolute;
      right: 50px;
      opacity: 0.8;
      transform: rotate(15deg);
    }
    
    .amount-section {
      margin: 15px 0;
    }
    
    .payment-info {
      margin-top: 20px;
    }
    
    .button-container {
      margin-bottom: 20px;
      text-align: right;
    }
    
    .btn {
      background-color: #4CAF50;
      border: none;
      color: white;
      padding: 10px 15px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 14px;
      margin: 4px 2px;
      cursor: pointer;
      border-radius: 4px;
      transition: background-color 0.3s;
    }
    
    .btn:hover {
      opacity: 0.9;
    }
    
    .btn-print {
      background-color: #2196F3;
    }
    
    .btn-download {
      background-color: #FF9800;
    }
    
    .btn-back {
      background-color: #9e9e9e;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }
    
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }
    
    th {
      background-color: #f2f2f2;
      font-weight: bold;
    }
    
    .text-right {
      text-align: right;
    }
    
    .text-center {
      text-align: center;
    }
    
    .font-bold {
      font-weight: bold;
    }
    
    .mt-4 {
      margin-top: 1rem;
    }
    
    .mb-4 {
      margin-bottom: 1rem;
    }
  </style>
</head>
<body class="bg-gray-100">
  <div class="invoice-container" id="invoice">
    <div class="button-container no-print">
      <button onclick="window.history.back()" class="btn btn-back">
        ← Retour
      </button>
      <button onclick="printInvoice()" class="btn btn-print">
        🖨️ Imprimer
      </button>
      <button onclick="generatePDF()" class="btn btn-download">
        📥 Télécharger PDF
      </button>
    </div>

    <table class="header-table">
      <tr>
        <td width="50%">
          <strong>PID</strong> : {{ $prescription->patient->id ?? 'N/A' }}<br>
          <strong>Visit ID</strong> : {{ $prescription->id ?? 'N/A' }}<br>
          <strong>Nom du patient</strong> : {{ $prescription->patient->civilite ?? '' }} {{ $prescription->patient->nom ?? '' }} {{ $prescription->patient->prenom ?? '' }}<br>
          <strong>DDN/sexe</strong> : {{ isset($prescription->patient->date_naissance) ? $prescription->patient->date_naissance->format('d/m/Y') : 'N/A' }}/{{ $prescription->patient->genre ?? 'N/A' }}<br>
          <strong>N° de portable</strong> : {{ $prescription->patient->telephone ?? 'N/A' }}<br>
          <strong>Address</strong> : {{ $prescription->patient->adresse ?? 'N/A' }}
        </td>
        <td width="50%" style="text-align: right;">
          <strong>Date de facturation</strong> : {{ $prescription->created_at->format('d/m/Y H:i') }}<br>
          <strong>Numéro de facture</strong> : {{ $prescription->reference }}<br>
          <strong>Panel</strong> : {{ $prescription->patient_type }}<br>
          <strong>STAT</strong> : <br>
          <strong>N.I.F</strong> : <br>
          <strong>Type de client</strong> : <br>
          <strong>Référé par</strong> : {{ $prescription->prescripteur->nom_complet ?? $prescription->prescripteur->nom ?? 'N/A' }}
        </td>
      </tr>
    </table>

    <div class="divider"></div>

    <h2 style="text-align: center; font-weight: bold; font-size: 1.5rem;">FACTURE</h2>

    <table>
      <thead>
        <tr>
          <th>N°</th>
          <th>Détails / Désignation</th>
          <th>Unités</th>
          <th>Tarif (MGA)</th>
          <th>Remise (MGA)</th>
          <th>Montant (MGA)</th>
        </tr>
      </thead>
      <tbody>
        @php 
          $total = 0; 
          $index = 1;
        @endphp
        
        <!-- Analyses -->
        @foreach($prescription->analyses as $analyse)
          @php
            $prix = $analyse->prix;
            $total += $prix;
          @endphp
          <tr>
            <td>{{ $index++ }}</td>
            <td>{{ $analyse->designation }}</td>
            <td class="text-right">1.00</td>
            <td class="text-right">{{ number_format($prix, 2, '.', ' ') }}</td>
            <td class="text-right">0.00</td>
            <td class="text-right">{{ number_format($prix, 2, '.', ' ') }}</td>
          </tr>
        @endforeach
        
        <!-- Prélèvements -->
        @foreach($prescription->prelevements as $prelevement)
          @php
            $quantite = $prelevement->pivot->quantite ?? 1;
            $prixUnitaire = $prelevement->pivot->prix_unitaire ?? $prelevement->prix ?? 0;
            $prix = $prixUnitaire * $quantite;
            $total += $prix;
          @endphp
          <tr>
            <td>{{ $index++ }}</td>
            <td>{{ $prelevement->nom }} (x{{ $quantite }})</td>
            <td class="text-right">{{ number_format($quantite, 2, '.', ' ') }}</td>
            <td class="text-right">{{ number_format($prixUnitaire, 2, '.', ' ') }}</td>
            <td class="text-right">0.00</td>
            <td class="text-right">{{ number_format($prix, 2, '.', ' ') }}</td>
          </tr>
        @endforeach
        
        <!-- Remise -->
        @if($prescription->remise > 0)
          <tr>
            <td>{{ $index++ }}</td>
            <td>Remise</td>
            <td class="text-right">1.00</td>
            <td class="text-right">-{{ number_format($prescription->remise, 2, '.', ' ') }}</td>
            <td class="text-right">{{ number_format($prescription->remise, 2, '.', ' ') }}</td>
            <td class="text-right">-{{ number_format($prescription->remise, 2, '.', ' ') }}</td>
          </tr>
          @php $total -= $prescription->remise; @endphp
        @endif
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5" class="text-right font-bold">Total (MGA)</td>
          <td class="text-right font-bold">{{ number_format($total, 2, '.', ' ') }}</td>
        </tr>
      </tfoot>
    </table>

    <div class="divider"></div>

    <div class="amount-section">
      <p><strong>Arrêté la présente facture à la somme de :</strong></p>
      <p class="font-bold">{{ nombreEnLettres($total) }} Ariary</p>
      
      <table class="header-table">
        <tr>
          <td width="50%">
            Montant total (hors TVA) : ______<br>
            Montant total de la TVA : ______<br>
            TVA incluse : ______
          </td>
          <td width="50%">
            Montant de la remise : {{ number_format($prescription->remise, 2, '.', ' ') }}<br>
            Montant assuré : ______<br>
            Montant payé : {{ number_format($total, 2, '.', ' ') }}<br>
            Montant du solde : 0.00
          </td>
        </tr>
      </table>
    </div>

    <div class="divider"></div>

    <div class="payment-info">
      <p><strong>Date et heure du reçu</strong></p>
      <table>
        <tr>
          <th>Date</th>
          <th>Montant</th>
          <th>Mode de paiement</th>
          <th>Collecté par</th>
        </tr>
        <tr>
          <td>{{ $prescription->created_at->format('d/m/Y H:i') }}</td>
          <td class="text-right">{{ number_format($total, 2, '.', ' ') }}</td>
          <td>{{ $prescription->paiement->paymentMethod->nom ?? 'ESPECES' }}</td>
          <td>{{ Auth::user()->name }}</td>
        </tr>
      </table>
      
      <div class="mt-4">
        <p>Payable par le patient : {{ number_format($total, 2, '.', ' ') }}</p>
        <p>Payé par le patient : {{ number_format($total, 2, '.', ' ') }}</p>
        <p>Montant du solde : 0.00</p>
        <p>Préparé par : {{ Auth::user()->name }}</p>
      </div>
    </div>

    <div style="position: relative;">
      <div class="stamp no-print">
        <div style="border: 2px solid red; padding: 10px; text-align: center; color: red; font-weight: bold; border-radius: 5px;">
          PAYÉ
        </div>
      </div>
    </div>

    <div class="footer">
      <p><strong>C-Lab/C-Care</strong></p>
      <p>NIF: 2000522438 - RC : 2010B00456 - STAT:72102 11 2010 010483</p>
      <p>Siège Lot 25A Antarctic Antehiroka - Tél. : 020 78 450 61 - 032 11 450 61 - Email : info@c-care.mg</p>
      <p>Comptes bancaires : MCB TANA : 00006 00003 00000845663 49 / BMOI TANA : 00004 00001 02157920101 23</p>
    </div>
  </div>

  <script>
    function printInvoice() {
      window.print();
    }
    
    function generatePDF() {
      const element = document.getElementById('invoice');
      
      // Configuration pour html2pdf
      const opt = {
        margin: 10,
        filename: 'facture_{{ $prescription->reference }}.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
      };
      
      // Générer le PDF et le télécharger
      html2pdf().set(opt).from(element).save();
    }
    
    // Fonction pour formater les nombres
    function formatNumber(number) {
      return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(number);
    }
  </script>
</body>
</html>