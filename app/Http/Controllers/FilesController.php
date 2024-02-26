<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function getPresignedUrl(Request $request)
    {
        $itemId = $request->input('id');
        $fileName = "manuals/manual-$itemId.pdf";
        try {
            $expiry = now()->addMinutes(10);
            $url = Storage::disk('s3')->temporaryUrl($fileName, $expiry);
            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}