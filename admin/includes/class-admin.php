<?php

namespace BlueBillywigPlugin\Admin;

/**
 *  Blue Billywig Admin class
 */
class Admin {

    /**
     * Initialize
     */
    public function init() {
        // Hooks.
        add_action('admin_menu', array($this, 'add_menus'));
        add_filter('plugin_action_links_' . plugin_basename(BLUE_BILLYWIG_PLUGIN_FILE), array($this, 'add_action_links'));
        add_action('media_buttons', array($this, 'render_media_button'), 15);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'), 99);
				add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) ); // New
                 // Register REST API route.
                // For Rest Api to get Custom playout Screen and Publication Data
                // add_action('rest_api_init', array($this, 'register_rest_routes'));
                // add_action('rest_api_init', array($this, 'register_custom_rest_routes'));

        // Classes.
        (new Ajax())->init();
    }

    /**
     * Enqueue backend scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_style('bb_admin_styles', BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/css/admin-styles.min.css', array(), BLUE_BILLYWIG_PLUGIN_VERSION);
        wp_enqueue_style('bb_jquery-ui', BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/css/jquery-smoothness.css', array(), '1.12.1');
        wp_enqueue_script('jquery-ui-datepicker');
        if (strstr(get_current_screen()->id, 'page_blue-billywig') !== false) {
            wp_enqueue_style('bb_styles', BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/css/styles.css', array(), BLUE_BILLYWIG_PLUGIN_VERSION);
            wp_enqueue_script('bb_imask_js', BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/js/imask.min.js', array(), '7.3.0', true);
            wp_enqueue_script('bb_masks_js', BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/js/bb_masks.min.js', array(), BLUE_BILLYWIG_PLUGIN_VERSION, true);
            add_filter('admin_footer_text', '__return_empty_string', 11);
            add_filter('update_footer', '__return_empty_string', 11);
        }
        wp_enqueue_script('bb_main_js', BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/js/bb_main.min.js', array('jquery'), BLUE_BILLYWIG_PLUGIN_VERSION, true);

        // Add nonce data to JavaScript.
        wp_localize_script(
            'bb_main_js',
            'blue_billywig_data',
            array(
                'delete_nonce' => wp_create_nonce('blue-billywig-delete-video'),
                'save_nonce' => wp_create_nonce('blue-billywig-save-video'),
                'load_playlists_nonce' => wp_create_nonce('blue-billywig-load-playlists'),
                'load_videos_nonce' => wp_create_nonce('blue-billywig-load-videos'),
                'upload_nonce' => wp_create_nonce('blue-billywig-upload-video'),
                'plugin_url' => BLUE_BILLYWIG_PLUGIN_URL
            )
        );

        // Enqueue the block editor script and style.
        wp_enqueue_script(
            'bluebillywig-block-script',
            BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/js/block.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
            filemtime(BLUE_BILLYWIG_PLUGIN_DIR . 'admin/assets/js/block.js'),
            true
        );

        wp_enqueue_style(
            'bluebillywig-block-editor-style',
            BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/css/editor.css',
            array('wp-edit-blocks'),
            filemtime(BLUE_BILLYWIG_PLUGIN_DIR . 'admin/assets/css/editor.css')
        );

        // wp_enqueue_script(
        //     'bluebillywig-gutenberg-plugin',
        //     BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/js/plugin.js',
        //     array('wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components'),
        //     filemtime(BLUE_BILLYWIG_PLUGIN_DIR . 'admin/assets/js/plugin.js'),
        //     true
        // );
    }

    /**
     * Register the Blue Billywig Gutenberg block
     */
    public function register_bluebillywig_block() {
        register_block_type('bluebillywig/embed', array(
            'editor_script' => 'bluebillywig-block-script',
            'editor_style' => 'bluebillywig-block-editor-style',
        ));
    }

/**
	 * Register REST API routes
	 */
	public function register_rest_routes() {
		register_rest_route('bluebillywig/v1', '/get_custom_playout_screen', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_custom_playout_screen'),
			'permission_callback' => '__return_true', // Allow public access; modify as needed
		));
	}
    	// Register the REST API route in your register_rest_routes() function
        public function register_custom_rest_routes() {
            register_rest_route('bluebillywig/v1', '/search_media_clips', array(
                'methods' => 'GET',
                'callback' => array($this, 'search_media_clips'),
                'permission_callback' => '__return_true', // Allow public access; modify as needed
            ));
        }
         // New method to handle the media clip search.
    public function search_media_clips() {
        $search_query = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

        // Query the media clips using the provided title search query.
        $query = array(
            'title' => $search_query,
        );

        $media_clips = apply_filters('blue_billywig_media_clip', get_option('blue-billywig-api-secret'), get_option('blue-billywig-api-id'), get_option('blue-billywig-publication'), $query, 0, false);

        if (isset($media_clips['items'])) {
            $media_clip_results = array_slice($media_clips['items'], 0, 5);
            wp_send_json($media_clip_results);
        } else {
            wp_send_json(array('error' => 'No media clips found'));
        }
    }


	/**
	 * REST API callback for getting custom playout screen and publication
	 */
	public function get_custom_playout_screen() {
		$playout_screen = get_option('blue-billywig-playout', 'custom_playout_screen');
		$publication = get_option('blue-billywig-publication', 'plugindemo');

		return rest_ensure_response(array(
			'customPlayoutScreen' => $playout_screen,
			'publication' => $publication,
		));
	}

    /**
     * Adds settings link to plugin action links
     *
     * @param array $links An array of plugin action links.
     *
     * @return array
     */
    public function add_action_links($links) {
        $links[] = '<a href="' . admin_url('admin.php?page=blue-billywig-settings') . '">Settings</a>';
        return $links;
    }

		public function enqueue_block_editor_assets() {
			wp_enqueue_script(
					'bluebillywig-block',
					BLUE_BILLYWIG_PLUGIN_URL . 'admin/assets/js/block.js',
					array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components' ),
					filemtime( BLUE_BILLYWIG_PLUGIN_DIR . 'admin/assets/js/block.js' )
			);
	}
    /**
     * Renders Blue Billywig media button
     */
    public function render_media_button() {
        echo '<button type="button" id="bb-add-media-button" class="button">Blue Billywig media</button>';
    }

    /**
     * Add admin menus
     */
    public function add_menus() {
        add_menu_page(
            'Blue Billywig',
            'Blue Billywig',
            'manage_options',
            'blue-billywig-upload',
            array($this, 'render_upload_page'),
            'dashicons-format-video',
            66
        );
        add_submenu_page(
            'blue-billywig-upload',
            'Upload',
            'Upload',
            'manage_options',
            'blue-billywig-upload',
            array($this, 'render_upload_page')
        );
        add_submenu_page(
            'blue-billywig-upload',
            'Library',
            'Library',
            'manage_options',
            'blue-billywig-library',
            array($this, 'render_library_page')
        );
        add_submenu_page(
            'blue-billywig-upload',
            'Settings',
            'Settings',
            'manage_options',
            'blue-billywig-settings',
            array($this, 'render_settings_page')
        );
        add_submenu_page(
            'blue-billywig-upload',
            'Platform',
            'Platform',
            'manage_options',
            'blue-billywig-platform',
            array($this, 'render_platform_page')
        );
    }

    public function render_settings_page() {
        include_once BLUE_BILLYWIG_PLUGIN_DIR . 'admin/partials/settings.php';
    }

    public function render_upload_page() {
        include_once BLUE_BILLYWIG_PLUGIN_DIR . 'admin/partials/upload.php';
    }

    public function render_library_page() {
        include_once BLUE_BILLYWIG_PLUGIN_DIR . 'admin/partials/library.php';
    }

    public function render_platform_page() {
        include_once BLUE_BILLYWIG_PLUGIN_DIR . 'admin/partials/platform.php';
    }
}
