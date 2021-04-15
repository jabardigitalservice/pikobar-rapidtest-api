<?php

namespace Tests\Feature\Setting;

use App\Entities\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    /**
     * @test
     */
    public function can_check_current_user()
    {
        $user = new User();

        $response = $this->actingAs($user)->get('/api/user');

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function failed_to_check_current_user_because_unauthorized()
    {
        $response = $this->get('/api/user');

        $response->assertUnauthorized();
    }
}
