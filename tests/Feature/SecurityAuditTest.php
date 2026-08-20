<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\SecurityAudit;
use App\Models\AuditLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_project_seals_a_genesis_audit_entry(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);

        $project = Project::create([
            'user_id' => $owner->id,
            'titre' => 'Projet audité',
            'description' => 'Description de test',
            'categorie' => 'test',
            'statut' => 'en_etude',
            'besoin_financier_target' => 750000,
            'besoin_financier_actuel' => 0,
        ]);

        $entry = AuditLog::firstOrFail();
        $this->assertSame('Création de projet', $entry->action);
        $this->assertSame('INITIAL_HASH_MUTUALIS_RIGO_2026', $entry->hash_parent);
        $this->assertSame(
            hash('sha256', $entry->hash_parent.$entry->donnees_auditees),
            $entry->hash_actuel
        );
        $this->assertStringContainsString((string) $project->id, $entry->donnees_auditees);
    }

    public function test_each_audit_block_chains_to_the_hash_of_the_previous_one(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);

        $project = Project::create([
            'user_id' => $owner->id,
            'titre' => 'Projet chaîné',
            'description' => 'Description de test',
            'categorie' => 'test',
            'statut' => 'en_etude',
            'besoin_financier_target' => 200000,
            'besoin_financier_actuel' => 0,
        ]);

        $project->update(['statut' => 'en_cours_de_mutualisation']);

        $this->assertSame(2, AuditLog::count());

        $genesis = AuditLog::oldest('id')->first();
        $second = AuditLog::latest('id')->first();

        $this->assertSame('Changement de statut du projet', $second->action);
        $this->assertSame($genesis->hash_actuel, $second->hash_parent);
        $this->assertNotSame($genesis->hash_actuel, $second->hash_actuel);
    }

    public function test_updating_a_project_without_changing_its_status_does_not_seal_a_new_block(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);

        $project = Project::create([
            'user_id' => $owner->id,
            'titre' => 'Projet stable',
            'description' => 'Description de test',
            'categorie' => 'test',
            'statut' => 'en_etude',
            'besoin_financier_target' => 200000,
            'besoin_financier_actuel' => 0,
        ]);

        $project->update(['description' => 'Nouvelle description, sans changement de statut.']);

        $this->assertSame(1, AuditLog::count());
    }

    public function test_the_security_audit_screen_lists_projects_and_their_sealed_financial_target(): void
    {
        $admin = User::factory()->create(['role' => 'admin_systeme']);
        $owner = User::factory()->create(['role' => 'chef_projet']);

        Project::create([
            'user_id' => $owner->id,
            'titre' => 'Projet visible dans le registre',
            'description' => 'Description de test',
            'categorie' => 'test',
            'statut' => 'en_etude',
            'besoin_financier_target' => 300000,
            'besoin_financier_actuel' => 0,
        ]);

        Livewire::actingAs($admin)
            ->test(SecurityAudit::class)
            ->assertSee('Carnet de Confiance Sécurisé')
            ->assertSee('Création de projet')
            ->assertSee('300 000');
    }
}
