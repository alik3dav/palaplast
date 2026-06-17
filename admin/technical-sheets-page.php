<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function palaplast_render_technical_sheets_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	$sheets  = palaplast_get_technical_sheets();
	$edit_id = absint( filter_input( INPUT_GET, 'edit_sheet', FILTER_SANITIZE_NUMBER_INT ) );
	$sheet   = ( $edit_id && isset( $sheets[ $edit_id ] ) ) ? $sheets[ $edit_id ] : array();
	$sheet_categories = palaplast_get_technical_sheet_categories();
	$brand_terms      = function_exists( 'palaplast_get_product_brand_terms' ) ? palaplast_get_product_brand_terms() : array();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Technical Sheets', 'palaplast' ); ?></h1>

		<div class="notice notice-info palaplast-admin-notice">
			<p>
				<strong><?php esc_html_e( 'Frontend shortcode (single product):', 'palaplast' ); ?></strong>
				<code>[palaplast_technical_sheet]</code>
			</p>
			<p>
				<strong><?php esc_html_e( 'Frontend shortcode (full list page):', 'palaplast' ); ?></strong>
				<code>[palaplast_technical_sheets_list]</code>
			</p>
			<p>
				<strong><?php esc_html_e( 'Filter by technical sheet category slug(s):', 'palaplast' ); ?></strong>
				<code>[palaplast_technical_sheets_list category="installation,compliance"]</code>
			</p>
		</div>

		<?php if ( filter_input( INPUT_GET, 'sheet_updated', FILTER_SANITIZE_NUMBER_INT ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Technical Sheet saved.', 'palaplast' ); ?></p></div>
		<?php endif; ?>

		<?php if ( filter_input( INPUT_GET, 'sheet_deleted', FILTER_SANITIZE_NUMBER_INT ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Technical Sheet deleted.', 'palaplast' ); ?></p></div>
		<?php endif; ?>

		<?php if ( filter_input( INPUT_GET, 'sheet_order_updated', FILTER_SANITIZE_NUMBER_INT ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Technical Sheet order saved.', 'palaplast' ); ?></p></div>
		<?php endif; ?>

		<div class="card palaplast-admin-card">
			<h2 class="palaplast-admin-card-title"><?php echo $edit_id ? esc_html__( 'Edit Technical Sheet', 'palaplast' ) : esc_html__( 'Add Technical Sheet', 'palaplast' ); ?></h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'palaplast_save_sheet' ); ?>
				<input type="hidden" name="action" value="palaplast_save_sheet" />
				<input type="hidden" name="sheet_id" value="<?php echo esc_attr( $edit_id ); ?>" />

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="palaplast_sheet_name"><?php esc_html_e( 'Name', 'palaplast' ); ?></label></th>
						<td><input type="text" class="regular-text" id="palaplast_sheet_name" name="sheet_name" required value="<?php echo isset( $sheet['name'] ) ? esc_attr( $sheet['name'] ) : ''; ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'PDF File', 'palaplast' ); ?></th>
						<td>
							<?php $attachment_id = isset( $sheet['attachment_id'] ) ? (int) $sheet['attachment_id'] : 0; ?>
							<input type="hidden" id="palaplast_attachment_id" name="attachment_id" value="<?php echo esc_attr( $attachment_id ); ?>" />
							<button class="button palaplast-select-pdf"><?php esc_html_e( 'Select PDF', 'palaplast' ); ?></button>
							<button class="button palaplast-remove-pdf"><?php esc_html_e( 'Remove', 'palaplast' ); ?></button>
							<p class="description palaplast-selected-file">
								<?php
								if ( $attachment_id ) {
									echo esc_html( basename( (string) get_attached_file( $attachment_id ) ) );
								} else {
									esc_html_e( 'No file selected.', 'palaplast' );
								}
								?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="palaplast_sheet_brand_term_id"><?php esc_html_e( 'Brand', 'palaplast' ); ?></label></th>
						<td>
							<?php $selected_brand_term_id = isset( $sheet['brand_term_id'] ) ? (int) $sheet['brand_term_id'] : 0; ?>
							<select id="palaplast_sheet_brand_term_id" name="sheet_brand_term_id">
								<option value=""><?php esc_html_e( '— No brand —', 'palaplast' ); ?></option>
								<?php foreach ( $brand_terms as $brand_term ) : ?>
									<option value="<?php echo esc_attr( $brand_term->term_id ); ?>" <?php selected( $selected_brand_term_id, (int) $brand_term->term_id ); ?>><?php echo esc_html( $brand_term->name ); ?></option>
								<?php endforeach; ?>
							</select>
							<?php if ( empty( $brand_terms ) ) : ?>
								<p class="description"><?php esc_html_e( 'No WooCommerce product brands were found.', 'palaplast' ); ?></p>
							<?php else : ?>
								<p class="description"><?php esc_html_e( 'Choose a brand from the WooCommerce product brands list. The brand logo is used automatically when available.', 'palaplast' ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="palaplast_sheet_category"><?php esc_html_e( 'Technical Sheet Category', 'palaplast' ); ?></label></th>
						<td>
							<select id="palaplast_sheet_category" name="sheet_category">
								<option value=""><?php esc_html_e( '— No category —', 'palaplast' ); ?></option>
								<?php foreach ( $sheet_categories as $category_slug => $category_name ) : ?>
									<option value="<?php echo esc_attr( $category_slug ); ?>" <?php selected( isset( $sheet['category'] ) ? (string) $sheet['category'] : '', (string) $category_slug ); ?>>
										<?php echo esc_html( $category_name ); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<p class="description"><?php esc_html_e( 'Use an existing technical sheet category.', 'palaplast' ); ?></p>
							<p>
								<label for="palaplast_sheet_category_new"><strong><?php esc_html_e( 'Or create a new category', 'palaplast' ); ?></strong></label><br />
								<input type="text" class="regular-text" id="palaplast_sheet_category_new" name="sheet_category_new" value="" />
							</p>
						</td>
					</tr>
				</table>

				<?php submit_button( $edit_id ? __( 'Update Sheet', 'palaplast' ) : __( 'Add Sheet', 'palaplast' ) ); ?>
			</form>
		</div>

		<h2 class="palaplast-admin-list-title"><?php esc_html_e( 'All Technical Sheets', 'palaplast' ); ?></h2>
		<p class="description palaplast-admin-list-description"><?php esc_html_e( 'Drag technical sheets within each category to set the frontend display order, then click Save Order.', 'palaplast' ); ?></p>
		<?php $sheets_by_category = palaplast_group_technical_sheets_by_category( $sheets ); ?>
		<?php if ( empty( $sheets_by_category ) ) : ?>
			<table class="widefat striped palaplast-admin-table">
				<tbody><tr><td><?php esc_html_e( 'No technical sheets found.', 'palaplast' ); ?></td></tr></tbody>
			</table>
		<?php else : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="palaplast-sheet-order-form">
				<?php wp_nonce_field( 'palaplast_save_sheet_order' ); ?>
				<input type="hidden" name="action" value="palaplast_save_sheet_order" />
				<?php foreach ( $sheets_by_category as $category_key => $category_group ) : ?>
					<div class="palaplast-sheet-category-sort-group">
						<h3 class="palaplast-sheet-category-sort-title"><?php echo esc_html( $category_group['name'] ); ?></h3>
						<table class="widefat striped palaplast-admin-table palaplast-sortable-sheets-table">
							<thead><tr><th class="palaplast-sort-handle-column"><?php esc_html_e( 'Sort', 'palaplast' ); ?></th><th><?php esc_html_e( 'Name', 'palaplast' ); ?></th><th><?php esc_html_e( 'Brand', 'palaplast' ); ?></th><th><?php esc_html_e( 'PDF File', 'palaplast' ); ?></th><th><?php esc_html_e( 'Date', 'palaplast' ); ?></th><th><?php esc_html_e( 'Actions', 'palaplast' ); ?></th></tr></thead>
							<tbody class="palaplast-sortable-sheets" data-category="<?php echo esc_attr( $category_key ); ?>">
								<?php foreach ( $category_group['sheets'] as $sheet_id => $sheet_data ) :
									$file_url  = ! empty( $sheet_data['attachment_id'] ) ? wp_get_attachment_url( (int) $sheet_data['attachment_id'] ) : '';
									$file_name = ! empty( $sheet_data['attachment_id'] ) ? basename( (string) get_attached_file( (int) $sheet_data['attachment_id'] ) ) : '';
									$edit_url  = add_query_arg( array( 'page' => 'palaplast-technical-sheets', 'edit_sheet' => $sheet_id ), admin_url( 'admin.php' ) );
									$delete_url = wp_nonce_url( add_query_arg( array( 'action' => 'palaplast_delete_sheet', 'sheet_id' => $sheet_id ), admin_url( 'admin-post.php' ) ), 'palaplast_delete_sheet_' . $sheet_id );
									?>
									<tr class="palaplast-sortable-sheet-row" data-sheet-id="<?php echo esc_attr( $sheet_id ); ?>">
										<td class="palaplast-sort-handle-column"><span class="palaplast-sort-handle dashicons dashicons-menu" aria-label="<?php esc_attr_e( 'Drag to reorder', 'palaplast' ); ?>"></span><input type="hidden" name="sheet_order[<?php echo esc_attr( $category_key ); ?>][]" value="<?php echo esc_attr( $sheet_id ); ?>" /></td>
										<td><?php echo esc_html( isset( $sheet_data['name'] ) ? $sheet_data['name'] : '' ); ?></td>
										<td><?php echo ! empty( $sheet_data['brand_name'] ) ? esc_html( $sheet_data['brand_name'] ) : '—'; ?></td>
										<td><?php if ( $file_url ) : ?><a href="<?php echo esc_url( $file_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $file_name ? $file_name : $file_url ); ?></a><?php else : esc_html_e( 'No file', 'palaplast' ); endif; ?></td>
										<td><?php echo ! empty( $sheet_data['created_at'] ) ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $sheet_data['created_at'] ) ) ) : ''; ?></td>
										<td><a class="button button-small" href="<?php echo esc_url( $edit_url ); ?>"><?php esc_html_e( 'Edit', 'palaplast' ); ?></a> <a class="button button-small" href="<?php echo esc_url( $delete_url ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Delete this technical sheet?', 'palaplast' ) ); ?>');"><?php esc_html_e( 'Delete', 'palaplast' ); ?></a></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endforeach; ?>
				<?php submit_button( __( 'Save Order', 'palaplast' ) ); ?>
			</form>
		<?php endif; ?>
	</div>
	<?php
}


function palaplast_group_technical_sheets_by_category( $sheets ) {
	$groups = array();

	foreach ( $sheets as $sheet_id => $sheet ) {
		if ( ! is_array( $sheet ) ) {
			continue;
		}

		$category_slug = isset( $sheet['category'] ) ? sanitize_title( (string) $sheet['category'] ) : '';
		$category_key  = '' !== $category_slug ? $category_slug : 'uncategorized';
		$category_name = ! empty( $sheet['category_name'] ) ? sanitize_text_field( (string) $sheet['category_name'] ) : '';

		if ( '' === $category_name ) {
			$category_name = '' !== $category_slug ? ucwords( str_replace( '-', ' ', $category_slug ) ) : __( 'Uncategorized', 'palaplast' );
		}

		if ( ! isset( $groups[ $category_key ] ) ) {
			$groups[ $category_key ] = array(
				'name'   => $category_name,
				'sheets' => array(),
			);
		}

		$groups[ $category_key ]['sheets'][ $sheet_id ] = $sheet;
	}

	uksort(
		$groups,
		function ( $a, $b ) use ( $groups ) {
			return strnatcasecmp( (string) $groups[ $a ]['name'], (string) $groups[ $b ]['name'] );
		}
	);

	return $groups;
}

function palaplast_get_next_sheet_position( $sheets, $category_slug ) {
	$category_slug = sanitize_title( (string) $category_slug );
	$max_position  = 0;

	foreach ( $sheets as $sheet ) {
		if ( ! is_array( $sheet ) ) {
			continue;
		}

		$sheet_category_slug = isset( $sheet['category'] ) ? sanitize_title( (string) $sheet['category'] ) : '';
		if ( $sheet_category_slug !== $category_slug ) {
			continue;
		}

		$max_position = max( $max_position, isset( $sheet['position'] ) ? (int) $sheet['position'] : 0 );
	}

	return $max_position + 1;
}

function palaplast_handle_save_sheet() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'palaplast' ) );
	}

	check_admin_referer( 'palaplast_save_sheet' );
	$sheet_id      = isset( $_POST['sheet_id'] ) ? absint( wp_unslash( $_POST['sheet_id'] ) ) : 0;
	$sheet_name    = isset( $_POST['sheet_name'] ) ? sanitize_text_field( wp_unslash( $_POST['sheet_name'] ) ) : '';
	$attachment_id = isset( $_POST['attachment_id'] ) ? absint( wp_unslash( $_POST['attachment_id'] ) ) : 0;
	$brand_term_id  = isset( $_POST['sheet_brand_term_id'] ) ? absint( wp_unslash( $_POST['sheet_brand_term_id'] ) ) : 0;
	$brand_taxonomy = function_exists( 'palaplast_get_product_brand_taxonomy' ) ? palaplast_get_product_brand_taxonomy() : '';
	$brand_term     = ( $brand_term_id && '' !== $brand_taxonomy ) ? get_term( $brand_term_id, $brand_taxonomy ) : null;
	$brand_name    = $brand_term instanceof WP_Term ? $brand_term->name : '';
	$brand_logo_id = ( $brand_term instanceof WP_Term && function_exists( 'palaplast_get_product_brand_logo_id' ) ) ? palaplast_get_product_brand_logo_id( $brand_term_id ) : 0;
	$selected_category_slug = isset( $_POST['sheet_category'] ) ? sanitize_title( wp_unslash( $_POST['sheet_category'] ) ) : '';
	$new_category_name      = isset( $_POST['sheet_category_new'] ) ? sanitize_text_field( wp_unslash( $_POST['sheet_category_new'] ) ) : '';
	$new_category_slug      = sanitize_title( $new_category_name );

	$sheet_category_slug = $new_category_slug ? $new_category_slug : $selected_category_slug;
	$sheet_category_name = $new_category_name ? $new_category_name : palaplast_get_technical_sheet_category_name_by_slug( $sheet_category_slug );

	if ( '' === $sheet_name || ! palaplast_is_valid_pdf_attachment( $attachment_id ) ) {
		wp_safe_redirect( admin_url( 'admin.php?page=palaplast-technical-sheets' ) );
		exit;
	}

	$sheets = palaplast_get_technical_sheets();
	if ( $sheet_id && isset( $sheets[ $sheet_id ] ) ) {
		$previous_category = isset( $sheets[ $sheet_id ]['category'] ) ? sanitize_title( (string) $sheets[ $sheet_id ]['category'] ) : '';
		$sheets[ $sheet_id ]['name']          = $sheet_name;
		$sheets[ $sheet_id ]['attachment_id'] = $attachment_id;
		$sheets[ $sheet_id ]['category']      = $sheet_category_slug;
		$sheets[ $sheet_id ]['category_name'] = $sheet_category_name;
		$sheets[ $sheet_id ]['brand_name']    = $brand_name;
		$sheets[ $sheet_id ]['brand_term_id'] = $brand_term_id;
		$sheets[ $sheet_id ]['brand_logo_id'] = $brand_logo_id;
		if ( $previous_category !== $sheet_category_slug || empty( $sheets[ $sheet_id ]['position'] ) ) {
			$sheets[ $sheet_id ]['position'] = palaplast_get_next_sheet_position( $sheets, $sheet_category_slug );
		}
	} else {
		$sheet_id            = time() + wp_rand( 1, 999 );
		$sheets[ $sheet_id ] = array(
			'name'          => $sheet_name,
			'attachment_id' => $attachment_id,
			'category'      => $sheet_category_slug,
			'category_name' => $sheet_category_name,
			'brand_name'    => $brand_name,
			'brand_term_id' => $brand_term_id,
			'brand_logo_id' => $brand_logo_id,
			'position'      => palaplast_get_next_sheet_position( $sheets, $sheet_category_slug ),
			'created_at'    => current_time( 'mysql' ),
		);
	}

	update_option( 'palaplast_technical_sheets', $sheets, false );
	wp_safe_redirect( admin_url( 'admin.php?page=palaplast-technical-sheets&sheet_updated=1' ) );
	exit;
}

function palaplast_handle_delete_sheet() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'palaplast' ) );
	}

	$sheet_id = isset( $_GET['sheet_id'] ) ? absint( wp_unslash( $_GET['sheet_id'] ) ) : 0;
	if ( ! $sheet_id ) {
		wp_safe_redirect( admin_url( 'admin.php?page=palaplast-technical-sheets' ) );
		exit;
	}

	check_admin_referer( 'palaplast_delete_sheet_' . $sheet_id );
	$sheets = palaplast_get_technical_sheets();
	if ( isset( $sheets[ $sheet_id ] ) ) {
		unset( $sheets[ $sheet_id ] );
		update_option( 'palaplast_technical_sheets', $sheets, false );
		palaplast_clear_sheet_from_categories( $sheet_id );
	}

	wp_safe_redirect( admin_url( 'admin.php?page=palaplast-technical-sheets&sheet_deleted=1' ) );
	exit;
}


function palaplast_handle_save_sheet_order() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'palaplast' ) );
	}

	check_admin_referer( 'palaplast_save_sheet_order' );

	$sheets       = palaplast_get_technical_sheets();
	$posted_order = isset( $_POST['sheet_order'] ) && is_array( $_POST['sheet_order'] ) ? wp_unslash( $_POST['sheet_order'] ) : array();

	foreach ( $posted_order as $category_key => $sheet_ids ) {
		if ( ! is_array( $sheet_ids ) ) {
			continue;
		}

		$category_key = sanitize_title( (string) $category_key );
		$position     = 1;

		foreach ( $sheet_ids as $sheet_id ) {
			$sheet_id = absint( $sheet_id );
			if ( ! $sheet_id || ! isset( $sheets[ $sheet_id ] ) ) {
				continue;
			}

			$sheet_category = isset( $sheets[ $sheet_id ]['category'] ) ? sanitize_title( (string) $sheets[ $sheet_id ]['category'] ) : '';
			$sheet_category = '' !== $sheet_category ? $sheet_category : 'uncategorized';
			if ( $sheet_category !== $category_key ) {
				continue;
			}

			$sheets[ $sheet_id ]['position'] = $position;
			++$position;
		}
	}

	update_option( 'palaplast_technical_sheets', $sheets, false );
	wp_safe_redirect( admin_url( 'admin.php?page=palaplast-technical-sheets&sheet_order_updated=1' ) );
	exit;
}
