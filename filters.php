<?php

# --------------------------------------------------------------------
# Restores old comment form layout
# --------------------------------------------------------------------
function crb_move_comment_field_to_bottom( $fields ) {
	$comment_field = $fields['comment'];
	unset( $fields['comment'] );
	$fields['comment'] = $comment_field;
	return $fields;
}
add_filter( 'comment_form_fields', 'crb_move_comment_field_to_bottom' );

# --------------------------------------------------------------------
#  Override WooCommerce template in a plugin file
#
#  Requrements:
#  - WooCommerce: https://www.woothemes.com/woocommerce/
# --------------------------------------------------------------------
function crb_plugin_path() { 
	return untrailingslashit( plugin_dir_path( __FILE__ ) ); 
}

function crb_woocommerce_locate_template( $template, $template_name, $template_path ) {
	global $woocommerce;

	$_template = $template;

	if ( ! $template_path ) {
		$template_path = $woocommerce->template_url;
	}

	$plugin_path = crb_plugin_path() . '/templates/';
	$template    = locate_template( 
		array( 
			$template_path . $template_name,
			$template_name 
		) 
	);

	if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
		$template = $plugin_path . $template_name;
	}

	if ( ! $template ) {
		$template = $_template;
	}

	return $template;
}
add_filter( 'woocommerce_locate_template', 'crb_woocommerce_locate_template', 10, 3 );

# --------------------------------------------------------------------
#  Override default WordPress avatar
#
#  Reference:
#  https://codex.wordpress.org/Plugin_API/Filter_Reference/get_avatar
# --------------------------------------------------------------------
function crb_custom_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
	$user = false;

	if ( is_numeric( $id_or_email ) ) {

		$id   = (int) $id_or_email;
		$user = get_user_by( 'id' , $id );

	} elseif ( is_object( $id_or_email ) ) {

		if ( ! empty( $id_or_email->user_id ) ) {
			$id   = (int) $id_or_email->user_id;
			$user = get_user_by( 'id' , $id );
		}

	} else {
		$user = get_user_by( 'email', $id_or_email );	
	}

	if ( $user && is_object( $user ) ) {

		# User avatar logic goes here
		$avatar = 'YOUR_NEW_IMAGE_URL';

		$avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
	}

	return $avatar;
}
add_filter( 'get_avatar', 'crb_custom_avatar', 1, 5 );
