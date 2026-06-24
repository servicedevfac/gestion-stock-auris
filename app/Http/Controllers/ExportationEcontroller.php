<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Vente;
use App\Models\Produit;
use Illuminate\Http\Request;

use Spatie\SimpleExcel\SimpleExcelWriter;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
class ExportationEcontroller extends Controller
{

    public function exportation(Request $request): StreamedResponse
    {

        $filename = "Liste des ventes.xlsx";
        // 1. Récupération des données
        $ventes = Vente::with('client')->where('statut', 'valide')->select('client_id','created_at','code_recu', 'mode_paiement',  'montant_paye','reste_a_payer','montant_total',)->get();

        // 2. Création du spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 3. Définir les en-têtes
        $headers = ['Date', 'Code Reçu','Nom client', 'Mode de Paiement', 'Montant payé', 'Reste à payer', 'Montant Total'];
        $sheet->fromArray($headers, null, 'A1');


        // 4. Appliquer le style aux en-têtes
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '007ACC']],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        // 5. Remplir les données
        $rowIndex = 2;
        foreach ($ventes as $vente) {
    $sheet->setCellValue("A{$rowIndex}", \Carbon\Carbon::parse($vente->created_at)->format('d/m/Y'));
    $sheet->setCellValue("B{$rowIndex}", $vente->code_recu);
    $sheet->setCellValue("D{$rowIndex}", $vente->mode_paiement);
    $sheet->setCellValue("C{$rowIndex}", $vente->client?->nom ?? ''); // <- Nom du client
    $sheet->setCellValue("E{$rowIndex}", $vente->montant_paye);
    $sheet->setCellValue("F{$rowIndex}", $vente->reste_a_payer);
    $sheet->setCellValue("G{$rowIndex}", $vente->montant_total);
    $rowIndex++;
}

        // 6. Ligne Total
        $sheet->setCellValue("E{$rowIndex}", 'TOTAL');
        $sheet->setCellValue("G{$rowIndex}", $ventes->sum('montant_total'));

        // Style total
        $sheet->getStyle("E{$rowIndex}:F{$rowIndex}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FCE4D6']],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ]);

        // 7. Appliquer les bordures à toutes les lignes
        $sheet->getStyle("A1:G1{$rowIndex}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // 8. Auto-size des colonnes
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 9. Retourner le fichier en téléchargement
        $writer = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    public function exportMouvementStock(Request $request): StreamedResponse
    {
        $filename = "Mouvement de stock.xlsx";

        // Get stock movements data
        $mouvements = MouvementStock::select(
            'produits.nom as produit_nom',
            'mouvement_stocks.type_mouvement',
            'mouvement_stocks.quantite',
            'mouvement_stocks.date_mouvement',
            'mouvement_stocks.motif'
        )->orderBy('date_mouvement', 'desc')
            ->join('produits', 'produits.id', '=', 'mouvement_stocks.produit_id')
            ->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['Produit', 'Type de Mouvement', 'Quantité', 'Date', 'Motif'];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '02228b']],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Fill data
        $rowIndex = 2;
        foreach ($mouvements as $mouvement) {
            $sheet->setCellValue("A{$rowIndex}", $mouvement->produit_nom);
            $sheet->setCellValue("B{$rowIndex}", $mouvement->type_mouvement);
            $sheet->setCellValue("C{$rowIndex}", $mouvement->quantite);
            $sheet->setCellValue("D{$rowIndex}", $mouvement->date_mouvement);
            $sheet->setCellValue("E{$rowIndex}", $mouvement->motif);
            $rowIndex++;
        }

        // Apply borders to all cells
        $sheet->getStyle("A1:E{$rowIndex}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Return file download
        $writer = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }




    public function exportProducts(string $format = 'excel'): StreamedResponse
    {
        $products = Produit::with('mouvements')->get();
        $headers  = ['Nom', 'Prix', 'Stock actuel', 'Seuil alerte'];

        if ($format === 'excel') {
            return $this->generateProductExcel($products, $headers, 'produits.xlsx');
        }

        if ($format === 'pdf') {
            return $this->generateProductPDF($products, $headers, 'produits.pdf');
        }

        abort(404, 'Format non supporté.');
    }

    /**
     * Génération Excel
     */
    private function generateProductExcel($products, array $headers, string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // En-têtes
        $sheet->fromArray($headers, null, 'A1');

        // Style des en-têtes
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '02228b']],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Remplissage des données
        $rowIndex = 2;
        foreach ($products as $product) {
            $sheet->setCellValue("A{$rowIndex}", $product->nom);
            $sheet->setCellValue("B{$rowIndex}", $product->prix);
            $sheet->setCellValue("C{$rowIndex}", $product->stock_actuel);
            $sheet->setCellValue("D{$rowIndex}", $product->seuil_alerte);

            // Surbrillance si stock bas
            if ($product->stock_actuel <= $product->seuil_alerte) {
                $sheet->getStyle("C{$rowIndex}:D{$rowIndex}")->applyFromArray([
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFB6C1']]
                ]);
            }

            $rowIndex++;
        }

        // Bordures + auto-size
        $sheet->getStyle("A1:D{$rowIndex}")
            ->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    /**
     * Génération PDF
     */
    private function generateProductPDF($products, array $headers, string $filename): StreamedResponse
    {
        $pdf = app()->make('dompdf.wrapper');


        $html = '<style>
            table { width: 100%; border-collapse: collapse; }
            th { background-color: #02228b; color: white; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            .low-stock { background-color: #FFB6C1;
            p{
                font-size: 12px;
                margin-bottom:50px;
            }

        }
        </style>';
        $html .='<p style="margin-bottom: 125px;">Édité le : ' . now()->format(format: 'd/m/Y H:i') . ' </p>';


        $html .= '<h2>Liste des Produits</h2>';
        $html .= '<table><tr>';

        foreach ($headers as $header) {
            $html .= "<th>{$header}</th>";
        }
        $html .= '</tr>';

        foreach ($products as $product) {
            $lowStockClass = $product->stock_actuel <= $product->seuil_alerte ? 'class="low-stock"' : '';

            $html .= '<tr>';
            $html .= "<td>{$product->nom}</td>";
            $html .= "<td>{$product->prix}</td>";
            $html .= "<td {$lowStockClass}>{$product->stock_actuel}</td>";
            $html .= "<td>{$product->seuil_alerte}</td>";
            $html .= '</tr>';
        }

        $html .= '</table>';

        $pdf->loadHTML($html);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }
    public function exportClients(Request $request): StreamedResponse
    {
        $filename = "Liste des clients.xlsx";

        // Get clients data
        $clients = \App\Models\Client::select(
            'nom',
            'prenom',
            'telephone',
            'email',
            'adresse'
        )->orderBy('nom')->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['Nom', 'Prénom', 'Téléphone', 'Email', 'Adresse'];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '02228b']],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Fill data
        $rowIndex = 2;
        foreach ($clients as $client) {
            $sheet->setCellValue("A{$rowIndex}", $client->nom);
            $sheet->setCellValue("B{$rowIndex}", $client->prenom);
            $sheet->setCellValue("C{$rowIndex}", $client->telephone);
            $sheet->setCellValue("D{$rowIndex}", $client->email);
            $sheet->setCellValue("E{$rowIndex}", $client->adresse);
            $rowIndex++;
        }

        // Apply borders to all cells
        $sheet->getStyle("A1:E{$rowIndex}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Auto-size columns
        // foreach (range('A', 'E') as $col) {
        //     $sheet->getColumnDimension($col)->setAutoSize(true);
        // }
        
        $lastColumn = $sheet->getHighestColumn();
        $sheet->getDefaultRowDimension()->setRowHeight(-1); // auto hauteur
        foreach (range('A', $lastColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true); // auto largeur
        }

        // Return file download
        $writer = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }
}

// routes/web.php


