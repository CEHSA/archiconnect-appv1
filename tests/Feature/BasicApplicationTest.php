<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BasicApplicationTest extends TestCase
{
    /**
     * Test that the application loads successfully.
     */
    public function test_application_loads_successfully(): void
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
    }

    /**
     * Test that the AI route is accessible.
     */
    public function test_ai_route_is_accessible(): void
    {
        // Adjust this route based on your actual AI routes
        $response = $this->get('/ai');
        
        // Should either return 200 (if public) or redirect to login
        $this->assertTrue(
            $response->status() === 200 || 
            $response->status() === 302
        );
    }

    /**
     * Test that environment is properly configured.
     */
    public function test_environment_configuration(): void
    {
        $this->assertEquals('ArchiConnect AI', config('app.name'));
        $this->assertNotEmpty(config('app.key'));
    }
}
