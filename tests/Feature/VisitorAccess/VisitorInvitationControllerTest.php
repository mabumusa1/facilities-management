<?php

namespace Tests\Feature\VisitorAccess;

use Tests\TestCase;

class VisitorInvitationControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
