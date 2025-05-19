<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    #[Test]
    public function it_allows_access_to_users_with_required_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $request = Request::create('/admin/dashboard', 'GET');
        $middleware = new RoleMiddleware();

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        }, 'admin');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    #[Test]
    public function it_denies_access_to_users_without_required_role()
    {
        $client = User::factory()->create(['role' => 'client']);
        $this->actingAs($client);

        $request = Request::create('/admin/dashboard', 'GET');
        $middleware = new RoleMiddleware();

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        }, 'admin');

        $this->assertEquals(403, $response->getStatusCode());
    }

    #[Test]
    public function it_allows_access_to_users_with_one_of_multiple_required_roles()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $this->actingAs($freelancer);

        $request = Request::create('/dashboard', 'GET');
        $middleware = new RoleMiddleware();

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        }, 'admin|freelancer');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }

    #[Test]
    public function it_denies_access_to_users_without_any_of_multiple_required_roles()
    {
        $client = User::factory()->create(['role' => 'client']);
        $this->actingAs($client);

        $request = Request::create('/dashboard', 'GET');
        $middleware = new RoleMiddleware();

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        }, 'admin|freelancer');

        $this->assertEquals(403, $response->getStatusCode());
    }

    #[Test]
    public function it_redirects_guests_to_login()
    {
        $request = Request::create('/admin/dashboard', 'GET');
        $middleware = new RoleMiddleware();

        $response = $middleware->handle($request, function () {
            return new Response('OK');
        }, 'admin');

        $this->assertTrue($response->isRedirect(route('login')));
    }
}
