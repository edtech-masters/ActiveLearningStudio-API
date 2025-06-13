<?php

return [
    'xapi_version' => env('XAPI_VERSION'),
    'lrs_username' => env('LRS_USERNAME'),
    'lrs_password' => env('LRS_PASSWORD'),
    'lrs_remote_url' => env('LRS_REMOTE_URL'),
    'lrs_db_statements_table' => env('LRS_DB_STATEMENTS_TABLE'),
    'lrs_job_row_limit' => env('LRS_JOB_ROW_LIMIT'),
    /*
    |--------------------------------------------------------------------------
    | Sql-Lrs Active Condition Variable
    |--------------------------------------------------------------------------
    */
    'active_lrs' => env('ACTIVE_LRS'),
    /*
    |--------------------------------------------------------------------------
    | Sql-Lrs XApi Version
    |--------------------------------------------------------------------------
    */
    'sqlrs_xapi_version' => env('SQLRS_XAPI_VERSION'),
    /*
    |--------------------------------------------------------------------------
    | Sql-Lrs Api Url
    |--------------------------------------------------------------------------
    */
    'sqlrs_api_url' => env('SQLRS_API_POINT'),
    /*
    |--------------------------------------------------------------------------
    | Sql-Lrs Api Key
    |--------------------------------------------------------------------------
    */
    'sqlrs_api_key' => env('SQLRS_API_KEY'),
    /*
    |--------------------------------------------------------------------------
    | Sql-Lrs Api Secret
    |--------------------------------------------------------------------------
    */
    'sqlrs_api_secret' => env('SQLRS_API_SECRET')
];
