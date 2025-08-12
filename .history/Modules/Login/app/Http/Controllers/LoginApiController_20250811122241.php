<?php

namespace Modules\Login\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginApiController extends Controller
{

    public function index()
    {
        return view('login::index');
    }
    public function login()
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|exists:users,mobile',
        ], [
            'mobile.required' => 'Enter mobile no.',
            'mobile.exists' => 'Mobile number not found.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Please input proper data.', 'errors'  => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $user = User::where('mobile', $request->mobile)
                ->where(function ($query) use ($request) {
                    if (isset($request->is_resend_type) && ($request->is_resend_type != 'register_resend')) {
                        $query->where('status', 'Active');
                    }
                })
                ->first(['id', 'otp', 'name', 'mobile']);
            if (is_null($user)) {
                return response()->json(['message' => 'User not found.'], 500);
            }
            $otp = '357335'; //str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // $name =  $name = ucwords($user->name);
            // $wpMessage = "Dear *{$name}*,\n\n"
            //     . "Your one-time password (OTP) is:\n"
            //     . "*{$otp}*\n\n"
            //     . "⚠️ Please note:\n"
            //     . "• Do not share with anyone\n";

            // $smsData = [
            //     'type' => 'login',
            //     'name' => $name,
            //     'message' => $wpMessage,
            //     'mobile' => $user->mobile,
            // ];

            // $maxRetries = 3;
            // $attempt = 0;
            // $messageSent = false;

            // while ($attempt < $maxRetries && !$messageSent) {
            //     $attempt++;
            //     $messageSent = sendWhatsapp($smsData, '+91');

            //     if (!$messageSent && $attempt < $maxRetries) {
            //         sleep(1);
            //     }
            // }

            // if (!$messageSent) {
            //     DB::rollback();
            //     return response()->json(['message' => 'Failed to send OTP after multiple attempts. Please try again.'], 500);
            // }

            $user->otp = $otp;
            $user->save();

            DB::commit();

            return response()->json([
                'message' => 'OTP sent successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('OTP Send Error: ' . $e->getMessage(), [
                'mobile' => $request->mobile,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }

    public function create()
    {
        return view('login::create');
    }

    public function store(Request $request) {}

    public function show($id)
    {
        return view('login::show');
    }

    public function edit($id)
    {
        return view('login::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
