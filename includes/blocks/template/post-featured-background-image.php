<?php

function render_getwid_template_post_featured_background_image( $attributes, $content ) {
    $block_name = 'wp-block-getwid-template-post-featured-background-image';
    $wrapper_class = $block_name;

    $wrapper_style = '';
    //Classes
    if ( isset( $attributes['minHeight'] ) ) {
        $wrapper_style .= 'min-height: '.$attributes['minHeight'].';';
    }

    if ( isset( $attributes['align'] ) ) {
        $wrapper_class .= ' align' . $attributes['align'];
    }  
    
    $imageSize = ( ( isset($attributes['imageSize']) && $attributes['imageSize'] ) ? $attributes['imageSize'] : 'post-thumbnail');

    $current_post = get_post( get_the_ID() );

    //Content Slide style
    $content_container_style = '';
    //Padding
    $content_container_class = $block_name.'__content_container';
    $content_container_class .= (isset($attributes['paddingTop']) && $attributes['paddingTop'] !='' && $attributes['paddingTop'] != 'custom') ? " getwid-padding-top-".esc_attr($attributes['paddingTop']) : '';
    $content_container_class .= (isset($attributes['paddingBottom']) && $attributes['paddingBottom'] !='' && $attributes['paddingBottom'] != 'custom') ? " getwid-padding-bottom-".esc_attr($attributes['paddingBottom']) : '';
    $content_container_class .= (isset($attributes['paddingLeft']) && $attributes['paddingLeft'] !='' && $attributes['paddingLeft'] != 'custom') ? " getwid-padding-left-".esc_attr($attributes['paddingLeft']) : '';
    $content_container_class .= (isset($attributes['paddingRight']) && $attributes['paddingRight'] !='' && $attributes['paddingRight'] != 'custom') ? " getwid-padding-right-".esc_attr($attributes['paddingRight']) : '';
    
    $content_container_class .= (isset($attributes['paddingTopTablet']) && $attributes['paddingTopTablet'] !='' && $attributes['paddingTopTablet'] != 'custom') ? " getwid-padding-tablet-top-".esc_attr($attributes['paddingTopTablet']) : '';
    $content_container_class .= (isset($attributes['paddingBottomTablet']) && $attributes['paddingBottomTablet'] !='' && $attributes['paddingBottomTablet'] != 'custom') ? " getwid-padding-tablet-bottom-".esc_attr($attributes['paddingBottomTablet']) : '';
    $content_container_class .= (isset($attributes['paddingLeftTablet']) && $attributes['paddingLeftTablet'] !='' && $attributes['paddingLeftTablet'] != 'custom') ? " getwid-padding-tablet-left-".esc_attr($attributes['paddingLeftTablet']) : '';
    $content_container_class .= (isset($attributes['paddingRightTablet']) && $attributes['paddingRightTablet'] !='' && $attributes['paddingRightTablet'] != 'custom') ? " getwid-padding-tablet-right-".esc_attr($attributes['paddingRightTablet']) : '';
    
    $content_container_class .= (isset($attributes['paddingTopMobile']) && $attributes['paddingTopMobile'] !='' && $attributes['paddingTopMobile'] != 'custom') ? " getwid-padding-mobile-top-".esc_attr($attributes['paddingTopMobile']) : '';
    $content_container_class .= (isset($attributes['paddingBottomMobile']) && $attributes['paddingBottomMobile'] !='' && $attributes['paddingBottomMobile'] != 'custom') ? " getwid-padding-mobile-bottom-".esc_attr($attributes['paddingBottomMobile']) : '';
    $content_container_class .= (isset($attributes['paddingLeftMobile']) && $attributes['paddingLeftMobile'] !='' && $attributes['paddingLeftMobile'] != 'custom') ? " getwid-padding-mobile-left-".esc_attr($attributes['paddingLeftMobile']) : '';
    $content_container_class .= (isset($attributes['paddingRightMobile']) && $attributes['paddingRightMobile'] !='' && $attributes['paddingRightMobile'] != 'custom') ? " getwid-padding-mobile-right-".esc_attr($attributes['paddingRightMobile']) : '';
    
    $content_container_style .= (isset($attributes['paddingTop']) && $attributes['paddingTop'] !='' && $attributes['paddingTop'] == 'custom') ? "padding-top:".esc_attr($attributes['paddingTopValue']).";" : '';
    $content_container_style .= (isset($attributes['paddingBottom']) && $attributes['paddingBottom'] !='' && $attributes['paddingBottom'] == 'custom') ? "padding-bottom:".esc_attr($attributes['paddingBottomValue']).";" : '';
    $content_container_style .= (isset($attributes['paddingLeft']) && $attributes['paddingLeft'] !='' && $attributes['paddingLeft'] == 'custom') ? "padding-left:".esc_attr($attributes['paddingLeftValue']).";" : '';
    $content_container_style .= (isset($attributes['paddingRight']) && $attributes['paddingRight'] !='' && $attributes['paddingRight'] == 'custom') ? "padding-right:".esc_attr($attributes['paddingRightValue']).";" : '';

    ob_start();
    ?>    
        <div class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo (!empty($wrapper_style) ? 'style="'.esc_attr($wrapper_style).'"' : '');?>>
            <div class="background_image_wrapper" style="background-image: url(<?php echo esc_url(get_the_post_thumbnail_url($current_post, $imageSize))?>);"></div>
            <div <?php echo (!empty($content_container_style) ? 'style="'.esc_attr($content_container_style).'"' : '');?> class="<?php echo esc_attr($content_container_class);?>">
                <?php echo $content; ?>
            </div>
        </div>
    <?php

    $result = ob_get_clean();
    return $result;    
}
register_block_type(
    'getwid/template-post-featured-background-image',
    array(
        'attributes' => array(           
            'align' => array(
                'type' => 'string',
            ),
            'imageSize' => array(
                'type' => 'string',
                'default' => 'large',
            ),
            
            //Content
            'minHeight' => array(
                'type' => 'string',
            ),

            // Padding
            'paddingTopValue' => array(
                'type' => 'string'
            ),
            'paddingBottomValue' => array(
                'type' => 'string'
            ),
            'paddingLeftValue' => array(
                'type' => 'string'
            ),
            'paddingRightValue' => array(
                'type' => 'string'
            ),

            'paddingTop' => array(
                'type' => 'string',
                'default' => ''
            ),
            'paddingBottom' => array(
                'type' => 'string',
                'default' => ''
            ),
            'paddingLeft' => array(
                'type' => 'string',
                'default' => ''
            ),
            'paddingRight' => array(
                'type' => 'string',
                'default' => ''
            ),

            'paddingTopTablet' => array(
                'type' => 'string',
                'default' => ''
            ),
            'paddingBottomTablet' => array(
                'type' => 'string',
                'default' => ''
            ),
            'paddingLeftTablet' => array(
                'type' => 'string',
                'default' => ''
            ),
            'paddingRightTablet' => array(
                'type' => 'string',
                'default' => ''
            ),


            'paddingTopMobile' => array(
                'type' => 'string',
                'default' => ''
            ),
            'paddingBottomMobile' => array(
                'type' => 'string',
                'default' => ''
            ),
            'paddingLeftMobile' => array(
                'type' => 'string',
                'default' => ''
            ),
            'paddingRightMobile' => array(
                'type' => 'string',
                'default' => ''
            ),            
        ),
        'render_callback' => 'render_getwid_template_post_featured_background_image',
    )
);