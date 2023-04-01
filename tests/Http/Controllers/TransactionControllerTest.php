<?php

namespace Tests\Http\Controllers;

use App\Domain\Models\Transaction;
use App\Domain\Models\User;
use App\Domain\Repositories\Transaction\InMemoryTransactionRepository;
use App\Domain\Repositories\User\InMemoryUserRepository;
use App\Http\Controllers\TransactionController;
use Exception;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Validates the TransactionController correctly handles use cases for point management
 */
class TransactionControllerTest extends TestCase
{
    public function testEarnPointsEndpointRequiresPointsAndDescription()
    {
        $user = new User('test@example.com', 'test', 100, 1);
        $userRepository = $this->createMock(InMemoryUserRepository::class);
        $userRepository->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($user);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(null);

        $controller = new TransactionController(new InMemoryTransactionRepository(), $userRepository);

        $response = $controller->earnPoints($request, new Response(), 1);
        $errors = json_decode($response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertNotEmpty($errors['errors']);

        // Would make test brittle if there was a lot of back and forth on request validation stuff
        $this->assertContains("'points' field is required", $errors['errors']);
        $this->assertContains("'description' field is required", $errors['errors']);
    }

    public function testPointsEndpointRequiresPositivePointValues()
    {
        $user = new User('test@example.com', 'test', 100, 1);
        $userRepository = $this->createMock(InMemoryUserRepository::class);
        $userRepository->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($user);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['points' => -1]);

        $controller = new TransactionController(new InMemoryTransactionRepository(), $userRepository);

        $response = $controller->earnPoints($request, new Response(), 1);
        $errors = json_decode($response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertNotEmpty($errors['errors']);

        // Would make test brittle if there was a lot of back and forth on request validation stuff
        $this->assertContains("'points' field must be greater than 0", $errors['errors']);
    }

    public function testPointsEndpointEnforcesPositivePointsTotals()
    {
        $user = new User('test@example.com', 'test', 100, 1);
        $userRepository = $this->createMock(InMemoryUserRepository::class);
        $userRepository->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($user);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['points' => 200]);

        $controller = new TransactionController(new InMemoryTransactionRepository(), $userRepository);

        $response = $controller->redeemPoints($request, new Response(), 1);
        $errors = json_decode($response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertNotEmpty($errors['errors']);

        // Would make test brittle if there was a lot of back and forth on request validation stuff
        $this->assertContains("Points transactions cannot leave a user with a negative points total", $errors['errors']);
    }

    public function testRedeemPointsEndpointRequiresPointsAndDescription()
    {
        $user = new User('test@example.com', 'test', 100, 1);
        $userRepository = $this->createMock(InMemoryUserRepository::class);
        $userRepository->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($user);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(null);

        $controller = new TransactionController(new InMemoryTransactionRepository(), $userRepository);

        $response = $controller->redeemPoints($request, new Response(), 1);
        $errors = json_decode($response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertNotEmpty($errors['errors']);

        // Would make test brittle if there was a lot of back and forth on request validation stuff
        $this->assertContains("'points' field is required", $errors['errors']);
        $this->assertContains("'description' field is required", $errors['errors']);
    }

    public function testFailureToCreateEarnTransactionDoesNotAlterPoints()
    {
        $pointsParameter = 10;
        $descriptionParameter = 'description';
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['points' => $pointsParameter, 'description' => $descriptionParameter]);

        $balance = 100;
        $user = new User('test@example.com', 'test', $balance, 1);

        $userRepository = $this->createMock(InMemoryUserRepository::class);
        $userRepository->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($user);

        $userRepository->expects($this->never())->method('updatePoints');

        $transactionRepository = $this->createMock(InMemoryTransactionRepository::class);
        $transactionRepository->expects($this->once())
            ->method('create')
            ->with($descriptionParameter, $pointsParameter, $user)
            ->willThrowException(new Exception('Test Exception'));

        $controller = new TransactionController($transactionRepository, $userRepository);

        $response = $controller->earnPoints($request, new Response(), 1);

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testFailureToCreateRedeemTransactionDoesNotAlterPoints()
    {
        $pointsParameter = 10;
        $descriptionParameter = 'description';
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['points' => $pointsParameter, 'description' => $descriptionParameter]);

        $balance = 100;
        $user = new User('test@example.com', 'test', $balance, 1);

        $userRepository = $this->createMock(InMemoryUserRepository::class);
        $userRepository->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($user);

        $userRepository->expects($this->never())->method('updatePoints');

        $transactionRepository = $this->createMock(InMemoryTransactionRepository::class);
        $transactionRepository->expects($this->once())
            ->method('create')
            ->with($descriptionParameter, -$pointsParameter, $user)
            ->willThrowException(new Exception('Test Exception'));

        $controller = new TransactionController($transactionRepository, $userRepository);

        $response = $controller->redeemPoints($request, new Response(), 1);

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testEarningPointsIncreasesUserPointsBalance()
    {
        $pointsParameter = 10;
        $descriptionParameter = 'description';
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['points' => $pointsParameter, 'description' => $descriptionParameter]);

        $balance = 100;
        $user = new User('test@example.com', 'test', $balance, 1);

        $userRepository = $this->createMock(InMemoryUserRepository::class);
        $userRepository->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($user);

        $userRepository->expects($this->once())
            ->method('updatePoints')
            ->with($user, $balance + $pointsParameter);

        $transactionRepository = $this->createMock(InMemoryTransactionRepository::class);
        $transactionRepository->expects($this->once())
            ->method('create')
            ->with($descriptionParameter, $pointsParameter, $user)
            ->willReturn(new Transaction($descriptionParameter, $pointsParameter, $user));

        $controller = new TransactionController($transactionRepository, $userRepository);

        $response = $controller->earnPoints($request, new Response(), 1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRedeemingPointsDecreasesUserPointsBalance()
    {
        $pointsParameter = 10;
        $descriptionParameter = 'description';
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['points' => $pointsParameter, 'description' => $descriptionParameter]);

        $balance = 100;
        $user = new User('test@example.com', 'test', $balance, 1);

        $userRepository = $this->createMock(InMemoryUserRepository::class);
        $userRepository->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($user);

        $userRepository->expects($this->once())
            ->method('updatePoints')
            ->with($user, $balance - $pointsParameter);

        $transactionRepository = $this->createMock(InMemoryTransactionRepository::class);
        $transactionRepository->expects($this->once())
            ->method('create')
            ->with($descriptionParameter, -$pointsParameter, $user)
            ->willReturn(new Transaction($descriptionParameter, $pointsParameter, $user));

        $controller = new TransactionController($transactionRepository, $userRepository);

        $response = $controller->redeemPoints($request, new Response(), 1);

        $this->assertEquals(200, $response->getStatusCode());
    }
}