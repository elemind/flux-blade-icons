<?php

declare(strict_types=1);

namespace Elemind\FluxBladeIcons;

use Illuminate\Contracts\Config\Repository;

readonly class IconSetRegistry
{
    public function __construct(
        private Repository $config,
    ) {}

    /**
     * @return array<string, array{name: string, url: string, svg: string}>
     */
    public function all(): array
    {
        return array_replace($this->defaultSets(), $this->configuredSets());
    }

    /**
     * @return array{name: string, url: string, svg: string}|null
     */
    public function get(string $key): ?array
    {
        return $this->all()[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->all()[$key]);
    }

    /**
     * @return list<string>
     */
    public function keys(): array
    {
        return array_keys($this->all());
    }

    /**
     * @return array<string, string>
     */
    public function searchByName(string $query): array
    {
        return collect($this->all())
            ->when($query !== '', fn ($collection) => $collection->filter(
                fn (array $set): bool => str($set['name'])->lower()->contains(str($query)->lower())
            ))
            ->mapWithKeys(fn (array $set, string $key): array => [$key => $set['name']])
            ->all();
    }

    /**
     * @return array<string, array{name: string, url: string, svg: string}>
     */
    private function configuredSets(): array
    {
        $iconSets = $this->config->get('flux-blade-icons.icon_sets', []);

        return is_array($iconSets) ? $iconSets : [];
    }

    /**
     * @return array<string, array{name: string, url: string, svg: string}>
     */
    private function defaultSets(): array
    {
        $iconSets = $this->config->get('flux-blade-icons.default_icon_sets', []);

        return is_array($iconSets) ? $iconSets : [];
    }
}
