<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return list<array{0: string, 1: string}>
     */
    public static function seededAccounts(): array
    {
        return [
            ['contact@liorcreation.com', 'personne_morale'],
            ['steve@example.com', 'personne_physique'],
            ['chef@rigo.com', 'chef_projet'],
            ['admin@rigo.com', 'admin_systeme'],
        ];
    }

    #[DataProvider('seededAccounts')]
    public function test_seeded_account_can_authenticate_with_the_documented_password(string $email, string $role): void
    {
        $this->seed(UserSeeder::class);

        $user = User::where('email', $email)->firstOrFail();
        $this->assertSame($role, $user->role->value);

        $this->assertTrue(Auth::attempt(['email' => $email, 'password' => 'password123']));
    }

    public function test_running_the_seeder_twice_keeps_exactly_one_row_per_account_and_a_working_password(): void
    {
        $this->seed(UserSeeder::class);

        // Simule un compte corrompu (mot de passe/rôle obsolètes) avant de re-seeder.
        User::where('email', 'chef@rigo.com')->update([
            'password' => Hash::make('un-ancien-mot-de-passe'),
        ]);

        $this->seed(UserSeeder::class);

        $this->assertSame(1, User::where('email', 'chef@rigo.com')->count());
        $this->assertSame(4, User::count());
        $this->assertTrue(Auth::attempt(['email' => 'chef@rigo.com', 'password' => 'password123']));
    }
}
