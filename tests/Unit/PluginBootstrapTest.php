<?php
/**
 * Tests that all core PHP files can be loaded without fatal errors.
 *
 * @package Enteco\ImmoConnector\Tests\Unit
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Smoke tests – verify all classes exist and can be instantiated/referenced.
 */
class PluginBootstrapTest extends TestCase
{
    /**
     * @dataProvider provide_class_names
     */
    public function test_class_exists(string $class_name): void
    {
        self::assertTrue(class_exists($class_name), "Class does not exist: $class_name");
    }

    /**
     * @dataProvider provide_interface_names
     */
    public function test_interface_exists(string $interface_name): void
    {
        self::assertTrue(interface_exists($interface_name), "Interface does not exist: $interface_name");
    }

    /**
     * @dataProvider provide_class_names_with_interfaces
     */
    public function test_class_implements_interface(string $class_name, string $interface_name): void
    {
        self::assertTrue(
            is_a($class_name, $interface_name, true),
            "$class_name does not implement $interface_name"
        );
    }

    /** @return array<string, array{string}> */
    public static function provide_class_names(): array
    {
        return [
            'Plugin'              => [\Enteco\ImmoConnector\Core\Plugin::class],
            'Activator'           => [\Enteco\ImmoConnector\Core\Activator::class],
            'Deactivator'         => [\Enteco\ImmoConnector\Core\Deactivator::class],
            'I18n'                => [\Enteco\ImmoConnector\Core\I18n::class],
            'Assets'              => [\Enteco\ImmoConnector\Core\Assets::class],
            'PropertyPostType'    => [\Enteco\ImmoConnector\PostTypes\PropertyPostType::class],
            'AgentPostType'       => [\Enteco\ImmoConnector\PostTypes\AgentPostType::class],
            'FieldDefinitions'    => [\Enteco\ImmoConnector\PostTypes\FieldEngine\FieldDefinitions::class],
            'NativeFieldEngine'   => [\Enteco\ImmoConnector\PostTypes\FieldEngine\NativeFieldEngine::class],
            'ObjectTypeTaxonomy'  => [\Enteco\ImmoConnector\Taxonomies\ObjectTypeTaxonomy::class],
            'MarketingTypeTaxonomy' => [\Enteco\ImmoConnector\Taxonomies\MarketingTypeTaxonomy::class],
            'UsageTypeTaxonomy'   => [\Enteco\ImmoConnector\Taxonomies\UsageTypeTaxonomy::class],
            'ConditionTaxonomy'   => [\Enteco\ImmoConnector\Taxonomies\ConditionTaxonomy::class],
            'HeatingTypeTaxonomy' => [\Enteco\ImmoConnector\Taxonomies\HeatingTypeTaxonomy::class],
            'LocationTaxonomy'    => [\Enteco\ImmoConnector\Taxonomies\LocationTaxonomy::class],
            'FeatureTaxonomy'     => [\Enteco\ImmoConnector\Taxonomies\FeatureTaxonomy::class],
            'Schema'              => [\Enteco\ImmoConnector\OpenImmo\Schema::class],
            'FieldGroups'         => [\Enteco\ImmoConnector\OpenImmo\FieldGroups::class],
            'Mapper'              => [\Enteco\ImmoConnector\OpenImmo\Mapper::class],
            'ApiResponse'         => [\Enteco\ImmoConnector\Api\ApiResponse::class],
            'JustimmoClient'      => [\Enteco\ImmoConnector\Api\Justimmo\JustimmoClient::class],
            'JustimmoMapper'      => [\Enteco\ImmoConnector\Api\Justimmo\JustimmoMapper::class],
            'JustimmoProvider'    => [\Enteco\ImmoConnector\Api\Justimmo\JustimmoProvider::class],
            'OnOfficeClient'      => [\Enteco\ImmoConnector\Api\OnOffice\OnOfficeClient::class],
            'OnOfficeMapper'      => [\Enteco\ImmoConnector\Api\OnOffice\OnOfficeMapper::class],
            'OnOfficeProvider'    => [\Enteco\ImmoConnector\Api\OnOffice\OnOfficeProvider::class],
            'ImportDiff'          => [\Enteco\ImmoConnector\Import\ImportDiff::class],
            'MediaHandler'        => [\Enteco\ImmoConnector\Import\MediaHandler::class],
            'PropertyImporter'    => [\Enteco\ImmoConnector\Import\PropertyImporter::class],
            'AgentImporter'       => [\Enteco\ImmoConnector\Import\AgentImporter::class],
            'ImportJob'           => [\Enteco\ImmoConnector\Import\ImportJob::class],
            'ImportEngine'        => [\Enteco\ImmoConnector\Import\ImportEngine::class],
            'AdminPage'           => [\Enteco\ImmoConnector\Admin\AdminPage::class],
            'SettingsPage'        => [\Enteco\ImmoConnector\Admin\SettingsPage::class],
            'OnboardingWizard'    => [\Enteco\ImmoConnector\Admin\OnboardingWizard::class],
            'ImportStatusPage'    => [\Enteco\ImmoConnector\Admin\ImportStatusPage::class],
        ];
    }

    /** @return array<string, array{string}> */
    public static function provide_interface_names(): array
    {
        return [
            'FieldEngineInterface' => [\Enteco\ImmoConnector\PostTypes\FieldEngine\FieldEngineInterface::class],
            'ApiInterface'         => [\Enteco\ImmoConnector\Api\ApiInterface::class],
        ];
    }

    /** @return array<string, array{string, string}> */
    public static function provide_class_names_with_interfaces(): array
    {
        return [
            'NativeFieldEngine implements FieldEngineInterface' => [
                \Enteco\ImmoConnector\PostTypes\FieldEngine\NativeFieldEngine::class,
                \Enteco\ImmoConnector\PostTypes\FieldEngine\FieldEngineInterface::class,
            ],
            'JustimmoProvider implements ApiInterface' => [
                \Enteco\ImmoConnector\Api\Justimmo\JustimmoProvider::class,
                \Enteco\ImmoConnector\Api\ApiInterface::class,
            ],
            'OnOfficeProvider implements ApiInterface' => [
                \Enteco\ImmoConnector\Api\OnOffice\OnOfficeProvider::class,
                \Enteco\ImmoConnector\Api\ApiInterface::class,
            ],
        ];
    }

    public function test_plugin_constants_are_defined(): void
    {
        self::assertTrue(defined('EIC_VERSION'));
        self::assertTrue(defined('EIC_PLUGIN_FILE'));
        self::assertTrue(defined('EIC_PLUGIN_DIR'));
        self::assertTrue(defined('EIC_PLUGIN_URL'));
        self::assertTrue(defined('EIC_PLUGIN_BASENAME'));
        self::assertTrue(defined('EIC_MIN_PHP'));
        self::assertTrue(defined('EIC_MIN_WP'));
    }

    public function test_min_php_version_met(): void
    {
        self::assertTrue(version_compare(PHP_VERSION, EIC_MIN_PHP, '>='));
    }
}
