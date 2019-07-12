( function ( $ ) {
    $( document ).ready( ( event ) => {

        const $getwid_contact_forms = $( '.wp-block-getwid-contact-form__form' );

        $getwid_contact_forms.each( (index, form) => {
            
            const $result  = $( form ).find( 'p[class$=__result]'      );
            const $submit  = $( form ).find( 'button[type=\'submit\']' );

            /* #region render captcha */
            const $captcha = $( form ).find( '.wp-block-getwid-captcha' );

            let captchaId;
            if ( $captcha.length ) {
                ( () => {
                    if ( $captcha.length ) {

                        const getwid_sitekey = $captcha.data( 'sitekey' );
                        const getwid_theme   = $captcha.data( 'theme'   );

                        grecaptcha.ready( () => {
                            captchaId = grecaptcha.render( $captcha[ 0 ], {
                                'sitekey': getwid_sitekey,
                                'theme'  : getwid_theme
                            } );
                        } ) ;
                    }
                } )();
            }
            /* #endregion */

            $result.hide();

            $( form ).submit( ( event ) => {
                event.preventDefault();

                $submit.prop( 'disabled', true );
                            
                const data = {
                    'action': 'getwid_contact_form_send',
					'security': Getwid.nonces.recaptcha_v2_contact_form,
                    'data': $( form ).serialize()
                };

                if ( $result.text() != '' ) {
                    $result.hide( 300 );
                }
                
                $.post( Getwid.ajax_url, data, ( response ) => {

                    if ( $result.hasClass( 'success' ) ) {
                        $result.removeClass( 'success' );

                    } else if ( $result.hasClass( 'fail' ) ) {
                        $result.removeClass( 'fail' );
                    }

                    $submit.prop( 'disabled', false );

                    /* #region reset captcha */
                    if ( $captcha.length && response.success ) {
                        grecaptcha.reset( captchaId );
                    }
                    /* #endregion */
                    
                    if ( response.success ) {
                        $( form )[ 0 ].reset();
                        $result.addClass( 'success' );
                    } else {
                        $result.addClass( 'fail' );
                    }

                    $result.html( response.data );
                    $result.show( 300 );
                } );
            } );
        } );
    } );
} )( jQuery );