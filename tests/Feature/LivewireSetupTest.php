<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireSetupTest extends TestCase
{
    use RefreshDatabase; // the footer reads published pages

    public function test_full_page_components_use_the_site_layout(): void
    {
        Livewire::component('setup-check', SetupCheckComponent::class);
        Route::livewire('/__livewire-setup-check', SetupCheckComponent::class)->middleware('web');

        $response = $this->get('/__livewire-setup-check');

        $response->assertOk();
        $response->assertSee('<html lang="fa" dir="rtl">', false);
        $response->assertSee('<header', false);
        $response->assertSee('<footer', false);
        $response->assertSee('livewire.js', false);
        $response->assertSee('شمارنده: 0', false);
    }

    public function test_component_actions_work(): void
    {
        Livewire::test(SetupCheckComponent::class)
            ->call('increment')
            ->call('increment')
            ->assertSet('count', 2)
            ->assertSee('شمارنده: 2');
    }
}

class SetupCheckComponent extends Component
{
    public int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }

    public function render(): string
    {
        return '<div><button wire:click="increment">+</button> شمارنده: {{ $count }}</div>';
    }
}
