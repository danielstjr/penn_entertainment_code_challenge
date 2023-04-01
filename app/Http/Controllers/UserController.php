<?php

namespace App\Http\Controllers;

use App\Domain\Repositories\User\UserRepository;
use Exception;
use InvalidArgumentException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Controller used to create, update, and delete a User instance
 */
class UserController extends Controller
{
    private UserRepository $userRepository;

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
            $response->getBody()->write("User with id {$id} not found");
            return $response->withStatus(404);
        }

        if (!$status) {
            return $response->withStatus(500, 'Internal Server Error');
        }

        $response->getBody()->write("User with id {$id} was deleted");
        return $response->withStatus(200);
    }
}