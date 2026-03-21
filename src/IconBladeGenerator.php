<?php

declare(strict_types=1);

namespace Elemind\FluxBladeIcons;

class IconBladeGenerator
{
    public function detectSvgStyle(string $svg): string
    {
        $svgTag = str($svg)->match('/<svg[^>]*>/s')->toString();

        if (str_contains($svgTag, 'fill="none"') && str_contains($svg, 'stroke')) {
            return 'stroke';
        }

        return 'fill';
    }

    public function extractViewBox(string $svg): string
    {
        if (preg_match('/viewBox=["\']([^"\']+)["\']/', $svg, $matches)) {
            return $matches[1];
        }

        return '0 0 24 24';
    }

    /**
     * @param  array{name: string, url: string, svg: string}  $iconSet
     */
    public function generateBlade(string $svg, array $iconSet): string
    {
        $style = $this->detectSvgStyle($svg);

        if ($style === 'stroke') {
            return $this->generateStrokeBlade($svg, $iconSet);
        }

        return $this->generateFillBlade($svg, $iconSet);
    }

    /**
     * @param  array{name: string, url: string, svg: string}  $iconSet
     */
    private function generateStrokeBlade(string $svg, array $iconSet): string
    {
        $viewBox = $this->extractViewBox($svg);

        $svg = str($svg)
            ->replaceMatches('/<svg.*?>/s', <<<SVG
            <svg
                {{ \$attributes->class(\$classes) }}
                data-flux-icon
                xmlns="http://www.w3.org/2000/svg"
                viewBox="{$viewBox}"
                fill="none"
                stroke="currentColor"
                stroke-width="{{ \$strokeWidth }}"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
                data-slot="icon"
            >
            SVG)->toString();

        $stub = <<<HTML
        {{-- Credit: {$iconSet['name']} ({$iconSet['url']}) --}}

        @props([
            'variant' => 'outline',
        ])

        @php
            if (\$variant === 'solid') {
                throw new \Exception('The "solid" variant is not supported for stroke-based icons.');
            }

            \$classes = Flux::classes('shrink-0')->add(
                match (\$variant) {
                    'outline' => '[:where(&)]:size-6',
                    'solid' => '[:where(&)]:size-6',
                    'mini' => '[:where(&)]:size-5',
                    'micro' => '[:where(&)]:size-4',
                },
            );

            \$strokeWidth = match (\$variant) {
                'outline' => 2,
                'mini' => 2.25,
                'micro' => 2.5,
            };
        @endphp

        [[INJECT:SVG]]
        HTML;

        return (string) str($stub)->replace('[[INJECT:SVG]]', $svg);
    }

    /**
     * @param  array{name: string, url: string, svg: string}  $iconSet
     */
    private function generateFillBlade(string $svg, array $iconSet): string
    {
        $viewBox = $this->extractViewBox($svg);

        $svg = str($svg)
            ->replaceMatches('/<svg.*?>/s', <<<SVG
            <svg
                {{ \$attributes->class(\$classes) }}
                data-flux-icon
                xmlns="http://www.w3.org/2000/svg"
                viewBox="{$viewBox}"
                fill="currentColor"
                aria-hidden="true"
                data-slot="icon"
            >
            SVG)->toString();

        $stub = <<<HTML
        {{-- Credit: {$iconSet['name']} ({$iconSet['url']}) --}}

        @props([
            'variant' => 'outline',
        ])

        @php
            \$classes = Flux::classes('shrink-0')->add(
                match (\$variant) {
                    'outline' => '[:where(&)]:size-6',
                    'solid' => '[:where(&)]:size-6',
                    'mini' => '[:where(&)]:size-5',
                    'micro' => '[:where(&)]:size-4',
                },
            );
        @endphp

        [[INJECT:SVG]]
        HTML;

        return (string) str($stub)->replace('[[INJECT:SVG]]', $svg);
    }
}
