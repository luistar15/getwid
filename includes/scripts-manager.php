<?php

namespace Getwid;

/**
 * Class ScriptsManager
 * @package Getwid
 */
class ScriptsManager {

	private $version;
	private $prefix;

	/**
	 * ScriptsManager constructor.
	 *
	 * @param Settings $settings Plugin settings
	 *
	 */
	public function __construct( $settings ) {

		$this->version = $settings->getVersion();
		$this->prefix  = $settings->getPrefix();

		add_action( 'wp_enqueue_scripts', [$this, 'enqueueScriptsAndStyles'], 5 );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueueScriptsAndStyles'], 5 );

		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueueEditorAssets' ] );
		add_action( 'enqueue_block_assets', [ $this, 'enqueueBlockAssets' ] );
		add_action( 'enqueue_block_assets', [ $this, 'enqueueFrontBlockAssets' ] );

		add_action( 'wp_ajax_getwid_api_key', [ $this, 'getwid_google_api_key' ] );
		add_action( 'wp_ajax_getwid_recaptcha_api_key', [ $this, 'getwid_recaptcha_api_key' ] );

		add_action( 'wp_ajax_getwid_instagram_token', [ $this, 'getwid_instagram_token' ] );
		add_action( 'wp_ajax_getwid_contact_form_send_mail', [ $this, 'getwid_contact_form_send_mail' ] );
		add_action( 'wp_ajax_nopriv_getwid_contact_form_send_mail', [ $this, 'getwid_contact_form_send_mail' ] );

		add_action( 'after_theme_setup', [ $this, 'getwid_enqueue_editor_section_css' ] );
	}
	
	public function getwid_instagram_token() {
		$action = $_POST['option'];
		$data = $_POST['data'];

		$response = false;
		if ($action == 'get') {
			$response = get_option( 'getwid_instagram_token', '' );
		}

		wp_send_json_success( $response );
	}

	public function getwid_contact_form_send_mail() {

		$data = $_POST['data'];

		function send_mail() {
			$to 	 = !empty($data['to']) ? trim($data['to']) : get_option('admin_email');
			$subject = !empty($data['subject']) ? trim($data['subject']) : get_option('blogname');

			$from = trim($data['from']);

			$name 	 = stripslashes($data['name']);
			$message = stripslashes($data['message']);
			
			$body = $name . "\r\n" . $from . "\r\n" . $message;
			$headers = array(
				'From: ' . get_option('blogname') . ' <' . get_option('admin_email') . '>' . "\r\n",
				'Reply-To: ' . $name . ' <' . $from . '>'
			);
			
			$return = getwid()->getMailer()->send( $to, $subject, $body, $headers );

			wp_send_json_success( $return );
		};

		if ( json_decode( $data['captcha'], 'boolean' ) ) {
			$recaptcha_challenge  = $data['challenge'];
			$recaptcha_secret_key = get_option('getwid_recaptcha_secret_key');

			$request = wp_remote_get(
				'https://google.com/recaptcha/api/siteverify?secret=' . $recaptcha_secret_key . '&response=' . $recaptcha_challenge,
				array( 'timeout' => 15 )
			);

			$return = json_decode( wp_remote_retrieve_body( $request ) );

			!$return->{'success'} ? wp_send_json_success( $return ) : send_mail();
		} else {
			send_mail();
		}
	}

	public function getwid_google_api_key() {
		$action = $_POST['option'];
		$data = $_POST['data'];
		$nonce = $_POST['nonce'];

		if ( ! wp_verify_nonce( $nonce, 'getwid_nonce_google_api_key' ) ) {
			wp_send_json_error();
		}

		$response = false;
		if ($action == 'get') {   //delete this option later
			$response = get_option( 'getwid_google_api_key', '');
		} elseif ($action == 'set') {
			$response = update_option( 'getwid_google_api_key', $data );
		} elseif ($action == 'delete') {
			$response = delete_option( 'getwid_google_api_key');
		}

		wp_send_json_success( $response );
	}

	public function getwid_recaptcha_api_key() {

		$data   = $_POST['data'  ];
		$option = $_POST['option'];
		
		$site_api_key   = $data['site_api_key'  ];
		$secret_api_key = $data['secret_api_key'];		

		$response = false;
		if ( $option == 'get' ) {
			$response = get_option( 'getwid_recaptcha_site_key', '') . ' ' . get_option( 'getwid_recaptcha_secret_key', '');
		} elseif ($option == 'set') {
			if ( !empty( $site_api_key )) {
				$response = update_option( 'getwid_recaptcha_site_key', $site_api_key );
			}
			if ( !empty( $secret_api_key ) ) {
				$response = update_option( 'getwid_recaptcha_secret_key', $secret_api_key );
			}
		} elseif ($option == 'delete') {
			$response = delete_option( 'getwid_recaptcha_site_key'	);
			$response = delete_option( 'getwid_recaptcha_secret_key');
		}
	}

	public function getwid_get_image_sizes() {

		global $_wp_additional_image_sizes;

		$intermediate_image_sizes = get_intermediate_image_sizes();

		$image_sizes = array();
		foreach ( $intermediate_image_sizes as $size ) {
			if ( isset($_wp_additional_image_sizes[$size]) ) {
				$image_sizes[$size] = array(
					'width'  => $_wp_additional_image_sizes[$size]['width'],
					'height' => $_wp_additional_image_sizes[$size]['height'],
				);
			} else {
				$image_sizes[$size] = array(
					'width'  => intval( get_option( "{$size}_size_w" ) ),
					'height' => intval( get_option( "{$size}_size_h" ) ),
				);
			}
		}

		$sizes_arr = [];
		foreach ($image_sizes as $key => $value) {
			$temp_arr = [];
			$temp_arr['value'] = $key;
			$temp_arr['label'] = ucwords(strtolower(preg_replace('/[-_]/', ' ', $key))). " - {$value['width']} x {$value['height']}";
			$sizes_arr[] = $temp_arr;
		}

		$sizes_arr[] = array(
			'value' => 'full',
			'label' => __('Full Size', 'getwid')
		);

		return $sizes_arr;
	}

	public function getwid_load_locale_data() {
		$locale_data = $this->getwid_locale_data( 'gutenberg' );
		wp_add_inline_script(
			'wp-i18n',
			'wp.i18n.setLocaleData( ' . json_encode( $locale_data ) . ' );'
		);
	}

	public function getwid_locale_data( $domain ) {
		$translations = get_translations_for_domain( $domain );

		$locale = array(
			'' => array(
				'domain' => $domain,
				'lang'   => is_admin() ? get_user_locale() : get_locale(),
			),
		);

		if ( ! empty( $translations->headers['Plural-Forms'] ) ) {
			$locale['']['plural_forms'] = $translations->headers['Plural-Forms'];
		}

		foreach ( $translations->entries as $msgid => $entry ) {
			$locale[ $msgid ] = $entry->translations;
		}

		return $locale;
	}

	public function enqueueScriptsAndStyles(){
		//Scripts
		wp_enqueue_script(
			'slick',
			getwid_get_plugin_url('vendors/slick/slick/slick.min.js'),
			['jquery'],
			'1.9.0',
			true
		);

		wp_enqueue_script(
			'wow',
			getwid_get_plugin_url('vendors/wow.js/dist/wow.min.js'),
			['jquery'],
			'1.2.1',
			true
		);

		wp_enqueue_script(
			'countup',
			getwid_get_plugin_url('vendors/countup.js/dist/countUp.min.js'),
			[],
			'2.0.4',
			true
		);

		wp_enqueue_script(
			'waypoints',
			getwid_get_plugin_url('vendors/waypoints/lib/jquery.waypoints.min.js'),
			['jquery'],
			'4.0.1'
		);

		//Styles
		wp_enqueue_style(
			'slick',
			getwid_get_plugin_url('vendors/slick/slick/slick.css'),
			[],
			'1.9.0'
		);

		wp_enqueue_style(
			'slick-theme',
			getwid_get_plugin_url('vendors/slick/slick/slick-theme.css'),
			[],
			'1.9.0'
		);

		wp_enqueue_style(
			'animate',
			getwid_get_plugin_url('vendors/animate.css/animate.min.css'),
			[],
			'3.7.0'
		);
	}

	/**
	 * Enqueue editor-only js and css
	 */
	public function enqueueEditorAssets() {

		// Enqueue the bundled block JS file
		wp_enqueue_script(
			"{$this->prefix}-blocks-editor-js",
			getwid_get_plugin_url( 'assets/js/editor.blocks.js' ),
			[
				'wp-i18n',
				'wp-editor',
				'wp-element',
				'wp-blocks',
				'wp-components',
				'wp-api',
				'wp-api-fetch',
				'imagesloaded',
				'slick',
				'wow',
				'countup',
				'waypoints',
				'jquery-ui-tabs',
				'jquery-ui-accordion',
			],
			$this->version,
			true
		);

		wp_localize_script(
			"{$this->prefix}-blocks-editor-js",
			'Getwid',			
			apply_filters(
				'getwid/editor_blocks_js/localize_data',
				[
					'localeData' => $this->getwid_locale_data( 'getwid' ),
					'settings' => [
						'google_api_key'  => get_option('getwid_google_api_key', '' ),
						'instagram_token' => get_option('getwid_instagram_token', ''),						
						'assets_path' => getwid_get_plugin_url('/assets'),
						'image_sizes' => $this->getwid_get_image_sizes(),
						'excerpt_length' => apply_filters( 'excerpt_length', 55 ),
						'recaptcha_site_key' => get_option('getwid_recaptcha_site_key', ''),
						'recaptcha_secret_key' => get_option('getwid_recaptcha_secret_key', '')
					],
					'templates' => [
						'new' => admin_url( 'post-new.php?post_type=getwid_template_part' ),
						'view' => admin_url( 'edit.php?post_type=getwid_template_part' ),				
					],
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'options_writing_url' => admin_url( 'options-writing.php' ),
					'nonces' => array(
						'google_api_key' => wp_create_nonce( 'getwid_nonce_google_api_key' ),
					)
				]
			)
		);

		wp_enqueue_style(
			'fonticonpicker-base-theme',
			getwid_get_plugin_url('vendors/fonticonpicker/react-fonticonpicker/dist/fonticonpicker.base-theme.react.css'),
			null,
			'1.2.0'
		);

		wp_enqueue_style(
			'fonticonpicker-react-theme',
			getwid_get_plugin_url('vendors/fonticonpicker/react-fonticonpicker/dist/fonticonpicker.material-theme.react.css'),
			null,
			'1.2.0'
		);

		// Enqueue optional editor only styles
		wp_enqueue_style(
			"{$this->prefix}-blocks-editor",
			getwid_get_plugin_url( 'assets/css/blocks.editor.css' ),
			null,
			$this->version
		);
	}

	/**
	 * Enqueue editor & frontend js and css
	 */
	public function enqueueBlockAssets() {
		wp_enqueue_style(
			"{$this->prefix}-blocks",
			getwid_get_plugin_url( 'assets/css/blocks.style.css' ),
			apply_filters(
				'getwid/blocks_style_css/dependencies',
				[]
			),
			$this->version
		);

		wp_add_inline_style("{$this->prefix}-blocks", getwid_generate_section_content_width_css());
	}

	/**
	 * Enqueue frontend-only block js and css
	 */
	public function enqueueFrontBlockAssets() {
		if ( is_admin() ) {
			return;
		}

		wp_enqueue_script(
			"{$this->prefix}-blocks-frontend-js",
			getwid_get_plugin_url( 'assets/js/frontend.blocks.js' ),
			apply_filters(
				'getwid/frontend_blocks_js/dependencies',
				[ 'wp-i18n', 'slick', 'wow', 'jquery-ui-tabs', 'jquery-ui-accordion', 'lodash' ]
			),
			$this->version,
			true
		);

		wp_localize_script(
			"{$this->prefix}-blocks-frontend-js",
			'Getwid',
			apply_filters(
				'getwid/frontend_blocks_js/localize_data',
				[
					'settings'   => [
						'google_api_key' => get_option('getwid_google_api_key', ''),
						'recaptcha_site_key' => get_option('getwid_recaptcha_site_key', ''),
						'recaptcha_secret_key' => get_option('getwid_recaptcha_secret_key', '')
					],
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
				]
			)
		);
	}

	function getwid_enqueue_editor_section_css(){
		add_editor_style(getwid_generate_section_content_width_css());
	}

}