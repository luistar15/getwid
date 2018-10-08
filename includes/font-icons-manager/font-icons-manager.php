<?php

namespace Getwid;

class FontIconsManager {

	/**
	 * @var array
	 */
	private $fonts;

	public function __construct() {
		add_action( 'init', [ $this, 'extendFontIcons' ] );
		add_filter( 'getwid_localize_blocks_js_data', [ $this, 'setIconsListLocalizeData' ] );
		add_filter( 'getwid_blocks_style_dependencies', [ $this, 'addFontStylesToDependencies' ] );

	}

	public function extendFontIcons() {

		$this->registerFontAwesome();

		do_action( 'getwid_extend_font_icons', $this );
	}

	private function registerFontAwesome(){

		add_action( 'enqueue_block_assets', function () {
			wp_register_style(
				'font-awesome-free',
				getwid_get_plugin_url( 'node_modules/@fortawesome/fontawesome-free/css/all.css' ),
				null,
				'5.3.1'
			);
		}, 8 );

		// Register Font Awesome by default
		$this->registerFont( 'fontawesome', [
			'icons' => require( dirname( __FILE__ ) . '/font-awesome-icon-list.php' ),
			'style' => 'font-awesome-free',
		] );
	}

	/**
	 * @param string $fontName
	 * @param array  $args       {
	 *                           Optional. Array of icon font arguments. Any arguments may be defined, however the
	 *                           ones described below are supported by default. Default empty array.
	 *
	 * @type array   $icons      Array of categories that contains icons
	 * @type array   $style      Handle of style
	 * }
	 */
	public function registerFont( $fontName, $args ) {
		$this->fonts[ $fontName ] = [
			'icons' => ! empty( $args['icons'] ) ? $args['icons'] : [],
			'style' => ! empty( $args['style'] ) ? $args['style'] : '',
		];
	}

	/**
	 * @param string $fontName
	 */
	public function deregisterFont( $fontName ) {
		if ( isset( $this->fonts[ $fontName ] ) ) {
			unset ( $this->fonts[ $fontName ] );
		}
	}

	/**
	 * @return array
	 */
	private function getCategorizedIconList() {
		$iconsByFonts = array_values( array_column( $this->fonts, 'icons' ) );

		return count( $iconsByFonts ) > 1 ? array_merge( ...$iconsByFonts ) : current( $iconsByFonts );
	}

	/**
	 * @param array $localizeData
	 *
	 * @return array
	 */
	public function setIconsListLocalizeData( $localizeData ) {
		$localizeData['settings']['iconList'] = $this->getCategorizedIconList();

		return $localizeData;
	}

	/**
	 * @param array $deps
	 *
	 * @return array mixed
	 */
	public function addFontStylesToDependencies( $deps ) {
		return array_unique(
			array_merge( $deps,
				array_filter(
					array_column( $this->fonts, 'style' )
				)
			)
		);
	}
}