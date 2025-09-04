<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats d'Analyses Médicales</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9.5px;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        
        .container {
            width: 100%;
            max-width: 21cm;
            margin: 0 auto;
            padding: 0.5cm;
        }
        
        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            border-bottom: 1px solid #cc0000;
            padding-bottom: 5px;
        }
        
        .lab-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .lab-info {
            font-size: 8px;
            color: #333;
        }
        
        .lab-contact {
            font-size: 8px;
            text-align: right;
        }
        
        .lab-id {
            font-size: 8px;
            margin-top: 5px;
        }
        
        /* Patient Information */
        .patient-info {
            margin: 10px 0;
            padding: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        
        .patient-name {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .patient-details {
            font-size: 9px;
            display: flex;
            justify-content: space-between;
        }
        
        /* Section Headers */
        .section-header {
            background-color: #0066cc;
            color: white;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 12px;
        }
        
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        
        th {
            background-color: #f0f0f0;
            padding: 3px 5px;
            font-size: 8px;
            font-weight: bold;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        td {
            padding: 2px 5px;
            font-size: 9px;
            border: 1px solid #ddd;
        }
        
        .param-name {
            width: 35%;
            font-weight: bold;
        }
        
        .param-value {
            width: 15%;
            text-align: center;
        }
        
        .param-ref {
            width: 25%;
            text-align: center;
            font-size: 8px;
        }
        
        .param-previous {
            width: 25%;
            text-align: center;
            font-size: 8px;
        }
        
        .sub-param {
            padding-left: 15px;
        }
        
        .method-info {
            font-size: 8px;
            font-style: italic;
            color: #666;
            margin: 3px 0;
        }
        
        .pathological {
            font-weight: bold;
        }
        
        /* Footer */
        .footer {
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            text-align: center;
            color: #666;
        }
        
        .footer-note {
            font-style: italic;
            margin-top: 3px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .controlled-note {
            text-align: center;
            font-style: italic;
            font-size: 8px;
            color: #666;
            margin: 5px 0;
        }
        
        .risk-table {
            width: 100%;
            margin: 10px 0;
            font-size: 8px;
        }
        
        .risk-table th {
            background-color: #e0e0e0;
        }
        
        .interpretation-section {
            margin: 10px 0;
            padding: 5px;
            font-size: 8px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div>
                <div class="lab-name">Laboratoires d'Analyses Médicales</div>
                <div class="lab-info">Ouvert du lundi au Vendredi de 7h à 11h30 - 14h30 à 17h30</div>
                <div class="lab-info">Immeuble - ARPU PENAIR LIFE -</div>
                <div class="lab-info">Résidence de l'entre-New Pie</div>
            </div>
            <div class="lab-contact">
                <div>Tél: +18 0003 34 6666</div>
                <div class="lab-id">
                    <div>NIF: 2002228206</div>
                    <div>RC: 2016800013</div>
                    <div>STAT: 86903 41 2016 0 00193 du 14.03.2016...</div>
                </div>
            </div>
        </div>
        
        <!-- Patient Information -->
        <div class="patient-info">
            <div class="patient-name">Résultats de: Monsieur Toly Dilip ISSOUFALY</div>
            <div class="patient-details">
                <div>Né(e) le: 24/04/1969</div>
                <div>Dossier n°: 584886 du 15/04/2024</div>
                <div>Prescription du: 15/04/2024</div>
            </div>
        </div>
        
        <!-- HEMATOLOGIE Section -->
        <div class="section-header">HEMATOLOGIE</div>
        
        <!-- HEMOGRAMME Subsection -->
        <div class="method-info">HEMOGRAMME (Humacount 5D HUMAN Germany)</div>
        
        <table>
            <thead>
                <tr>
                    <th class="param-name">Paramètre</th>
                    <th class="param-value">Valeur</th>
                    <th class="param-ref">Valeurs de référence</th>
                    <th class="param-previous">Antérieur (14/06/2023)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="param-name">HEMATIES</td>
                    <td class="param-value">4.93 T/L</td>
                    <td class="param-ref">4.00 à 5.45</td>
                    <td class="param-previous">4.76 T/L</td>
                </tr>
                <tr>
                    <td class="param-name">Hémoglobine</td>
                    <td class="param-value">15.4 g/dl</td>
                    <td class="param-ref">12.0 à 18.0</td>
                    <td class="param-previous">15.1 g/dl</td>
                </tr>
                <tr>
                    <td class="param-name">Hématocrite</td>
                    <td class="param-value">46 %</td>
                    <td class="param-ref">40 à 55</td>
                    <td class="param-previous">43 %</td>
                </tr>
                <tr>
                    <td class="param-name">VGM</td>
                    <td class="param-value">93 μ3</td>
                    <td class="param-ref">80 à 100</td>
                    <td class="param-previous">90 μ3</td>
                </tr>
                <tr>
                    <td class="param-name">TCMH</td>
                    <td class="param-value">31 pg</td>
                    <td class="param-ref">27 à 32</td>
                    <td class="param-previous">32 pg</td>
                </tr>
                <tr>
                    <td class="param-name">CCMH</td>
                    <td class="param-value">33 %</td>
                    <td class="param-ref">32 à 36</td>
                    <td class="param-previous">35 %</td>
                </tr>
            </tbody>
        </table>
        
        <!-- LEUCOCYTES Section -->
        <table>
            <tr>
                <td class="param-name">LEUCOCYTES</td>
                <td class="param-value">7.1 G/L</td>
                <td class="param-ref">4.0 à 10.0</td>
                <td class="param-previous">7.90 G/L</td>
            </tr>
            <tr>
                <td class="param-name sub-param">Polynucléaires neutrophiles</td>
                <td class="param-value">60 %</td>
                <td class="param-ref"></td>
                <td class="param-previous">56 %</td>
            </tr>
            <tr>
                <td class="param-name sub-param">Polynucléaires éosinophiles</td>
                <td class="param-value">1 %</td>
                <td class="param-ref"></td>
                <td class="param-previous">1 %</td>
            </tr>
            <tr>
                <td class="param-name sub-param">Polynucléaires basophiles</td>
                <td class="param-value">0 %</td>
                <td class="param-ref"></td>
                <td class="param-previous">0 %</td>
            </tr>
            <tr>
                <td class="param-name sub-param">Lymphocytes</td>
                <td class="param-value">35 %</td>
                <td class="param-ref"></td>
                <td class="param-previous">40 %</td>
            </tr>
            <tr>
                <td class="param-name sub-param">Monocytes</td>
                <td class="param-value">4 %</td>
                <td class="param-ref"></td>
                <td class="param-previous">3 %</td>
            </tr>
        </table>
        
        <div class="method-info">Soit</div>
        
        <table>
            <tr>
                <td class="param-name sub-param">Polynucléaires neutrophiles</td>
                <td class="param-value">4.26 G/L</td>
                <td class="param-ref">2.20 à 6.50</td>
                <td class="param-previous">4.42 G/L</td>
            </tr>
            <tr>
                <td class="param-name sub-param">Polynucléaires éosinophiles</td>
                <td class="param-value">0.07 G/L</td>
                <td class="param-ref">0.04 à 0.40</td>
                <td class="param-previous">0.08 G/L</td>
            </tr>
            <tr>
                <td class="param-name sub-param">Polynucléaires basophiles</td>
                <td class="param-value">0.0 G/L</td>
                <td class="param-ref">0.0 à 0.1</td>
                <td class="param-previous">0.0 G/L</td>
            </tr>
            <tr>
                <td class="param-name sub-param">Lymphocytes</td>
                <td class="param-value">2.49 G/L</td>
                <td class="param-ref">0.80 à 4.10</td>
                <td class="param-previous">3.16 G/L</td>
            </tr>
            <tr>
                <td class="param-name sub-param">Monocytes</td>
                <td class="param-value">0.28 G/L</td>
                <td class="param-ref">0.12 à 1.10</td>
                <td class="param-previous">0.24 G/L</td>
            </tr>
        </table>
        
        <!-- PLAQUETTES -->
        <table>
            <tr>
                <td class="param-name">PLAQUETTES</td>
                <td class="param-value pathological">135 G/L</td>
                <td class="param-ref">150 à 450</td>
                <td class="param-previous">207 G/L</td>
            </tr>
        </table>
        
        <!-- VITESSE DE SEDIMENTATION -->
        <div class="controlled-note">Résultat(s) contrôlé(s) et vérifié(s)</div>
        <table>
            <tr>
                <td class="param-name">VITESSE DE SEDIMENTATION (Première heure)</td>
                <td class="param-value pathological">27 mm</td>
                <td class="param-ref">4 à 12</td>
                <td class="param-previous">10 mm</td>
            </tr>
        </table>
        
        <!-- BIOCHIMIE SANG Section -->
        <div class="section-header">BIOCHIMIE SANG</div>
        
        <!-- GLYCEMIE -->
        <div class="method-info">GLYCEMIE à jeun (GOD-PAP-BIOLABO/Konelab)</div>
        <table>
            <tr>
                <td class="param-name">GLYCEMIE à jeun</td>
                <td class="param-value pathological">1.18 g/l (6.55 mmol/l)</td>
                <td class="param-ref">0.72 à 1.10</td>
                <td class="param-previous">1.48 g/l</td>
            </tr>
        </table>
        
        <div class="method-info">
            Nouveau-né (1j): 0.31 à 0.41 g/l<br>
            Nouveau-né > 1j: 0.50 - 0.80 g/l<br>
            Adulte à jeun: 0.72 à 1.10 g/l
        </div>
        
        <!-- Page Break for Next Page -->
        <div class="page-break"></div>
        
        <!-- Header for Second Page -->
        <div class="header">
            <div>
                <div class="lab-name">Laboratoires d'Analyses Médicales</div>
                <div class="lab-info">Ouvert du lundi au Vendredi de 7h à 11h30 - 14h30 à 17h30</div>
            </div>
            <div class="lab-contact">
                <div>Tél: +18 0003 34 6666</div>
            </div>
        </div>
        
        <div class="patient-info">
            <div class="patient-name">Résultats de: Monsieur Toly Dilip ISSOUFALY</div>
            <div class="patient-details">
                <div>Dossier n°: 584886 du 15/04/2024</div>
            </div>
        </div>
        
        <!-- HEMOGLOBINE GLYQUEE -->
        <div class="method-info">HÉMOGLOBINE GLYQUÉE (Dosage Turbidimétrique / HbA1C - TURBI BioSystems / Konélab)</div>
        <div class="method-info">Valeur de référence selon les normes NGSP (National Glycohemoglobin Standardization Program)</div>
        <div class="method-info">Valeur de référence : 4.0 à 6.0 %</div>
        
        <table>
            <tr>
                <td class="param-name">HBA1c</td>
                <td class="param-value pathological">6.20 %</td>
                <td class="param-ref">4.00 à 6.00</td>
                <td class="param-previous">8.00 %</td>
            </tr>
        </table>
        
        <!-- CREATININE -->
        <div class="method-info">CREATININE (Photometric Colorimetric / BIOLABO/Konélab)</div>
        <table>
            <tr>
                <td class="param-name">CREATININE</td>
                <td class="param-value">81 μmol/l</td>
                <td class="param-ref">53 à 115</td>
                <td class="param-previous">79 μmol/l</td>
            </tr>
        </table>
        
        <!-- CHOLESTEROL TOTAL -->
        <div class="method-info">CHOLESTEROL TOTAL (Titrée-Biolabo/Konélab)</div>
        <table>
            <tr>
                <td class="param-name">CHOLESTEROL TOTAL</td>
                <td class="param-value pathological">2.05 g/l</td>
                <td class="param-ref">0.00 à 2.00</td>
                <td class="param-previous">1.22 g/l</td>
            </tr>
        </table>
        
        <div class="method-info">
            Valeur recommandée : < 2 g/l<br>
            Risque modéré : 2.00 à 2.39 g/l<br>
            Risque élevé : ≥ 2.4 g/l
        </div>
        
        <!-- TRIGLYCERIDES -->
        <div class="method-info">TRIGLYCERIDES (GPO-BIOLABO/Konélab)</div>
        <table>
            <tr>
                <td class="param-name">TRIGLYCERIDES</td>
                <td class="param-value pathological">2.43 g/l</td>
                <td class="param-ref">0.35 à 1.60</td>
                <td class="param-previous">2.02 g/l</td>
            </tr>
        </table>
        
        <!-- CHOLESTEROL HDL -->
        <div class="method-info">CHOLESTEROL HDL (Méthode directe-BIOLABO/Konélab)</div>
        <table>
            <tr>
                <td class="param-name">CHOLESTEROL HDL</td>
                <td class="param-value">0.46 g/l</td>
                <td class="param-ref">0.35 à 0.62</td>
                <td class="param-previous">0.36 g/l</td>
            </tr>
        </table>
        
        <div class="method-info">
            Taux faible : < 0.40 g/l<br>
            Taux élevé : ≥ 0.60 g/l
        </div>
        
        <!-- RAPPORT CHOLESTEROL -->
        <table>
            <tr>
                <td class="param-name">Rapport cholestérol total / cholestérol HDL</td>
                <td class="param-value pathological">4.5</td>
                <td class="param-ref">2.0 à 5.0</td>
                <td class="param-previous">3.4</td>
            </tr>
        </table>
        
        <!-- CHOLESTEROL LDL -->
        <div class="method-info">CHOLESTEROL LDL (Formule de Friedewald / BIOLABO/Konélab)</div>
        <table>
            <tr>
                <td class="param-name">CHOLESTEROL LDL</td>
                <td class="param-value pathological">1.10 g/l</td>
                <td class="param-ref">1.35 à 1.75</td>
                <td class="param-previous">0.46 g/l</td>
            </tr>
        </table>
        
        <div class="interpretation-section">
            <p>Aspect du plasma : Opalescent ++.</p>
            <p>Résultats contrôlés et vérifiés.</p>
        </div>
        
        <div class="method-info">
            Le calcul de LDL, par la formule de FRIEDEWALD est valable à condition que le taux de triglycérides soit inférieur à 4 g/l soit 4.60 mmol/l :
        </div>
        
        <div class="method-info">Taux de LDL en fonction du nombre de facteurs de risque cardio-vasculaire:</div>
        
        <table class="risk-table">
            <tr>
                <th>FACTEURS DE RISQUE (FDR) :</th>
                <th>Taux de LDL :</th>
            </tr>
            <tr>
                <td>Absent</td>
                <td>LDL < 2.20 g/l</td>
            </tr>
            <tr>
                <td>Avec 1 FDR</td>
                <td>LDL < 1.90 g/l</td>
            </tr>
            <tr>
                <td>Avec 2 FDR</td>
                <td>LDL < 1.60 g/l</td>
            </tr>
            <tr>
                <td>Avec 3 FDR</td>
                <td>LDL < 1.30 g/l</td>
            </tr>
            <tr>
                <td>Patient à haut risque</td>
                <td>LDL < 1 g/l</td>
            </tr>
        </table>
        
        <div class="method-info">
            FACTEURS DE RISQUE (FDR):<br>
            Age : Homme > 50 ans, Femme > 60 ans<br>
            ATCD Familiaux de maladie coronaire précoce<br>
            ATCD Familiaux d'artériopathie ou infarctus de myocarde ou mort subite<br>
            Tabagisme actuel ou arrêté depuis moins de 3 mois<br>
            HTA (traité ou non)<br>
            Diabète type 2 (traité ou non)<br><br>
            
            NB: Une concentration de HDL > 0,60 g/l signifie un facteur protecteur.<br>
            Réf: Agence Française de sécurité Sanitaire des produits de santé.2005
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div>Toutes les valeurs de référence sont données en fonction de l'âge et du sexe.</div>
            <div class="footer-note">En collaboration technique avec CTB MADAGASCAR</div>
        </div>
    </div>
</body>
</html>