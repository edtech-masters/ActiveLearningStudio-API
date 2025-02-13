<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\XapiStatementRequest;
use App\Http\Requests\V1\XapiParamRequest;
use Illuminate\Http\Request;
use App\Services\LearnerRecordStoreService;
use App\Services\SqlrsService;

/**
 * @group 20. XAPI
 *
 * An XAPI Controller Class
 * Handles xAPI operations.
 */
class XapiController extends Controller
{
    /**
     * Save an xAPI Statement
     *
     * Creates a new statement in the database.
     *
     * @param XapiStatementRequest $statementRequest
     *
     * @response 201 {
     *   "id": "61ffa986-1dcc-3df0-94d8-a09384b197a7"
     * }
     *
     * @response 500 {
     *   "errors": [
     *     "The statement could not be saved due to an error"
     *   ]
     * }
     *
     * @param XapiStatementRequest $statementRequest
     * @return Response
     */
    public function saveStatement(XapiStatementRequest $statementRequest)
    {
        $data = $statementRequest->validated();
        $activeLrs = config('xapi.active_lrs');

        try {
            if (isset($activeLrs) && $activeLrs == 'sql_lrs') {
                $service = new SqlrsService();
                $response = $service->saveStatement($data['statement']);
                if (isset($response[0])) {
                    $response = [
                        "id" => $response[0]  // Use the first element of the array
                    ];
                    return response( $response, 201 );
                } else {
                    return response( $response, 500 );
                }
            } else {
                $service = new LearnerRecordStoreService();
                $response = $service->saveStatement($data['statement']);
                if ($response->success) {
                    return response([
                        'id' => $response->content->getId(),
                    ], 201);
                }
                else {
                    return response([
                        'errors' => ["Error: " . $response->content],
                    ], 500);
                }
            }
        } catch (\Exception $e) {
            return response([
                'errors' => ["The statement could not be saved due to an error: " . $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Fetch multiple xAPI Statements
     *
     *
     * @param XapiParamRequest $statementRequest
     *
     * @response 201 {
     *   "statements": {json-objects}
     * }
     *
     * @response 500 {
     *   "errors": [
     *     "The statement could not be fetch due to wrong params"
     *   ]
     * }
     *
     * @param XapiParamRequest $statementRequest
     * @return Response
     */
    public function fetchStatement(XapiParamRequest $statementRequest)
    {
        $activeLrs = config('xapi.active_lrs');
        try {
            if (isset($activeLrs) && $activeLrs == 'sql_lrs') {
                $paramsData = $statementRequest->validated();
                $service = new SqlrsService();
                $response = $service->fetchStatement($paramsData);
                return $response;
            } else {
                return [ 'error' => 'Sqlrs not connected!' ];
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
