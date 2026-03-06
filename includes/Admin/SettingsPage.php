<?php
/**
 * Settings Page – API credentials, provider selection.
 *
 * @package Enteco\ImmoConnector\Admin
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Admin;

/**
 * Handles plugin settings via WordPress Settings API.
 */
final class SettingsPage {

	private const OPTION_GROUP = 'eic_settings';
	private const NONCE_ACTION = 'eic_settings_save';

	/** Register hooks. */
	public function register(): void {
		add_action( 'admin_menu', [ $this, 'add_submenu' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'wp_ajax_eic_test_connection', [ $this, 'ajax_test_connection' ] );
	}

	/** Add submenu under eic-dashboard. */
	public function add_submenu(): void {
		add_submenu_page(
			'eic-dashboard',
			esc_html__( 'Einstellungen', 'enteco-immo-connector' ),
			esc_html__( 'Einstellungen', 'enteco-immo-connector' ),
			'manage_options',
			'eic-settings',
			[ $this, 'render' ]
		);
	}

	/** Register settings, sections, and fields. */
	public function register_settings(): void {
		// General.
		register_setting( self::OPTION_GROUP, 'eic_active_provider', [
			'type'              => 'string',
			'sanitize_callback' => [ $this, 'sanitize_provider' ],
			'default'           => '',
		] );

		// Field engine – registered here so the sanitize guard applies even
		// if a PRO plugin adds an input field for it.
		register_setting( self::OPTION_GROUP, 'eic_field_engine', [
			'type'              => 'string',
			'sanitize_callback' => [ $this, 'sanitize_field_engine' ],
			'default'           => 'native',
		] );

		// Justimmo.
		register_setting( self::OPTION_GROUP, 'eic_justimmo_username', [
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		] );
		register_setting( self::OPTION_GROUP, 'eic_justimmo_password', [
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		] );

		// OnOffice.
		register_setting( self::OPTION_GROUP, 'eic_onoffice_token', [
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		] );
		register_setting( self::OPTION_GROUP, 'eic_onoffice_secret', [
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		] );

		// Sections.
		add_settings_section( 'eic_general', __( 'Allgemein', 'enteco-immo-connector' ), '__return_false', 'eic-settings' );
		add_settings_section( 'eic_justimmo', __( 'Justimmo', 'enteco-immo-connector' ), '__return_false', 'eic-settings' );
		add_settings_section( 'eic_onoffice', __( 'OnOffice', 'enteco-immo-connector' ), '__return_false', 'eic-settings' );

		// General fields.
		add_settings_field( 'eic_active_provider', __( 'Aktiver Provider', 'enteco-immo-connector' ), [ $this, 'render_provider_field' ], 'eic-settings', 'eic_general' );

		// Justimmo fields.
		add_settings_field( 'eic_justimmo_username', __( 'Benutzername', 'enteco-immo-connector' ), [ $this, 'render_justimmo_username' ], 'eic-settings', 'eic_justimmo' );
		add_settings_field( 'eic_justimmo_password', __( 'Passwort', 'enteco-immo-connector' ), [ $this, 'render_justimmo_password' ], 'eic-settings', 'eic_justimmo' );

		// OnOffice fields.
		add_settings_field( 'eic_onoffice_token', __( 'API Token', 'enteco-immo-connector' ), [ $this, 'render_onoffice_token' ], 'eic-settings', 'eic_onoffice' );
		add_settings_field( 'eic_onoffice_secret', __( 'API Secret', 'enteco-immo-connector' ), [ $this, 'render_onoffice_secret' ], 'eic-settings', 'eic_onoffice' );
	}

	/** Render the settings page. */
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Keine Berechtigung.', 'enteco-immo-connector' ) );
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Immo Connector – Einstellungen', 'enteco-immo-connector' ); ?></h1>

			<form method="post" action="options.php">
				<?php
				settings_fields( self::OPTION_GROUP );
				do_settings_sections( 'eic-settings' );
				submit_button( __( 'Einstellungen speichern', 'enteco-immo-connector' ) );
				?>
			</form>

			<hr>
			<p>
				<button type="button" id="eic-test-connection" class="button">
					<?php echo esc_html__( 'Verbindung testen', 'enteco-immo-connector' ); ?>
				</button>
				<span id="eic-test-result"></span>
			</p>
		</div>
		<?php
	}

	/** Render provider select field. */
	public function render_provider_field(): void {
		$value = (string) get_option( 'eic_active_provider', '' );
		$options = [
			''          => __( '— Bitte wählen —', 'enteco-immo-connector' ),
			'justimmo'  => 'Justimmo',
			'onoffice'  => 'OnOffice',
		];
		echo '<select name="eic_active_provider" id="eic_active_provider">';
		foreach ( $options as $key => $label ) {
			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $key ),
				selected( $value, $key, false ),
				esc_html( $label )
			);
		}
		echo '</select>';
	}

	/** Render Justimmo username field. */
	public function render_justimmo_username(): void {
		$value = (string) get_option( 'eic_justimmo_username', '' );
		printf(
			'<input type="text" name="eic_justimmo_username" id="eic_justimmo_username" value="%s" class="regular-text" autocomplete="off">',
			esc_attr( $value )
		);
	}

	/** Render Justimmo password field. */
	public function render_justimmo_password(): void {
		printf(
			'<input type="password" name="eic_justimmo_password" id="eic_justimmo_password" value="%s" class="regular-text" autocomplete="new-password">',
			esc_attr( (string) get_option( 'eic_justimmo_password', '' ) )
		);
	}

	/** Render OnOffice token field. */
	public function render_onoffice_token(): void {
		printf(
			'<input type="text" name="eic_onoffice_token" id="eic_onoffice_token" value="%s" class="regular-text" autocomplete="off">',
			esc_attr( (string) get_option( 'eic_onoffice_token', '' ) )
		);
	}

	/** Render OnOffice secret field. */
	public function render_onoffice_secret(): void {
		printf(
			'<input type="password" name="eic_onoffice_secret" id="eic_onoffice_secret" value="%s" class="regular-text" autocomplete="new-password">',
			esc_attr( (string) get_option( 'eic_onoffice_secret', '' ) )
		);
	}

	/**
	 * Sanitize provider option.
	 *
	 * @param mixed $value Raw posted value.
	 */
	public function sanitize_provider( mixed $value ): string {
		$allowed = [ '', 'justimmo', 'onoffice' ];
		$value   = sanitize_text_field( (string) $value );
		return in_array( $value, $allowed, true ) ? $value : '';
	}

	/**
	 * Sanitize field engine option.
	 *
	 * After onboarding the engine choice is permanent – the stored value is
	 * returned unchanged regardless of what was submitted.
	 *
	 * @param mixed $value Raw posted value.
	 */
	public function sanitize_field_engine( mixed $value ): string {
		if ( get_option( 'eic_onboarding_complete' ) ) {
			// Engine is locked after onboarding – ignore any submitted value.
			return (string) get_option( 'eic_field_engine', 'native' );
		}

		/** @var array<string, mixed> $engines */
		$engines = apply_filters( 'eic/field_engines', [ 'native' => '' ] );
		$value   = sanitize_key( (string) $value );
		return array_key_exists( $value, $engines ) ? $value : 'native';
	}

	/** AJAX handler: test API connection. */
	public function ajax_test_connection(): void {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Keine Berechtigung.', 'enteco-immo-connector' ) ] );
			return;
		}

		$engine = new \Enteco\ImmoConnector\Import\ImportEngine();
		$ok     = $engine->test_connection();

		if ( $ok ) {
			wp_send_json_success( [ 'message' => __( 'Verbindung erfolgreich!', 'enteco-immo-connector' ) ] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Verbindung fehlgeschlagen. Bitte Zugangsdaten prüfen.', 'enteco-immo-connector' ) ] );
		}
	}
}
