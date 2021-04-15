<?php

namespace Tests\Feature\RdtApplicants;

use App\Entities\RdtApplicant;
use App\Entities\User;
use Tests\TestCase;

class ShowRdtApplicantTest extends TestCase
{
    /** @test */
    public function can_show_applicant()
    {
        $user = new User();
        $user->assignPermissions(['view-applicants']);

        /**
         * @var RdtApplicant $rdtApplicant
         */
        $rdtApplicant = factory(RdtApplicant::class)->create();

        $this->actingAs($user)
            ->getJson("/api/rdt/applicants/{$rdtApplicant->id}")
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => ['name', 'nik', 'address', 'registration_at']
            ])
            ->assertJsonFragment([
                'name'    => $rdtApplicant->name,
                'nik'     => $rdtApplicant->nik,
                'address' => $rdtApplicant->address,
                'registration_at' => $rdtApplicant->registration_at,
            ]);
    }

    /** @test */
    public function cannot_show_applicant_unauthenticated()
    {
        /**
         * @var RdtApplicant $rdtApplicant
         */
        $rdtApplicant = factory(RdtApplicant::class)->create();

        $this->getJson("/api/rdt/applicants/{$rdtApplicant->id}")->assertUnauthorized();
    }

    /** @test */
    public function cannot_show_applicant_no_permission()
    {
        $user = new User();

        /**
         * @var RdtApplicant $rdtApplicant
         */
        $rdtApplicant = factory(RdtApplicant::class)->create();

        $this->actingAs($user)
            ->getJson("/api/rdt/applicants/{$rdtApplicant->id}")
            ->assertForbidden();
    }
}
