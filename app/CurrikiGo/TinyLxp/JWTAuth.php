<?php

namespace App\CurrikiGo\TinyLxp;

class JWTAuth
{
    private $lmsSetting;

    public function __construct($lmsSetting)
    {
        $this->lmsSetting = $lmsSetting;
    }

    function createToken() {
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $payload = json_encode([
            'data' => (object) [
                'user' => (object) [
                    'id' => 1,
                    'email' => $this->lmsSetting->lms_login_id
                ]
            ],
            'iss' => $this->lmsSetting->lms_url,
            'iat' => time(),
            'exp' => time() + 300  // Token valid for 5 min
        ]);

        // Encode header and payload
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        // Create signature
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->lmsSetting->lms_access_token, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        // Return JWT
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
