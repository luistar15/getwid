import Inspector from './inspector';
import classnames from 'classnames';
import './editor.scss'

const {__} = wp.i18n;
const {Component, Fragment} = wp.element;

const {
	ResizableBox,
} = wp.components;

class Edit extends Component {

	constructor() {
		super(...arguments);
	}

	render() {
		
		const {
			attributes: {
				height,
				isHideDesktop,
				isHideTablet,			
				isHideMobile,
			},
			isSelected,
			className,
			setAttributes,
			toggleSelection,
		} = this.props;

console.error(this.props);

		return (
			<Fragment>
				<Inspector {...this.props} />
				<ResizableBox
					className={ classnames(
						className, { 
							'is-selected'    : isSelected,
							'is-hide-desktop': isHideDesktop,
							'is-hide-tablet' : isHideTablet,
							'is-hide-mobile' : isHideMobile,
						}
					) }
					size={ {
						height,
					} }
					minHeight="100"
					enable={ {
						top: false,
						right: false,
						bottom: true,
						left: false,
						topRight: false,
						bottomRight: false,
						bottomLeft: false,
						topLeft: false,
					} }
					onResizeStop={ ( event, direction, elt, delta ) => {
						const val = parseInt( height , 10 ) + delta.height;
						var units = (/\d+(\w+)/g).exec(height)[1];
						setAttributes( {
							height: val + units,
						} );
						toggleSelection( true );
					} }
					onResizeStart={ () => {
						toggleSelection( false );
					} }
				/>
			</Fragment>
		);
	}
}

export default (Edit);