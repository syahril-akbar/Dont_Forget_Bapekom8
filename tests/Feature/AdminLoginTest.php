<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Filament\Auth\Pages\Login;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $this->get('/admin/login')->assertStatus(200);
    }

    public function test_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'admin@dontforget.test',
            'password' => bcrypt('password'),
        ]);

        Livewire::test(Login::class)
            ->set('data.email', 'admin@dontforget.test')
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_wrong_password_rejected(): void
    {
        User::factory()->create([
            'email'    => 'admin@dontforget.test',
            'password' => bcrypt('password'),
        ]);

        Livewire::test(Login::class)
            ->set('data.email', 'admin@dontforget.test')
            ->set('data.password', 'wrongpassword')
            ->call('authenticate')
            ->assertHasErrors(['data.email']);

        $this->assertGuest();
    }
}
