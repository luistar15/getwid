/**
 * Block dependencies
 */
import Edit from './edit';
import attributes from './attributes';

import './style.scss'
import classnames from "classnames";

const { __ } = wp.i18n;
const {
	registerBlockType,
} = wp.blocks;
const {
	BlockControls,
	InspectorControls,
	BlockAlignmentToolbar,
	MediaPlaceholder,
	MediaUpload,
	MediaUploadCheck,
	AlignmentToolbar,
	PanelColorSettings,
	RichText,
	withColors,
	getColorClassName,
	getColorObjectByAttributeValues
} = wp.editor;

const {
	IconButton,
	PanelBody,
	RangeControl,
	ToggleControl,
	Toolbar,
	withNotices,
	SVG,
	Path,
} = wp.components;

const { Fragment } = wp.element;

const validAlignments = [ 'left', 'center', 'right', 'wide', 'full' ];

const ALLOWED_MEDIA_TYPES = [ 'image', 'video' ];
const IMAGE_BACKGROUND_TYPE = 'image';
const VIDEO_BACKGROUND_TYPE = 'video';

/**
 * Register static block example block
 */
export default registerBlockType(
	'getwid/banner',
	{
		title: __('Banner', 'getwid'),
		description: __('Link an image or video with a text overlay.', 'getwid'),
		category: 'getwid-blocks',
		icon: <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 24 24"><rect x="14" y="5" width="6" height="2"/><rect x="14" y="9" width="6" height="2"/><polygon points="8,4 9.1,5.2 10.8,5.2 10.8,6.9 12,8 10.8,9.1 10.8,10.8 9.1,10.8 8,12 6.9,10.8 5.2,10.8 5.2,9.1 4,8 5.2,6.9 5.2,5.2 6.9,5.2 "/><polygon points="17.6,15 10.3,12.6 11.9,20.1 13.8,18.4 17,21.9 18.9,20.2 15.7,16.7"/><g><polygon points="0,0 0,16 9,16 8.6,14 2,14 2,2 22,2 22,14 19.3,14 19.7,16 24,16 24,0"/></g></svg>,

		keywords: [
			__('image', 'getwid'),
			__('cover', 'getwid')
		],
		attributes,

		getEditWrapperProps( attributes ) {
			const { align } = attributes;
			if ( -1 !== validAlignments.indexOf( align ) ) {
				return { 'data-align': align };
			}
		},

		edit: Edit,

		save: props => {
			const {
				attributes: {
					videoAutoplay,
					id,
					url,
					type,
					title,
					text,
					link,
					align,
					minHeight,
					contentMaxWidth,
					verticalAlign,
					horizontalAlign,

                    rel,
                    linkTarget,

					backgroundColor,
					textColor,
					customBackgroundColor,
					customTextColor,

					backgroundOpacity,
					blockAnimation,
					textAnimation,					
				}
			} = props;

			const className = 'wp-block-getwid-banner';

			const textClass = getColorClassName( 'color', textColor );
			const backgroundClass = getColorClassName( 'background-color', backgroundColor );

			const imageProps = {
				className: classnames(
					`${className}__wrapper`,
					{				
						'has-background': (backgroundColor || customBackgroundColor),
						[ backgroundClass ]: (backgroundClass),		
					}
				),
				style: {
					backgroundColor: (props.attributes.backgroundColor ? undefined : props.attributes.customBackgroundColor),
				},
			};

			const captionProps = {
				className: classnames(
					`${className}__caption`,
					{
						'has-text-color': textColor || customTextColor,
						[ textClass ]: textClass,
					},
				),
				style: {
					color: (typeof textColor != 'undefined' ? undefined : customTextColor),
					minHeight: minHeight,
				},
			};

			const wrapperProps = {
				className: classnames(
					className,
					`${className}--${blockAnimation}`,
					{
						[ `${className}--${textAnimation}` ]: textAnimation != 'none',
						[ `${className}--foreground-${backgroundOpacity}` ]: backgroundOpacity != 35,
						[ `${className}--vertical-${verticalAlign}` ]: verticalAlign != 'center',
						[ `${className}--horizontal-${horizontalAlign}` ]: horizontalAlign != 'center',
					},
					align ? `align${ align }` : null,
				),
			};

			return (
				<div {...wrapperProps}>
					<a href={typeof link != 'undefined' ? link : ''} target={ linkTarget } rel={ rel } class={`${className}__link`}>

						{ !! url && (
							<div {...imageProps}>
								{ (VIDEO_BACKGROUND_TYPE === type && !!url ) ? (
									<video
										className= {`${className}__video ${className}__source`}
										autoPlay={videoAutoplay}
										muted
										loop
										src={ url }
									/>
								) : (<img src={ url } alt="" className={ `${className}__image ${className}__source ` + (id ? `wp-image-${ id }` : null) }/>) }

								<Fragment>
									<div {...captionProps}>
										<div style={{maxWidth: contentMaxWidth}} className= {`${className}__caption-wrapper`}>
											{ ! RichText.isEmpty( title ) && (
												<RichText.Content tagName="span" className= {`${className}__title`} value={ title } />
											) }

											{ ! RichText.isEmpty( text ) && (
												<RichText.Content tagName="p" className= {`${className}__text`} value={ text } />
											) }
										</div>
									</div>
								</Fragment>
							</div>
						) }	
					</a>				
				</div>
			);
		},

	},
);