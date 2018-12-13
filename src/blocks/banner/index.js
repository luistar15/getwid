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
		title: __('Getwid Banner', 'getwid'),
		description: __('Getwid Banner', 'getwid'),
		category: 'common',
		icon: {
			foreground: '#bf3737',
			src: 'format-image',
		},	

		keywords: [
			__('Getwid', 'getwid'),
			__('Banner', 'getwid'),
		],
		attributes,

		getEditWrapperProps( attributes ) {
			const { align } = attributes;
			if ( -1 !== validAlignments.indexOf( align ) ) {
				return { 'data-align': align };
			}
		},

		edit: Edit,

		save( { attributes } ) {
			const {
				backgroundId,
				backgroundUrl,
				backgroundType,
				title,
				text,
				link,
				align,
				minHeight,
				verticalAlign,
				horizontalAlign,
				textColor,
				overlayColor,
				backgroundOpacity,
				blockAnimation,
				textAnimation,
			} = attributes;

			const className = 'wp-block-getwid-banner';

			const wrapperStyle = {
				color: textColor,
			};

			const imageStyle = {
				backgroundColor: overlayColor,
			};

			const captionStyle = {
				minHeight: minHeight,
			};

			const wrapperClasses = classnames(
				className,
				`${className}--${blockAnimation}`,
				{
					[ `${className}--${textAnimation}` ]: textAnimation != 'none',
					[ `${className}--foreground-${backgroundOpacity}` ]: backgroundOpacity != 35,
					[ `${className}--vertical-${verticalAlign}` ]: verticalAlign != 'center',
					[ `${className}--horizontal-${horizontalAlign}` ]: horizontalAlign != 'center',
				},
				align ? `align${ align }` : null,
			);

			return (
				<div className={ wrapperClasses } style={ wrapperStyle }>
					<a href={typeof link != 'undefined' ? link : '#'} class={`${className}__link`}>

						{ VIDEO_BACKGROUND_TYPE === backgroundType && backgroundUrl && ( <video
							className= {`${className}__video`}
							autoPlay
							muted
							loop
							src={ backgroundUrl }
						/> ) }

						{ !! backgroundUrl && (
							<figure
								className= {`${className}__wrapper`}
								style= {imageStyle}
							>
							<img src={ backgroundUrl } alt="" className= {`${className}__image` }/>							
								<Fragment>
									<figcaption
										className= {`${className}__caption`}
										style= {captionStyle}
									>
										<div className= {`${className}__caption-wrapper`}>
											{ ! RichText.isEmpty( title ) && (
												<RichText.Content tagName="span" className= {`${className}__title`} value={ title } />
											) }

											{ ! RichText.isEmpty( text ) && (
												<RichText.Content tagName="p" className= {`${className}__text`} value={ text } />
											) }
										</div>
									</figcaption>
								</Fragment>
							</figure>
						) }	
					</a>				
				</div>
			);
		},

	},
);