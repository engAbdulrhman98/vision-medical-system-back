<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Display a listing of the devices.
     */
    public function index(Request $request)
    {
        $devices = Device::with(['client', 'product'])->get();

        return response()->json($devices->map(function ($device) {
            return [
                'id' => $device->id,
                'serial_number' => $device->serial_number,
                'status' => $device->status,
                'client_id' => $device->client_id,
                'client_name' => $device->client ? $device->client->getTranslations('name') : null,
                'product_id' => $device->product_id,
                'product_name' => $device->product ? $device->product->getTranslations('name') : null,
            ];
        }));
    }
}
