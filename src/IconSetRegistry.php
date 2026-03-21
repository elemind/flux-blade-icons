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
        return array_replace($this->builtInSets(), $this->configuredSets());
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
    private function builtInSets(): array
    {
        return [
            'blade-academicons' => [
                'name' => 'Blade Academicons',
                'url' => 'https://github.com/codeat3/blade-academicons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-academicons/refs/heads/main/resources/svg/',
            ],
            'blade-akar-icons' => [
                'name' => 'Blade Akar Icons',
                'url' => 'https://github.com/codeat3/blade-akar-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-akar-icons/refs/heads/main/resources/svg/',
            ],
            'blade-ant-design-icons' => [
                'name' => 'Blade Ant Design Icons',
                'url' => 'https://github.com/codeat3/blade-ant-design-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-ant-design-icons/refs/heads/main/resources/svg/',
            ],
            'blade-bootstrap-icons' => [
                'name' => 'Blade Bootstrap Icons',
                'url' => 'https://github.com/davidhsianturi/blade-bootstrap-icons',
                'svg' => 'https://raw.githubusercontent.com/davidhsianturi/blade-bootstrap-icons/refs/heads/main/resources/svg/',
            ],
            'blade-boxicons' => [
                'name' => 'Blade Boxicons',
                'url' => 'https://github.com/mallardduck/blade-boxicons',
                'svg' => 'https://raw.githubusercontent.com/mallardduck/blade-boxicons/refs/heads/main/resources/svg/',
            ],
            'blade-bytesize-icons' => [
                'name' => 'Blade Bytesize Icons',
                'url' => 'https://github.com/codeat3/blade-bytesize-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-bytesize-icons/refs/heads/main/resources/svg/',
            ],
            'blade-car-makes-icons' => [
                'name' => 'Blade Car Makes Icons',
                'url' => 'https://github.com/johan-boshoff/blade-car-makes-icons',
                'svg' => 'https://raw.githubusercontent.com/johan-boshoff/blade-car-makes-icons/refs/heads/main/resources/svg/',
            ],
            'blade-carbon-icons' => [
                'name' => 'Blade Carbon Icons',
                'url' => 'https://github.com/codeat3/blade-carbon-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-carbon-icons/refs/heads/main/resources/svg/',
            ],
            'blade-circle-flags' => [
                'name' => 'Blade Circle Flags',
                'url' => 'https://github.com/fahrim/blade-circle-flags',
                'svg' => 'https://raw.githubusercontent.com/fahrim/blade-circle-flags/refs/heads/main/resources/svg/',
            ],
            'blade-clarity-icons' => [
                'name' => 'Blade Clarity Icons',
                'url' => 'https://github.com/codeat3/blade-clarity-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-clarity-icons/refs/heads/main/resources/svg/',
            ],
            'blade-codicons' => [
                'name' => 'Blade VSCode Codicons',
                'url' => 'https://github.com/codeat3/blade-codicons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-codicons/refs/heads/main/resources/svg/',
            ],
            'blade-coolicons' => [
                'name' => 'Blade Coolicons',
                'url' => 'https://github.com/codeat3/blade-coolicons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-coolicons/refs/heads/main/resources/svg/',
            ],
            'blade-coreui-icons' => [
                'name' => 'Blade CoreUI Icons',
                'url' => 'https://github.com/ublabs/blade-coreui-icons',
                'svg' => 'https://raw.githubusercontent.com/ublabs/blade-coreui-icons/refs/heads/main/resources/svg/',
            ],
            'blade-country-flags' => [
                'name' => 'Blade Country Flags',
                'url' => 'https://github.com/stijnvanouplines/blade-country-flags',
                'svg' => 'https://raw.githubusercontent.com/stijnvanouplines/blade-country-flags/refs/heads/main/resources/svg/',
            ],
            'blade-cryptocurrency-icons' => [
                'name' => 'Blade Cryptocurrency Icons',
                'url' => 'https://github.com/codeat3/blade-cryptocurrency-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-cryptocurrency-icons/refs/heads/main/resources/svg/',
            ],
            'blade-css-icons' => [
                'name' => 'Blade CSS Icons',
                'url' => 'https://github.com/khatabwedaa/blade-css-icons',
                'svg' => 'https://raw.githubusercontent.com/khatabwedaa/blade-css-icons/refs/heads/main/resources/svg/',
            ],
            'blade-devicons' => [
                'name' => 'Blade Dev Icons',
                'url' => 'https://github.com/codeat3/blade-devicons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-devicons/refs/heads/main/resources/svg/',
            ],
            'blade-element-plus-icons' => [
                'name' => 'Blade Element Plus Icons',
                'url' => 'https://github.com/codeat3/blade-element-plus-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-element-plus-icons/refs/heads/main/resources/svg/',
            ],
            'blade-elusive-icons' => [
                'name' => 'Blade Elusive Icons',
                'url' => 'https://github.com/codeat3/blade-elusive-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-elusive-icons/refs/heads/main/resources/svg/',
            ],
            'blade-emblemicons' => [
                'name' => 'Blade Emblemicons',
                'url' => 'https://github.com/codeat3/blade-emblemicons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-emblemicons/refs/heads/main/resources/svg/',
            ],
            'blade-emojis' => [
                'name' => 'Blade Emojis',
                'url' => 'https://github.com/MaidenVoyageSoftware/blade-emojis',
                'svg' => 'https://raw.githubusercontent.com/MaidenVoyageSoftware/blade-emojis/refs/heads/main/resources/svg/',
            ],
            'blade-entypo' => [
                'name' => 'Blade Entypo',
                'url' => 'https://github.com/owenvoke/blade-entypo',
                'svg' => 'https://raw.githubusercontent.com/owenvoke/blade-entypo/refs/heads/main/resources/svg/',
            ],
            'blade-eos-icons' => [
                'name' => 'Blade EOS Icons',
                'url' => 'https://github.com/codeat3/blade-eos-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-eos-icons/refs/heads/main/resources/svg/',
            ],
            'blade-eva-icons' => [
                'name' => 'Blade Eva Icons',
                'url' => 'https://github.com/Hasnayeen/blade-eva-icons',
                'svg' => 'https://raw.githubusercontent.com/Hasnayeen/blade-eva-icons/refs/heads/main/resources/svg/',
            ],
            'blade-evil-icons' => [
                'name' => 'Blade Evil Icons',
                'url' => 'https://github.com/codeat3/blade-evil-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-evil-icons/refs/heads/main/resources/svg/',
            ],
            'blade-feather-icons' => [
                'name' => 'Blade Feather Icons',
                'url' => 'https://github.com/brunocfalcao/blade-feather-icons',
                'svg' => 'https://raw.githubusercontent.com/brunocfalcao/blade-feather-icons/refs/heads/main/resources/svg/',
            ],
            'blade-file-icons' => [
                'name' => 'Blade File Icons',
                'url' => 'https://github.com/codeat3/blade-file-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-file-icons/refs/heads/main/resources/svg/',
            ],
            'blade-filetype-icons' => [
                'name' => 'Blade File Type Icons',
                'url' => 'https://github.com/log1x/blade-filetype-icons',
                'svg' => 'https://raw.githubusercontent.com/log1x/blade-filetype-icons/refs/heads/main/resources/svg/',
            ],
            'blade-fluentui-system-icons' => [
                'name' => 'Blade FluentUI System Icons',
                'url' => 'https://github.com/codeat3/blade-fluentui-system-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-fluentui-system-icons/refs/heads/main/resources/svg/',
            ],
            'blade-flowbite-icons' => [
                'name' => 'Blade Flowbite Icons',
                'url' => 'https://github.com/themesberg/flowbite-blade-icons',
                'svg' => 'https://raw.githubusercontent.com/themesberg/flowbite-blade-icons/refs/heads/main/resources/svg/',
            ],
            'blade-fontaudio' => [
                'name' => 'Blade Font Audio',
                'url' => 'https://github.com/codeat3/blade-fontaudio',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-fontaudio/refs/heads/main/resources/svg/',
            ],
            'blade-fontawesome' => [
                'name' => 'Blade Font Awesome',
                'url' => 'https://github.com/owenvoke/blade-fontawesome',
                'svg' => 'https://raw.githubusercontent.com/owenvoke/blade-fontawesome/refs/heads/main/resources/svg/',
            ],
            'blade-fontisto-icons' => [
                'name' => 'Blade Fontisto Icons',
                'url' => 'https://github.com/codeat3/blade-fontisto-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-fontisto-icons/refs/heads/main/resources/svg/',
            ],
            'blade-forkawesome' => [
                'name' => 'Blade Fork Awesome',
                'url' => 'https://github.com/codeat3/blade-forkawesome',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-forkawesome/refs/heads/main/resources/svg/',
            ],
            'blade-github-octicons' => [
                'name' => 'Blade Github Octicons',
                'url' => 'https://github.com/Activisme-be/Blade-github-octicons',
                'svg' => 'https://raw.githubusercontent.com/Activisme-be/Blade-github-octicons/refs/heads/main/resources/svg/',
            ],
            'blade-gms-o' => [
                'name' => 'Blade Google Material Symbols (Outlined)',
                'url' => 'https://github.com/enso-san/blade-gms-o',
                'svg' => 'https://raw.githubusercontent.com/enso-san/blade-gms-o/refs/heads/main/resources/svg/',
            ],
            'blade-gms-of' => [
                'name' => 'Blade Google Material Symbols (Outlined/Filled)',
                'url' => 'https://github.com/enso-san/blade-gms-of',
                'svg' => 'https://raw.githubusercontent.com/enso-san/blade-gms-of/refs/heads/main/resources/svg/',
            ],
            'blade-gms-r' => [
                'name' => 'Blade Google Material Symbols (Rounded)',
                'url' => 'https://github.com/enso-san/blade-gms-r',
                'svg' => 'https://raw.githubusercontent.com/enso-san/blade-gms-r/refs/heads/main/resources/svg/',
            ],
            'blade-gms-rf' => [
                'name' => 'Blade Google Material Symbols (Rounded/Filled)',
                'url' => 'https://github.com/enso-san/blade-gms-rf',
                'svg' => 'https://raw.githubusercontent.com/enso-san/blade-gms-rf/refs/heads/main/resources/svg/',
            ],
            'blade-gms-s' => [
                'name' => 'Blade Google Material Symbols (Sharp)',
                'url' => 'https://github.com/enso-san/blade-gms-s',
                'svg' => 'https://raw.githubusercontent.com/enso-san/blade-gms-s/refs/heads/main/resources/svg/',
            ],
            'blade-gms-sf' => [
                'name' => 'Blade Google Material Symbols (Sharp/Filled)',
                'url' => 'https://github.com/enso-san/blade-gms-sf',
                'svg' => 'https://raw.githubusercontent.com/enso-san/blade-gms-sf/refs/heads/main/resources/svg/',
            ],
            'blade-google-material-design-icons' => [
                'name' => 'Blade Google Material Design Icons',
                'url' => 'https://github.com/codeat3/blade-google-material-design-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-google-material-design-icons/refs/heads/main/resources/svg/',
            ],
            'blade-govicons' => [
                'name' => 'Blade Gov Icons',
                'url' => 'https://github.com/codeat3/blade-govicons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-govicons/refs/heads/main/resources/svg/',
            ],
            'blade-gravity-ui-icons' => [
                'name' => 'Blade Gravity UI Icons',
                'url' => 'https://github.com/codeat3/blade-gravity-ui-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-gravity-ui-icons/refs/heads/main/resources/svg/',
            ],
            'blade-grommet-icons' => [
                'name' => 'Blade Grommet Icons',
                'url' => 'https://github.com/codeat3/blade-grommet-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-grommet-icons/refs/heads/main/resources/svg/',
            ],
            'blade-health-icons' => [
                'name' => 'Blade Health Icons',
                'url' => 'https://github.com/troccoli/blade-health-icons',
                'svg' => 'https://raw.githubusercontent.com/troccoli/blade-health-icons/refs/heads/main/resources/svg/',
            ],
            'blade-heroicons' => [
                'name' => 'Blade Heroicons',
                'url' => 'https://github.com/blade-ui-kit/blade-heroicons',
                'svg' => 'https://raw.githubusercontent.com/blade-ui-kit/blade-heroicons/refs/heads/main/resources/svg/',
            ],
            'blade-hugeicons' => [
                'name' => 'Blade Hugeicons',
                'url' => 'https://github.com/afatmustafa/blade-hugeicons',
                'svg' => 'https://raw.githubusercontent.com/afatmustafa/blade-hugeicons/refs/heads/main/resources/svg/',
            ],
            'blade-humbleicons' => [
                'name' => 'Blade Humbleicons',
                'url' => 'https://github.com/codeat3/blade-humbleicons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-humbleicons/refs/heads/main/resources/svg/',
            ],
            'blade-icomoon' => [
                'name' => 'Blade IcoMoon Icons',
                'url' => 'https://github.com/nerdroid23/blade-icomoon',
                'svg' => 'https://raw.githubusercontent.com/nerdroid23/blade-icomoon/refs/heads/main/resources/svg/',
            ],
            'blade-iconic' => [
                'name' => 'Blade Iconic Icons',
                'url' => 'https://github.com/ItsMalikJones/blade-iconic',
                'svg' => 'https://raw.githubusercontent.com/ItsMalikJones/blade-iconic/refs/heads/main/resources/svg/',
            ],
            'blade-iconoir' => [
                'name' => 'Blade Iconoir',
                'url' => 'https://github.com/andreiio/blade-iconoir',
                'svg' => 'https://raw.githubusercontent.com/andreiio/blade-iconoir/refs/heads/main/resources/svg/',
            ],
            'blade-iconpark' => [
                'name' => 'Blade Icon Park Icons',
                'url' => 'https://github.com/codeat3/blade-iconpark',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-iconpark/refs/heads/main/resources/svg/',
            ],
            'blade-iconsax' => [
                'name' => 'Blade Iconsax',
                'url' => 'https://github.com/saade/blade-iconsax',
                'svg' => 'https://raw.githubusercontent.com/saade/blade-iconsax/refs/heads/main/resources/svg/',
            ],
            'blade-ikonate' => [
                'name' => 'Blade Ikonate Icons',
                'url' => 'https://github.com/codeat3/blade-ikonate',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-ikonate/refs/heads/main/resources/svg/',
            ],
            'blade-ionicons' => [
                'name' => 'Blade Ionicons',
                'url' => 'https://github.com/Faisal50x/blade-ionicons',
                'svg' => 'https://raw.githubusercontent.com/Faisal50x/blade-ionicons/refs/heads/main/resources/svg/',
            ],
            'blade-iranian-brands-icons' => [
                'name' => 'Blade Iranian Brands Icons',
                'url' => 'https://github.com/rezadindar/blade-iranian-brands-icons',
                'svg' => 'https://raw.githubusercontent.com/rezadindar/blade-iranian-brands-icons/refs/heads/main/resources/svg/',
            ],
            'blade-jam-icons' => [
                'name' => 'Blade Jam Icons',
                'url' => 'https://github.com/codeat3/blade-jam-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-jam-icons/refs/heads/main/resources/svg/',
            ],
            'blade-lets-icons' => [
                'name' => 'Blade Lets Icons',
                'url' => 'https://github.com/mansoorkhan96/blade-lets-icons',
                'svg' => 'https://raw.githubusercontent.com/mansoorkhan96/blade-lets-icons/refs/heads/main/resources/svg/',
            ],
            'blade-line-awesome-icons' => [
                'name' => 'Blade Line Awesome Icons',
                'url' => 'https://github.com/codeat3/blade-line-awesome-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-line-awesome-icons/refs/heads/main/resources/svg/',
            ],
            'blade-lineicons' => [
                'name' => 'Blade Lineicons',
                'url' => 'https://github.com/datlechin/blade-lineicons',
                'svg' => 'https://raw.githubusercontent.com/datlechin/blade-lineicons/refs/heads/main/resources/svg/',
            ],
            'blade-lucide-icons' => [
                'name' => 'Blade Lucide Icons',
                'url' => 'https://github.com/mallardduck/blade-lucide-icons',
                'svg' => 'https://raw.githubusercontent.com/mallardduck/blade-lucide-icons/refs/heads/main/resources/svg/',
            ],
            'blade-majestic-icons' => [
                'name' => 'Blade Majestic Icons',
                'url' => 'https://github.com/codeat3/blade-majestic-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-majestic-icons/refs/heads/main/resources/svg/',
            ],
            'blade-maki-icons' => [
                'name' => 'Blade Maki Icons',
                'url' => 'https://github.com/codeat3/blade-maki-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-maki-icons/refs/heads/main/resources/svg/',
            ],
            'blade-material-design-icons' => [
                'name' => 'Blade Material Design Icons',
                'url' => 'https://github.com/postare/blade-mdi',
                'svg' => 'https://raw.githubusercontent.com/postare/blade-mdi/refs/heads/main/resources/svg/',
            ],
            'blade-memory-icons' => [
                'name' => 'Blade Memory Icons',
                'url' => 'https://github.com/codeat3/blade-memory-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-memory-icons/refs/heads/main/resources/svg/',
            ],
            'blade-microns' => [
                'name' => 'Blade Microns',
                'url' => 'https://github.com/codeat3/blade-microns',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-microns/refs/heads/main/resources/svg/',
            ],
            'blade-mono-icons' => [
                'name' => 'Blade Mono Icons',
                'url' => 'https://github.com/codeat3/blade-mono-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-mono-icons/refs/heads/main/resources/svg/',
            ],
            'blade-payment-logos' => [
                'name' => 'Blade Payment Logos',
                'url' => 'https://github.com/isap-ou/blade-payment-logos',
                'svg' => 'https://raw.githubusercontent.com/isap-ou/blade-payment-logos/refs/heads/main/resources/svg/',
            ],
            'blade-pepicons' => [
                'name' => 'Blade Pepicons',
                'url' => 'https://github.com/codeat3/blade-pepicons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-pepicons/refs/heads/main/resources/svg/',
            ],
            'blade-phosphor-icons' => [
                'name' => 'Blade Phosphor Icons',
                'url' => 'https://github.com/codeat3/blade-phosphor-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-phosphor-icons/refs/heads/main/resources/svg/',
            ],
            'blade-pixelarticons' => [
                'name' => 'Blade Pixelarticons',
                'url' => 'https://github.com/codeat3/blade-pixelarticons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-pixelarticons/refs/heads/main/resources/svg/',
            ],
            'blade-polaris-icons' => [
                'name' => 'Blade Polaris Icons',
                'url' => 'https://github.com/Eduard9969/blade-polaris-icons',
                'svg' => 'https://raw.githubusercontent.com/Eduard9969/blade-polaris-icons/refs/heads/main/resources/svg/',
            ],
            'blade-prime-icons' => [
                'name' => 'Blade Prime Icons',
                'url' => 'https://github.com/codeat3/blade-prime-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-prime-icons/refs/heads/main/resources/svg/',
            ],
            'blade-radix-icons' => [
                'name' => 'Blade Radix Icons',
                'url' => 'https://github.com/codeat3/blade-radix-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-radix-icons/refs/heads/main/resources/svg/',
            ],
            'blade-remix-icon' => [
                'name' => 'Blade Remix Icon',
                'url' => 'https://github.com/andreiio/blade-remix-icon',
                'svg' => 'https://raw.githubusercontent.com/andreiio/blade-remix-icon/refs/heads/main/resources/svg/',
            ],
            'blade-rpg-awesome-icons' => [
                'name' => 'Blade RPG Awesome Icons',
                'url' => 'https://github.com/codeat3/blade-rpg-awesome-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-rpg-awesome-icons/refs/heads/main/resources/svg/',
            ],
            'blade-simple-icons' => [
                'name' => 'Blade Simple Icons',
                'url' => 'https://github.com/ublabs/blade-simple-icons',
                'svg' => 'https://raw.githubusercontent.com/ublabs/blade-simple-icons/refs/heads/main/resources/svg/',
            ],
            'blade-simple-line-icons' => [
                'name' => 'Blade Simple Line Icons',
                'url' => 'https://github.com/codeat3/blade-simple-line-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-simple-line-icons/refs/heads/main/resources/svg/',
            ],
            'blade-solar-icons' => [
                'name' => 'Blade Solar Icons',
                'url' => 'https://github.com/codeat3/blade-solar-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-solar-icons/refs/heads/main/resources/svg/',
            ],
            'blade-system-uicons' => [
                'name' => 'Blade System UIcons',
                'url' => 'https://github.com/codeat3/blade-system-uicons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-system-uicons/refs/heads/main/resources/svg/',
            ],
            'blade-tabler-icons' => [
                'name' => 'Blade Tabler Icons',
                'url' => 'https://github.com/secondnetwork/blade-tabler-icons',
                'svg' => 'https://raw.githubusercontent.com/secondnetwork/blade-tabler-icons/refs/heads/main/resources/svg/',
            ],
            'blade-teeny-icons' => [
                'name' => 'Blade Teeny Icons',
                'url' => 'https://github.com/codeat3/blade-teeny-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-teeny-icons/refs/heads/main/resources/svg/',
            ],
            'blade-typicons' => [
                'name' => 'Blade Typicons',
                'url' => 'https://github.com/codeat3/blade-typicons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-typicons/refs/heads/main/resources/svg/',
            ],
            'blade-uiw-icons' => [
                'name' => 'Blade Uiw Icons',
                'url' => 'https://github.com/codeat3/blade-uiw-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-uiw-icons/refs/heads/main/resources/svg/',
            ],
            'blade-unicons' => [
                'name' => 'Blade Unicons',
                'url' => 'https://github.com/codeat3/blade-unicons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-unicons/refs/heads/main/resources/svg/',
            ],
            'blade-untitledui-icons' => [
                'name' => 'Blade UntitledUI Icons',
                'url' => 'https://github.com/mckenziearts/blade-untitledui-icons',
                'svg' => 'https://raw.githubusercontent.com/mckenziearts/blade-untitledui-icons/refs/heads/main/resources/svg/',
            ],
            'blade-vaadin-icons' => [
                'name' => 'Blade Vaadin Icons',
                'url' => 'https://github.com/codeat3/blade-vaadin-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-vaadin-icons/refs/heads/main/resources/svg/',
            ],
            'blade-weather-icons' => [
                'name' => 'Blade Weather Icons',
                'url' => 'https://github.com/codeat3/blade-weather-icons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-weather-icons/refs/heads/main/resources/svg/',
            ],
            'blade-zondicons' => [
                'name' => 'Blade Zondicons',
                'url' => 'https://github.com/codeat3/blade-zondicons',
                'svg' => 'https://raw.githubusercontent.com/codeat3/blade-zondicons/refs/heads/main/resources/svg/',
            ],
        ];
    }
}
