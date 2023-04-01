<?php

namespace App\Http\Controllers;

use App\Domain\Repositories\Transaction\TransactionRepository;
use App\Domain\Repositories\User\UserRepository;
use Exception;
use InvalidArgumentException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Controller used for updating User Point Balance state
 */
class TransactionController extends Controller
{
    private UserRepository $userRepository;

    private TransactionRepository $transactionRepository;

    /**
     * Construct and associate a transaction and user repository for data management
     *
     * @param TransactionRepository $transactionRepository
     * @param UserRepository $userRepository
     */
    public function __construct(TransactionRepository $transactionRepository, UserRepository $userRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
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
            return $this->setResponseStatusAndBody($response, 500, "User with id {$id} not found");
        } catch (Exception) {
            return $this->setResponseStatusAndBody($response, 500, 'Failed to update point balance');
        }

        $postBody = $request->getParsedBody() ?? [];
        $errors = static::buildErrorArray($postBody, ['description' => "'description' field is required"]);

        if (!array_key_exists('points', $postBody)) {
            $errors[] = "'points' field is required";
        } else if ($postBody['points'] < 1) {
            $errors[] = "'points' field must be greater than 0";
        } else if ($user->getPointsBalance() + ($add ? $postBody['points'] : -$postBody['points']) < 0) {
            $errors[] = "Points transactions cannot leave a user with a negative points total";
        }

        if (!empty($errors)) {
            return $this->setJsonResponseStatusAndBody($response, 400, json_encode(['errors' => $errors]));
        }

        $points = $add ? $postBody['points'] : -$postBody['points'];
        try {
            $this->transactionRepository->create($postBody['description'], $points, $user);
            $this->userRepository->updatePoints($user, $user->getPointsBalance() + $points);
        } catch (Exception) {
            return $this->setResponseStatusAndBody($response, 500, "Failed to update points balance for User id {$id}");
        }

        return $this->setResponseStatusAndBody($response, 200, 'Points balance updated');
    }
}