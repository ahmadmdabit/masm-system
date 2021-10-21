<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Device;
use GuzzleHttp\Client;
use App\Models\Purchase;
use App\Helpers\ApiResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    /**
     * List all purchases.
     *
     * @return Response
     */
    public function index()
    {
        return response()->json(new ApiResult(true, Purchase::all(), ''));
    }

    /**
     * Show a purchase info by its primary key (optional)
     * (Note: If the primary key not supplied will show the info  based on user id).
     *
     * @param  int $id
     * @return Response
     */
    public function show(int $id = null)
    {
        try {
            if (is_null($id)) {
                $result = new ApiResult(true, Purchase::where('user_id', auth()->user()->id)->where('status', 1)->first(), '');
            }
            else {
                $result = new ApiResult(true, Purchase::findOrFail($id), '');
            }
        } catch (\Throwable $e) {
            $result = new ApiResult(false, null, $e->getMessage());
        }
        return response()->json($result);
    }

    /**
     * Create a purchase.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // Purchase rules
                'receipt' => 'required|uuid',
            ]);

            if ($validator->fails()) {
                $result = new ApiResult(false, null, 'Validation errors.', $validator->errors());
            }
            else {
                $user = auth()->user();
                $device = Device::where('user_id', $user->id)->first();
                if (is_null($device)) {
                    $result = new ApiResult(false, null, 'Sorry, but you have to register your device before.');
                }
                else {
                    $purchase = Purchase::where('user_id', $user->id)->where('status', '1')->first();
                    if (is_null($purchase)) {
                        $verificationResult = $this->mockingVerification($request->receipt, $device->os);

                        $purchase = new Purchase();
                        $purchase->user_id = $user->id;
                        $purchase->receipt = $request->receipt;
                        $purchase->status = $verificationResult['status'];
                        $purchase->expire_date = $verificationResult['expire_date'];
                        $purchase->state = $verificationResult['status'] ? 0 : null; // state: 0 started
                        if ($purchase->save()) {
                            $result = new ApiResult(true, null, 'Purchase saved successfully.');
                        }
                        else {
                            $result = new ApiResult(false, null, 'Something went wrong while saving the purchase.');
                        }
                    }
                    else {
                        // Check if expired
                        $expire_date = Carbon::parse($purchase->expire_date);
                        if ($expire_date->greaterThanOrEqualTo(Carbon::now())) {
                            $verificationResult = $this->mockingVerification($request->receipt, $device->os);

                            $purchase->status = $verificationResult['status'];
                            $purchase->expire_date = $verificationResult['expire_date'];
                            $purchase->state = $verificationResult['status'] ? 1 : null; // state: 1 renewed
                            if ($purchase->save()) {
                                $result = new ApiResult(true, null, 'Purchase updated successfully.');
                            }
                            else {
                                $result = new ApiResult(false, null, 'Something went wrong while updating the purchase.');
                            }
                        }
                        else {
                            $result = new ApiResult(false, null, 'Sorry, but you have an active subscribtion! And will expire at '.$purchase->expire_date);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            $result = new ApiResult(false, null, $e->getMessage());
        }
        return response()->json($result);
    }

    /**
     * Update a purchase info by its primary key.
     *
     * @param  Request $request
     * @param  int $id
     * @return Response
     */
    public function update(Request $request, int $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                // Purchase rules
                'state' => 'required|digits_between:0,2',
            ]);

            if ($validator->fails()) {
                $result = new ApiResult(false, null, 'Validation errors.', $validator->errors());
            }
            else {
                $purchase = Purchase::findOrFail($id);
                $purchase->state = $request->state;

                if ($purchase->save()) {
                    $result = new ApiResult(true, null, 'Purchase updated successfully.');
                }
                else {
                    $result = new ApiResult(false, null, 'Something went wrong while updating the purchase.');
                }
            }
        } catch (\Throwable $e) {
            $result = new ApiResult(false, null, $e->getMessage());
        }
        return response()->json($result);
    }

    /**
     * Delete a purchase by its primary key.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(int $id)
    {
        try {
            $purchase = Purchase::findOrFail($id);
            if ($purchase->delete()) {
                $result = new ApiResult(true, null, 'Purchase deleted successfully.');
            }
        } catch (\Throwable $e) {
            $result = new ApiResult(false, null, $e->getMessage());
        }
        return response()->json($result);
    }

    /**
     * Mocking the purchase verification.
     *
     * @param  string $receipt
     * @param  int $os: 0: ios, 1:google
     * @return mixed
     */
    protected function mockingVerification(string $receipt, int $os)
    {
        $status = 0;
        $expire_date = null;
        $error = null;
        try {
            $client = new Client();
            $result = $client->post(env('MOCKING_ENDPOINT').'/purchases/verification/'.($os == 0 ? 'ios' : 'google'),[
                'form_params' => [
                    "receipt" => $receipt,
                ]
            ]);

            if (isset($result)) {
                $status = json_decode($result->getBody(), false)->status ? 1 : 0;
                $expire_date = json_decode($result->getBody(), false)->data->expire_date;
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return [
            'status' => $status,
            'expire_date' => $expire_date,
            'error' => $error,
        ];
    }
}
