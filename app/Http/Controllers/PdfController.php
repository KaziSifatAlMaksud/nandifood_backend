<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Warehouse;
use Illuminate\Support\Str;


class PdfController extends Controller
{
  
    public function warehouse_pdf(){
        $warehouses = Warehouse::all();

        $data = ['title' => 'NandiFood Industries',        
            'date' => date('m/d/Y'),
            'result' => $warehouses
            ]; 
        $slugDate = Str::slug(date('Y-m-d')); 
        $fileName = "{$slugDate}_warehouse_List.pdf";

        $pdf = Pdf::loadView('pdf.warehouse_pdf', $data);
        return $pdf->download($fileName);
        
    }
}
