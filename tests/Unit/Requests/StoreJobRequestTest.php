<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreJobRequest;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Validator;


use Tests\TestCase;
class StoreJobRequestTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function it_passes_with_valid_data()
    {
        $request = new StoreJobRequest();
        
        $validator = Validator::make([
            'title' => 'Test Job',
            'description' => 'This is a test job description',
            'budget' => 1000,
            'skills_required' => 'PHP, Laravel',
        ], $request->rules());
        
        $this->assertFalse($validator->fails());
    }
    
    #[Test]
    public function it_requires_a_title()
    {
        $request = new StoreJobRequest();
        
        $validator = Validator::make([
            'description' => 'This is a test job description',
            'budget' => 1000,
            'skills_required' => 'PHP, Laravel',
        ], $request->rules());
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('title'));
    }
    
    #[Test]
    public function it_requires_a_description()
    {
        $request = new StoreJobRequest();
        
        $validator = Validator::make([
            'title' => 'Test Job',
            'budget' => 1000,
            'skills_required' => 'PHP, Laravel',
        ], $request->rules());
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('description'));
    }
    
    #[Test]
    public function budget_must_be_numeric_if_provided()
    {
        $request = new StoreJobRequest();
        
        $validator = Validator::make([
            'title' => 'Test Job',
            'description' => 'This is a test job description',
            'budget' => 'not-a-number',
            'skills_required' => 'PHP, Laravel',
        ], $request->rules());
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('budget'));
    }
    
    #[Test]
    public function budget_must_be_at_least_zero()
    {
        $request = new StoreJobRequest();
        
        $validator = Validator::make([
            'title' => 'Test Job',
            'description' => 'This is a test job description',
            'budget' => -100,
            'skills_required' => 'PHP, Laravel',
        ], $request->rules());
        
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('budget'));
    }
}
