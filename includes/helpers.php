<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function palaplast_get_technical_sheets() {
	$sheets = get_option( 'palaplast_technical_sheets', array() );

	if ( ! is_array( $sheets ) ) {
		return array();
	}

	uksort(
		$sheets,
		function ( $a, $b ) use ( $sheets ) {
			$sheet_a = isset( $sheets[ $a ] ) && is_array( $sheets[ $a ] ) ? $sheets[ $a ] : array();
			$sheet_b = isset( $sheets[ $b ] ) && is_array( $sheets[ $b ] ) ? $sheets[ $b ] : array();

			$category_a = ! empty( $sheet_a['category_name'] ) ? (string) $sheet_a['category_name'] : ( isset( $sheet_a['category'] ) ? (string) $sheet_a['category'] : '' );
			$category_b = ! empty( $sheet_b['category_name'] ) ? (string) $sheet_b['category_name'] : ( isset( $sheet_b['category'] ) ? (string) $sheet_b['category'] : '' );
			$category_compare = strnatcasecmp( $category_a, $category_b );
			if ( 0 !== $category_compare ) {
				return $category_compare;
			}

			$position_a = isset( $sheet_a['position'] ) ? (int) $sheet_a['position'] : PHP_INT_MAX;
			$position_b = isset( $sheet_b['position'] ) ? (int) $sheet_b['position'] : PHP_INT_MAX;
			if ( $position_a !== $position_b ) {
				return $position_a <=> $position_b;
			}

			return (int) $a <=> (int) $b;
		}
	);

	return $sheets;
}

function palaplast_get_technical_sheet_categories() {
	$sheets     = palaplast_get_technical_sheets();
	$categories = array();

	foreach ( $sheets as $sheet ) {
		if ( ! is_array( $sheet ) ) {
			continue;
		}

		$category_slug = isset( $sheet['category'] ) ? sanitize_title( (string) $sheet['category'] ) : '';
		if ( '' === $category_slug ) {
			continue;
		}

		$category_name = isset( $sheet['category_name'] ) ? sanitize_text_field( (string) $sheet['category_name'] ) : '';
		if ( '' === $category_name ) {
			$category_name = ucwords( str_replace( '-', ' ', $category_slug ) );
		}

		$categories[ $category_slug ] = $category_name;
	}

	asort( $categories, SORT_NATURAL | SORT_FLAG_CASE );

	return $categories;
}

function palaplast_get_technical_sheet_category_name_by_slug( $category_slug ) {
	$category_slug = sanitize_title( (string) $category_slug );

	if ( '' === $category_slug ) {
		return '';
	}

	$categories = palaplast_get_technical_sheet_categories();
	if ( isset( $categories[ $category_slug ] ) ) {
		return (string) $categories[ $category_slug ];
	}

	return '';
}


function palaplast_get_product_brand_taxonomy() {
	$brand_taxonomies = array(
		'product_brand',
		'product_brands',
		'pwb-brand',
		'yith_product_brand',
		'pa_brand',
	);

	foreach ( $brand_taxonomies as $taxonomy ) {
		if ( taxonomy_exists( $taxonomy ) ) {
			return $taxonomy;
		}
	}

	return '';
}

function palaplast_get_product_brand_terms() {
	$taxonomy = palaplast_get_product_brand_taxonomy();

	if ( '' === $taxonomy ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	return $terms;
}

function palaplast_get_product_brand_logo_id( $term_id ) {
	$term_id = (int) $term_id;

	if ( ! $term_id ) {
		return 0;
	}

	$logo_meta_keys = array(
		'thumbnail_id',
		'brand_thumbnail_id',
		'pwb_brand_image',
		'yith_product_brand_thumbnail_id',
		'logo_id',
	);

	foreach ( $logo_meta_keys as $meta_key ) {
		$logo_id = absint( get_term_meta( $term_id, $meta_key, true ) );
		if ( $logo_id ) {
			return $logo_id;
		}
	}

	return 0;
}

function palaplast_get_default_variation_colors() {
	return array(
		array(
			'name' => __( 'Black', 'palaplast' ),
			'hex'  => '#000000',
		),
		array(
			'name' => __( 'White', 'palaplast' ),
			'hex'  => '#ffffff',
		),
		array(
			'name' => __( 'Gray', 'palaplast' ),
			'hex'  => '#808080',
		),
		array(
			'name' => __( 'Red', 'palaplast' ),
			'hex'  => '#ff0000',
		),
		array(
			'name' => __( 'Green', 'palaplast' ),
			'hex'  => '#00a651',
		),
		array(
			'name' => __( 'Blue', 'palaplast' ),
			'hex'  => '#0057ff',
		),
		array(
			'name' => __( 'Yellow', 'palaplast' ),
			'hex'  => '#ffd400',
		),
		array(
			'name' => __( 'Orange', 'palaplast' ),
			'hex'  => '#ff8a00',
		),
		array(
			'name' => __( 'Purple', 'palaplast' ),
			'hex'  => '#7b3fe4',
		),
		array(
			'name' => __( 'Brown', 'palaplast' ),
			'hex'  => '#8b5a2b',
		),
	);
}

function palaplast_get_variation_colors() {
	$stored_colors = get_option( 'palaplast_variation_colors', false );
	$colors        = false === $stored_colors ? palaplast_get_default_variation_colors() : $stored_colors;

	if ( ! is_array( $colors ) ) {
		return array();
	}

	$clean_colors = array();
	foreach ( $colors as $color ) {
		if ( ! is_array( $color ) ) {
			continue;
		}

		$name = isset( $color['name'] ) ? sanitize_text_field( (string) $color['name'] ) : '';
		$hex  = isset( $color['hex'] ) ? sanitize_hex_color( (string) $color['hex'] ) : '';

		if ( '' === $name || ! $hex ) {
			continue;
		}

		$clean_colors[] = array(
			'name' => $name,
			'hex'  => strtolower( $hex ),
		);
	}

	return $clean_colors;
}

function palaplast_get_pricelists() {
	$pricelists = get_option( 'palaplast_pricelists', array() );

	return is_array( $pricelists ) ? $pricelists : array();
}

function palaplast_get_sheet_name_by_id( $sheet_id ) {
	$sheets = palaplast_get_technical_sheets();

	if ( empty( $sheets[ $sheet_id ]['name'] ) ) {
		return '';
	}

	return (string) $sheets[ $sheet_id ]['name'];
}

function palaplast_get_pricelist_name_by_id( $pricelist_id ) {
	$pricelists = palaplast_get_pricelists();

	if ( empty( $pricelists[ $pricelist_id ]['name'] ) ) {
		return '';
	}

	return (string) $pricelists[ $pricelist_id ]['name'];
}

function palaplast_is_valid_pdf_attachment( $attachment_id ) {
	if ( ! $attachment_id || 'attachment' !== get_post_type( $attachment_id ) ) {
		return false;
	}

	$file_path = get_attached_file( $attachment_id );
	$file_type = $file_path ? wp_check_filetype( $file_path ) : array();

	return isset( $file_type['ext'] ) && 'pdf' === strtolower( (string) $file_type['ext'] );
}

function palaplast_clear_sheet_from_categories( $sheet_id ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'fields'     => 'ids',
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return;
	}

	foreach ( $terms as $term_id ) {
		$current_sheet = (int) get_term_meta( (int) $term_id, 'palaplast_technical_sheet_id', true );

		if ( $current_sheet === (int) $sheet_id ) {
			delete_term_meta( (int) $term_id, 'palaplast_technical_sheet_id' );
		}
	}
}

function palaplast_clear_pricelist_from_categories( $pricelist_id ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'fields'     => 'ids',
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return;
	}

	foreach ( $terms as $term_id ) {
		$current_pricelist = (int) get_term_meta( (int) $term_id, 'palaplast_pricelist_id', true );

		if ( $current_pricelist === (int) $pricelist_id ) {
			delete_term_meta( (int) $term_id, 'palaplast_pricelist_id' );
		}
	}
}

function palaplast_get_wpml_aware_term_meta_value( $term_id, $meta_key ) {
	$term_id  = (int) $term_id;
	$meta_key = (string) $meta_key;

	if ( ! $term_id || '' === $meta_key ) {
		return 0;
	}

	$term_ids_to_check = array( $term_id );

	if ( has_filter( 'wpml_object_id' ) ) {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WPML's documented filter name is intentionally external.
		$current_lang = apply_filters( 'wpml_current_language', null );
		if ( is_string( $current_lang ) && '' !== $current_lang ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WPML's documented filter name is intentionally external.
			$current_lang_term_id = (int) apply_filters( 'wpml_object_id', $term_id, 'product_cat', true, $current_lang );
			if ( $current_lang_term_id ) {
				$term_ids_to_check[] = $current_lang_term_id;
			}
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WPML's documented filter name is intentionally external.
		$default_lang = apply_filters( 'wpml_default_language', null );
		if ( is_string( $default_lang ) && '' !== $default_lang ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WPML's documented filter name is intentionally external.
			$default_lang_term_id = (int) apply_filters( 'wpml_object_id', $term_id, 'product_cat', true, $default_lang );
			if ( $default_lang_term_id ) {
				$term_ids_to_check[] = $default_lang_term_id;
			}
		}
	}

	$term_ids_to_check = array_unique( array_map( 'intval', $term_ids_to_check ) );

	foreach ( $term_ids_to_check as $meta_term_id ) {
		$meta_value = (int) get_term_meta( $meta_term_id, $meta_key, true );
		if ( $meta_value ) {
			return $meta_value;
		}
	}

	return 0;
}

function palaplast_resolve_category_sheet( $term_id ) {
	$term = get_term( $term_id, 'product_cat' );

	if ( ! $term instanceof WP_Term ) {
		return array();
	}

	$sheets       = palaplast_get_technical_sheets();
	$current_term = $term;
	$distance     = 0;

	while ( $current_term instanceof WP_Term && 'product_cat' === $current_term->taxonomy ) {
		$sheet_id = palaplast_get_wpml_aware_term_meta_value( (int) $current_term->term_id, 'palaplast_technical_sheet_id' );

		if ( $sheet_id && isset( $sheets[ $sheet_id ] ) ) {
			$file_url = ! empty( $sheets[ $sheet_id ]['attachment_id'] ) ? wp_get_attachment_url( (int) $sheets[ $sheet_id ]['attachment_id'] ) : '';

			if ( $file_url ) {
				return array(
					'name'     => (string) $sheets[ $sheet_id ]['name'],
					'file_url' => (string) $file_url,
					'distance' => $distance,
					'sheet_id' => $sheet_id,
				);
			}
		}

		if ( empty( $current_term->parent ) ) {
			break;
		}

		$current_term = get_term( (int) $current_term->parent, 'product_cat' );
		++$distance;
	}

	return array();
}

function palaplast_resolve_category_pricelist( $term_id ) {
	$term = get_term( $term_id, 'product_cat' );

	if ( ! $term instanceof WP_Term ) {
		return array();
	}

	$pricelists   = palaplast_get_pricelists();
	$current_term = $term;
	$distance     = 0;

	while ( $current_term instanceof WP_Term && 'product_cat' === $current_term->taxonomy ) {
		$pricelist_id = palaplast_get_wpml_aware_term_meta_value( (int) $current_term->term_id, 'palaplast_pricelist_id' );

		if ( $pricelist_id && isset( $pricelists[ $pricelist_id ] ) ) {
			$file_url = ! empty( $pricelists[ $pricelist_id ]['attachment_id'] ) ? wp_get_attachment_url( (int) $pricelists[ $pricelist_id ]['attachment_id'] ) : '';

			if ( $file_url ) {
				return array(
					'name'         => (string) $pricelists[ $pricelist_id ]['name'],
					'file_url'     => (string) $file_url,
					'distance'     => $distance,
					'pricelist_id' => $pricelist_id,
				);
			}
		}

		if ( empty( $current_term->parent ) ) {
			break;
		}

		$current_term = get_term( (int) $current_term->parent, 'product_cat' );
		++$distance;
	}

	return array();
}

function palaplast_get_category_inherited_sheet( $term_id ) {
	$term = get_term( $term_id, 'product_cat' );

	if ( ! $term instanceof WP_Term || empty( $term->parent ) ) {
		return array();
	}

	return palaplast_resolve_category_sheet( (int) $term->parent );
}

function palaplast_get_category_inherited_pricelist( $term_id ) {
	$term = get_term( $term_id, 'product_cat' );

	if ( ! $term instanceof WP_Term || empty( $term->parent ) ) {
		return array();
	}

	return palaplast_resolve_category_pricelist( (int) $term->parent );
}

function palaplast_get_product_technical_sheet( $product_id ) {
	$terms = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return array();
	}

	sort( $terms, SORT_NUMERIC );
	$candidates = array();

	foreach ( $terms as $index => $term_id ) {
		$resolved_sheet = palaplast_resolve_category_sheet( (int) $term_id );

		if ( empty( $resolved_sheet['file_url'] ) ) {
			continue;
		}

		$candidates[] = array(
			'distance' => isset( $resolved_sheet['distance'] ) ? (int) $resolved_sheet['distance'] : PHP_INT_MAX,
			'order'    => (int) $index,
			'term_id'  => (int) $term_id,
			'sheet'    => array(
				'name'     => (string) $resolved_sheet['name'],
				'file_url' => (string) $resolved_sheet['file_url'],
			),
		);
	}

	if ( empty( $candidates ) ) {
		return array();
	}

	usort(
		$candidates,
		static function ( $a, $b ) {
			if ( $a['distance'] !== $b['distance'] ) {
				return $a['distance'] <=> $b['distance'];
			}
			if ( $a['order'] !== $b['order'] ) {
				return $a['order'] <=> $b['order'];
			}

			return $a['term_id'] <=> $b['term_id'];
		}
	);

	return $candidates[0]['sheet'];
}

function palaplast_get_product_pricelist( $product_id ) {
	$terms = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return array();
	}

	sort( $terms, SORT_NUMERIC );
	$candidates = array();

	foreach ( $terms as $index => $term_id ) {
		$resolved_pricelist = palaplast_resolve_category_pricelist( (int) $term_id );

		if ( empty( $resolved_pricelist['file_url'] ) ) {
			continue;
		}

		$candidates[] = array(
			'distance'  => isset( $resolved_pricelist['distance'] ) ? (int) $resolved_pricelist['distance'] : PHP_INT_MAX,
			'order'     => (int) $index,
			'term_id'   => (int) $term_id,
			'pricelist' => array(
				'name'     => (string) $resolved_pricelist['name'],
				'file_url' => (string) $resolved_pricelist['file_url'],
			),
		);
	}

	if ( empty( $candidates ) ) {
		return array();
	}

	usort(
		$candidates,
		static function ( $a, $b ) {
			if ( $a['distance'] !== $b['distance'] ) {
				return $a['distance'] <=> $b['distance'];
			}
			if ( $a['order'] !== $b['order'] ) {
				return $a['order'] <=> $b['order'];
			}

			return $a['term_id'] <=> $b['term_id'];
		}
	);

	return $candidates[0]['pricelist'];
}
