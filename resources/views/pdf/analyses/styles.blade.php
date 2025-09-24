{{-- resources/views/pdf/analyses/css.blade.php - VERSION CORRIGÉE COMPLÈTE --}}
<style>
    /* Reset et styles de base */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    @page {
        size: A4;
        margin: 1cm 2cm 1cm 2cm;
        
        /* En-tête répété automatiquement sur chaque page (sauf la première) */
        @top-left {
            content: element(running-header);
            margin-top: 0.5cm;
        }
    }

    /* Première page normale */
    @page :first {
        margin-top: 1cm;
        @top-left {
            content: none;
        }
    }

    /* Pages suivantes avec espace pour l'en-tête */
    @page :not(:first) {
        margin-top: 3cm;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 11pt;
        color: black;
        line-height: 1.1;
    }

    .bold {
        font-weight: bold;
    }

    /* En-tête principal (première page seulement) */
    .header-section {
        width: 100%;
        display: block;
        margin: 0;
        padding: 0;
        line-height: 0;
    }

    .header-logo {
        width: 100%;
        max-height: 180px;
        object-fit: contain;
        object-position: left top;
        margin: 0;
        padding: 0;
        display: block;
    }

    /* En-tête répété pour les pages suivantes */
    .running-header {
        position: running(running-header);
        display: block;
        width: 100%;
        padding: 10px 0;
        border-bottom: 0.5px solid #0b48eeff;
        background: white;
    }

    .running-header-logo {
        width: 100%;
        max-height: 50px;
        object-fit: contain;
        object-position: left top;
        margin-bottom: 5px;
    }

    .running-header-text {
        text-align: center;
        font-size: 7pt;
        color: #0b48eeff;
        margin-bottom: 5px;
        font-style: italic;
    }

    .running-header-patient {
        font-size: 9pt;
        font-weight: bold;
        margin-bottom: 2px;
    }

    .running-header-dossier {
        font-size: 8pt;
        color: #666;
    }

    /* Section contenu */
    .content-wrapper {
        padding: 0 40px;
    }

    /* Information patient */
    .patient-info {
        margin: 15px 0;
        width: 100%;
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
        display: table;
        table-layout: fixed;
        page-break-inside: avoid;
        page-break-after: avoid;
    }

    .patient-info-row {
        display: table-row;
    }

    .patient-info-left {
        display: table-cell;
        width: 50%;
        padding-right: 20px;
        vertical-align: top;
        line-height: 1.5;
    }

    .patient-info-right {
        display: table-cell;
        width: 50%;
        padding-left: 20px;
        vertical-align: top;
        line-height: 1.5;
    }

    .info-label {
        color: #374151;
        font-size: 9pt;
    }

    .info-value {
        color: #111827;
        font-size: 9pt;
        margin-bottom: 0px;
    }

    .text-fine {
        font-weight: normal;
        font-size: 9pt;
    }

    .patient-name {
        font-weight: bold;
    }

    .medecin-name {
        font-weight: bold;
    }

    /* NOUVEAU : Gestion intelligente des examens */
    .examen-section {
        page-break-inside: avoid; /* Évite de couper un examen */
        margin-bottom: 20px;
        orphans: 3; /* Au moins 3 lignes en bas de page */
        widows: 3;  /* Au moins 3 lignes en haut de nouvelle page */
    }

    /* Pour les très longs examens, permettre la coupure mais intelligemment */
    .examen-section.long-content {
        page-break-inside: auto;
    }

    .examen-section.long-content .examen-content {
        orphans: 5;
        widows: 5;
    }

    /* Tables principales */
    .main-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
        padding: 0;
    }

    .main-table td {
        padding: 1px 0;
        line-height: 1.2;
        vertical-align: middle;
    }

    .main-table tr {
        page-break-inside: avoid;
    }

    /* Ligne rouge */
    .red-line {
        border-top: 0.5px solid #0b48eeff;
        margin: 1px 0;
        width: 100%;
    }

    /* Colonnes */
    .col-designation {
        width: 40%;
        text-align: left;
        padding-right: 10px;
        font-size: 10.5pt;
    }

    .col-resultat {
        width: 20%;
        text-align: left;
        padding-left: 20px;
        font-size: 9pt;
    }

    .col-valref {
        width: 20%;
        text-align: left;
        padding-left: 20px;
        font-size: 8pt;
    }

    .col-anteriorite {
        width: 8%;
        padding-left: 10px;
        text-align: left;
        font-size: 10.5pt;
    }

    /* Styles des titres */
    .section-title {
        color: #042379ff;
        font-weight: bold;
        text-transform: uppercase;
        page-break-after: avoid; /* Évite un saut juste après un titre */
    }

    .header-cols {
        font-size: 8pt;
        color: #000;
        font-style: italic;
    }

    /* Niveaux de hiérarchie */
    .parent-row {
        font-weight: bold;
        page-break-after: avoid;
    }

    .child-row td:first-child {
        padding-left: 20px;
    }

    .subchild-row td:first-child {
        padding-left: 40px;
    }

    /* Styles antibiogrammes */
    .antibiogramme-section {
        page-break-inside: avoid;
        margin: 10px 0;
    }

    .antibiogramme-header {
        page-break-inside: avoid;
        page-break-after: avoid;
    }

    .antibiogramme-header td {
        background-color: #f8f9fa;
        border-top: 1px solid #ccc;
        border-bottom: 1px solid #e9ecef;
        font-weight: bold;
        font-size: 10pt;
        color: #333;
        padding: 6px 0 4px 0;
    }

    .antibiogramme-row td {
        padding: 2px 0;
        font-size: 9pt;
        line-height: 1.3;
    }

    .antibiogramme-row td:first-child {
        color: #666;
        font-weight: 500;
    }

    /* Styles antibiotiques */
    .antibiotique-sensible {
        color: #28a745;
    }

    .antibiotique-resistant {
        color: #0542ebff;
        font-weight: bold;
    }

    .antibiotique-intermediaire {
        color: #ffc107;
        font-style: italic;
    }

    /* Styles spéciaux */
    .indent-1 {
        padding-left: 20px !important;
    }

    .indent-2 {
        padding-left: 40px !important;
    }

    .signature {
        margin-top: 10px;
        text-align: right;
        padding-right: 10px;
        page-break-inside: avoid;
        /* PAS de position: absolute ici ! */
    }

    /* Espacement */
    .spacing {
        height: 3px;
    }

    /* Résultats pathologiques */
    .pathologique {
        font-weight: bold;
        color: #000;
    }

    /* Styles pour conclusions */
    .conclusion-section {
        margin-top: 15px;
        margin-bottom: 10px;
        border-top: 1px solid #ddd;
        padding-top: 10px;
        page-break-inside: avoid;
    }

    .conclusion-title {
        font-weight: bold;
        font-size: 11pt;
        margin-bottom: 8px;
        color: #333;
        page-break-after: avoid;
    }

    .conclusion-content {
        font-size: 10pt;
        line-height: 1.4;
        text-align: justify;
        color: #000;
    }

    .conclusion-examen {
        margin-top: 10px;
        margin-bottom: 5px;
        page-break-inside: avoid;
    }

    .conclusion-examen-title {
        font-weight: bold;
        font-size: 10pt;
        margin-bottom: 3px;
        color: #666;
        page-break-after: avoid;
    }

    .conclusion-examen-content {
        font-size: 9.5pt;
        line-height: 1.3;
        text-align: justify;
        margin-left: 10px;
    }

    .conclusion-row td {
        padding: 3px 0;
        font-size: 9pt;
        color: #666;
        font-style: italic;
    }
</style>