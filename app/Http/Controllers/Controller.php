<?php

namespace App\Http\Controllers;

use Slim\Psr7\Response;

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
            if (!array_key_exists($requiredField, $postData) || $postData[$requiredField] === null) {
                $errors[] = $errorMessage;
            }
        }

        return $errors;
    }

    /**
     * Create a simple alias function to reduce noise in response changing function calls
     *
     * @param Response $response
     * @param int $statusCode
     * @param string $responseBody
     *
     * @return Response
     */
    protected function setResponseStatusAndBody(Response $response, int $statusCode, string $responseBody): Response
    {
        $response->getBody()->write($responseBody);
        return $response->withStatus($statusCode);
    }

    /**
     * Further alias of ->setResponseStatusAndBody to include the JSON content header
     *
     * @param Response $response
     * @param int $statusCode
     * @param string $responseBody
     *
     * @return Response
     */
    protected function setJsonResponseStatusAndBody(Response $response, int $statusCode, string $responseBody): Response
    {
        return $this->setResponseStatusAndBody($response, $statusCode, $responseBody)
            ->withHeader(self::CONTENT_TYPE, self::JSON);
    }
}