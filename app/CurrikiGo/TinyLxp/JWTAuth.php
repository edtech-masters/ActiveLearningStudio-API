<?php

namespace App\CurrikiGo\TinyLxp;

use GuzzleHttp;

class JWTAuth
{
    private $client;
    private $lmsSetting;

    public function __construct($lmsSetting)
    {
        $this->client = new GuzzleHttp\Client();
        $this->lmsSetting = $lmsSetting;
    }

    function createToken() {

        $lmsHost = $this->lmsSetting->lms_url;
        $webServiceURL = $lmsHost . "/wp-json/learnpress/v1/token";
        $requestParams = [
            "username" => $this->lmsSetting->lms_login_id,
            "password" => $this->lmsSetting->lms_access_token
        ];
        $response = $this->client->request('POST', $webServiceURL, [
        'verify' => false,  // Disable SSL certificate verification,
        'json' => $requestParams
        ]);
        $tokenBody = $response->getBody()->getContents();
        $tokenBody = json_decode($tokenBody);
        return $tokenBody->token;
    }
}
