<?php

# Custom array_chunk() that makes X number of chunks
# instead of chunks with X number of items
function crb_array_chunk( $array = array(), $per_column = 2 ) {
	$per_chunk = ceil( count( $array ) / $per_column  );

	return array_chunk( $array, $per_chunk );
}

# Gets a list of specific post type(s)
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
