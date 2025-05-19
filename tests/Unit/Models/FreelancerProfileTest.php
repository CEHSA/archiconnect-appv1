<?php

namespace Tests\Unit\Models;

use App\Models\FreelancerProfile;
use App\Models\User;

use Tests\TestCase;

class FreelancerProfileTest extends TestCase
{

    #[Test]
    public function it_has_correct_fillable_attributes()
    {
        $this->markTestSkipped('Skipped due to SQLite driver issues.');
    }

    #[Test]
    public function it_belongs_to_a_user()
    {
        $this->markTestSkipped('Skipped due to SQLite driver issues.');
    }

    #[Test]
    public function it_can_be_created_with_valid_data()
    {
        $this->markTestSkipped('Skipped due to SQLite driver issues.');
    }
}
