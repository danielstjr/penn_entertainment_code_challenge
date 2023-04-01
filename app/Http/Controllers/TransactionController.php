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
            return $response->withStatus(404, "User with id {$id} not found");
        } catch (Exception) {
            return $response->withStatus(500, 'Internal Server Error');
        }

        $postBody = $request->getParsedBody() ?? [];
        $errors = static::buildErrorArray($postBody, [
            'points' => "'points' field is required",
            'description' => "'description' field is required"
        ]);

        if (!empty($errors)) {
            $response->getBody()->write(json_encode(['errors' => $errors]));

            return $response->withHeader(self::CONTENT_TYPE, self::JSON)
                ->withStatus(400, 'Invalid data for user creation');
        }

        $points = $add ? $postBody['points'] : -$postBody['points'];
        try {
            $this->transactionRepository->create($postBody['description'], $points, $user);
            $this->userRepository->updatePoints($user, $user->getPointsBalance() + $points);
        } catch (Exception) {
            return $response->withStatus(500, "Failed to update points balance for User id {$id}");
        }

        return $response->withStatus(200, 'Points balance updated');
    }
}