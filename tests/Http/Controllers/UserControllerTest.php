<?php

namespace Test\Http\Controllers;

use App\Domain\Models\User;
use App\Domain\Repositories\User\InMemoryUserRepository;
use App\Http\Controllers\UserController;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Class used to test all the major breakpoints of the UserController
 */
class UserControllerTest extends TestCase
{
    public function testIndexFunctionReturnsAllUsers()
    {
        $users = [new User('test@example.com', 'test', 1, 1)];
        $repository = new InMemoryUserRepository($users);
        $controller = new UserController($repository);

        $response = $controller->index(new Response());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($users), $response->getBody());
    }

    public function testUserCreateRequiresEmailAndName()
    {
        $controller = new UserController(new InMemoryUserRepository());

        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method('getParsedBody')->willReturn(null);

        $response = $controller->create($request, new Response());
        $errors = json_decode($response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertNotEmpty($errors['errors']);

        // Would make test brittle if there was a lot of back and forth on request validation stuff
        $this->assertContains("'email' field is required", $errors['errors']);
        $this->assertContains("'name' field is required", $errors['errors']);
    }

    public function testUserCreationFailureReturns500()
    {
        $repository = $this->createMock(InMemoryUserRepository::class);
        $repository->expects($this->once())
            ->method('create')
            ->willThrowException(new \Exception('test exception'));

        $controller = new UserController($repository);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['email' => 'test@example.com', 'name' => 'name']);

        $response = $controller->create($request, new Response());

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testValidUserDataCreatesUser()
    {
        $controller = new UserController(new InMemoryUserRepository());

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['email' => 'test@example.com', 'name' => 'name']);

        $response = $controller->create($request, new Response());

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testMissingUserIdOnDeletionReturns404()
    {
        $controller = new UserController(new InMemoryUserRepository());

        $response = $controller->delete(new Response(), -1);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testSuccessfullyDeletingUserReturns200()
    {
        $controller = new UserController(new InMemoryUserRepository());

        $response = $controller->delete(new Response(), 1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testEarnPointsEndpointRequiresPointsAndDescription()
    {
        $user = new User('test@example.com', 'test', 100, 1);
        $repository = $this->createMock(InMemoryUserRepository::class);
        $repository->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($user);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(null);

        $controller = new UserController($repository);

        $response = $controller->earnPoints($request, new Response(), 1);
        $errors = json_decode($response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertNotEmpty($errors['errors']);

        // Would make test brittle if there was a lot of back and forth on request validation stuff
        $this->assertContains("'points' field is required", $errors['errors']);
        $this->assertContains("'description' field is required", $errors['errors']);
    }

    public function testRedeemPointsEndpointRequiresPointsAndDescription()
    {
        $user = new User('test@example.com', 'test', 100, 1);
        $repository = $this->createMock(InMemoryUserRepository::class);
        $repository->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($user);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(null);

        $controller = new UserController($repository);

        $response = $controller->redeemPoints($request, new Response(), 1);
        $errors = json_decode($response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertNotEmpty($errors['errors']);

        // Would make test brittle if there was a lot of back and forth on request validation stuff
        $this->assertContains("'points' field is required", $errors['errors']);
        $this->assertContains("'description' field is required", $errors['errors']);
    }

    public function testEarningPointsIncreasesUserPointsBalance()
    {
        $balance = 100;
        $pointsParameter = 10;

        $user = new User('test@example.com', 'test', $balance, 1);

        $repository = $this->createMock(InMemoryUserRepository::class);
        $repository->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($user);

        $repository->expects($this->once())
            ->method('updatePoints')
            ->with($user, $balance + $pointsParameter);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['points' => 10, 'description' => 'description']);

        $controller = new UserController($repository);

        $response = $controller->earnPoints($request, new Response(), 1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRedeemingPointsDecreasesUserPointsBalance()
    {
        $balance = 100;
        $pointsParameter = 10;

        $user = new User('test@example.com', 'test', $balance, 1);

        $repository = $this->createMock(InMemoryUserRepository::class);
        $repository->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($user);

        $repository->expects($this->once())
            ->method('updatePoints')
            ->with($user, $balance - $pointsParameter);

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['points' => 10, 'description' => 'description']);

        $controller = new UserController($repository);

        $response = $controller->redeemPoints($request, new Response(), 1);

        $this->assertEquals(200, $response->getStatusCode());
    }
}