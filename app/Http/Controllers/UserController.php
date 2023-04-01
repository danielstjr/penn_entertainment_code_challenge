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
            return $this->setResponseStatusAndBody($response, 500, 'Failed to retrieve users');
        }

        return $this->setJsonResponseStatusAndBody($response, 200, json_encode($users));
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
            return $this->setJsonResponseStatusAndBody($response, 400, json_encode(['errors' => $errors]));
        }

        try {
            $user = $this->userRepository->create($postData['email'], $postData['name']);

            return $this->setJsonResponseStatusAndBody($response, 201, json_encode($user));
        } catch (InvalidArgumentException) {
            return $this->setResponseStatusAndBody($response, 400, 'User with this email address already exists');
        } catch (Exception $e) {
            return $this->setResponseStatusAndBody($response, 500, 'Failed to Create User');
        }
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
            return $this->setResponseStatusAndBody($response, 404, "User with id {$id} not found");
        }

        if (!$status) {
            return $this->setResponseStatusAndBody($response, 500, "Failed to delete user with id {$id}");
        }

        return $this->setResponseStatusAndBody($response, 200, "User with id {$id} was deleted");
    }
}