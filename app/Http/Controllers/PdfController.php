<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Warehouse;
use App\Models\Uom;
use Illuminate\Support\Str;


class PdfController extends Controller
{
  
    public function warehouse_pdf(){
        $warehouses = Warehouse::all();
        $imagePath = public_path('storage/company-logo.png'); 
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageSrc = 'data:image/png;base64,' . $imageData;

        $data = [
            'title' => 'NandiFood Industries',
            'date' => date('m/d/Y'),
            'result' => $warehouses,
            'imageSrc' => $imageSrc 
        ];
    
        $slugDate = Str::slug(date('Y-m-d'));
        $fileName = "{$slugDate}_warehouse_List.pdf";
        $pdf = Pdf::loadView('pdf.warehouse_pdf', $data);
        return $pdf->download($fileName);
    }
    


    
    public function uom_list_pdf()
    {
        $uom = Uom::all(); // Removed the extra ->get() as all() already retrieves the data.
        $imagePath = public_path('storage/company-logo.png'); 
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageSrc = 'data:image/png;base64,' . $imageData;
    
        $data = [
            'title' => 'Unit of Measure (UOM) List',        
            'date' => date('m/d/Y'),
            'imageSrc' => $imageSrc ,
            'result' => $uom,
        ]; 
    
        $pdf = Pdf::loadView('pdf.uom_list', $data);
    
        // Use stream to view the PDF in the browser
        return $pdf->stream('uom_list.pdf');
    }
    
}

