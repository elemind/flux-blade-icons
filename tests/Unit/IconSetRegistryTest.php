<?php

declare(strict_types=1);

use Elemind\FluxBladeIcons\IconSetRegistry;

it('returns all icon sets', function (): void {
    $sets = app(IconSetRegistry::class)->all();

    expect($sets)->toBeArray()->not->toBeEmpty();

    foreach ($sets as $set) {
        expect($set)->toHaveKeys(['name', 'url', 'svg']);
    }
});

it('finds a set by key', function (): void {
    $set = app(IconSetRegistry::class)->get('blade-feather-icons');

    expect($set)
        ->not->toBeNull()
        ->and($set['name'])->toBe('Blade Feather Icons');
});

it('returns null for unknown key', function (): void {
    expect(app(IconSetRegistry::class)->get('non-existent'))->toBeNull();
});

it('checks if a set exists', function (): void {
    $registry = app(IconSetRegistry::class);

    expect($registry->has('blade-heroicons'))->toBeTrue();
    expect($registry->has('non-existent'))->toBeFalse();
});

it('returns all keys', function (): void {
    $keys = app(IconSetRegistry::class)->keys();

    expect($keys)
        ->toBeArray()
        ->toContain('blade-feather-icons')
        ->toContain('blade-heroicons');
});

it('searches sets by name', function (): void {
    $results = app(IconSetRegistry::class)->searchByName('feather');

    expect($results)
        ->toHaveKey('blade-feather-icons')
        ->and($results['blade-feather-icons'])->toBe('Blade Feather Icons');
});

it('returns all sets when searching with empty string', function (): void {
    $registry = app(IconSetRegistry::class);
    $results = $registry->searchByName('');

    expect(count($results))->toBe(count($registry->all()));
});

it('reads default icon sets from configuration', function (): void {
    config()->set('flux-blade-icons.default_icon_sets', [
        'config-default-icons' => [
            'name' => 'Config Default Icons',
            'url' => 'https://github.com/elemind/config-default-icons',
            'svg' => 'https://raw.githubusercontent.com/elemind/config-default-icons/main/resources/svg/',
        ],
    ]);
    config()->set('flux-blade-icons.icon_sets', []);

    $registry = app(IconSetRegistry::class);

    expect($registry->keys())->toBe(['config-default-icons']);
    expect($registry->get('config-default-icons')['name'])->toBe('Config Default Icons');
    expect($registry->has('blade-feather-icons'))->toBeFalse();
});

it('merges configured icon sets with the configured defaults', function (): void {
    config()->set('flux-blade-icons.icon_sets', [
        'custom-icons' => [
            'name' => 'Custom Icons',
            'url' => 'https://github.com/elemind/custom-icons',
            'svg' => 'https://raw.githubusercontent.com/elemind/custom-icons/main/resources/svg/',
        ],
    ]);

    $registry = app(IconSetRegistry::class);

    expect($registry->has('custom-icons'))->toBeTrue();
    expect($registry->get('custom-icons')['name'])->toBe('Custom Icons');
    expect($registry->has('blade-feather-icons'))->toBeTrue();
});

it('allows configured icon sets to override default icon sets', function (): void {
    config()->set('flux-blade-icons.icon_sets', [
        'blade-feather-icons' => [
            'name' => 'Overridden Feather Icons',
            'url' => 'https://github.com/elemind/overridden-feather-icons',
            'svg' => 'https://raw.githubusercontent.com/elemind/overridden-feather-icons/main/resources/svg/',
        ],
    ]);

    $registry = app(IconSetRegistry::class);

    expect($registry->get('blade-feather-icons'))
        ->not->toBeNull()
        ->and($registry->get('blade-feather-icons')['name'])->toBe('Overridden Feather Icons')
        ->and($registry->get('blade-feather-icons')['url'])->toBe('https://github.com/elemind/overridden-feather-icons');
});
