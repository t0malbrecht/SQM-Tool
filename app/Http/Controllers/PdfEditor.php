<?php

namespace App\Http\Controllers;

use App\OneTimePayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use mikehaertl\pdftk\Pdf;

class PdfEditor extends Controller
{
    public function index()
    {
        return view('pdftest.upload');
    }

    public function preview(Request $request)
    {
        $filepath = storage_path('app/public/') . 'formularDocuments/AntragAufBudgetumbuchung.pdf';
        //$storageOfPdftk = Storage::disk('partitionC')->get('Program Files (x86)\PDFtk Server\bin\test.pdf');
        $filepath = 'C:\Program Files (x86)\PDFtk Server\bin\test.pdf';

        if (file_exists($filepath)) {

            $headers = [
                'Content-Type' => 'application/pdf'
            ];

            return response()->download($filepath, 'Test File', $headers, 'inline');
        } else {
            abort(404, 'File not found!');
        }
    }

    public function getDataFields(Request $request)
    {
        $filename = $request->input(['filename']);
        $filepath = storage_path('app/public/') . 'formularDocuments/'.$filename.'pdf';
        $pdf = new Pdf($filepath, [
            'command' => 'C:\Program Files (x86)\PDFtk Server\bin\pdftk.exe',
            // or on most Windows systems:
            // 'command' => 'C:\Program Files (x86)\PDFtk\bin\pdftk.exe',
            //'useExec' => true,  // May help on Windows systems if execution fails
        ]);

        $fields = $pdf->getDataFields(true)->__toArray();
        dd($fields);
    }

}
