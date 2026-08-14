<?php

namespace Tests\Feature\Auth;

use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_registration_is_disabled(): void
    {
        $this->assertFalse(Features::enabled(Features::registration()));

        $this->get('/register')->assertNotFound();
    }
}
