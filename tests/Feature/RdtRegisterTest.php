<?php

namespace Tests\Feature;

use App\Enums\PersonCaseStatusEnum;
use Tests\TestCase;

class RdtRegisterTest extends TestCase
{
    /** @test */
    public function can_register()
    {
        $this->postJson('/api/rdt/register', [
            'g-recaptcha-response' => 'X',
            'name'                 => 'Test User',
            'nik'                  => '3518814601106254',
            'address'              => 'Address',
            'city_code'            => '32.73',
            'district_code'        => '32.73.07',
            'village_code'         => '32.73.07.1002',
            'email'                => 'test@email.org',
            'phone_number'         => '62857123456',
            'gender'               => 'M',
            'birth_date'           => '1988-11-15',
            'workplace_name'       => 'Tempat Kerja',
            'symptoms'             => [1, 2, 3],
            'symptoms_notes'       => 'Notes',
            'city_visited'         => 'jakarta',
            'congenital_disease'   => [1,2,3],
            'have_interacted'      => 'NO',
            'person_status'        => PersonCaseStatusEnum::CLOSE_CONTACT(),
            'birth_place'          => 'Kota Bandung'
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['registration_code', 'download_url']);
    }

    /** @test */
    public function cannot_register_nik_invalid()
    {
        $this->postJson('/api/rdt/register', [
            'nik' => '0000',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['nik']);
    }

    /** @test */
    public function cannot_register_required()
    {
        $this->postJson('/api/rdt/register')
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response', 'nik', 'name', 'address', 'city_code', 'district_code', 'village_code', 'phone_number',
                'gender', 'birth_date', 'symptoms'
            ]);
    }
}
