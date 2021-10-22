<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function verifyIos(Request $request)
    {
        return response()->json($this->verification($request));
    }

    public function verifyGoogle(Request $request)
    {
        return response()->json($this->verification($request));
    }

    /**
     * Verification.
     *
     * @param  Request $request
     * @return Response
     */
    function verification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Purchase rules
            'receipt' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return [ 'data' => null, 'status' => false, 'message' => 'Validation errors.', 'errors' => $validator->errors() ];
        }
        else {
            $lastTwoChar = substr(((String)$request->receipt), -2);
            if (str_contains($lastTwoChar, '6')) {
                return [ 'data' => [ 'error_code' => 429 ], 'status' => false, 'message' => 'Rate limit error.' ];
            }

            $lastChar = substr(((String)$request->receipt), -1);
            if (is_numeric($lastChar) && $lastChar % 2) {
                $now = new DateTime();
                $expire_date = $now->modify('+'.rand(1,3).' month')->format('Y-m-d H:i:s');
                return [ 'data' => [ 'expire_date' => $expire_date ], 'status' => true, 'message' => '' ];
            }
            else {
                return [ 'data' => null, 'status' => false, 'message' => 'Purchase verification faild.' ];
            }
        }
    }
}
