<?php

declare(strict_types=1);

use Elemind\FluxBladeIcons\IconBladeGenerator;

$strokeSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>';

$fillSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"><path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0z"/></svg>';

$iconSet = [
    'name' => 'Blade Test Icons',
    'url' => 'https://github.com/test/blade-test-icons',
    'svg' => 'https://raw.githubusercontent.com/test/blade-test-icons/refs/heads/main/resources/svg/',
];

it('detects stroke style from svg', function () use ($strokeSvg): void {
    $generator = new IconBladeGenerator;

    expect($generator->detectSvgStyle($strokeSvg))->toBe('stroke');
});

it('detects fill style from svg', function () use ($fillSvg): void {
    $generator = new IconBladeGenerator;

    expect($generator->detectSvgStyle($fillSvg))->toBe('fill');
});

it('extracts view box from svg', function () use ($fillSvg): void {
    $generator = new IconBladeGenerator;

    expect($generator->extractViewBox($fillSvg))->toBe('0 0 16 16');
});

it('defaults view box when missing', function (): void {
    $generator = new IconBladeGenerator;

    expect($generator->extractViewBox('<svg><path d="M0 0"/></svg>'))->toBe('0 0 24 24');
});

it('generates stroke blade template', function () use ($strokeSvg, $iconSet): void {
    $generator = new IconBladeGenerator;
    $content = $generator->generateBlade($strokeSvg, $iconSet);

    expect($content)
        ->toContain('stroke="currentColor"')
        ->toContain('stroke-width="{{ $strokeWidth }}"')
        ->toContain('Blade Test Icons')
        ->toContain('$strokeWidth = match');
});

it('generates fill blade template', function () use ($fillSvg, $iconSet): void {
    $generator = new IconBladeGenerator;
    $content = $generator->generateBlade($fillSvg, $iconSet);

    expect($content)
        ->toContain('fill="currentColor"')
        ->toContain('viewBox="0 0 16 16"')
        ->toContain('Blade Test Icons')
        ->not->toContain('$strokeWidth');
});
