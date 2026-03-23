<?php

declare(strict_types=1);

namespace Elemind\FluxBladeIcons\Commands;

use const STDOUT;

use Elemind\FluxBladeIcons\IconBladeGenerator;
use Elemind\FluxBladeIcons\IconListCache;
use Elemind\FluxBladeIcons\IconSetRegistry;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\multisearch;
use function Laravel\Prompts\note;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

class FluxBladeIconsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flux:blade-icons {icons?*} {--set= : The icon set slug} {--fresh : Bypass icon list cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import third-party icons from Blade icon packages into Flux.';

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly IconBladeGenerator $generator,
        private readonly IconSetRegistry $iconSets,
        private readonly IconListCache $iconListCache,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->displayLogo();

        $setKey = $this->option('set');

        if ($setKey !== null) {
            if (! $this->iconSets->has($setKey)) {
                error("Unknown icon set: {$setKey}");
                info('Available sets: '.implode(', ', $this->iconSets->keys()));

                return self::SUCCESS;
            }
        } else {
            $setKey = $this->promptForIconSet();
        }

        $iconSet = $this->iconSets->get($setKey);

        if ($iconSet === null) {
            error("Unknown icon set: {$setKey}");

            return self::SUCCESS;
        }

        if (count($icons = $this->argument('icons')) > 0) {
            foreach ($icons as $icon) {
                if ($this->isAlreadyPublished($icon, $setKey) && ! confirm("Icon '{$icon}' is already published. Override?")) {
                    continue;
                }

                $this->publishIcon($icon, $setKey, $iconSet);
            }

            return self::SUCCESS;
        }

        intro("Importing icons from {$iconSet['name']}");

        $iconList = $this->fetchIconList($setKey, $iconSet);

        while (true) {
            $publishedIcons = $this->getPublishedIcons($setKey);

            if ($iconList !== []) {
                $icons = multisearch(
                    label: 'Which icons would you like to import?',
                    options: fn (string $value) => collect($iconList)
                        ->when(
                            ($query = $value) !== '',
                            fn ($collection) => $collection->filter(
                                fn (string $name): bool => str($name)->lower()->contains(str($query)->lower()->toString())
                            )
                        )
                        ->mapWithKeys(fn (string $name): array => [
                            $name => in_array($name, $publishedIcons, true)
                                ? "{$name} (published)"
                                : $name,
                        ])
                        ->all(),
                    placeholder: 'Type to search icons...',
                    required: 'Select at least one icon.',
                    hint: 'Use the space bar to select options.',
                );
            } else {
                info("Browse available icons at: {$iconSet['url']}");

                $icon = text(
                    label: 'Which icon would you like to import?',
                    placeholder: 'e.g. arrow-left',
                    required: 'An icon name is required.',
                );

                $icons = [$icon];
            }

            foreach ($icons as $icon) {
                if ($this->isAlreadyPublished($icon, $setKey) && ! confirm("Icon '{$icon}' is already published. Override?")) {
                    continue;
                }

                $this->publishIcon($icon, $setKey, $iconSet);
            }

            $next = select(
                label: 'Would you like to import more icons?',
                options: [
                    'same' => "Yes, from {$iconSet['name']}",
                    'other' => 'Yes, from a different package',
                    'done' => 'No, I\'m done',
                ],
            );

            if ($next === 'done') {
                break;
            }

            if ($next === 'other') {
                $setKey = $this->promptForIconSet();
                $iconSet = $this->iconSets->get($setKey);

                if ($iconSet === null) {
                    error("Unknown icon set: {$setKey}");

                    return self::SUCCESS;
                }

                intro("Importing icons from {$iconSet['name']}");
                $iconList = $this->fetchIconList($setKey, $iconSet);
            }
        }

        return self::SUCCESS;
    }

    private function displayLogo(): void
    {
        $logoLines = [
            '  ████████ ██       ██    ██ ██   ██',
            '  ██       ██       ██    ██  ██ ██ ',
            '  █████    ██       ██    ██   ███  ',
            '  ██       ██       ██    ██  ██ ██ ',
            '  ██       ████████  ██████  ██   ██',
        ];

        $subtitleLine = '            Blade Icons';

        $gradientColors = [
            '38;2;200;200;200',
            '38;2;170;170;170',
            '38;2;140;140;140',
            '38;2;110;110;110',
            '38;2;80;80;80',
        ];

        $supportsAnsi = stream_isatty(STDOUT);

        $this->line('');

        foreach ($logoLines as $i => $line) {
            $this->line(
                $supportsAnsi
                    ? "\033[{$gradientColors[$i]}m{$line}\033[0m"
                    : $line
            );
        }

        $this->line(
            $supportsAnsi
                ? "\033[38;2;130;130;130m{$subtitleLine}\033[0m"
                : $subtitleLine
        );

        $this->line('');
    }

    /**
     * @param  array{name: string, url: string, svg: string}  $iconSet
     */
    protected function publishIcon(string $icon, string $setKey, array $iconSet): void
    {
        try {
            $response = spin(
                callback: fn () => Http::get($iconSet['svg'].$icon.'.svg'),
                message: 'Fetching icon...',
            );
        } catch (ConnectionException) {
            error("Could not fetch icon '{$icon}' from {$iconSet['name']}.");
            note("Browse available icons at: {$iconSet['url']}");

            return;
        }

        if ($response->failed()) {
            error(
                $response->status() === 404
                    ? "Icon '{$icon}' was not found in {$iconSet['name']}."
                    : "Could not fetch icon '{$icon}' from {$iconSet['name']}."
            );
            note("Browse available icons at: {$iconSet['url']}");

            return;
        }

        $svg = $response->body();

        $directory = $this->outputPath($setKey);

        if (($subdirectory = dirname($icon)) !== '.') {
            $directory = $this->outputPath("{$setKey}/{$subdirectory}");
        }

        if (! $this->filesystem->isDirectory($directory)) {
            $this->filesystem->ensureDirectoryExists($directory);
        }

        $destinationAsFile = $this->outputPath("{$setKey}/{$icon}.blade.php");

        $this->filesystem->put($destinationAsFile, $this->generator->generateBlade($svg, $iconSet));

        $usageName = str_replace('/', '.', $icon);
        $relativePath = str_replace(base_path().DIRECTORY_SEPARATOR, '', $destinationAsFile);
        info("Published icon: {$relativePath}");
        note("Usage: <flux:icon.{$setKey}.{$usageName} />");
    }

    /**
     * @param  array{name: string, url: string, svg: string}  $iconSet
     * @return list<string>
     */
    protected function fetchIconList(string $setKey, array $iconSet): array
    {
        if ($this->option('fresh')) {
            $this->iconListCache->forget($setKey);
        }

        $cachedIcons = $this->iconListCache->get($setKey);

        if ($cachedIcons !== null) {
            return $cachedIcons;
        }

        $ownerRepo = $this->extractOwnerRepo($iconSet['url']);

        if ($ownerRepo === null) {
            info('This icon set is not hosted on GitHub. You can still type the icon name manually.');
            $this->iconListCache->put($setKey, [], $this->cacheTtl());

            return [];
        }

        try {
            $icons = spin(
                callback: fn () => $this->fetchDirectoryIcons(
                    "https://api.github.com/repos/{$ownerRepo}/contents/resources/svg"
                ),
                message: 'Fetching icon list...',
            );
        } catch (ConnectionException) {
            warning('Could not reach the GitHub API. You can still type the icon name manually.');

            return [];
        }

        if ($icons === null) {
            warning('Could not fetch icon list from GitHub. You can still type the icon name manually.');

            return [];
        }

        sort($icons);
        $this->iconListCache->put($setKey, $icons, $this->cacheTtl());

        return $icons;
    }

    /**
     * @return list<string>|null
     *
     * @throws ConnectionException
     */
    protected function fetchDirectoryIcons(string $apiUrl, string $prefix = ''): ?array
    {
        $response = Http::withHeaders(['Accept' => 'application/vnd.github.v3+json'])
            ->get($apiUrl);

        if ($response->failed()) {
            return null;
        }

        $icons = [];

        foreach ($response->json() as $item) {
            if ($item['type'] === 'file' && str_ends_with($item['name'], '.svg')) {
                $name = str_replace('.svg', '', $item['name']);
                $icons[] = $prefix !== '' ? "{$prefix}/{$name}" : $name;
            }

            if ($item['type'] === 'dir') {
                $subIcons = $this->fetchDirectoryIcons(
                    $item['url'],
                    $prefix !== '' ? "{$prefix}/{$item['name']}" : $item['name']
                );

                if ($subIcons !== null) {
                    $icons = [...$icons, ...$subIcons];
                }
            }
        }

        return $icons;
    }

    /**
     * @return list<string>
     */
    protected function getPublishedIcons(string $setKey): array
    {
        $directory = $this->outputPath($setKey);

        if (! $this->filesystem->isDirectory($directory)) {
            return [];
        }

        $icons = [];

        foreach ($this->filesystem->allFiles($directory) as $file) {
            if (str_ends_with($file->getFilename(), '.blade.php')) {
                $icons[] = $this->normalizePublishedIconPath($file->getRelativePathname());
            }
        }

        sort($icons);

        return $icons;
    }

    private function normalizePublishedIconPath(string $relativePath): string
    {
        return str_replace(['\\', '.blade.php'], ['/', ''], $relativePath);
    }

    protected function extractOwnerRepo(string $url): ?string
    {
        if (preg_match('/github\.com\/([^\/]+\/[^\/]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function promptForIconSet(): string
    {
        return search(
            label: 'Which icon package would you like to import from?',
            options: fn (string $value) => $this->iconSets->searchByName($value),
            placeholder: 'Type to search icon packages...',
        );
    }

    private function isAlreadyPublished(string $icon, string $setKey): bool
    {
        return $this->filesystem->exists(
            $this->outputPath("{$setKey}/{$icon}.blade.php")
        );
    }

    private function outputPath(string $path = ''): string
    {
        $basePath = rtrim(
            (string) config('flux-blade-icons.output_path', resource_path('views/flux/icon')),
            DIRECTORY_SEPARATOR
        );

        if ($path === '') {
            return $basePath;
        }

        return $basePath.DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR);
    }

    private function cacheTtl(): int
    {
        return (int) config('flux-blade-icons.cache_ttl', 86400);
    }
}
