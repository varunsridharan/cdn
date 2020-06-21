<?php
define( 'APP_PATH', __DIR__ . '/' );

if ( ! file_exists( APP_PATH . 'vendor/autoload.php' ) ) {
	die( '🛑 Envato API Library Not Found !' );
}

use MorningTrain\EnvatoApi\EnvatoApi;

try {
	require APP_PATH . 'vendor/autoload.php';
	require APP_PATH . '../functions.php';

	$final = array();
	$token = ( isset( $argv[1] ) ) ? trim( $argv[1], '/' ) : false;

	if ( empty( $token ) ) {
		throw new Exception( 'Invalid Envato Token' );
	}

	$client = new EnvatoApi( $token );
	$result = $client->getItems( [ 'username' => 'varunsridharan' ] );

	if ( isset( $result->matches ) ) {
		foreach ( $result->matches as $item ) {
			$data                 = array();
			$data['id']           = $item->id;
			$data['updated_at']   = $item->updated_at;
			$data['published_at'] = $item->published_at;
			$data['trending']     = $item->trending;
			$data['name']         = $item->name;
			$data['site']         = $item->site;
			$data['url']          = $item->url;

			$slug         = '';
			$data['slug'] = strtolower( preg_replace( '~[^\pL\d]+~u', '-', $item->name ) );
			$slug_gen     = explode( '-', $data['slug'] );
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

			$data['mini_slug'] = $slug;

			if ( isset( $item->previews->icon_with_video_preview ) ) {
				$data['banner'] = $item->previews->icon_with_video_preview->landscape_url;
				$data['icon']   = $item->previews->icon_with_video_preview->icon_url;
			} elseif ( isset( $item->previews->icon_with_landscape_preview ) ) {
				$data['banner'] = $item->previews->icon_with_landscape_preview->landscape_url;
				$data['icon']   = $item->previews->icon_with_landscape_preview->icon_url;
			}


			if ( 'codecanyon.net' === $data['site'] ) {
				$final['plugins'][] = $data;
			} elseif ( 'themeforest.net' === $data['site'] ) {
				$final['html'][] = $data;
			}
		}

		@mkdir( APP_PATH . '../../envato/' );
		@file_put_contents( APP_PATH . '../../envato/items.json', json_encode( $final, JSON_PRETTY_PRINT ) );
	}
} catch ( \Exception $exception ) {
	$msg = '🛑 Unknown Error !!' . PHP_EOL . PHP_EOL;
	$msg .= print_r( $exception->getMessage(), true );
	die( $msg );
}