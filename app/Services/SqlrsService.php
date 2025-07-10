<?php

namespace App\Services;

use App\Services\SqlrsServiceInterface;
use Illuminate\Support\Facades\Http;
use \TinCan\RemoteLRS;
use \TinCan\Statement;

/**
 * SQL LRS Service class
 */
class SqlrsService implements SqlrsServiceInterface
{
    /**
     * SQL LRS Serivce object
     */
    protected $sqlrsXapiVersion;
    protected $sqlrsApiUrl;
    protected $sqlrsApikey;
    protected $sqlrsApisecret;
    protected $service;

    /**
     * Creates an instance of the class
     *
     * @return void
     */
    function __construct()
    {
        $this->sqlrsApiUrl = config('xapi.sqlrs_api_url');
        $this->sqlrsXapiVersion = config('xapi.sqlrs_xapi_version');
        $this->sqlrsApikey = config('xapi.sqlrs_api_key');
        $this->sqlrsApisecret = config('xapi.sqlrs_api_secret');
        $this->service = new RemoteLRS(
            $this->sqlrsApiUrl,
            $this->sqlrsXapiVersion,
            $this->sqlrsApikey,
            $this->sqlrsApisecret);
    }

    /**
     * Build Statement from JSON
     *
     * @param string $statement A stringified xAPI statment
     * @return \Statement
     */
    public function buildStatementfromJSON($statement)
    {
        return Statement::fromJSON($statement);
    }

    /**
     * Save Statement
     *
     * @param string $statement A stringified xAPI statment
     * @return \SQLRSResponse
     */
    public function saveStatement($statementData)
    {
        $statement = $this->buildStatementfromJSON($statementData);
        return $this->service->saveStatement($statement);
    }

    /**
     * Save Statement
     *
     * @param integer $limit            Limit must be an integer between 1 and 1000
     * @param uuid $agent               Example: agent must be a UUID (if provided)
     * @param string $verb              Example: verb must be a string
     * @param string $activity          Example: activity must be a string
     * @param bool $related_agents      0 or 1
     * @param bool $related_activities  0 or 1
     * @param date $since               Since must be a valid date (if provided)
     * @param date $until               Until must be a valid date (if provided)
     * @param bool $ascending           0 or 1
     * @return JsonResponse
     */
    public function fetchStatement($params)
    {
        if (!isset($params['agent']) && !isset($params['verb']) && !isset($params['activity']) && !isset($params['since']) && !isset($params['until'])) {
            return null;
        }
        if (isset($params['limit'])) {
            $paramUrl = $this->sqlrsApiUrl.'statements?limit='.$params['limit'];
        } else {
            $paramUrl = $this->sqlrsApiUrl.'statements?limit=1';
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
        if (isset($params['related_agents'])  && $params['related_agents'] == 1) {
            $paramUrl .= '&related_agents=true';
        } else {
            $paramUrl .= '&related_agents=false';
        }
        if (isset($params['related_activities']) && $params['related_activities'] == 1) {
            $paramUrl .= '&related_activities=true';
        } else {
            $paramUrl .= '&related_activities=false';
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
            'X-Experience-API-Version' => $this->sqlrsXapiVersion
        ];
        $response = Http::withHeaders($authHeaders)->get($paramUrl);

        if ($response->ok()) {
            return $response->json();
        } else {
            return $response->toException();
        }
    }
}
