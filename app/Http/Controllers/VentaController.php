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

        $fileName = 'voucher' . $venta->id . '.zip';

        return response()->download(storage_path($fileName), null, ['Content-Type' => 'application/octet-stream']);
    }
}
