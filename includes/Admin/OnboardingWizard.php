<?php
/**
 * Onboarding Wizard – shown on first activation.
 *
 * @package Enteco\ImmoConnector\Admin
 */

declare(strict_types=1);

namespace Enteco\ImmoConnector\Admin;

/**
 * Guides the user through initial setup (provider + field engine selection).
 * In FREE tier the only field engine is Native, so that step is hidden.
 */
final class OnboardingWizard {

	/** Register hooks. */
	public function register(): void {
		add_action( 'admin_init', [ $this, 'maybe_redirect' ] );
		add_action( 'admin_menu', [ $this, 'add_page' ] );
		add_action( 'admin_post_eic_onboarding_save', [ $this, 'handle_save' ] );
	}

	/** Redirect to onboarding page after activation if not complete. */
	public function maybe_redirect(): void {
		if (
			get_option( 'eic_onboarding_complete' ) ||
			! current_user_can( 'manage_options' ) ||
			wp_doing_ajax()
		) {
			return;
		}

		// Prevent redirect loops.
		if ( isset( $_GET['page'] ) && 'eic-onboarding' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {
			return;
		}

		$redirect_flag = get_option( 'eic_redirect_to_onboarding' );
		if ( $redirect_flag ) {
			delete_option( 'eic_redirect_to_onboarding' );
			wp_safe_redirect( admin_url( 'admin.php?page=eic-onboarding' ) );
			exit;
		}
	}

	/** Register hidden onboarding page. */
	public function add_page(): void {
		add_submenu_page(
			null, // Hidden (not in menu).
			esc_html__( 'Immo Connector Setup', 'enteco-immo-connector' ),
			esc_html__( 'Setup', 'enteco-immo-connector' ),
			'manage_options',
			'eic-onboarding',
			[ $this, 'render' ]
		);
	}

	/** Render wizard. */
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Keine Berechtigung.', 'enteco-immo-connector' ) );
		}
		?>
		<div class="wrap eic-onboarding">
			<h1><?php echo esc_html__( 'Willkommen beim Enteco Immo Connector!', 'enteco-immo-connector' ); ?></h1>
			<p><?php echo esc_html__( 'Bitte wählen Sie Ihren Immobilien-Provider.', 'enteco-immo-connector' ); ?></p>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'eic_onboarding_save', 'eic_onboarding_nonce' ); ?>
				<input type="hidden" name="action" value="eic_onboarding_save">

				<table class="form-table">
					<tr>
						<th><?php echo esc_html__( 'Ihr Provider', 'enteco-immo-connector' ); ?></th>
						<td>
							<label>
								<input type="radio" name="eic_active_provider" value="justimmo"
									<?php checked( 'justimmo', get_option( 'eic_active_provider', '' ) ); ?>>
								Justimmo
							</label>
							<br>
							<label>
								<input type="radio" name="eic_active_provider" value="onoffice"
									<?php checked( 'onoffice', get_option( 'eic_active_provider', '' ) ); ?>>
								OnOffice
							</label>
						</td>
					</tr>
				</table>

				<?php submit_button( __( 'Setup abschließen', 'enteco-immo-connector' ) ); ?>
			</form>
		</div>
		<?php
	}

	/** Process onboarding form submission. */
	public function handle_save(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Keine Berechtigung.', 'enteco-immo-connector' ) );
		}

		check_admin_referer( 'eic_onboarding_save', 'eic_onboarding_nonce' );

		$provider = sanitize_text_field( wp_unslash( $_POST['eic_active_provider'] ?? '' ) );
		$allowed  = [ 'justimmo', 'onoffice' ];

		if ( in_array( $provider, $allowed, true ) ) {
			update_option( 'eic_active_provider', $provider, false );
		}

		update_option( 'eic_field_engine', 'native', false );
		update_option( 'eic_onboarding_complete', true, false );

		wp_safe_redirect( admin_url( 'admin.php?page=eic-settings' ) );
		exit;
	}
}
