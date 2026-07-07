<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VcisoPageDesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_vciso_page_displays_redesigned_member_hub(): void
    {
        $role = UserRole::create(['role_name' => 'User']);
        $user = User::factory()->create([
            'first_name' => 'Saudi',
            'last_name' => 'CISO',
            'role_id' => $role->id,
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($user)->get('/vciso');

        $response
            ->assertStatus(200)
            ->assertSee('vciso-page', false)
            ->assertSee('vciso-hero', false)
            ->assertSee('CISO Toolkit')
            ->assertSee('CISO Education')
            ->assertSee('Hot Topics for CISO')
            ->assertSee('People')
            ->assertSee('Processes')
            ->assertSee('Products')
            ->assertSee('href="'.route('ciso-toolkit.index').'"', false)
            ->assertSee('href="'.route('ciso-education.index').'"', false)
            ->assertSee('href="'.route('hot-topics.index').'"', false)
            ->assertSee('href="'.route('people.index').'"', false)
            ->assertSee('href="'.route('ciso-process.index').'"', false)
            ->assertSee('href="'.route('ciso-products.index').'"', false)
            ->assertDontSee('branch-connector')
            ->assertDontSee('ðŸ');
    }
}
