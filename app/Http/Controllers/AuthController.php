<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth,Log};

class AuthController extends ApiBaseController
{

    /**
     * Constructer
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * API Auth JWT Token Issue function
     *
     * @param string $serviceName
     *
     * @return JsonResponse
     */
    public function getAuthToken()
    {
        $serviceName = "EcomMediaPlayer";
        // Init return array
        $result = [
            'status' => false,
            'token' => '',
            'error' => ''
        ];

        try {
            // Issue JWT Token
            $token = Auth::guard('api')->claims(
                [
                    'exp' => Carbon::now()->addHour()->getTimestamp(),
                ]
            )->attempt(
                [
                    'service_name' => $serviceName,
                    'get_permission' => true,
                    'put_permission' => true,
                    'del_permission' => true,
                ]
            );
        } catch (\Throwable $e) {
            // Fail Generate JWT Token
            $msg = "Failed to create the auth token: ".$e->getMessage();
            Log::error($msg);
            $result['error'] = $msg;
            return response()->json($result,500);
        }

        $result['status'] = true;
        $result['token'] = $token;
        return response()->json($result, 200);
    }
}
