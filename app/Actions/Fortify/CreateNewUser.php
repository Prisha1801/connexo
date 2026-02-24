<?php

namespace App\Actions\Fortify;

use App\Helpers\StaticHelper;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {

        $phone = "+" . $input['country_code'] . trim(preg_replace('/\s+/', '', $input['phone']));
        $input['phone'] = $phone;

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:255'], // Add 'phone' to the validation rules
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $otp = rand(100000, 999999);
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'phone' =>$phone, // Add 'phone' to the user creation
            'otp'=>$otp,
            'otp_sent_at'=>now(),
        ]);

        $user->plan_id = config('settings.free_pricing_id');
        $user->trial_ends_at = Carbon::now()->addDays(config('settings.free_trail_days'));
        $user->free_plan_used = true;
        $user->assignRole('owner');
        $user->save();


        //Create company
        $lastCompanyId = DB::table('companies')->insertGetId([
            'name' => $input['name'],
            'subdomain' => strtolower(preg_replace('/[^A-Za-z0-9]/', '', $input['name'])),
            'user_id' => $user->id,
            'created_at' => now(),  
            'updated_at' => now(),
            'phone'=>$phone, // Add 'phone' to the company creation
            'logo'=>asset('uploads').'/default/no_image.jpg',
        ]);

       //Send Message to WA
       StaticHelper::sendWA_OTP($otp,$phone);
        
       $user->company_id = $lastCompanyId;
       return $user;
    }
}
