/**
* WordPress dependencies
*/
import { __ } from 'wp.i18n';

/**
* External dependencies
*/
import attributes from './attributes';
import edit from './edit';
import save from './save';

/**
* WordPress dependencies
*/
const { select } = wp.data;
const { registerBlockType, createBlock } = wp.blocks;

let mediaContent, mediaAttributes;

/**
* Register the block
*/
registerBlockType( 'getwid/media-text-slider', {
	title: __('Media & Text Slider', 'getwid'),
	icon: <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 24 24"><path d="M0,0v1v0.2V2v1.9v10.3V16v0.9V18h3h18h3v-2v-1.9V3.9V2V1.2V1V0H0z M22,7l-8,7l-4.9-2.1L4,15c0,0-1.8,0-2,0V4.8V2h20V7z"/><rect x="4" y="4" width="11" height="2"/><rect x="4" y="7" width="7" height="2"/><circle cx="6" cy="22" r="2"/><circle cx="12" cy="22" r="2"/><circle cx="18" cy="22" r="2"/></svg>,
	category: 'getwid-blocks',
	keywords: [
		__( 'gallery' , 'getwid' ),
		__( 'carousel', 'getwid' ),
		__( 'photo'   , 'getwid' )
	],
	supports: {
		alignWide: true,
		align: [ 'wide', 'full' ],
	},
	attributes,
	transforms: {
		from: [
			{
				type: 'block',
				blocks: [ 'core/gallery' ],
				transform: ( content ) => {

					// console.log( content );
					console.log( mediaContent );

					debugger;
					return createBlock( 'getwid/media-text-slider',mediaAttributes ,
					/* #region   */
					// [						
					// 	{
					// 		name: "getwid/media-text-slider-slide",
					// 		innerBlocks: [
					// 			{
					// 				name: "getwid/media-text-slider-slide-content",
					// 				attributes: {
					// 					mediaId: 5527,
					// 					mediaType: "image",
					// 					mediaUrl: "http://wordpress/wp-content/uploads/2019/06/img1-1.jpg"
					// 				},

					// 				innerBlocks: [
					// 					{
					// 						name: "core/heading",
					// 						attributes: {
					// 							content: "swamp thing"
					// 						},
					// 						isValid: true,
					// 						clientId: "54eaad88-c90b-4e4a-873e-8bea4c40f625"
					// 					}
					// 				],
					// 				isValid: true,
					// 				clientId: "54eaad88-c90b-4e4a-873e-8bea4c40f622"
					// 			}
					// 		],
					// 		isValid: true,
					// 		clientId: "54eaad88-c90b-4e4a-873e-8bea4c40f621"
					// 	}
					// ]
					/* #endregion */
					mediaContent );
				}
			}
		],
		to: [
			{
				type: 'block',
				blocks: [ 'core/gallery' ],
				transform: ( attributes ) => {

					const ids = [];
					const images = [];

					const clientId 	  = select( 'core/editor' ).getSelectedBlockClientId();
					const innerBlocks = select( 'core/editor' ).getBlock( clientId ).innerBlocks;

					console.log( innerBlocks );

					/* #region shit code */
					mediaContent 	= innerBlocks;
					mediaAttributes = attributes;
					/* #endregion */

					if ( innerBlocks.length ) {
						$.each(innerBlocks, (index, slide) => {

							if ( typeof slide.innerBlocks[ 0 ].attributes[ 'mediaUrl' ] != 'undefined' ) {
				
								images.push( {
									caption: '',
									id:  slide.innerBlocks[ 0 ].attributes.mediaId,
									url: slide.innerBlocks[ 0 ].attributes.mediaUrl
								});

								ids.push( slide.innerBlocks[ 0 ].attributes.mediaId );

								const heading = slide.innerBlocks[ 0 ].innerBlocks[ 0 ];

								if ( heading.attributes.content != '' ) {
									images[ images.length - 1 ] .caption = heading.attributes.content;
								}
							}
						});
					}

					return createBlock( 'core/gallery', {
						images: images.length ? images.map( ( { id, url, caption } ) => ({
							id,
							url,
							caption
						}) ) : [],
						ids: ids
					} );
				}
			}
		]
	},
	edit,
	save,
} );
