# ArchiConnect App Tests

This directory contains tests for the ArchiConnect application. The tests are written using both PHPUnit and Pest testing frameworks.

## Test Structure

The tests are organized into the following directories:

- `Feature`: Contains feature tests that test the application's endpoints and functionality
- `Unit`: Contains unit tests for individual components
- `Pest`: Contains tests written using the Pest testing framework
- `Architecture`: Contains tests that verify the application's architecture

Within these directories, tests are further organized by component type:

- `Models`: Tests for Eloquent models
- `Controllers`: Tests for controllers
- `Middleware`: Tests for middleware
- `Policies`: Tests for authorization policies
- `Events`: Tests for events and listeners
- `Requests`: Tests for form requests and validation
- `Api`: Tests for API endpoints

## Running Tests

### Running All Tests

To run all tests, use the following command:

```bash
php artisan test
```

### Running PHPUnit Tests

To run only PHPUnit tests:

```bash
./vendor/bin/phpunit
```

### Running Pest Tests

To run only Pest tests:

```bash
./vendor/bin/pest
```

### Running Specific Test Files

To run a specific test file:

```bash
php artisan test --filter=JobTest
```

### Running Test Suites

To run a specific test suite:

```bash
php artisan test --testsuite=Models
```

Available test suites:

- Unit
- Feature
- Pest
- Architecture
- Models
- Controllers
- API
- Events
- Middleware
- Policies

### Running Tests with Coverage Report

To run tests with coverage report (requires Xdebug or PCOV):

```bash
php artisan test --coverage
```

For a more detailed coverage report:

```bash
php artisan test --coverage-html reports/
```

### Running Tests in Parallel

To run tests in parallel for faster execution:

```bash
php artisan test --parallel
```

## Writing Tests

### PHPUnit Style

```php
<?php

namespace Tests\Unit\Models;

use App\Models\Job;
use Tests\TestCase;

class JobTest extends TestCase
{
    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $job = new Job();

        $this->assertEquals([
            'user_id',
            'title',
            'description',
            'budget',
            'skills_required',
            'status',
        ], $job->getFillable());
    }
}
```

### Pest Style

```php
<?php

use App\Models\Job;

test('job has correct fillable attributes', function () {
    $job = new Job();

    expect($job->getFillable())->toContain('user_id')
        ->toContain('title')
        ->toContain('description')
        ->toContain('budget')
        ->toContain('skills_required')
        ->toContain('status');
});
```

## Best Practices

1. Use the `RefreshDatabase` trait for tests that interact with the database
2. Use factories to create test data
3. Test both happy paths and edge cases
4. Keep tests focused and small
5. Use descriptive test names that explain what is being tested
6. Use assertions that provide meaningful error messages
7. Avoid testing implementation details, focus on behavior
8. Mock external services and dependencies
9. Use database transactions for complex operations
10. Test authorization and validation rules
11. Test events and listeners separately
12. Use architecture tests to enforce coding standards

## Advanced Testing Techniques

### Testing with Mocks

```php
use Mockery;
use App\Services\PaymentGateway;

test('payment processor uses payment gateway', function () {
    $mockGateway = Mockery::mock(PaymentGateway::class);
    $mockGateway->shouldReceive('processPayment')
        ->once()
        ->with(100, 'USD')
        ->andReturn(true);

    $processor = new PaymentProcessor($mockGateway);
    $result = $processor->makePayment(100, 'USD');

    expect($result)->toBeTrue();
});
```

### Testing Events

```php
use Illuminate\Support\Facades\Event;
use App\Events\JobAssigned;

test('job assignment dispatches event', function () {
    Event::fake();

    $assignment = JobAssignment::factory()->create();

    // Perform the action that should dispatch the event
    $assignment->accept();

    Event::assertDispatched(JobAssigned::class, function ($event) use ($assignment) {
        return $event->jobAssignment->id === $assignment->id;
    });
});
```

### Testing API Endpoints

```php
use Laravel\Sanctum\Sanctum;

test('api returns jobs list', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Job::factory()->count(3)->create();

    $response = $this->getJson('/api/jobs');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});
```

## Troubleshooting

If you encounter issues running tests:

1. Make sure your `.env.testing` file is properly configured
2. Ensure the database connection in `phpunit.xml` is set to use an in-memory SQLite database
3. Check that all required dependencies are installed
4. Run `php artisan config:clear` before running tests
5. If using Pest, ensure the Pest plugin is properly installed
6. For parallel testing issues, try running with `--processes=1` to debug
7. For memory issues, increase PHP memory limit in php.ini
8. For database transaction issues, ensure you're using the RefreshDatabase trait correctly
