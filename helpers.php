<?php

# -------------------------------------------------------------
# Custom array_chunk() that makes X number of chunks
# instead of chunks with X number of items
# -------------------------------------------------------------
function crb_array_chunk( $array = array(), $chunks = 2 ) {
	$per_chunk = ceil( count( $array ) / $chunks  );

	return array_chunk( $array, $per_chunk );
}

# -------------------------------------------------------------
# Checks if any of the provided arguments is false
# -------------------------------------------------------------
function crb_check_args() {
	$valid = true;	
	$args  = func_get_args();

	foreach ( $args as $arg ) {
		if ( ! $arg ) {
			$valid = false;
			break;
		}
	}

	return $valid;
}

# -------------------------------------------------------------
# Gets a list of specific post type(s)
# -------------------------------------------------------------
function crb_get_posts_list( $post_type = 'post', $posts_per_page = -1, $orderby = 'title', $order = 'ASC', $additional_args = array() ) {
	$list = array();
	
	$args = array_merge( array(
		'post_type'      => $post_type,
		'posts_per_page' => $posts_per_page,
		'orderby'        => $orderby,
		'order'          => $order,
	), $additional_args );
	
	$posts = get_posts( $args );

	foreach ( $posts as $p ) {
		$list[$p->ID] = $p->post_title;
	}

	return $list;
}

# -------------------------------------------------------------
#  Helper function for Carbon Fields complex field labels
#
#  Requrements:
#  - Carbon Fields: https://github.com/htmlburger/carbon-fields
# -------------------------------------------------------------
function crb_complex_labels( $singular = false, $plural = false ) {
	if ( ! $singular ) {
		$singular = __( 'Entry', 'crb' );
	}
	if ( ! $plural ) {
		$plural = __( 'Entries', 'crb' );
	}
	return array(
		'singular_name' => $singular,
		'plural_name'   => $plural,
	);
}

# -------------------------------------------------------------
# Helper function for WPThumb
#
# Requirements:
# - WPThumb: https://github.com/humanmade/WPThumb
# -------------------------------------------------------------
function crb_wpthumb( $id, $width = 0, $height = 0, $src = false, $crop = true, $classes = '', $echo = true ) {
	$size = array( 
		'width'  => $width, 
		'height' => $height, 
		'crop'   => $crop, 
	);
	
	$attr = array(
		'class' => $classes,
	);
	if ( $src ) {
		$image_src = wp_get_attachment_image_src( $id, $size, false, $attr );
		return $image_src[0];
	}
	
	if ( ! $echo ) {
		return wp_get_attachment_image( $id, $size, false, $attr );
	}
	echo wp_get_attachment_image( $id, $size, false, $attr );
}

# -------------------------------------------------------------
# Enqueue WooCommerce price filter slider
#
# Requirements:
# - WooCommerce: https://www.woothemes.com/woocommerce/
# -------------------------------------------------------------
function crb_enqueue_woocommerce_slider() {
	if ( ! function_exists( 'WC' ) || wp_script_is( 'wc-price-slider', 'enqueue' ) ) {
		return;
	}
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_register_script( 'wc-jquery-ui-touchpunch', WC()->plugin_url() . '/assets/js/jquery-ui-touch-punch/jquery-ui-touch-punch' . $suffix . '.js', array( 'jquery-ui-slider' ), WC_VERSION, true );
	wp_register_script( 'wc-price-slider', WC()->plugin_url() . '/assets/js/frontend/price-slider' . $suffix . '.js', array( 'jquery-ui-slider', 'wc-jquery-ui-touchpunch' ), WC_VERSION, true );
	wp_localize_script( 'wc-price-slider', 'woocommerce_price_slider_params', array(
		'currency_symbol' => get_woocommerce_currency_symbol(),
		'currency_pos'    => get_option( 'woocommerce_currency_pos' ),
		'min_price'       => isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : '',
		'max_price'       => isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : ''
	) );
	wp_enqueue_script( 'wc-price-slider' );
}

# -------------------------------------------------------------
# Get the minimum and maximum price of products
#
# Requirements:
# - WooCommerce: https://www.woothemes.com/woocommerce/
# -------------------------------------------------------------
function crb_get_product_prices( $term_id = false ) {
	$prices = array(
		'min' => 1,
		'max' => 100
	);
	global $wpdb;
	$sql_start = "
		SELECT MIN( meta_value+0 ) as min, MAX( meta_value+0 ) as max
		FROM {$wpdb->posts}
	";
	$sql_middle = "INNER JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id)";
	if ( $term_id ) {
		$sql_middle = "INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)" . $sql_middle;
		$sql_middle .= "WHERE ( {$wpdb->term_relationships}.term_taxonomy_id IN (%d) )";
	}
	$sql_end = "
		AND {$wpdb->posts}.post_type   = 'product' 
		AND {$wpdb->posts}.post_status = 'publish' 
		AND {$wpdb->postmeta}.meta_key = '_price'
	";
	$sql = $sql_start . $sql_middle . $sql_end;
	if ( $term_id ) {
		$sql = $wpdb->prepare( $sql, $term_id );
	}
	$prices = $wpdb->get_row( $sql );
	return $prices;
}
