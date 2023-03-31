<?php

namespace App\Http\Controllers;

use App\Domain\Repositories\UserRepository;
use Exception;
use InvalidArgumentException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Controller used to create, update, and delete a User instance
 */
class UserController
{
    private UserRepository $userRepository;

    private const CONTENT_TYPE = 'Content-Type';

    private const JSON = 'application/json';

    /**
     * Store a user repository capable of managing user state
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Retrieve all users and return them as a json array
     *
     * @param Response $response
     *
     * @return Response
     */
    public function index(Response $response): Response
    {
        try {
            $users = $this->userRepository->getAll();
        } catch (Exception) {
            return $response->withStatus(500, 'Failed to retrieve users');
        }

        $response->getBody()->write(json_encode($users));

        return $response->withHeader(self::CONTENT_TYPE, self::JSON)->withStatus(200);
    }

    /**
     * Create a user with the given post data
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $postData = $request->getParsedBody() ?? [];
        var_dump($postData) . "\n";
        $errors = static::buildErrorArray($postData, [
            'email' => "'email' field is required",
            'name' => "'name' field is required",
        ]);

        if (!empty($errors)) {
            $response->getBody()->write(json_encode(['errors' => $errors]));

            return $response->withHeader(self::CONTENT_TYPE, self::JSON)
                ->withStatus(400, 'Invalid data for user creation');
        }

        try {
            $user = $this->userRepository->create($postData['email'], $postData['name']);
        } catch (InvalidArgumentException) {
            $response->getBody()->write('User with this email address already exists');
            return $response->withStatus(400, 'Duplicate Email');
        } catch (Exception) {
            $response->getBody()->write('Failed to create user');
            return $response->withStatus(500, 'Internal Server Error');
        }

        $response->getBody()->write(json_encode($user));

        return $response->withStatus(201);
    }

    /**
     * Delete a user identified by the path's user id
     *
     * @param Response $response
     * @param int $id
     *
     * @return Response
     */
    public function delete(Response $response, int $id): Response
    {
        try {
            $status = $this->userRepository->delete($id);
        } catch (Exception) {
            return $response->withStatus(404, "User with id {$id} not found");
        }

        if (!$status) {
            return $response->withStatus(500, 'Internal Server Error');
        }

        return $response->withStatus(200, "User with id {$id} was deleted");
    }

    /**
     * Add points to the given user's balance
     *
     * @param Request $request
     * @param Response $response
     * @param int $id
     *
     * @return Response
     */
    public function earnPoints(Request $request, Response $response, int $id): Response
    {
        return $this->updatePoints($request, $response, $id, true);
    }

    /**
     * Removes points from the given user's balance
     *
     * @param Request $request
     * @param Response $response
     * @param int $id
     *
     * @return Response
     */
    public function redeemPoints(Request $request, Response $response, int $id): Response
    {
        return $this->updatePoints($request, $response, $id, false);
    }

    /**
     * Updates the point balance for a user
     *
     * @param Request $request
     * @param Response $response
     * @param int $id
     * @param bool $add
     *
     * @return Response
     */
    private function updatePoints(Request $request, Response $response, int $id, bool $add): Response
    {
        try {
            $user = $this->userRepository->get($id);
        } catch (InvalidArgumentException) {
            return $response->withStatus(404, "User with id {$id} not found");
        } catch (Exception) {
            return $response->withStatus(500, 'Internal Server Error');
        }

        $postBody = $request->getParsedBody() ?? [];
        $errors = static::buildErrorArray($postBody, [
            'points' => "'points' field is required",
            'description' => "'description' field is required"
        ]);

        // TODO:: store description in user_point_transactions table

        if (!empty($errors)) {
            $response->getBody()->write(json_encode(['errors' => $errors]));

            return $response->withHeader(self::CONTENT_TYPE, self::JSON)
                ->withStatus(400, 'Invalid data for user creation');
        }

        $newPointsBalance = $add ?
            $user->getPointsBalance() + $postBody['points'] :
            $user->getPointsBalance() - $postBody['points'];

        try {
            $this->userRepository->updatePoints(
                $user,
                $newPointsBalance
            );
        } catch (Exception) {
            return $response->withStatus(500, "Failed to update points balance for User id {$id}");
        }

        return $response->withStatus(200, 'Points balance updated');
    }

    /**
     * Determines if the post data has all relevant rules
     *
     * @param array $postData
     * @param array $rules
     *
     * @return array
     */
    private static function buildErrorArray(array $postData, array $rules): array
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