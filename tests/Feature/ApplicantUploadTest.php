<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApplicantUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_applicant_manually()
    {
        // 1. Create Admin
        $admin = \App\Models\User::factory()->create();

        // 2. Submit Form Data
        $data = [
            'jamb_reg_no' => 'TEST2025',
            'full_name' => 'Test Applicant',
            'email' => 'test@applicant.com',
            'jamb_score' => 250,
            'course_applied' => 'Computer Science',
            'state' => 'Lagos'
        ];

        $response = $this->actingAs($admin)
                         ->post('/admin/applicants/manual', $data);

        // 3. Assertions
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('applicants', [
            'email' => 'test@applicant.com',
            'jamb_score' => 250,
            'aggregate' => 62.5 // 250 / 4
        ]);
    }

    public function test_upload_validation_requires_email()
    {
        $admin = \App\Models\User::factory()->create();
        
        $response = $this->actingAs($admin)
                         ->post('/admin/applicants/manual', [
                             'full_name' => 'No Email User'
                         ]);

        $response->assertSessionHasErrors('email');
    }
}
