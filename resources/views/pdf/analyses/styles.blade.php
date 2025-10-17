{{-- resources/views/pdf/analyses/styles.blade.php --}}
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    @page {
        size: A4;
        margin: 1cm 2cm 1.5cm 2cm;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 11pt;
        color: black;
        line-height: 1.1;
    }

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
        margin: 0;
        padding: 0;
    }

    .content-wrapper {
        padding: 0 35px;
    }

    /* ✅ Mini-séparateur discret entre examens */
    .examen-wrapper {
        margin-bottom: 25px;
    }

    .examen-wrapper::before {
        content: "";
        display: block;
        height: 5px; 
        page-break-before: auto;
    }


    /* ✅ Mini-séparateur discret entre examens */
    .mini-separator {
        page-break-inside: avoid;
        page-break-after: avoid;
        margin: 10px 0;
    }


    /* Information patient */
    .patient-info {
        margin: 15px 0;
        width: 100%;
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
        display: table;
        table-layout: fixed;
    }

    .patient-info-row { display: table-row; }
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

    .info-label { color: #374151; font-size: 9pt; }
    .info-value { color: #111827; font-size: 9pt; }
    .text-fine { font-weight: normal; font-size: 9pt; }
    .patient-name { font-weight: bold; }
    .medecin-name { font-weight: bold; }
    .bold { font-weight: bold; }

    /* Tables */
    .main-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
        padding: 0;
    }

    .main-table td {
        padding: 1px 0;
        line-height: 1.15;
        vertical-align: middle;
    }

    .red-line {
        border-top: 0.5px solid #0b48eeff;
        margin: 1px 0;
        width: 100%;
    }

    /* Colonnes */
    .col-designation { width: 40%; text-align: left; padding-right: 10px; font-size: 10.5pt; }
    .col-resultat { width: 20%; text-align: left; padding-left: 20px; font-size: 9pt; }
    .col-valref { width: 20%; text-align: left; padding-left: 20px; font-size: 8pt; }
    .col-anteriorite { width: 20%; padding-left: 10px; text-align: left; font-size: 8pt; }

    /* Titres */
    .section-title {
        color: #042379ff;
        font-weight: bold;
        text-transform: uppercase;
        page-break-after: avoid;
    }

    .header-cols {
        font-size: 8pt;
        color: #000;
        font-style: italic;
    }

    /* Hiérarchie */
    .parent-row { font-weight: bold; }
    .child-row td:first-child { padding-left: 20px; }
    .subchild-row td:first-child { padding-left: 40px; }

    /* Antibiogrammes */
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

    .antibiotique-sensible { color: #28a745; }
    .antibiotique-resistant { color: #0542ebff; font-weight: bold; }
    .antibiotique-intermediaire { color: #ffc107; font-style: italic; }

    .indent-1 { padding-left: 20px !important; }
    .indent-2 { padding-left: 40px !important; }
    .signature { margin-top: 20px; text-align: right; padding-right: 10px; }
    .spacing { height: 3px; }
    .pathologique { font-weight: bold; color: #000; }

    /* Conclusions */
    .conclusion-section {
        margin-top: 12px;
        margin-bottom: 8px;
        border-top: 1px solid #ddd;
        padding-top: 8px;
    }

    .conclusion-title {
        font-weight: bold;
        font-size: 11pt;
        margin-bottom: 6px;
        color: #333;
    }

    .conclusion-content {
        font-size: 10pt;
        line-height: 1.4;
        text-align: justify;
        color: #000;
    }

    .conclusion-examen {
        margin-top: 8px;
        margin-bottom: 5px;
    }

    .conclusion-examen-title {
        font-weight: bold;
        font-size: 10pt;
        margin-bottom: 3px;
        color: #666;
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
