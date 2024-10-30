<?php

namespace BlueBillywigPlugin\Admin;

use BlueBillywig\Sdk;
use BlueBillywig\Request;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\Coroutine;

/**
 *  BlueBillywig Filters class
 */
class Filters {


	/**
	 * Initialize
	 */
	public function init() {
		// Hooks.
		add_filter( 'blue_billywig_media_clip', array( $this, 'get_media_clip' ), 10, 6 );
		add_filter( 'blue_billywig_clip_list', array( $this, 'get_clip_list' ), 10, 4 );
		add_filter( 'blue_billywig_progress_list', array( $this, 'get_upload_progress' ), 10, 5 );
		add_filter( 'blue_billywig_publication_data', array( $this, 'get_publication_data' ), 10, 4 );
		add_filter( 'blue_billywig_delete_clip', array( $this, 'delete_clip' ), 10, 4 );
		add_filter( 'blue_billywig_save_new_data', array( $this, 'save_new_data' ), 10, 4 );
		add_filter( 'blue_billywig_get_embedcode', array( $this, 'get_embedcode' ), 10, 4 );
		add_filter( 'blue_billywig_get_playout_list', array( $this, 'get_playout_list' ), 10, 3 );
	}

	/**
	 * Initialize
	 */
	public function get_upload_progress( $bb_api_secret, $bb_api_id, $bb_api_publication, $bb_file_path, $new_data ) {
		try {
			$sdk        = Sdk::withRPCTokenAuthentication( $bb_api_publication, $bb_api_id, $bb_api_secret );
			$data_array = array();
			$file_title = 'new-file-' . gmdate( 'y-m-d-g:i' );
			if ( count( $new_data ) > 0 && $new_data['newtitle'] ) {
				$data_array['title'] = '' . $new_data['newtitle'] . '';
				if ( isset( $new_data['newdescription'] ) ) {
					$data_array['description'] = htmlspecialchars( $new_data['newdescription'] );
				}
				if ( isset( $new_data['newtags'] ) ) {
					$data_array['cat'] = array_map( 'trim', explode( ',', $new_data['newtags'] ) );
				}
				if ( isset( $new_data['newstatus'] ) ) {
					$data_array['status'] = htmlspecialchars( $new_data['newstatus'] );
				}
			} else {
				$data_array['title'] = $file_title;
			}
			$promise = Coroutine::of(
				function () use ( $sdk, $bb_file_path, $data_array ) {
					$response = ( yield $sdk->mediaclip->create( $data_array ) );
					$response->assertIsOk();
					$media_clip_id = $response->getDecodedBody()['id'];
					$response      = ( yield $sdk->mediaclip->initializeUploadAsync( $bb_file_path, $media_clip_id ) );
					$response->assertIsOk();
					yield $sdk->mediaclip->helper->executeUploadAsync( $bb_file_path, $response->getDecodedBody() );
				}
			);
			return $promise->wait();
		} catch ( ConnectException $e ) {
			return array(
				'Error' => $e->getCode() . ': ' . $e->getMessage() . PHP_EOL,
			);
		}
	}

	public function get_publication_data( $bb_api_secret, $bb_api_id, $bb_api_publication ) {
		try {
			$sdk      = Sdk::withRPCTokenAuthentication( $bb_api_publication, $bb_api_id, $bb_api_secret );
			$promise  = $sdk->sendRequestAsync( new Request( 'GET', '/sapi/publication' ) );
			$response = $promise->wait();
			return $response->getDecodedBody();
		} catch ( ConnectException $e ) {
			return array(
				'Error' => $e->getCode() . ': ' . $e->getMessage() . PHP_EOL,
			);
		}
	}

	public function delete_clip( $bb_api_secret, $bb_api_id, $bb_api_publication, $bb_video_id ) {
		try {
			$sdk      = Sdk::withRPCTokenAuthentication( $bb_api_publication, $bb_api_id, $bb_api_secret );
			$promise  = $sdk->sendRequestAsync( new Request( 'DELETE', '/sapi/mediaclip/' . $bb_video_id ) );
			$response = $promise->wait();
			return $response->getDecodedBody();
		} catch ( ConnectException $e ) {
			return array(
				'Error' => $e->getCode() . ': ' . $e->getMessage() . PHP_EOL,
			);
		}
	}

	public function save_new_data( $bb_api_secret, $bb_api_id, $bb_api_publication, $new_data ) {
		$video_id  = $new_data['videoId'];
		$new_title = $new_data['newTitle'];
		$new_desc  = $new_data['newDesc'];

		$new_tags = array_map( 'trim', explode( ',', $new_data['newTags'] ) );
		try {
			$sdk      = Sdk::withRPCTokenAuthentication( $bb_api_publication, $bb_api_id, $bb_api_secret );
			$response = $sdk->mediaclip->create(
				array(
					'id'          => $video_id,
					'title'       => $new_title,
					'description' => $new_desc,
					'cat'         => $new_tags,
				)
			);
			$response->assertIsOk();
			return $response->getDecodedBody();
		} catch ( ConnectException $e ) {
			return array(
				'Error' => $e->getCode() . ': ' . $e->getMessage() . PHP_EOL,
			);
		}
	}

	public function get_embedcode( $bb_api_secret, $bb_api_id, $bb_api_publication, $bb_entity_id ) {
		$playout_array     = get_option( 'blue-billywig-playout-video-status' );
		$bb_playout_status = isset( $playout_array[ $bb_entity_id ] ) ? $playout_array[ $bb_entity_id ] : 'default';
		try {
			$sdk      = Sdk::withRPCTokenAuthentication( $bb_api_publication, $bb_api_id, $bb_api_secret );
			$promise  = $sdk->sendRequestAsync( new Request( 'GET', '/sapi/embedcode/' . $bb_entity_id . '/' . $bb_playout_status . '/' . get_option( 'blue-billywig-embed' ) ) );
			$response = $promise->wait();
			return $response->getDecodedBody();
		} catch ( ConnectException $e ) {
			return array(
				'Error' => $e->getCode() . ': ' . $e->getMessage() . PHP_EOL,
			);
		}
	}

	// public function get_playout_list( $bb_api_secret, $bb_api_id, $bb_api_publication ) {
	// 	if ( ! $bb_api_secret || ! $bb_api_id || ! $bb_api_publication ) {
	// 		return;
	// 	}
	// 	try {
	// 		$sdk      = Sdk::withRPCTokenAuthentication( $bb_api_publication, $bb_api_id, $bb_api_secret );
	// 		$promise  = $sdk->sendRequestAsync( new Request( 'GET', '/sapi/playout/' ) );
	// 		$response = $promise->wait();
	// 		return $response->getDecodedBody();
	// 	} catch ( ConnectException $e ) {
	// 		return array(
	// 			'Error' => $e->getCode() . ': ' . $e->getMessage() . PHP_EOL,
	// 		);
	// 	}
	// }
	public function get_playout_list( $bb_api_secret, $bb_api_id, $bb_api_publication ) {
    if ( ! $bb_api_secret || ! $bb_api_id || ! $bb_api_publication ) {
        return;
    }
    try {
        $sdk      = Sdk::withRPCTokenAuthentication( $bb_api_publication, $bb_api_id, $bb_api_secret );
        $promise  = $sdk->sendRequestAsync( new Request( 'GET', '/sapi/playout/' ) );
        $response = $promise->wait();
        $decoded_body = $response->getDecodedBody();

        // Transform the data structure if necessary
        if (!is_array($decoded_body)) {
            $decoded_body = json_decode($decoded_body, true);
        }

        // Ensure the 'items' key exists and is an array
        if (!isset($decoded_body['items']) || !is_array($decoded_body['items'])) {
            $decoded_body['items'] = array();
        }

        return $decoded_body;
    } catch ( ConnectException $e ) {
        return array(
            'Error' => $e->getCode() . ': ' . $e->getMessage() . PHP_EOL,
        );
    }
}


	public function get_clip_list( $bb_api_secret, $bb_api_id, $bb_api_publication, $bb_api_quary ) {

		$media_clip_title_data = ! is_null( $bb_api_quary ) && isset( $bb_api_quary['title'] ) ? $bb_api_quary['title'] : null;
		$media_clip_date_data  = ! is_null( $bb_api_quary ) && isset( $bb_api_quary['createddate'] ) ? $bb_api_quary['createddate'] : null;
		$media_clip_title      = isset( $media_clip_title_data ) ? '{"filters":[{"type":"MediaClipList","field":"title","operator":"contains","value":["' . $media_clip_title_data . '"]}]}' : '';
		$media_clip_date       = isset( $media_clip_date_data ) ? '{"filters":[{"type":"MediaClipList","field":"createddate","operator":"is","value":"' . $media_clip_date_data . '"}]}' : '';
		$koma                  = strlen( $media_clip_title ) > 0 && strlen( $media_clip_date ) > 0 ? ',' : '';
		try {
			$sdk      = Sdk::withRPCTokenAuthentication( $bb_api_publication, $bb_api_id, $bb_api_secret );
			$promise  = $sdk->sendRequestAsync( new Request( 'GET', '/sapi/cliplist?filterset=[' . $media_clip_title . $koma . $media_clip_date . ']' ) );
			$response = $promise->wait();
			return $response->getDecodedBody();
		} catch ( ConnectException $e ) {
			return array(
				'Error' => $e->getCode() . ': ' . $e->getMessage() . PHP_EOL,
			);
		}
	}

	public function get_media_clip( $bb_api_secret, $bb_api_id, $bb_api_publication, $bb_quary, $bb_api_page, $bb_api_limitoff = false ) {
		$media_clip_limit      = '&limit=' . get_option( 'blue-billywig-page-count' );
		$bb_api_page           = 0 !== $bb_api_page && strlen( $bb_api_page ) > 0 ? $bb_api_page * intval( get_option( 'blue-billywig-page-count' ) ) : 0;
		$media_clip_offset     = '&offset=' . $bb_api_page;
		$media_clip_title_data = ! is_null( $bb_quary ) && isset( $bb_quary['title'] ) ? $bb_quary['title'] : null;
		$media_clip_type_data  = ! is_null( $bb_quary ) && isset( $bb_quary['mediaType'] ) ? $bb_quary['mediaType'] : null;
		$media_clip_date_data  = ! is_null( $bb_quary ) && isset( $bb_quary['createddate'] ) ? $bb_quary['createddate'] : null;
		$media_clip_type       = isset( $media_clip_type_data ) && 'all' !== $media_clip_type_data ? '{"filters":[{"type":"mediaclip","field":"mediatype","operator":"is","value":"' . $media_clip_type_data . '"}]}' : '';
		$media_clip_title      = isset( $media_clip_title_data ) ? '{"filters":[{"type":"mediaclip","field":"title","operator":"contains","value":["' . $media_clip_title_data . '"]}]}' : '';
		$media_clip_date       = isset( $media_clip_date_data ) ? 'createddate:' . $media_clip_date_data : '';
		$koma                  = strlen( $media_clip_title ) > 0 && strlen( $media_clip_type ) > 0 ? ',' : '';
		$media_clip_quota      = '' !== $media_clip_date ? '&q=' . $media_clip_date : '';
		try {
			$sdk = Sdk::withRPCTokenAuthentication( $bb_api_publication, $bb_api_id, $bb_api_secret );
			if ( $bb_api_limitoff ) {
				$promise = $sdk->sendRequestAsync( new Request( 'GET', '/sapi/mediaclip?filterset=[' . $media_clip_type . $koma . $media_clip_title . ']' . $media_clip_quota ) );
			} else {
				$promise = $sdk->sendRequestAsync( new Request( 'GET', '/sapi/mediaclip?filterset=[' . $media_clip_type . $koma . $media_clip_title . ']' . $media_clip_limit . $media_clip_offset . $media_clip_quota ) );
			}
			$response = $promise->wait();
			return $response->getDecodedBody();
		} catch ( ConnectException $e ) {
			return array(
				'Error' => $e->getCode() . ': ' . $e->getMessage() . PHP_EOL,
			);
		}
	}
}
