<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AppSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_app_name(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $this->actingAs($owner)->post(route('settings.update'), [
            'app_name' => 'Kabar Banten Plus',
            'company_name' => 'PT Kabar Banten',
            'tagline' => 'Keuangan Daerah',
        ])->assertRedirect(route('settings.index'));

        $this->assertSame('Kabar Banten Plus', AppSettings::appName());
        $this->assertSame('PT Kabar Banten', AppSettings::companyName());

        $this->actingAs($owner)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Kabar Banten Plus')
            ->assertSee('PT Kabar Banten');
    }

    public function test_viewer_cannot_update_settings(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);

        $this->actingAs($viewer)->post(route('settings.update'), [
            'app_name' => 'Hacked',
            'company_name' => 'Hacked',
            'tagline' => 'Hacked',
        ])->assertForbidden();
    }
}
