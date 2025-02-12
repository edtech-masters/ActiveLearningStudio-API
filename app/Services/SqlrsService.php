<?php

namespace App\Services;

use App\Services\SqlrsServiceInterface;
use Illuminate\Support\Facades\Http;
use PhpParser\Node\Stmt\TryCatch;

/**
 * SQL LRS Service class
 */
class SqlrsService implements SqlrsServiceInterface
{
    /**
     * SQL LRS Serivce object
     */
    protected $sqlrsApiUrl;
    protected $sqlrsApikey;
    protected $sqlrsApisecret;

    /**
     * Creates an instance of the class
     *
     * @return void
     */
    function __construct()
    {
        $this->sqlrsApiUrl = config('xapi.sqlrs_api_url');
        $this->sqlrsApikey = config('xapi.sqlrs_api_key');
        $this->sqlrsApisecret = config('xapi.sqlrs_api_secret');
    }

    /**
     * Save Statement
     *
     * @param string $statement A stringified xAPI statment
     * @return \SQLRSResponse
     */
    public function saveStatement($statementData)
    {
        try {
            $data = json_decode($statementData, true);
            $authHeaders = [
                'Authorization' => 'Basic ' . base64_encode($this->sqlrsApikey.':'.$this->sqlrsApisecret),
                'X-Experience-API-Version' => '1.0.3',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];
            $response = Http::withHeaders($authHeaders)->post($this->sqlrsApiUrl, $data);
            $response = json_decode($response->getBody());
        } catch (\Throwable $th) {
            $response = $th;
        }
        return $response;
    }

    /**
     * Save Statement
     *
     * @param uuid $agent           Example: agent must be a UUID (if provided)
     * @param string $verb          Example: verb must be a string
     * @param string $activity      Example: activity must be a string
     * @param date $since           Since must be a valid date (if provided)
     * @param date $until           Until must be a valid date (if provided)
     * @param integer $limit        Limit must be an integer between 1 and 1000
     * @param bool $ascending       Limit must be an integer between 1 and 1000
     * @return JsonResponse
     */
    public function fetchStatement($params)
    {
        if (!isset($params['agent']) && !isset($params['verb']) && !isset($params['activity']) && !isset($params['since']) && !isset($params['until'])) {
            return null;
        }
        if (isset($params['limit'])) {
            $paramUrl = $this->sqlrsApiUrl.'?limit='.$params['limit'];
        } else {
            $paramUrl = $this->sqlrsApiUrl.'?limit=1';
        }
        if (isset($params['agent'])) {
            $paramUrl .= '&agent='.$params['agent'];
        }
        if (isset($params['verb'])) {
            $paramUrl .= '&verb='.$params['verb'];
        }
        if (isset($params['activity'])) {
            $paramUrl .= '&activity='.$params['activity'];
        }
        if (isset($params['related_agents'])) {
            $paramUrl .= '&related_agents='.$params['related_agents'];
        }
        if (isset($params['related_activities'])) {
            $paramUrl .= '&related_activities='.$params['related_activities'];
        }
        if (isset($params['since'])) {
            $paramUrl .= '&since='.$params['since'];
        }
        if (isset($params['until'])) {
            $paramUrl .= '&until='.$params['until'];
        }
        if (isset($params['ascending']) && $params['ascending'] == 1) {
            $paramUrl .= '&ascending=true';
        } else {
            $paramUrl .= '&ascending=false';
        }

        $authHeaders = [
            'Authorization' => 'Basic ' . base64_encode($this->sqlrsApikey.':'.$this->sqlrsApisecret),
            'X-Experience-API-Version' => '1.0.3',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
        $response = Http::withHeaders($authHeaders)->get($paramUrl);

        if ($response->ok()) {
            return $response->json();
        } else {
            return $response->toException();
        }
    }
}
