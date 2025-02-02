/**
 * External dependencies
 */
import { closeSmall } from '@wordpress/icons';

import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { InnerBlocks } from '@wordpress/block-editor';

const Save = ({
	attributes,
	className
}) => {
	return (
		<div
			className={ classnames(
				className,
				'is-front'
			) }
			id={ attributes.id }
			data-open={ attributes.trigger }
			data-dismiss={ attributes.recurringClose ? attributes.recurringTime : '' }
			data-time={ ( undefined === attributes.trigger || 'onLoad' === attributes.trigger ) ? ( attributes.wait || 0 ) : '' }
			data-anchor={ 'onClick' === attributes.trigger ? attributes.anchor : '' }
			data-offset={ 'onScroll' === attributes.trigger ? attributes.scroll : '' }
			data-outside={ attributes.outsideClose ? attributes.outsideClose : '' }
			data-anchorclose={ attributes.anchorClose ? attributes.closeAnchor : '' }
		>
			<div className="otter-popup__modal_wrap">
				<div
					role="presentation"
					className="otter-popup__modal_wrap_overlay"
				/>

				<div className="otter-popup__modal_content">
					{ attributes.showClose && (
						<div className="otter-popup__modal_header">
							<button type="button" class="components-button has-icon"><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true"><path d="M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"></path></svg></button>
						</div>
					) }

					<div className="otter-popup__modal_body">
						<InnerBlocks.Content />
					</div>
				</div>
			</div>
		</div>
	);
};

export default Save;
