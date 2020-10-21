<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VentaController extends Controller
{
    public function downloadVoucher(Request $request, $id)
    {
    	if (! $request->hasValidSignature()) {
	        return('zzz');
	    }

	    $venta = Venta::pagadas()->findOrFail($id);

	    $pdfs = $venta->generatePdfs();

        foreach ($pdfs as $key => $elem) {

        	// Save PDF's on disk
			Storage::disk('pdfs')->put($venta->id . '/' . $elem['pdf_filename'], $elem['pdf']->output());
        }

        $zip = new \ZipArchive();

        // Create zip File
        $fileName = 'voucher' . $venta->id . '.zip';

        if ($zip->open(storage_path($fileName), \ZipArchive::CREATE) === TRUE)
        {
        	// Add each PDF to ZIP file
            $files = \File::files(Storage::disk('pdfs')->path($venta->id . '/' ));
            foreach ($files as $key => $value) {

                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }

            $zip->close();
        }

        // Delete innecesary pdf files
        Storage::disk('pdfs')->deleteDirectory($venta->id);

        return response()->download(storage_path($fileName));
    }
}
