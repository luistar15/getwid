/**
* External dependencies
*/
import { __ } from 'wp.i18n';

/**
* WordPress dependencies
*/
const {
	TextareaControl
} = wp.components;

const {
	Component,
	Fragment
} = wp.element;

/**
* Create an Component
*/
class Save extends Component {
	render() {
		const {
			attributes: {
				label,
				message
			},

			className,
			baseClass

		} = this.props;

		return (
			<Fragment>
				<div className={`${className}`} data-label={label} data-message={message}>
					<div className={`${baseClass}__wrapper`}>
						<label
							className={`${baseClass}__label`}
							for={'message-textarea'}
						>
							{label ? label : __('Message', 'getwid')}
						</label>

						<TextareaControl
							name={'message'}
							id={'message-textarea'}
							className={`${baseClass}__textarea`}
							placeholder={message ? message : __('Enter message here...', 'getwid')}
						/>
					</div>
				</div>
			</Fragment>
		);
	}
}

export default (Save);