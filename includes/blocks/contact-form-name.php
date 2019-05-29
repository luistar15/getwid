<?php

function render_getwid_contact_form_name( $attributes ) {  

    $block_name = 'wp-block-getwid-contact-form-name';

    $extra_attr = array(
        'block_name' => $block_name
    );

    ob_start();
?>
    <?php getwid_get_template_part('contact-form/name', $attributes, false, $extra_attr); ?>
    
<?php

    $result = ob_get_clean();
    return $result;
}

register_block_type(
    'getwid/contact-form-name',
    array(
        'attributes' => array(
            'isRequired' => array(
                'type' => 'string',
                'default' => 'true'
            ),
            'label' => array(
                'type' => 'string'
            ),
            'name' => array(
                'type' => 'string'
            ) 
        ),
        'render_callback' => 'render_getwid_contact_form_name'
    )
);