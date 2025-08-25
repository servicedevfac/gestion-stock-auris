<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de vente</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            max-width: 80mm; /* Largeur standard imprimante thermique */
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }

        .header, .footer {
            text-align: center;
        }

        h2, h3 {
            margin: 5px 0;
        }

        .details, .items, .totals {
            width: 100%;
            margin-top: 10px;
        }

        .items table, .totals table {
            width: 100%;
            border-collapse: collapse;
        }

        .items th, .items td, .totals th, .totals td {
            text-align: left;
            padding: 3px 0;
        }

        .items th {
            border-bottom: 1px dashed #000;
        }

        .items td {
            border-bottom: 1px dotted #000;
        }

        .totals td {
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('assets/images/logo-light.png') }}"
     alt="Logo"
     style="width: 100px; height: auto; display: block; margin: 0 auto;">

        <p>Adresse | Téléphone</p>
        <hr>
    </div>

    <div class="details">
        <p>Client: {{ $vente->client->nom ?? '' }}</p>
        <p>Date: {{ $vente->created_at->format('d/m/Y H:i') }}</p>
        <p>Reçu: {{ $vente->code_recu }}</p>
    </div>

    <div class="items">
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="center">Qté</th>
                    <th class="center">Prix</th>
                    <th class="center">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vente->details as $detail)
                <tr>
                    <td>{{ $detail->produit->nom }}</td>
                    <td class="center">{{ $detail->quantite }}</td>
                    <td class="center">{{ number_format($detail->prix,0,',',' ') }}</td>
                    <td class="center">{{ number_format($detail->total,0,',',' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="totals">
        <table>
            <tr>
                <td>Montant total:</td>
                <td class="center"> {{ number_format($vente->details->sum('total'), 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>Remise:</td>
                <td class="center">{{ number_format($vente->remise ?? 0,0,',',' ') }} FCFA</td>
            </tr>
            <tr>
                <td>À payer:</td>
                <td class="center">{{ number_format($vente->details->sum('total') - ($vente->remise ?? 0),0,',',' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <hr>
        <p>Merci pour votre achat !</p>
    </div>

    <script>
        // Ouvre directement la fenêtre d'impression pour tester
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
