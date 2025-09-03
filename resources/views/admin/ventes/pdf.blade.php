<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des ventes</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th {
            background-color: #3498db;
            color: #fff;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            border: 1px solid #ddd;
            padding: 6px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .total-row {
            font-weight: bold;
            background-color: #ecf0f1;
        }
        .total-label {
            text-align: right;
            padding-right: 10px;
        }
        .total-value {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <h2>Résultats de la recherche des ventes</h2>
    <table>
        <thead>
            <tr>
                <th>Code Reçu</th>
                <th>Client</th>
                <th>Montant payé</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        @php $sommeTotale = 0; @endphp
        @foreach($ventes as $vente)
            <tr>
                <td>{{ $vente->code_recu }}</td>
                <td>{{ $vente->client->nom ?? '' }} {{ $vente->client->prenom ?? '' }}</td>
                <td>{{ number_format($vente->montant_total, 0, ',', ' ') }} XOF</td>
                <td>{{ $vente->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @php $sommeTotale += $vente->montant_total; @endphp
        @endforeach
        <tr class="total-row">
            <td colspan="2" class="total-label">Total général</td>
            <td colspan="2" class="total-value">{{ number_format($sommeTotale, 0, ',', ' ') }} XOF</td>
        </tr>
        </tbody>
    </table>
</body>
</html>
