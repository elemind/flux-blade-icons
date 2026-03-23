# Changelog

All notable changes to `flux-blade-icons` will be documented in this file.

## [v1.0.0] - 2026-03-23

Initial public release.

### Added

- Added the `flux:blade-icons` Artisan command to import SVG icons from supported Blade icon packages into Flux-compatible Blade views.
- Added interactive icon import with package selection, icon search, multi-select support, and the ability to continue importing from the same or another package.
- Added direct command-line imports through positional icon arguments and the `--set` option.
- Added support for importing nested icons using relative paths such as `outline/arrow-left`.
- Added generation of Flux-compatible Blade icon views under `resources/views/flux/icon` by default.
- Added automatic adaptation of imported SVGs for Flux usage, including viewBox preservation and stroke-based icon handling.
- Added configurable output path, icon list cache TTL, built-in icon set registry, and custom icon set overrides through `config/flux-blade-icons.php`.
- Added caching for fetched icon lists to speed up interactive imports, with support for bypassing the cache via `--fresh`.
- Added the `flux:blade-icons:clear-cache` Artisan command to clear cached icon lists for one icon set or all registered sets.
- Added fallback manual icon entry for non-GitHub icon sources or when GitHub API lookups are unavailable.
- Added a large built-in registry of supported third-party Blade icon packages.
- Added automated test coverage for icon generation, registry behavior, interactive and direct imports, cache handling, and command error scenarios.
