<?php

namespace App\Helpers;

use Akaunting\Module\Facade as Module;

class StaticHelper
{

    public static function sendWA_OTP($otp, $RecipientWAID)
    {
        $rawData = [
            'messaging_product' => "whatsapp",
            'to' => preg_replace('/\s+/', '', $RecipientWAID),
            'type' => "template",
            "template" => [
                "name" => "sendinai_loginotp",
                "language" => [
                    "code" => "en"
                ],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $otp
                            ]
                        ]
                    ],
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => 0,
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $otp
                            ]
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = (new \GuzzleHttp\Client())->request('POST', config('otp.url'), [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('otp.token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => $rawData,
            ]);
            $responseBody = $response->getBody()->getContents();
            $decodedResponse = json_decode($responseBody, true);
            return true;
        } catch (\Exception $e) {
            // echo '<pre>';
            // print_r($e->getMessage());
            // die;
            return false;
        }
    }

    public static function getOwnerMenu()
    {
        $menus = [];
        foreach (Module::all() as $key => $module):
            if (is_array($module->get('ownermenus'))):
                foreach ($module->get('ownermenus') as $key => $menu):
                    if (isset($menu['onlyin'])):
                        if (config('app.' . $menu['onlyin'])):
                            array_push($menus, $menu);
                        endif;
                    else:
                        array_push($menus, $menu);
                    endif;
                endforeach;
            endif;
        endforeach;
        return array_column($menus, 'name');
    }

    public static function  curlPostRequest($url, $data, $token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Token ' . $token,
                'Content-Type: application/json',
                'Accept: application/json',
            ),
        ));

        $response['status'] = true;
        $data = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) :
            $response['status'] = false;
            $response['data'] = $err;
            return $response;
        endif;
        $data = json_decode($data);
        if ($data->success) :
            $response['data'] = $data;
        else :
            $response['status'] = false;
            $response['data'] = $data;
        endif;
        // pre($response);
        return $response;
    }

    // Shipping API GET REQUEST
    public static function curlGetRequest($url, $token="", $data = array())
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                // 'Authorization: Token ' . $token,
                // 'Content-Type: application/json',
                'Accept: application/json',
            ),
        ));
        $response['status'] = true;
        $response['data'] = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) :
            $response['status'] = false;
            $response['data'] = $err;
        endif;
        $response['data'] = json_decode($response['data'],true);
        return $response;
    }


    public static function __log_the_payment_response($data,$status="SUCCESS"){
            $log = "\n========================RAzorPay  ".$status."  ".Date("Y-m-d h:i:s")."===========================\n";
			$log .= json_encode($data);
            $log .= "\n===================================================\n\n";
            $directoryPath = public_path('logs/');
            if (!\Illuminate\Support\Facades\File::isDirectory($directoryPath)):
                \Illuminate\Support\Facades\File::makeDirectory($directoryPath);
            endif;
			file_put_contents($directoryPath . '/razor_pay_log.txt', $log, FILE_APPEND);
    }
}
