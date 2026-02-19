<?php

namespace App\Http\Controllers;

use App\Helpers\StaticHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    
    public function show()
    {
        if(auth()->user()->is_otp_verified):
            return redirect('home');
        endif;
        return view('auth.verify-otp');
        
    }

    public function verify(Request $request)
    {
        if(auth()->user()->is_otp_verified):
            return redirect('home');
        endif;
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $user = Auth::user();

        if ($user->otp == $request->otp) {
            $user->otp = null;
            $user->is_otp_verified=1;
            $user->save();
            return response()->json(['message' => 'OTP verified successfully'], 200);
        } else {
            return response()->json(['error' => 'Invalid OTP'], 400);
        }
        // if ($user->otp == $request->otp) {
        //     $user->is_otp_verified = 1;
        //     $user->save();
        //     return redirect()->route('home')->with('success', 'OTP verified successfully');
        // }

        // return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
    }
    public function resendOtp(Request $request)
    {
        if(auth()->user()->is_otp_verified):
            return redirect('home');
        endif;
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        if ($user->otp_sent_at && now()->diffInSeconds($user->otp_sent_at) < config('otp.otp_timer')) {
            return response()->json(['error' => 'Please wait before resending the OTP'], 429);
        }
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_sent_at = now();
        $user->save();
        
        //ReSend Message to WA
        StaticHelper::sendWA_OTP($otp,$user->phone);
        //ReSend Message to WA
        return response()->json(['message' => 'OTP resent successfully'], 200);
    }
}
