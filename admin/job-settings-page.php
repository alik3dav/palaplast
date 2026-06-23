<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function palaplast_render_job_settings_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	$form_shortcode = palaplast_get_job_contact_form_shortcode();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Job Settings', 'palaplast' ); ?></h1>

		<div class="notice notice-info palaplast-admin-notice">
			<p><?php esc_html_e( 'Add one Contact Form 7 shortcode here and it will appear below the content on every single Job post.', 'palaplast' ); ?></p>
		</div>

		<?php if ( filter_input( INPUT_GET, 'job_settings_updated', FILTER_SANITIZE_NUMBER_INT ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Job settings saved.', 'palaplast' ); ?></p></div>
		<?php endif; ?>

		<div class="card palaplast-admin-card">
			<h2 class="palaplast-admin-card-title"><?php esc_html_e( 'Jobs Cards Shortcode', 'palaplast' ); ?></h2>
			<p><?php esc_html_e( 'Add this shortcode to any page where you want to show the published Job posts as cards:', 'palaplast' ); ?></p>
			<p><code>[palaplast_jobs]</code></p>
			<p class="description"><?php esc_html_e( 'Optional example: [palaplast_jobs posts_per_page="6" orderby="date" order="DESC" show_excerpt="yes"]', 'palaplast' ); ?></p>
		</div>

		<div class="card palaplast-admin-card">
			<h2 class="palaplast-admin-card-title"><?php esc_html_e( 'Job Contact Form', 'palaplast' ); ?></h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'palaplast_save_job_settings' ); ?>
				<input type="hidden" name="action" value="palaplast_save_job_settings" />

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="palaplast_job_contact_form_shortcode"><?php esc_html_e( 'Contact Form 7 shortcode', 'palaplast' ); ?></label>
						</th>
						<td>
							<textarea id="palaplast_job_contact_form_shortcode" name="palaplast_job_contact_form_shortcode" class="large-text code" rows="4" placeholder="<?php echo esc_attr__( '[contact-form-7 id="123" title="Job Application"]', 'palaplast' ); ?>"><?php echo esc_textarea( $form_shortcode ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Paste the Contact Form 7 shortcode that should be shown below every custom Job post. Leave empty to hide the form.', 'palaplast' ); ?></p>
						</td>
					</tr>
				</table>

				<?php submit_button( __( 'Save Job Settings', 'palaplast' ) ); ?>
			</form>
		</div>
	</div>
	<?php
}

function palaplast_handle_save_job_settings() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'palaplast' ) );
	}

	check_admin_referer( 'palaplast_save_job_settings' );

	$form_shortcode = isset( $_POST['palaplast_job_contact_form_shortcode'] ) ? sanitize_textarea_field( wp_unslash( $_POST['palaplast_job_contact_form_shortcode'] ) ) : '';

	update_option( 'palaplast_job_contact_form_shortcode', $form_shortcode, false );
	wp_safe_redirect( admin_url( 'admin.php?page=palaplast-job-settings&job_settings_updated=1' ) );
	exit;
}
