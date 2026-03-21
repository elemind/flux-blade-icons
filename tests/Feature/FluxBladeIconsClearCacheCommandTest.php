<?php

declare(strict_types=1);

use Elemind\FluxBladeIcons\IconSetRegistry;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    config()->set('flux-blade-icons.icon_sets', []);

    Cache::forget('blade-icons:blade-feather-icons');
    Cache::forget('blade-icons:blade-bootstrap-icons');
    Cache::forget('blade-icons:custom-icons');
    Cache::forget('unrelated-cache-key');
});

it('clears the cache for a single icon set', function (): void {
    Cache::put('blade-icons:blade-feather-icons', ['plus'], 86400);
    Cache::put('blade-icons:blade-bootstrap-icons', ['circle'], 86400);

    $this->artisan('flux:blade-icons:clear-cache', ['--set' => 'blade-feather-icons'])
        ->expectsPromptsInfo('Cleared cached icon list for blade-feather-icons.')
        ->assertSuccessful();

    expect(Cache::get('blade-icons:blade-feather-icons'))->toBeNull();
    expect(Cache::get('blade-icons:blade-bootstrap-icons'))->toBe(['circle']);
});

it('clears the cached icon lists for all registered icon sets without touching unrelated cache entries', function (): void {
    config()->set('flux-blade-icons.icon_sets', [
        'custom-icons' => [
            'name' => 'Custom Icons',
            'url' => 'https://icons.example.com',
            'svg' => 'https://cdn.example.com/icons/',
        ],
    ]);

    Cache::put('blade-icons:blade-feather-icons', ['plus'], 86400);
    Cache::put('blade-icons:custom-icons', ['camera'], 86400);
    Cache::put('unrelated-cache-key', 'keep-me', 86400);

    $registeredSetCount = count(app(IconSetRegistry::class)->keys());

    $this->artisan('flux:blade-icons:clear-cache')
        ->expectsPromptsInfo("Cleared cached icon lists for {$registeredSetCount} icon sets.")
        ->assertSuccessful();

    expect(Cache::get('blade-icons:blade-feather-icons'))->toBeNull();
    expect(Cache::get('blade-icons:custom-icons'))->toBeNull();
    expect(Cache::get('unrelated-cache-key'))->toBe('keep-me');
});

it('fails when trying to clear the cache for an unknown icon set', function (): void {
    $availableSets = implode(', ', app(IconSetRegistry::class)->keys());

    $this->artisan('flux:blade-icons:clear-cache', ['--set' => 'missing-set'])
        ->expectsPromptsError('Unknown icon set: missing-set')
        ->expectsPromptsInfo("Available sets: {$availableSets}")
        ->assertExitCode(1);
});
