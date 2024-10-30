<?php

namespace BlueBillywigPlugin\Admin;

/**
 *  Ajax api class
 */
class Ajax {


	/**
	 * Initialize
	 */
	public function init() {
		// Settings submit endpoint.
		add_action( 'wp_ajax_blue_billywig_settings_submit_form', array( $this, 'settings_submit_form' ) );
		// Load library videos endpoint.
		add_action( 'wp_ajax_blue_billywig_load_library_videos', array( $this, 'load_library_videos' ) );
		// Load library playlists endpoint.
		add_action( 'wp_ajax_blue_billywig_load_library_playlists', array( $this, 'load_library_playlists' ) );
		// Save new library videos endpoint.
		add_action( 'wp_ajax_blue_billywig_save_new_data_library_videos', array( $this, 'save_new_data_library_videos' ) );
		// Delete library videos endpoint.
		add_action( 'wp_ajax_blue_billywig_delete_library_videos', array( $this, 'delete_library_videos' ) );
		// Upload videos endpoint.
		add_action( 'wp_ajax_blue_billywig_upload_videos_server', array( $this, 'upload_videos_server' ) );
		// Upload videos account endpoint.
		add_action( 'wp_ajax_blue_billywig_upload_videos_account', array( $this, 'upload_videos_account' ) );
		// Remove uploaded file endpoint.
		add_action( 'wp_ajax_blue_billywig_remove_uploaded_file', array( $this, 'remove_uploaded_file' ) );
		// Get post modal template endpoint.
		add_action( 'wp_ajax_blue_billywig_get_post_modal_template', array( $this, 'get_post_modal_template' ) );

		// My Ajax

		add_action('wp_ajax_get_custom_playout_screen',array( $this, 'get_custom_playout_screen' ) );
		add_action('wp_ajax_nopriv_get_custom_playout_screen',array( $this, 'get_custom_playout_screen' ));



	}


	/**
	 * My Ajax function for custom playout Screen
	 */
	public function get_custom_playout_screen() {
		$playout_screen = get_option('blue-billywig-playout');
		$publication = get_option('blue-billywig-publication');

		// Return the JSON encoded array
	echo	wp_send_json(array('customPlayoutScreen' => $playout_screen, 'publication' => $publication));
		// wp_die();
		// return json_encode(array('customPlayoutScreen' => $playout_screen, 'publication' => $publication));

	}



	/**
	 * AJAX endpoint to return modal template html
	 */
	public function get_post_modal_template() {
		ob_start();
		include_once BLUE_BILLYWIG_PLUGIN_DIR . 'admin/partials/modal.php';
		$content = ob_get_clean();
		wp_send_json( array( 'data' => $content ) );
	}

	/**
	 * AJAX endpoint to remove uploaded file
	 */
	public function remove_uploaded_file() {
    // Check nonce
    if (!check_admin_referer('blue-billywig-upload-video', 'blue-billywig-nonce')) {
        wp_send_json_error('Invalid nonce', 400);
        wp_die();
    }

    if (isset($_POST['filePath'])) {
        $file_path = sanitize_text_field(wp_unslash($_POST['filePath']));
        wp_delete_file($file_path);
        wp_send_json_success(array('data' => !file_exists($file_path)), 200);
    } else {
        wp_send_json_error('File path not provided', 400);
    }
}

	/**
	 * AJAX endpoint to uplpoad videos to account
	 */
	public function upload_videos_account() {
    // Check nonce
    if (!check_admin_referer('blue-billywig-upload-video', 'blue-billywig-nonce')) {
        wp_send_json_error('Invalid nonce', 400);
        wp_die();
    }

    $video_path = sanitize_text_field(wp_unslash($_POST['videoPath']));
    if (!$video_path) {
        wp_send_json_error('Invalid video path', 400);
        wp_die();
    }

    $new_data = array(
        'newtitle' => sanitize_text_field(wp_unslash($_POST['newtitle'])),
        'title' => sanitize_text_field(wp_unslash($_POST['title'])),
        'newdescription' => sanitize_text_field(wp_unslash($_POST['newdescription'])),
        'description' => sanitize_text_field(wp_unslash($_POST['description'])),
        'newtags' => sanitize_text_field(wp_unslash($_POST['newtags'])),
        'cat' => sanitize_text_field(wp_unslash($_POST['cat'])),
        'newstatus' => sanitize_text_field(wp_unslash($_POST['newstatus'])),
        'status' => sanitize_text_field(wp_unslash($_POST['status'])),
    );

    $bb_upload_data = apply_filters('blue_billywig_progress_list', get_option('blue-billywig-api-secret'), get_option('blue-billywig-api-id'), get_option('blue-billywig-publication'), $video_path, $new_data);

		wp_send_json_success(array('data' => $bb_upload_data), 200);
}


	/**
	 * AJAX endpoint to save video to server
	 */
// 	public function upload_videos_server() {
//     // Check nonce
//     if (!check_admin_referer('blue-billywig-upload-video', 'blue-billywig-nonce')) {
//         error_log('Nonce verification failed');
//         wp_send_json_error('Invalid nonce', 400);
//         wp_die();
//     }
//     error_log('Nonce verification succeeded');

//     $allow_type = array();
//     $deny_type = array(
//         'phtml', 'php', 'php3', 'php4', 'php5', 'php6', 'php7', 'phps', 'cgi', 'pl',
//         'asp', 'aspx', 'shtml', 'shtm', 'htaccess', 'htpasswd', 'ini', 'log', 'sh',
//         'js', 'html', 'htm', 'css', 'sql', 'spl', 'scgi', 'fcgi', 'exe'
//     );

//     $upload_error = '';
//     $upload_success = '';
//     $upload_title = '';

//     if (!isset($_FILES['file'])) {
//         error_log('No file uploaded');
//         wp_send_json_error('No file uploaded', 400);
//         wp_die();
//     }

//     $tmp_name = sanitize_text_field(wp_unslash($_FILES['file']['tmp_name']));
//     error_log('Temporary file name: ' . $tmp_name);

//     // Detailed logging of $_FILES['file']
//     error_log('$_FILES[file]: ' . print_r($_FILES['file'], true));

//     // Validate file errors
//     if (!empty($_FILES['file']['error'])) {
//         error_log('File upload error code: ' . $_FILES['file']['error']);
//     }else{
// 			error_log('Failed to upload file is empty: ' . print_r($_FILES['file'], true));
// 			wp_send_json_error('Failed to upload file is empty', 400);
// 			wp_die();
// 		}

//     if (!$tmp_name) {
//         error_log('Temporary file name is empty or invalid');
//     }else {
// 			error_log('Failed to upload file: Temporary file name is empty or invalid' . print_r($_FILES['file'], true));
// 			wp_send_json_error('Failed to upload file Temporary file name is empty or invalid', 400);
// 			wp_die();
// 		}

//     if ('none' === $tmp_name) {
//         error_log('Temporary file name is "none"');
//     }else {
// 			error_log('Failed to upload file: Temporary file name is "none"' . print_r($_FILES['file'], true));
// 			wp_send_json_error('Failed to upload file Temporary file name is "none"', 400);
// 			wp_die();
// 		}

//     if (!is_uploaded_file($tmp_name)) {
//         error_log('is_uploaded_file check failed');
//     }else {
// 			error_log('Failed to upload file: is_uploaded_file check failed' . print_r($_FILES['file'], true));
// 			wp_send_json_error('Failed to upload file is_uploaded_file check failed', 400);
// 			wp_die();
// 		}

//     if (!empty($_FILES['file']['error']) || !$tmp_name || 'none' === $tmp_name || !is_uploaded_file($tmp_name)) {
//         error_log('Failed to upload file: ' . print_r($_FILES['file'], true));
//         wp_send_json_error('Failed to upload file', 400);
//         wp_die();
//     }

//     $name = sanitize_file_name(wp_unslash($_FILES['file']['name']));
//     $parts = pathinfo($name);
//     if (empty($name) || empty($parts['extension'])) {
//         error_log('Invalid file type');
//         wp_send_json_error('Invalid file type', 400);
//         wp_die();
//     } elseif (!empty($allow_type) && !in_array(strtolower($parts['extension']), $allow_type, true)) {
//         error_log('File type not allowed');
//         wp_send_json_error('Invalid file type', 400);
//         wp_die();
//     } elseif (!empty($deny_type) && in_array(strtolower($parts['extension']), $deny_type, true)) {
//         error_log('File type denied');
//         wp_send_json_error('Invalid file type', 400);
//         wp_die();
//     }

//     $upload_data = wp_handle_upload($_FILES['file'], array('test_form' => false));
//     if (isset($upload_data['file'])) {
//         $upload_success = $upload_data['file'];
//         $upload_title = $name;
//         error_log('File uploaded successfully: ' . $upload_success);
//     } else {
//         error_log('Failed to upload file');
//         wp_send_json_error('Failed to upload file', 400);
//         wp_die();
//     }

//     $new_data = array();
//     if (isset($_POST['newTitle'])) {
//         $new_data['newtitle'] = sanitize_text_field(wp_unslash($_POST['newTitle']));
//     }
//     if (isset($_POST['newDescription'])) {
//         $new_data['newdescription'] = sanitize_text_field(wp_unslash($_POST['newDescription']));
//     }
//     if (isset($_POST['newTags'])) {
//         $new_data['newtags'] = sanitize_text_field(wp_unslash($_POST['newTags']));
//     }
//     if (isset($_POST['newStatus'])) {
//         $new_data['newstatus'] = sanitize_text_field(wp_unslash($_POST['newStatus']));
//     }

//     $data = array(
//         'error' => $upload_error,
//         'success' => array(
//             'data' => $upload_success,
//             'title' => $upload_title,
//             'newData' => $new_data,
//         ),
//     );
//     wp_send_json($data, 200);
//     wp_die();
// }

// Asynce request code
public function upload_videos_server() {
	// Check nonce
	if (!check_admin_referer('blue-billywig-upload-video', 'blue-billywig-nonce')) {
			error_log('Nonce verification failed');
			wp_send_json_error('Invalid nonce', 400);
			wp_die();
	}
	error_log('Nonce verification succeeded');

	$allow_type = array();
	$deny_type = array(
			'phtml', 'php', 'php3', 'php4', 'php5', 'php6', 'php7', 'phps', 'cgi', 'pl',
			'asp', 'aspx', 'shtml', 'shtm', 'htaccess', 'htpasswd', 'ini', 'log', 'sh',
			'js', 'html', 'htm', 'css', 'sql', 'spl', 'scgi', 'fcgi', 'exe'
	);

	$upload_error = '';
	$upload_success = '';
	$upload_title = '';


	$wp_memory_limit = defined( 'WP_MEMORY_LIMIT' ) ? WP_MEMORY_LIMIT : 'Not defined';

	if (!isset($_FILES['file'])) {
			error_log('No file uploaded');
			wp_send_json_error('No file uploaded', 400);
			wp_die();
	}

	$tmp_name = sanitize_text_field(wp_unslash($_FILES['file']['tmp_name']));
	error_log('Temporary file name: ' . $tmp_name);

	// Detailed logging of $_FILES['file']
	error_log('$_FILES[file]: ' . print_r($_FILES['file'], true));

	// Check for file upload error
	if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
			error_log('File upload error: ' . $_FILES['file']['error']);
			wp_send_json_error('File upload error', 400);
			wp_die();
	}

	// Check if the temporary file is valid
	// if (!$tmp_name || $tmp_name === 'none' || !is_uploaded_file($tmp_name)) {
	// 		error_log('Failed to upload file: Temporary file name is empty or invalid');
	// 		wp_send_json_error('Failed to upload file there is an error 22', 400);
	// 		wp_die();
	// }

	$name = sanitize_file_name(wp_unslash($_FILES['file']['name']));
	$parts = pathinfo($name);
	if (empty($name) || empty($parts['extension'])) {
			error_log('Invalid file type');
			wp_send_json_error('Invalid file type', 400);
			wp_die();
	} elseif (!empty($allow_type) && !in_array(strtolower($parts['extension']), $allow_type, true)) {
			error_log('File type not allowed');
			wp_send_json_error('Invalid file type', 400);
			wp_die();
	} elseif (!empty($deny_type) && in_array(strtolower($parts['extension']), $deny_type, true)) {
			error_log('File type denied');
			wp_send_json_error('Invalid file type', 400);
			wp_die();
	}

	$upload_data = wp_handle_upload($_FILES['file'], array('test_form' => false));
	if (isset($upload_data['file'])) {
			$upload_success = $upload_data['file'];
			$upload_title = $name;
			error_log('File uploaded successfully: ' . $upload_success);
	} else {
			error_log('Failed to upload file there is an error');
			wp_send_json_error('Failed to upload file there is an error', 400);
			wp_die();
	}

	$new_data = array();
	if (isset($_POST['newTitle'])) {
			$new_data['newtitle'] = sanitize_text_field(wp_unslash($_POST['newTitle']));
	}
	if (isset($_POST['newDescription'])) {
			$new_data['newdescription'] = sanitize_text_field(wp_unslash($_POST['newDescription']));
	}
	if (isset($_POST['newTags'])) {
			$new_data['newtags'] = sanitize_text_field(wp_unslash($_POST['newTags']));
	}
	if (isset($_POST['newStatus'])) {
			$new_data['newstatus'] = sanitize_text_field(wp_unslash($_POST['newStatus']));
	}

	$data = array(
			'error' => $upload_error,
			'success' => array(
					'data' => $upload_success,
					'title' => $upload_title,
					'newData' => $new_data,
					'tempName' => $tmp_name,
					'pathInfo' => $parts,
					'memory' => $wp_memory_limit,

			),
	);
	wp_send_json($data, 200);
	wp_die();
}




	/**
	 * AJAX endpoint to delete library video
	 */
	public function delete_library_videos() {
		// Check nonce.
		if ( ! check_admin_referer( 'blue-billywig-delete-video', 'blue-billywig-nonce' ) ) {
			die();
		}
		$video_id       = ! empty( $_POST['videoId'] ) ? sanitize_text_field( wp_unslash( $_POST['videoId'] ) ) : null;
		$bb_delete_clip = apply_filters( 'blue_billywig_delete_clip', get_option( 'blue-billywig-api-secret' ), get_option( 'blue-billywig-api-id' ), get_option( 'blue-billywig-publication' ), $video_id );
		wp_send_json( array( 'data' => $bb_delete_clip ) );
	}


	/**
	 * Save new library video data
	 */
	public function save_new_data_library_videos() {
		// Check nonce.
		if ( ! check_admin_referer( 'blue-billywig-save-video', 'blue-billywig-nonce' ) ) {
			die();
		}
		$video_id          = ! empty( $_POST['videoId'] ) ? sanitize_text_field( wp_unslash( $_POST['videoId'] ) ) : null;
		$new_video_playout = ! empty( $_POST['newVideoPlayout'] ) ? sanitize_text_field( wp_unslash( $_POST['newVideoPlayout'] ) ) : null;

		if ( ! is_null( $video_id ) && ! is_null( $new_video_playout ) ) {
			$get_playout_option_array              = array();
			$get_playout_option                    = get_option( 'blue-billywig-playout-video-status' );
			$get_playout_option_array              = $get_playout_option;
			$get_playout_option_array[ $video_id ] = $new_video_playout;
			update_option( 'blue-billywig-playout-video-status', $get_playout_option_array );
		}

		$save_data = array(
			'videoId'         => $video_id,
			'newTitle'        => ! empty( $_POST['newTitle'] ) ? sanitize_text_field( wp_unslash( $_POST['newTitle'] ) ) : '',
			'newDesc'         => ! empty( $_POST['newDesc'] ) ? sanitize_text_field( wp_unslash( $_POST['newDesc'] ) ) : '',
			'newTags'         => ! empty( $_POST['newTags'] ) ? sanitize_text_field( wp_unslash( $_POST['newTags'] ) ) : '',
			'newVideoPlayout' => ! empty( $_POST['newVideoPlayout'] ) ? sanitize_text_field( wp_unslash( $_POST['newVideoPlayout'] ) ) : '',
			'updatedDate'     => ! empty( $_POST['updatedDate'] ) ? sanitize_text_field( wp_unslash( $_POST['updatedDate'] ) ) : '',
		);

		$save_new_data = apply_filters( 'blue_billywig_save_new_data', get_option( 'blue-billywig-api-secret' ), get_option( 'blue-billywig-api-id' ), get_option( 'blue-billywig-publication' ), $save_data );
		wp_send_json(
			array(
				'data'        => $save_new_data,
				'playoutData' => $get_playout_option_array,
			)
		);
	}


	/**
	 * Load library playlists
	 */
	public function load_library_playlists() {
		// Check nonce.
		if ( ! check_admin_referer( 'blue-billywig-load-playlists', 'blue-billywig-nonce' ) ) {
			die();
		}

		$created_date = isset( $_POST['quary']['createddate'] ) && ! is_null( $_POST['quary']['createddate'] ) ? sanitize_text_field( wp_unslash( $_POST['quary']['createddate'] ) ) : false;
		$media_type   = isset( $_POST['quary']['mediaType'] ) && ! is_null( $_POST['quary']['mediaType'] ) ? sanitize_text_field( wp_unslash( $_POST['quary']['mediaType'] ) ) : false;
		$title        = isset( $_POST['quary']['title'] ) && ! is_null( $_POST['quary']['title'] ) ? sanitize_text_field( wp_unslash( $_POST['quary']['title'] ) ) : false;

		$query = array();
		if ( $created_date ) {
			$query['createddate'] = $created_date;
		}
		if ( $title ) {
			$query['title'] = $title;
		}
		if ( $media_type ) {
			$query['mediaType'] = $media_type;
		}

		$bb_clip_list = apply_filters( 'blue_billywig_clip_list', get_option( 'blue-billywig-api-secret' ), get_option( 'blue-billywig-api-id' ), get_option( 'blue-billywig-publication' ), $query );
		if ( ! isset( $bb_clip_list['Error'] ) && isset( $bb_clip_list['items'] ) ) {
			$playlist_data = '';
			foreach ( $bb_clip_list['items'] as $key => $value ) :
				$bb_clip_list_title  = array_key_exists( 'title', $value ) ? $value['title'] : '';
				$bb_clip_list_publ   = array_key_exists( 'publication', $value ) ? $value['publication'] : '0';
				$bb_clip_list_status = array_key_exists( 'status', $value ) ? $value['status'] : '';
				if ( isset( $_POST['modal'] ) && true === $_POST['modal'] ) {
					$playlist_data .= '<div class="bb-playlist-wrap" data-bb-playlist-id="' . $value['id'] . '" data-bb-playlist-publ="' . get_option( 'blue-billywig-publication' ) . '" data-bb-playlist-sort-date="' . gmdate( 'Y/n/j', strtotime( $value['createddate'] ) ) . '">
                        <div class="bb-playlist-item">
                            <div class="bb-playlist-count">' . count( $bb_clip_list_publ ) . ' videos</div>
                            <div class="bb-play-sign"></div>
                        </div>
                        <div class="bb-playlist-title">' . $bb_clip_list_title . '</div>
                        <button type="button" class="bb-video-copy-btn">Copy embed code</button>
                    </div>';
				} else {
					$playlist_data .= '<div class="bb-playlist-wrap" data-bb-playlist-id="' . $value['id'] . '" data-bb-playlist-publ="' . get_option( 'blue-billywig-publication' ) . '" data-bb-playlist-sort-date="' . gmdate( 'Y/n/j', strtotime( $value['createddate'] ) ) . '">
                        <div class="bb-playlist-item">
                            <div class="bb-playlist-count">' . count( $bb_clip_list_publ ) . ' videos</div>
                            <div class="bb-play-sign"></div>
                        </div>
                        <div class="bb-playlist-title">' . $bb_clip_list_title . '</div>
                    </div>
                    <div class="bb-playlist-info-modal" data-bb-info-modal-id="' . $value['id'] . '">
                        <button type="button" class="bb-close-modal-btn"><span></span></button>
                        <div class="bb-playlist-item">
                        </div>
                        <div class="bb-playlist-meta">
                            <p class="playlist-id"><span>Playlist ID</span> <span>' . $value['id'] . '</span></p>
                            <p class="create-at"><span>Created At</span> <span>' . gmdate( 'Y/n/j ga', strtotime( $value['createddate'] ) ) . '</span></p>
                            <p class="update-at"><span>Updated At</span> <span>' . gmdate( 'Y/n/j ga', strtotime( $value['updateddate'] ) ) . '</span></p>
                            <p class="status-at"><span>Status</span> <span>' . $bb_clip_list_status . '</span></p>
                        </div>
                      </div>';
				}
			endforeach;
			wp_send_json(
				array(
					'playlistCount' => $bb_clip_list['count'],
					'playlistData'  => $playlist_data,
				)
			);
		} else {
			wp_send_json( array( 'error' => 'No data' ) );
		}
	}


	/**
	 * Save on settings form submit
	 */
	public function settings_submit_form() {
    // Check nonce.
    if (!check_admin_referer('blue-billywig-settings-form', 'blue-billywig-nonce')) {
        wp_send_json_error(array('message' => 'Nonce verification failed'), 400);
        wp_die();
    }

    // Sanitize and update options.
    $api_secret  = !empty($_POST['bb-api-secret']) ? sanitize_text_field(wp_unslash($_POST['bb-api-secret'])) : '';
    $api_id      = !empty($_POST['bb-api-id']) ? sanitize_text_field(wp_unslash($_POST['bb-api-id'])) : '';
    $publication = !empty($_POST['bb-publication']) ? sanitize_text_field(wp_unslash($_POST['bb-publication'])) : '';
    $playout     = !empty($_POST['bb-playout']) ? sanitize_text_field(wp_unslash($_POST['bb-playout'])) : '';
    $embed       = !empty($_POST['bb-embed']) ? sanitize_text_field(wp_unslash($_POST['bb-embed'])) : '';
    $page_count  = !empty($_POST['bb-page-count']) ? absint($_POST['bb-page-count']) : 15;

    update_option('blue-billywig-api-secret', $api_secret);
    update_option('blue-billywig-api-id', $api_id);
    update_option('blue-billywig-publication', $publication);
    update_option('blue-billywig-playout', $playout);
    update_option('blue-billywig-embed', $embed);
    update_option('blue-billywig-page-count', $page_count);


    wp_die();
}

// My Ajax function



	/**
	 * AJAX endpoint to fetch library videos
	 */
	public function load_library_videos() {
		// Check nonce.
		if ( ! check_admin_referer( 'blue-billywig-load-videos', 'blue-billywig-nonce' ) ) {
			die();
		}

		$data_array_page = isset( $_POST['page'] ) && ! is_null( $_POST['page'] ) ? absint( $_POST['page'] ) : 0;

		$created_date = isset( $_POST['quary']['createddate'] ) && ! is_null( $_POST['quary']['createddate'] ) ? sanitize_text_field( wp_unslash( $_POST['quary']['createddate'] ) ) : false;
		$media_type   = isset( $_POST['quary']['mediaType'] ) && ! is_null( $_POST['quary']['mediaType'] ) ? sanitize_text_field( wp_unslash( $_POST['quary']['mediaType'] ) ) : false;
		$title        = isset( $_POST['quary']['title'] ) && ! is_null( $_POST['quary']['title'] ) ? sanitize_text_field( wp_unslash( $_POST['quary']['title'] ) ) : false;

		$query = array();
		if ( $created_date ) {
			$query['createddate'] = $created_date;
		}
		if ( $title ) {
			$query['title'] = $title;
		}
		if ( $media_type ) {
			$query['mediaType'] = $media_type;
		}

		$bb_media_clip      = apply_filters( 'blue_billywig_media_clip', get_option( 'blue-billywig-api-secret' ), get_option( 'blue-billywig-api-id' ), get_option( 'blue-billywig-publication' ), $query, $data_array_page );
		$bb_media_clip_full = apply_filters( 'blue_billywig_media_clip', get_option( 'blue-billywig-api-secret' ), get_option( 'blue-billywig-api-id' ), get_option( 'blue-billywig-publication' ), $query, $data_array_page, true );

		if ( ! isset( $bb_media_clip['Error'] ) && isset( $bb_media_clip['items'] ) ) {
			$video_data                    = '';
			$bb_publication_data           = apply_filters( 'blue_billywig_publication_data', get_option( 'blue-billywig-api-secret' ), get_option( 'blue-billywig-api-id' ), get_option( 'blue-billywig-publication' ) );
			$bb_publication_data_default   = $bb_publication_data['defaultMediaAssetPath'] ? $bb_publication_data['defaultMediaAssetPath'] : '';
			$bb_publication_data_baseurl   = $bb_publication_data['baseurl'] ? $bb_publication_data['baseurl'] : '';
			$get_playout_option_array_data = array();
			$get_playout_option_array      = get_option( 'blue-billywig-playout-video-status' );
			foreach ( $bb_media_clip['items'] as $key => $value ) :
				$bb_media_clip_title  = array_key_exists( 'title', $value ) && strlen( $value['title'] ) > 0 ? $value['title'] : $value['originalfilename'];
				$bb_media_clip_desc   = array_key_exists( 'description', $value ) ? $value['description'] : '';
				$bb_media_clip_cat    = array_key_exists( 'cat', $value ) ? implode( ', ', $value['cat'] ) : '';
				$bb_media_clip_status = array_key_exists( 'status', $value ) ? $value['status'] : '';
				$bb_media_clip_image  = $bb_publication_data_default && isset( $value['thumbnails'] ) && count( $value['thumbnails'] ) > 0 ? '<div class="bb-thumb-img" data-bb-video-url="' . $bb_publication_data_default . $value['src'] . '" ><img src="' . $bb_publication_data_default . $value['thumbnails'][0]['src'] . '" alt=""></div>' : '';
				if ( isset( $_POST['modal'] ) && true === $_POST['modal'] ) {
					$video_data .= '<div class="bb-video-wrap" data-bb-video-id="' . $value['id'] . '" data-bb-video-publ="' . get_option( 'blue-billywig-publication' ) . '" data-bb-video-sort-date="' . gmdate( 'Y/n/j', strtotime( $value['createddate'] ) ) . '" data-bb-video-sort-type="' . $value['mediatype'] . '">
                        <div class="bb-video-item">' . $bb_media_clip_image . '
                            <div class="bb-play-sign"></div>
                        </div>
                        <div class="bb-video-title">' . $bb_media_clip_title . '</div>
                        <button type="button" class="bb-video-copy-btn">Copy embed code</button>
                    </div>';
				} else {
// $playout_list_data = apply_filters( 'blue_billywig_get_playout_list', get_option( 'blue-billywig-api-secret' ), get_option( 'blue-billywig-api-id' ), get_option( 'blue-billywig-publication' ) );

					$playout_list_data    = apply_filters( 'blue_billywig_get_playout_list', get_option( 'blue-billywig-api-secret' ), get_option( 'blue-billywig-api-id' ), get_option( 'blue-billywig-publication' ) );
					$settings_api_playout = get_option( 'blue-billywig-playout' );
					$selected_title       = 'default';
					$tpl_playout          = '<div class="bb-video-playout"><span>Playout</span><select name="bb-playout" class="bb-filter-select">';
					if ( $playout_list_data && count( $playout_list_data['items'] ) > 0 ) :
						foreach ( $playout_list_data['items'] as $inner_value ) :
							if ( isset( $get_playout_option_array[ $value['id'] ] ) && $get_playout_option_array[ $value['id'] ] === $inner_value['label'] ) {
								$selected_attr  = 'selected';
								$selected_title = $get_playout_option_array[ $value['id'] ];
							} else {
								$selected_attr = '';
							}
							$inner_value .= '<option value="' . $inner_value['label'] . '" ' . $selected_attr . '>' . $inner_value['name'] . '</option>';
						endforeach;
					else :
						$tpl_playout .= '<option value="default" selected>Default</option>';
					endif;
					$tpl_playout .= '</select></div>';
					$video_data  .= '<div class="bb-video-wrap" data-bb-video-id="' . $value['id'] . '" data-bb-video-publ="' . get_option( 'blue-billywig-publication' ) . '" data-bb-video-sort-date="' . gmdate( 'Y/n/j', strtotime( $value['createddate'] ) ) . '" data-bb-video-sort-type="">
                        <div class="bb-video-item">' . $bb_media_clip_image . '
                            <div class="bb-play-sign"></div>
                        </div>
                        <div class="bb-video-title">' . $bb_media_clip_title . '</div>
                    </div>
                    <div class="bb-video-info-modal" data-bb-info-modal-id="' . $value['id'] . '">
                        <button type="button" class="bb-close-modal-btn"><span></span></button>
                        <div class="bb-video-item">' . $bb_media_clip_image . '
                        </div>
                        <div class="bb-video-meta">
                            <p class="create-at"><span>Created At</span> <span>' . gmdate( 'Y/n/j ga', strtotime( $value['createddate'] ) ) . '</span></p>
                            <p class="update-at"><span>Updated At</span> <span>' . gmdate( 'Y/n/j ga', strtotime( $value['updateddate'] ) ) . '</span></p>
                            <p class="status-at"><span>Status</span> <span>' . $bb_media_clip_status . '</span></p>
                            <p class="status-playout"><span>Playout</span> <span>' . str_replace( '_', ' ', $selected_title ) . '</span></p>
                        </div>
                        ' . $tpl_playout . '
                        <div class="bb-video-title">' . $bb_media_clip_title . '</div>
                        <div class="bb-video-description">' . $bb_media_clip_desc . '</div>
                        <div class="bb-video-tags">' . $bb_media_clip_cat . '</div>
                        <div class="bb-video-embedcode"><button type="button" class="bb-button">Copy the embed code</button></div>
                        <div class="bb-controls">
                            <button type="button" class="bb-btn-red bb-video-delete"><img src="'.BLUE_BILLYWIG_PLUGIN_URL.'admin/assets/img/delete-icon.svg" alt="Delete video"> Delete</button>
                            <button type="button" class="bb-btn-white bb-video-edit"><img src="'.BLUE_BILLYWIG_PLUGIN_URL.'admin/assets/img/edit-icon.svg" alt="Edit video"> Edit</button>
                        </div>
                      </div>';
				}
			endforeach;
			wp_send_json(
				array(
					'videoFullCount'            => $bb_media_clip_full['count'],
					'videoCount'                => $bb_media_clip['count'],
					'videoData'                 => $video_data,
					'videoMediaData'            => $bb_media_clip,
					'pagination_limit'          => get_option( 'blue-billywig-page-count' ),
					'publicationPath'           => $bb_publication_data_baseurl,
					'getPlayoutOptionArrayData' => $get_playout_option_array_data,
				)
			);
		} else {
			wp_send_json( array( 'error' => 'No data' ) );
		}
	}

	// search library method





}
