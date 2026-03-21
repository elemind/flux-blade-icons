<?php

namespace Elemind\FluxBladeIcons;

use Elemind\FluxBladeIcons\Commands\FluxBladeIconsClearCacheCommand;
use Elemind\FluxBladeIcons\Commands\FluxBladeIconsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FluxBladeIconsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('flux-blade-icons')
            ->hasConfigFile()
            ->hasCommands([
                FluxBladeIconsCommand::class,
                FluxBladeIconsClearCacheCommand::class,
            ]);
    }
}
