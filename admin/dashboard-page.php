<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function palaplast_render_dashboard_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	$admin_links = array(
		array(
			'title'       => __( 'Technical Sheets', 'palaplast' ),
			'description' => __( 'Upload PDF technical sheets and assign them globally or to product categories.', 'palaplast' ),
			'url'         => admin_url( 'admin.php?page=palaplast-technical-sheets' ),
		),
		array(
			'title'       => __( 'Pricelists', 'palaplast' ),
			'description' => __( 'Upload PDF pricelists and assign them globally or to product categories.', 'palaplast' ),
			'url'         => admin_url( 'admin.php?page=palaplast-pricelists' ),
		),
		array(
			'title'       => __( 'Variation Colors', 'palaplast' ),
			'description' => __( 'Create reusable color labels and apply them to variation attribute values.', 'palaplast' ),
			'url'         => admin_url( 'admin.php?page=palaplast-variation-colors' ),
		),
		array(
			'title'       => __( 'Certificates', 'palaplast' ),
			'description' => __( 'Create certificate entries with thumbnails, descriptions, and PDF downloads.', 'palaplast' ),
			'url'         => admin_url( 'edit.php?post_type=palaplast_cert' ),
		),
	);

	$shortcodes = array(
		array(
			'code'        => '[palaplast_variation_table]',
			'description' => __( 'Shows the variation matrix for the current variable product.', 'palaplast' ),
			'example'     => '[palaplast_variation_table product_id="123"]',
		),
		array(
			'code'        => '[palaplast_technical_sheet]',
			'description' => __( 'Shows the technical sheet button for the current product.', 'palaplast' ),
			'example'     => '[palaplast_technical_sheet product_id="123"]',
		),
		array(
			'code'        => '[palaplast_pricelist_pdf]',
			'description' => __( 'Shows the pricelist PDF button for the current product.', 'palaplast' ),
			'example'     => '[palaplast_pricelist_pdf product_id="123"]',
		),
		array(
			'code'        => '[palaplast_technical_sheets_list]',
			'description' => __( 'Shows a list of all technical sheet PDFs.', 'palaplast' ),
			'example'     => '[palaplast_technical_sheets_list category="installation,compliance"]',
		),
		array(
			'code'        => '[palaplast_pricelists_list]',
			'description' => __( 'Shows a list of all pricelist PDFs.', 'palaplast' ),
			'example'     => '[palaplast_pricelists_list show_title="no"]',
		),
		array(
			'code'        => '[palaplast_certificates_list]',
			'description' => __( 'Shows all published certificates with PDF download links.', 'palaplast' ),
			'example'     => '[palaplast_certificates_list]',
		),
	);
	?>
	<div class="wrap palaplast-dashboard-wrap">
		<h1><?php esc_html_e( 'Palaplast Dashboard', 'palaplast' ); ?></h1>
		<p class="palaplast-dashboard-intro"><?php esc_html_e( 'Use this page as a quick guide for managing Palaplast content and adding frontend shortcodes to pages, posts, and product descriptions.', 'palaplast' ); ?></p>

		<div class="palaplast-dashboard-grid">
			<div class="card palaplast-admin-card palaplast-dashboard-card">
				<h2 class="palaplast-admin-card-title"><?php esc_html_e( 'How to use Palaplast', 'palaplast' ); ?></h2>
				<ol class="palaplast-dashboard-steps">
					<li><?php esc_html_e( 'Create or edit WooCommerce variable products and add variations as usual.', 'palaplast' ); ?></li>
					<li><?php esc_html_e( 'Upload technical sheets, pricelists, and certificates from the Palaplast admin pages.', 'palaplast' ); ?></li>
					<li><?php esc_html_e( 'Assign PDFs to product categories when you want products in those categories to inherit the same download.', 'palaplast' ); ?></li>
					<li><?php esc_html_e( 'Paste the shortcodes below into any page, post, product content, or compatible page builder block.', 'palaplast' ); ?></li>
				</ol>
			</div>

			<div class="card palaplast-admin-card palaplast-dashboard-card">
				<h2 class="palaplast-admin-card-title"><?php esc_html_e( 'Admin pages', 'palaplast' ); ?></h2>
				<ul class="palaplast-dashboard-links">
					<?php foreach ( $admin_links as $admin_link ) : ?>
						<li>
							<a href="<?php echo esc_url( $admin_link['url'] ); ?>"><?php echo esc_html( $admin_link['title'] ); ?></a>
							<span><?php echo esc_html( $admin_link['description'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<div class="card palaplast-admin-card palaplast-dashboard-shortcodes-card">
			<h2 class="palaplast-admin-card-title"><?php esc_html_e( 'Available shortcodes', 'palaplast' ); ?></h2>
			<p><?php esc_html_e( 'Copy any shortcode into your content. Examples show optional attributes you can customize.', 'palaplast' ); ?></p>
			<table class="widefat striped palaplast-admin-table palaplast-shortcodes-table">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Shortcode', 'palaplast' ); ?></th>
						<th scope="col"><?php esc_html_e( 'What it does', 'palaplast' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Example', 'palaplast' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $shortcodes as $shortcode ) : ?>
						<tr>
							<td><code><?php echo esc_html( $shortcode['code'] ); ?></code></td>
							<td><?php echo esc_html( $shortcode['description'] ); ?></td>
							<td><code><?php echo esc_html( $shortcode['example'] ); ?></code></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
}
