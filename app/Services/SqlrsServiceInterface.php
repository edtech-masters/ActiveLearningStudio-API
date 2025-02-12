<?php

namespace App\Services;

/**
 * SQL LRS Service Interface
 */
interface SqlrsServiceInterface
{

    /**
     * Save Statement
     *
     * @param string $statement A stringified xAPI statment
     * @return \LRSResponse
     */
    public function saveStatement($statement);

    /**
     * Query Statements
     *
     * Get statements from the LRS based on parameters.
     * 
     * @param array $queryParams An array of query parameters
     * @return \LRSResponse
     */
    // public function queryStatements($queryParams);
    
}
