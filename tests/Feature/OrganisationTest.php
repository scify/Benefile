<?php

namespace Tests\Feature;

use App\Models\User;

/**
 * The organisation that uses BeneFile comes from config/benefile.php.
 */
class OrganisationTest extends FeatureTestCase
{
    public function test_the_screens_show_the_configured_organisation(): void
    {
        config([
            'benefile.organisation.name' => 'Example NGO',
            'benefile.organisation.logo' => 'images/example-logo.png',
        ]);

        $response = $this->actingAs(User::factory()->role(User::ROLE_DOCTOR)->create())->get('/benefiter/1/medical-folder');

        $response->assertOk()
            ->assertSee('Example NGO')
            ->assertSee('images/example-logo.png')
            ->assertDontSee('praksis_logo_black.png');
    }

    public function test_without_a_contact_url_there_is_no_contact_link(): void
    {
        config(['benefile.organisation.contact_url' => 'https://example.org/contact']);
        $this->actingAs(User::factory()->deactivated()->create())->get('/disabledUser')->assertSee('https://example.org/contact');

        config(['benefile.organisation.contact_url' => '']);
        $this->actingAs(User::factory()->deactivated()->create())->get('/disabledUser')->assertDontSee('target="_blank"', false);
    }
}
