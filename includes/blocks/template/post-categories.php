<?php

function render_getwid_template_post_categories( $attributes ) {
    //Not BackEnd render if we view from template page
    if (get_post_type() == 'getwid_template_part'){
        return;
    }

    $block_name = 'wp-block-getwid-template-post-categories';
    $wrapper_class = $block_name;

    $wrapper_style = '';
    //Classes
    if ( isset( $attributes['align'] ) ) {
        $wrapper_class .= ' align' . $attributes['align'];
    }
    if ( isset( $attributes['textAlignment']) ) {
        $wrapper_style .= 'text-align: '.esc_attr($attributes['textAlignment']).';';
    }      

	$categories_list = get_the_category_list( esc_html__(', ', 'getwid') );
	$result = '';

	if ( $categories_list ) {
		ob_start();
		?>
			<div class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo (!empty($wrapper_style) ? 'style="'.esc_attr($wrapper_style).'"' : '');?>>
				<?php echo $categories_list ?>
			</div>
		<?php

		$result = ob_get_clean();
	}

    return $result;
}
register_block_type(
    'getwid/template-post-categories',
    array(
        'attributes' => array(
            'align' => array(
                'type' => 'string',
            ),
            'textAlignment' => array(
                'type' => 'string',
            ),
        ),
        'render_callback' => 'render_getwid_template_post_categories',
    )
);