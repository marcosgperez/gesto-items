<?php

namespace App\Http\Controllers;

use App\Models\Histories;
use App\Models\Sectors;
use App\Models\Items;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class ItemsController extends Controller
{

    private function _generateAndUploadQr($itemId)
    {
        $frontUrl = "http://localhost:8000/item?item_id=$itemId";
        $fileName = "qr-code-$itemId.png"; // Nombre del archivo en S3
        $qrCode = QrCode::size(200)->format('png')->generate($frontUrl);
        Storage::disk('s3')->put($fileName, $qrCode);
        $url = 'https://gesto-items.s3.amazonaws.com/' . $fileName;
        return $url;
    }
    
    public function index()
    {
        try {
            $items = Items::all();
        } catch (\Exception $error) {
            return $this->resultError($error->getMessage());
        }
        return $this->resultOk($items);
    }

    public function store(Request $request)
    {
        $validated = $this->customValidate($request, [
            'name' => 'required|string',
            'brand' => 'required|string',
            'code' => 'required|string',
            'serial' => 'required|string',
            'model' => 'required|string',
            'chasis' => 'required|string',
            'description' => 'required|string',
            'sector_id' => 'required|integer',
            'manual' => '',
            'photos' => '',
        ]);
    
        $sector = Sectors::find($validated['sector_id']);
        if (empty($sector)) {
            return $this->resultError('Sector not found');
        }
    
        $item = new Items();
        $item->name = $validated['name'];
        $item->photos = $validated['photos'] ?? '';
        $item->brand = $validated['brand'];
        $item->code = $validated['code'];
        $item->serial = $validated['serial'];
        $item->model = $validated['model'];
        $item->chasis = $validated['chasis'];
        $item->description = $validated['description'];
        $item->manual = $validated['manual'] ?? '';
        $item->sector_id = $sector->id;
        $item->floor_id = $sector->floor_id;
        try {
            $item->save();
            $qr = $this->_generateAndUploadQr($item->id);
            $item->qr = $qr;
            $item->save();
            $history = new Histories();
            $history->item_id = $item->id;
            $history->save();
            return $this->resultOk($item);
        } catch (\Exception $error) {
            // En caso de error, devolver un error
            return $this->resultError($error->getMessage());
        }
    }
    
    
}