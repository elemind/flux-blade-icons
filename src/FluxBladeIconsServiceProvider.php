<?php

namespace Elemind\FluxBladeIcons;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Elemind\FluxBladeIcons\Commands\FluxBladeIconsCommand;

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
            ->hasCommand(FluxBladeIconsCommand::class);
    }
}
