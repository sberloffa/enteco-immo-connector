<?php
/**
 * Core Plugin class – Singleton, hook registration.
 *
 * @package Enteco\ImmoConnector\Core
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Core;

use Enteco\ImmoConnector\PostTypes\PropertyPostType;
use Enteco\ImmoConnector\PostTypes\AgentPostType;
use Enteco\ImmoConnector\Taxonomies\ObjectTypeTaxonomy;
use Enteco\ImmoConnector\Taxonomies\MarketingTypeTaxonomy;
use Enteco\ImmoConnector\Taxonomies\UsageTypeTaxonomy;
use Enteco\ImmoConnector\Taxonomies\ConditionTaxonomy;
use Enteco\ImmoConnector\Taxonomies\HeatingTypeTaxonomy;
use Enteco\ImmoConnector\Taxonomies\LocationTaxonomy;
use Enteco\ImmoConnector\Taxonomies\FeatureTaxonomy;
use Enteco\ImmoConnector\Admin\AdminPage;
use Enteco\ImmoConnector\Admin\SettingsPage;
use Enteco\ImmoConnector\Admin\OnboardingWizard;
use Enteco\ImmoConnector\Admin\ImportStatusPage;

/**
 * Main plugin controller.
 */
final class Plugin {

	/** @var Plugin|null */
	private static ?Plugin $instance = null;

	/** @var string */
	private string $version;

	/** Private constructor – use get_instance(). */
	private function __construct() {
		$this->version = EIC_VERSION;
	}

	/** Singleton accessor. */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/** Register all hooks and boot subsystems. */
	public function run(): void {
		$this->load_dependencies();
		$this->define_hooks();
	}

	/** Instantiate subsystem objects and attach their hooks. */
	private function load_dependencies(): void {
		( new I18n() )->load();
		( new Assets() )->register();
		( new PropertyPostType() )->register();
		( new AgentPostType() )->register();
		( new ObjectTypeTaxonomy() )->register();
		( new MarketingTypeTaxonomy() )->register();
		( new UsageTypeTaxonomy() )->register();
		( new ConditionTaxonomy() )->register();
		( new HeatingTypeTaxonomy() )->register();
		( new LocationTaxonomy() )->register();
		( new FeatureTaxonomy() )->register();

		if ( is_admin() ) {
			( new OnboardingWizard() )->register();
			( new AdminPage() )->register();
			( new SettingsPage() )->register();
			( new ImportStatusPage() )->register();
		}
	}

	/** Plugin-level hooks not covered by subsystems. */
	private function define_hooks(): void {
		add_filter( 'plugin_action_links_' . EIC_PLUGIN_BASENAME, [ $this, 'add_action_links' ] );
	}

	/**
	 * Add Settings link to plugin list.
	 *
	 * @param string[] $links Existing action links.
	 * @return string[]
	 */
	public function add_action_links( array $links ): array {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=eic-settings' ) ),
			esc_html__( 'Einstellungen', 'enteco-immo-connector' )
		);
		array_unshift( $links, $settings_link );
		return $links;
	}

	/** @return string */
	public function get_version(): string {
		return $this->version;
	}
}
