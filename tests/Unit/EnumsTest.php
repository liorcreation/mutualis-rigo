<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\ContractStatus;
use App\Enums\ContributionStatus;
use App\Enums\ContributionType;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Enums\ReservationStatus;
use App\Enums\UserRole;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EnumsTest extends TestCase
{
    /**
     * @return list<array{0: string}>
     */
    public static function statusEnums(): array
    {
        return [
            [ProjectStatus::class],
            [ContributionStatus::class],
            [ContractStatus::class],
            [PaymentStatus::class],
            [ReservationStatus::class],
            [ContributionType::class],
        ];
    }

    #[DataProvider('statusEnums')]
    public function test_every_case_has_a_non_empty_label(string $enumClass): void
    {
        foreach ($enumClass::cases() as $case) {
            $this->assertNotSame('', $case->label());
        }
    }

    #[DataProvider('statusEnums')]
    public function test_every_case_has_a_valid_badge_color(string $enumClass): void
    {
        $allowed = ['indigo', 'emerald', 'amber', 'fuchsia', 'rose', 'slate'];

        foreach ($enumClass::cases() as $case) {
            $this->assertContains($case->color(), $allowed);
        }
    }

    public function test_project_status_allowed_transitions_form_a_forward_only_path(): void
    {
        $this->assertSame(
            ['en_cours_de_mutualisation', 'cloture'],
            ProjectStatus::EN_ETUDE->allowedTransitions()->all()
        );
        $this->assertSame(
            ['cloture'],
            ProjectStatus::EN_COURS_DE_MUTUALISATION->allowedTransitions()->all()
        );
        $this->assertTrue(ProjectStatus::CLOTURE->allowedTransitions()->isEmpty());
        $this->assertTrue(ProjectStatus::BROUILLON->allowedTransitions()->isEmpty());
    }

    public function test_contribution_type_allowed_for_matches_the_role_perimeter(): void
    {
        $this->assertSame(
            ['financier', 'competence', 'materiel'],
            ContributionType::allowedFor(UserRole::ADMIN_SYSTEME)
        );
        $this->assertSame(['financier'], ContributionType::allowedFor(UserRole::RESPONSABLE_FINANCIER));
        $this->assertSame(['competence'], ContributionType::allowedFor(UserRole::RESPONSABLE_RH));
        $this->assertSame(['materiel'], ContributionType::allowedFor(UserRole::CHEF_PROJET));
        $this->assertSame([], ContributionType::allowedFor(UserRole::PERSONNE_PHYSIQUE));
    }

    public function test_user_role_group_helpers_cover_the_expected_and_only_the_expected_roles(): void
    {
        $financeOrManagement = array_filter(UserRole::cases(), fn (UserRole $r): bool => $r->isFinanceOrManagement());
        $this->assertEqualsCanonicalizing(
            [UserRole::RESPONSABLE_FINANCIER, UserRole::TOP_MANAGEMENT, UserRole::ADMIN_SYSTEME],
            $financeOrManagement
        );

        $backOffice = array_filter(UserRole::cases(), fn (UserRole $r): bool => $r->canAccessBackOffice());
        $this->assertEqualsCanonicalizing(
            [UserRole::ADMIN_SYSTEME, UserRole::TOP_MANAGEMENT, UserRole::RESPONSABLE_FINANCIER, UserRole::RESPONSABLE_RH, UserRole::CHEF_PROJET],
            $backOffice
        );

        $reviewers = array_filter(UserRole::cases(), fn (UserRole $r): bool => $r->canReviewProjects());
        $this->assertEqualsCanonicalizing(
            [UserRole::ADMIN_SYSTEME, UserRole::TOP_MANAGEMENT, UserRole::CHEF_PROJET],
            $reviewers
        );

        // canReviewProjects is a strict subset of canAccessBackOffice, never a superset.
        foreach ($reviewers as $role) {
            $this->assertTrue($role->canAccessBackOffice());
        }
    }
}
