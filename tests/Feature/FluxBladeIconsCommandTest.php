<?php

declare(strict_types=1);

use Elemind\FluxBladeIcons\Commands\FluxBladeIconsCommand;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

$strokeSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>';

$fillSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"><path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0z"/></svg>';

$githubContentsResponse = [
    ['name' => 'arrow-left.svg', 'type' => 'file'],
    ['name' => 'arrow-right.svg', 'type' => 'file'],
    ['name' => 'plus.svg', 'type' => 'file'],
    ['name' => 'README.md', 'type' => 'file'],
];

$githubContentsWithSubdirs = [
    ['name' => 'outline', 'type' => 'dir', 'url' => 'https://api.github.com/repos/test/test/contents/resources/svg/outline'],
    ['name' => 'solid', 'type' => 'dir', 'url' => 'https://api.github.com/repos/test/test/contents/resources/svg/solid'],
];

$githubSubdirResponse = [
    ['name' => 'arrow-left.svg', 'type' => 'file'],
    ['name' => 'check.svg', 'type' => 'file'],
];

beforeEach(function (): void {
    $this->iconDir = storage_path('framework/testing/flux-blade-icons');

    config()->set('flux-blade-icons.output_path', $this->iconDir);
    config()->set('flux-blade-icons.icon_sets', []);

    Cache::forget('blade-icons:blade-feather-icons');
    Cache::forget('blade-icons:blade-bootstrap-icons');
    Cache::forget('blade-icons:blade-akar-icons');
    Cache::forget('blade-icons:custom-icons');
});

afterEach(function (): void {
    if (! is_dir($this->iconDir)) {
        return;
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($this->iconDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $file) {
        $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
    }

    rmdir($this->iconDir);
});

it('downloads a stroke-based icon with --set option', function () use ($strokeSvg): void {
    Http::fake([
        'raw.githubusercontent.com/*' => Http::response($strokeSvg),
        'api.github.com/*' => Http::response([]),
    ]);

    $this->artisan('flux:blade-icons', ['icons' => ['plus'], '--set' => 'blade-feather-icons'])
        ->assertSuccessful();

    $file = "{$this->iconDir}/blade-feather-icons/plus.blade.php";
    expect(file_exists($file))->toBeTrue();

    $content = file_get_contents($file);
    expect($content)
        ->toContain('stroke="currentColor"')
        ->toContain('stroke-width="{{ $strokeWidth }}"')
        ->toContain('Blade Feather Icons')
        ->toContain('$strokeWidth = match');
});

it('downloads a fill-based icon with --set option', function () use ($fillSvg): void {
    Http::fake([
        'raw.githubusercontent.com/*' => Http::response($fillSvg),
        'api.github.com/*' => Http::response([]),
    ]);

    $this->artisan('flux:blade-icons', ['icons' => ['circle'], '--set' => 'blade-bootstrap-icons'])
        ->assertSuccessful();

    $file = "{$this->iconDir}/blade-bootstrap-icons/circle.blade.php";
    expect(file_exists($file))->toBeTrue();

    $content = file_get_contents($file);
    expect($content)
        ->toContain('fill="currentColor"')
        ->toContain('viewBox="0 0 16 16"')
        ->toContain('Blade Bootstrap Icons')
        ->not->toContain('$strokeWidth');
});

it('shows error for unknown icon set', function (): void {
    $this->artisan('flux:blade-icons', ['icons' => ['test'], '--set' => 'non-existent'])
        ->assertSuccessful();

    expect(file_exists("{$this->iconDir}/non-existent"))->toBeFalse();
});

it('shows error when icon fetch fails', function (): void {
    Http::fake([
        '*' => Http::response('Not Found', 404),
    ]);

    $this->artisan('flux:blade-icons', ['icons' => ['missing-icon'], '--set' => 'blade-akar-icons'])
        ->assertSuccessful();

    expect(file_exists("{$this->iconDir}/blade-akar-icons/missing-icon.blade.php"))->toBeFalse();
});

it('downloads multiple icons in one command', function () use ($strokeSvg): void {
    Http::fake([
        'raw.githubusercontent.com/*' => Http::response($strokeSvg),
        'api.github.com/*' => Http::response([]),
    ]);

    $this->artisan('flux:blade-icons', ['icons' => ['plus', 'minus'], '--set' => 'blade-feather-icons'])
        ->assertSuccessful();

    expect(file_exists("{$this->iconDir}/blade-feather-icons/plus.blade.php"))->toBeTrue();
    expect(file_exists("{$this->iconDir}/blade-feather-icons/minus.blade.php"))->toBeTrue();
});

it('preserves original viewBox in generated blade', function () use ($fillSvg): void {
    Http::fake([
        'raw.githubusercontent.com/*' => Http::response($fillSvg),
        'api.github.com/*' => Http::response([]),
    ]);

    $this->artisan('flux:blade-icons', ['icons' => ['circle'], '--set' => 'blade-bootstrap-icons'])
        ->assertSuccessful();

    $content = file_get_contents("{$this->iconDir}/blade-bootstrap-icons/circle.blade.php");
    expect($content)->toContain('viewBox="0 0 16 16"');
});

it('uses cached icon list without making API calls', function (): void {
    Http::fake();

    Cache::put('blade-icons:blade-feather-icons', ['arrow-left', 'arrow-right', 'plus'], 86400);

    expect(Cache::get('blade-icons:blade-feather-icons'))->toBe(['arrow-left', 'arrow-right', 'plus']);
});

it('falls back to manual icon entry for non-github icon sets', function () use ($strokeSvg): void {
    config()->set('flux-blade-icons.icon_sets', [
        'custom-icons' => [
            'name' => 'Custom Icons',
            'url' => 'https://icons.example.com',
            'svg' => 'https://cdn.example.com/icons/',
        ],
    ]);

    Http::fake([
        'https://cdn.example.com/icons/camera.svg' => Http::response($strokeSvg),
    ]);

    $this->artisan('flux:blade-icons', ['--set' => 'custom-icons'])
        ->expectsPromptsInfo('This icon set is not hosted on GitHub. You can still type the icon name manually.')
        ->expectsPromptsInfo('Browse available icons at: https://icons.example.com')
        ->expectsQuestion('Which icon would you like to import?', 'camera')
        ->expectsQuestion('Would you like to import more icons?', 'done')
        ->assertSuccessful();

    expect(file_exists("{$this->iconDir}/custom-icons/camera.blade.php"))->toBeTrue();
});

it('shows an explicit error when a manually entered icon does not exist', function (): void {
    config()->set('flux-blade-icons.icon_sets', [
        'custom-icons' => [
            'name' => 'Custom Icons',
            'url' => 'https://icons.example.com',
            'svg' => 'https://cdn.example.com/icons/',
        ],
    ]);

    Http::fake([
        'https://cdn.example.com/icons/missing-icon.svg' => Http::response('Not Found', 404),
    ]);

    $this->artisan('flux:blade-icons', ['--set' => 'custom-icons'])
        ->expectsPromptsInfo('This icon set is not hosted on GitHub. You can still type the icon name manually.')
        ->expectsPromptsInfo('Browse available icons at: https://icons.example.com')
        ->expectsQuestion('Which icon would you like to import?', 'missing-icon')
        ->expectsPromptsError("Icon 'missing-icon' was not found in Custom Icons.")
        ->expectsQuestion('Would you like to import more icons?', 'done')
        ->assertSuccessful();

    expect(file_exists("{$this->iconDir}/custom-icons/missing-icon.blade.php"))->toBeFalse();
});

it('filters out non-svg files from icon list', function () use ($githubContentsResponse): void {
    Http::fake([
        'api.github.com/*' => Http::response($githubContentsResponse),
    ]);

    Cache::forget('blade-icons:blade-feather-icons');

    $command = app(FluxBladeIconsCommand::class);
    $method = new ReflectionMethod($command, 'fetchDirectoryIcons');

    $result = $method->invoke($command, 'https://api.github.com/repos/test/test/contents/resources/svg');

    expect($result)
        ->toContain('arrow-left')
        ->toContain('arrow-right')
        ->toContain('plus')
        ->not->toContain('README.md')
        ->not->toContain('README');
});

it('falls back to manual icon entry when the github api is unreachable', function () use ($strokeSvg): void {
    Http::fake([
        'https://api.github.com/*' => Http::failedConnection(),
        'https://raw.githubusercontent.com/brunocfalcao/blade-feather-icons/refs/heads/main/resources/svg/plus.svg' => Http::response($strokeSvg),
    ]);

    $this->artisan('flux:blade-icons', ['--set' => 'blade-feather-icons'])
        ->expectsPromptsWarning('Could not reach the GitHub API. You can still type the icon name manually.')
        ->expectsPromptsInfo('Browse available icons at: https://github.com/brunocfalcao/blade-feather-icons')
        ->expectsQuestion('Which icon would you like to import?', 'plus')
        ->expectsQuestion('Would you like to import more icons?', 'done')
        ->assertSuccessful();

    expect(file_exists("{$this->iconDir}/blade-feather-icons/plus.blade.php"))->toBeTrue();
});

it('handles subdirectories in icon packages', function () use ($githubContentsWithSubdirs, $githubSubdirResponse): void {
    Http::fake([
        'api.github.com/repos/test/test/contents/resources/svg' => Http::response($githubContentsWithSubdirs),
        'api.github.com/repos/test/test/contents/resources/svg/outline' => Http::response($githubSubdirResponse),
        'api.github.com/repos/test/test/contents/resources/svg/solid' => Http::response($githubSubdirResponse),
    ]);

    $command = app(FluxBladeIconsCommand::class);
    $method = new ReflectionMethod($command, 'fetchDirectoryIcons');

    $result = $method->invoke($command, 'https://api.github.com/repos/test/test/contents/resources/svg');

    expect($result)
        ->toContain('outline/arrow-left')
        ->toContain('outline/check')
        ->toContain('solid/arrow-left')
        ->toContain('solid/check');
});

it('skips already published icon when user declines override', function () use ($strokeSvg): void {
    Http::fake([
        'raw.githubusercontent.com/*' => Http::response($strokeSvg),
        'api.github.com/*' => Http::response([]),
    ]);

    $this->artisan('flux:blade-icons', ['icons' => ['plus'], '--set' => 'blade-feather-icons'])
        ->assertSuccessful();

    $file = "{$this->iconDir}/blade-feather-icons/plus.blade.php";
    expect(file_exists($file))->toBeTrue();

    $originalContent = file_get_contents($file);

    $this->artisan('flux:blade-icons', ['icons' => ['plus'], '--set' => 'blade-feather-icons'])
        ->expectsConfirmation("Icon 'plus' is already published. Override?", 'no')
        ->assertSuccessful();

    expect(file_get_contents($file))->toBe($originalContent);
});

it('overrides already published icon when user confirms', function () use ($strokeSvg, $fillSvg): void {
    Http::fake([
        'raw.githubusercontent.com/*' => Http::sequence()
            ->push($strokeSvg)
            ->push($fillSvg),
        'api.github.com/*' => Http::response([]),
    ]);

    $this->artisan('flux:blade-icons', ['icons' => ['plus'], '--set' => 'blade-feather-icons'])
        ->assertSuccessful();

    $file = "{$this->iconDir}/blade-feather-icons/plus.blade.php";
    $originalContent = file_get_contents($file);

    $this->artisan('flux:blade-icons', ['icons' => ['plus'], '--set' => 'blade-feather-icons'])
        ->expectsConfirmation("Icon 'plus' is already published. Override?", 'yes')
        ->assertSuccessful();

    expect(file_get_contents($file))->not->toBe($originalContent);
});

it('extracts owner and repo from github url', function (): void {
    $command = app(FluxBladeIconsCommand::class);
    $method = new ReflectionMethod($command, 'extractOwnerRepo');

    expect($method->invoke($command, 'https://github.com/codeat3/blade-academicons'))
        ->toBe('codeat3/blade-academicons');

    expect($method->invoke($command, 'https://github.com/blade-ui-kit/blade-heroicons'))
        ->toBe('blade-ui-kit/blade-heroicons');

    expect($method->invoke($command, 'not-a-github-url'))
        ->toBeNull();
});

it('writes icons to the configured output path', function () use ($strokeSvg): void {
    Http::fake([
        'raw.githubusercontent.com/*' => Http::response($strokeSvg),
        'api.github.com/*' => Http::response([]),
    ]);

    $this->artisan('flux:blade-icons', ['icons' => ['plus'], '--set' => 'blade-feather-icons'])
        ->assertSuccessful();

    expect(file_exists("{$this->iconDir}/blade-feather-icons/plus.blade.php"))->toBeTrue();
});
