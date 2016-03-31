<?php

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
		$plural = $singular . 's';
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
