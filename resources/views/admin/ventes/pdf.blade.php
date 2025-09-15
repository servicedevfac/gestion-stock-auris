<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des ventes</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DejaVu+Sans:wght@400;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 5px 20px;
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
       .header {
    margin: 0 50px;
}

.entete {
    display: flex !important;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #ccc;
}

.entete p {
    margin: 0;
    font-size: 14px;
    color: #333;
}

    </style>
</head>

<body>

<header class="header" style="margin: 0 20px;">
    <div class="entete" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="first">
            <p>Édité le : {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        <div class="second">
            {{-- <p>Par : {{ auth()->user()->nom }} {{ auth()->user()->prenom }}</p> --}}
        </div>
    </div>
</header>




    <h2 style="margin-top: 50px;">Résultats de la recherche des ventes</h2>
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
    <br>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>
