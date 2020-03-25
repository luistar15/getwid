<?php

namespace Getwid\Blocks;

class Icon extends \Getwid\Blocks\AbstractBlock {

	protected static $blockName = 'getwid/icon';

    public function __construct() {

		parent::__construct( self::$blockName );

        register_block_type(
            self::$blockName
        );

		if ( $this->isEnabled() ) {

			add_filter( 'getwid/blocks_style_css/dependencies', [ $this, 'block_frontend_styles' ] );

			//Register JS/CSS assets
			wp_register_style(
				'animate',
				getwid_get_plugin_url( 'vendors/animate.css/animate.min.css' ),
				[],
				'3.7.0'
			);
		}
    }

	public function getLabel() {
		return __('Icon', 'getwid');
	}

    public function block_frontend_styles($styles) {

		getwid_log( self::$blockName, $this->hasBlock() );

		if ( !$this->hasBlock() && !has_getwid_nested_blocks() ) {
			return $styles;
		}

		//fontawesome
		$styles = \Getwid\FontIconsManager::getInstance()->enqueueDefaultFont( $styles );

		//animate.min.css
        if ( ! in_array( 'animate', $styles ) ) {
            array_push( $styles, 'animate' );
        }

        return $styles;
    }
}

\Getwid\BlocksManager::getInstance()->addBlock(
	new \Getwid\Blocks\Icon()
);
