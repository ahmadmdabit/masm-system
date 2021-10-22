<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Device;
use App\Helpers\ApiResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        } catch (\Throwable $e) {
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
            $validator = Validator::make($request->all(), [
                // Device rules
                'device_uid' => 'required|uuid',
                'app_id' => 'required|uuid',
                'language' => 'required|string|size:2',
                'os' => 'required|digits_between:0,1',

                // User rules
                'name' => 'required|string|between:3,255',
                'username' => 'required|email',
                'password' => 'required|string|between:8,16',
            ]);

            if ($validator->fails()) {
                $result = new ApiResult(false, null, 'Validation errors.', $validator->errors());
            }
            else {
                $user = User::where('email', '=', $request->username)->first();
                if (is_null($user)) {
                    $user = new User();
                    $user->name = $request->name;
                    $user->email = $request->username;
                    $user->password = Hash::make($request->password);
                    $user->save();
                }

                if (isset($user) && $user->id > 0) {
                    $device = new Device();
                    $device->device_uid = $request->device_uid;
                    $device->app_id = $request->app_id;
                    $device->language = $request->language;
                    $device->os = $request->os;
                    $device->user_id = $user->id;

                    if ($device->save()) {
                        $result = new ApiResult(true, [
                            "client_id"=> config('service.passport.password_'.($device->os == 0 ? 'ios' : 'google').'.client_id'), // $device->os: 0: ios, 1: google
                            "client_secret"=> config('service.passport.password_'.($device->os == 0 ? 'ios' : 'google').'.client_secret'),
                            "grant_type"=> config('service.passport.password_'.($device->os == 0 ? 'ios' : 'google').'.grant_type'),
                        ], 'Device registered successfully.');
                    }
                    else {
                        $result = new ApiResult(false, null, 'Something went wrong while registerring the device.');
                    }
                }
                else {
                    $result = new ApiResult(false, null, 'Something went wrong while registerring the user.');
                }
            }
        } catch (\Throwable $e) {
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
            $validator = Validator::make($request->all(), [
                // Device rules
                'uid' => 'required|uuid',
                'app_id' => 'required|uuid',
                'language' => 'required|string|size:2',
                'os' => 'required|digits_between:0,1',
            ]);

            if ($validator->fails()) {
                $result = new ApiResult(false, null, 'Validation errors.', $validator->errors());
            }
            else {
                $device = Device::findOrFail($id);
                $device->device_uid = $request->device_uid;
                $device->app_id = $request->app_id;
                $device->language = $request->language;
                $device->os = $request->os;

                if ($device->save()) {
                    $result = new ApiResult(true, null, 'Device registeration updated successfully.');
                }
                else {
                    $result = new ApiResult(false, null, 'Something went wrong while updating the device registeration.');
                }
            }
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
            $result = new ApiResult(false, null, $e->getMessage());
        }
        return response()->json($result);
    }
}
