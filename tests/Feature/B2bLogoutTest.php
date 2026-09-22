<?php

namespace Tests\Feature;

use App\Models\B2bUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class B2bLogoutTest extends TestCase
{
    use DatabaseTransactions;

    private function salon(): B2bUser
    {
        return B2bUser::forceCreate([
            'email' => 'salon-test@example.com', 'password' => bcrypt('secret'),
            'contact_name' => 'Test', 'salon_name' => 'Test salón', 'status' => 'active', 'approved_at' => now(),
        ]);
    }

    public function test_post_logout_signs_the_salon_out_and_redirects_home(): void
    {
        $this->actingAs($this->salon(), 'b2b');

        $this->post('/b2b/logout')->assertRedirect(route('home'));
        $this->assertGuest('b2b');
    }

    public function test_get_logout_works_without_javascript(): void
    {
        $this->actingAs($this->salon(), 'b2b');

        $this->get('/b2b/logout')->assertRedirect(route('home'));
        $this->assertGuest('b2b');
    }

    public function test_logout_as_guest_is_harmless(): void
    {
        $this->get('/b2b/logout')->assertRedirect(route('home'));
    }
}
