<?php
function sanitize_key( $key ) {
	$key = strtolower( $key );
	$key = preg_replace( '/[^a-z0-9_\-]/', '', $key );
	return $key;
}

function slugify( $islug ) {
	$slug     = '';
	$slug_gen = explode( '-', $islug );
	if ( is_array( $slug_gen ) ) {
		foreach ( $slug_gen as $slugi ) {
			$slugi = trim( strtolower( $slugi ) );
			if ( in_array( $slugi, array( 'wc', 'WC', 'WooCommerce', 'woocommerce' ) ) ) {
				$slug .= 'wc';
			} elseif ( in_array( $slugi, array( 'for', 'FOR' ) ) ) {
				$slug .= '';
			} else {
				$slug .= sanitize_key( $slugi[0] );
			}
		}
	}
	return $slug;
}