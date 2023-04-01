<?php

namespace App\Http\Controllers;

/**
 * Class used for grouping common functions shared between all controllers
 */
abstract class Controller
{
    protected const CONTENT_TYPE = 'Content-Type';

    protected const JSON = 'application/json';

    /**
     * Determines if the post data has all relevant rules
     *
     * @param array $postData
     * @param array $rules
     *
     * @return array
     */
    protected static function buildErrorArray(array $postData, array $rules): array
    {
        $errors = [];
        foreach ($rules as $requiredField => $errorMessage) {
            if (!array_key_exists($requiredField, $postData)) {
                $errors[] = $errorMessage;
            }
        }

        return $errors;
    }
}