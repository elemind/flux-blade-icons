<?php

declare(strict_types=1);

namespace Elemind\FluxBladeIcons;

use Illuminate\Contracts\Cache\Repository;

readonly class IconListCache
{
    public function __construct(
        private Repository $cache,
    ) {}

    public function key(string $setKey): string
    {
        return "blade-icons:{$setKey}";
    }

    /**
     * @return list<string>|null
     */
    public function get(string $setKey): ?array
    {
        $icons = $this->cache->get($this->key($setKey));

        return is_array($icons) ? array_values($icons) : null;
    }

    /**
     * @param  list<string>  $icons
     */
    public function put(string $setKey, array $icons, int $ttl): void
    {
        $this->cache->put($this->key($setKey), $icons, $ttl);
    }

    public function forget(string $setKey): bool
    {
        return $this->cache->forget($this->key($setKey));
    }

    /**
     * @param  iterable<string>  $setKeys
     */
    public function forgetMany(iterable $setKeys): void
    {
        foreach ($setKeys as $setKey) {
            $this->forget($setKey);
        }
    }
}
