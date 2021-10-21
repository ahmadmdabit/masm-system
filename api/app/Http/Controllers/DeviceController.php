<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Helpers\ApiResult;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * List all registered devices.
     *
     * @return Response
     */
    public function index()
    {
        return response()->json(new ApiResult(true, Device::all(), ''));
    }

    /**
     * Show a registered device info by its primary key.
     *
     * @param  int $id
     * @return Response
     */
    public function show(int $id)
    {
        try {
            $result = new ApiResult(true, Device::findOrFail($id), '');
        } catch (\Exception $e) {
            $result = new ApiResult(false, null, $e->getMessage());
        }
        return response()->json($result);
    }

    /**
     * Register a device.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $device = new Device();
            $device->device_uid = $request->device_uid;
            $device->app_id = $request->app_id;
            $device->language = $request->language;
            $device->os = $request->os;

            if ($device->save()) {
                $result = new ApiResult(true, null, 'Device registered successfully.');
            }
        } catch (\Exception $e) {
            $result = new ApiResult(false, null, $e->getMessage());
        }
        return response()->json($result);
    }

    /**
     * Update a registered device info by its primary key.
     *
     * @param  Request $request
     * @param  int $id
     * @return Response
     */
    public function update(Request $request, int $id)
    {
        try {
            $device = Device::findOrFail($id);
            $device->device_uid = $request->device_uid;
            $device->app_id = $request->app_id;
            $device->language = $request->language;
            $device->os = $request->os;

            if ($device->save()) {
                $result = new ApiResult(true, null, 'Device registeration updated successfully.');
            }
        } catch (\Exception $e) {
            $result = new ApiResult(false, null, $e->getMessage());
        }
        return response()->json($result);
    }

    /**
     * Unregister a device by its primary key.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(int $id)
    {
        try {
            $device = Device::findOrFail($id);
            if ($device->delete()) {
                $result = new ApiResult(true, null, 'Device unregistered successfully.');
            }
        } catch (\Exception $e) {
            $result = new ApiResult(false, null, $e->getMessage());
        }
        return response()->json($result);
    }
}
