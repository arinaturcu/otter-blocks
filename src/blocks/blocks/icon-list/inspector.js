/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

import { pick } from 'lodash';

import {
	__experimentalColorGradientControl as ColorGradientControl,
	InspectorControls,
	MediaPlaceholder
} from '@wordpress/block-editor';

import {
	PanelBody,
	RangeControl,
	Placeholder,
	ToggleControl,
	Spinner
} from '@wordpress/components';

import {
	lazy,
	Suspense
} from '@wordpress/element';

/**
 * Internal dependencies
 */
const IconPickerControl = lazy( () => import( '../../components/icon-picker-control/index.js' ) );

const Inspector = ({
	attributes,
	setAttributes
}) => {
	const changeLibrary = value => {
		setAttributes({
			defaultLibrary: value,
			defaultIcon: undefined,
			defaultPrefix: 'fas'
		});
	};

	const changeIcon = value => {
		if ( 'object' === typeof value ) {
			setAttributes({
				defaultIcon: value.name,
				defaultPrefix: value.prefix
			});
		} else {
			setAttributes({ defaultIcon: value });
		}
	};

	const onDefaultContentColorChange = value => {
		setAttributes({ defaultContentColor: value });
	};

	const onDefaultIconColorChange = value => {
		setAttributes({ defaultIconColor: value });
	};

	const onDefaultSizeChange = value => {
		setAttributes({ defaultSize: value });
	};

	const onGapChange = value => {
		setAttributes({ gap: value });
	};

	return (
		<InspectorControls>
			<PanelBody
				title={ __( 'Settings', 'otter-blocks' ) }
			>

				{
					( attributes.defaultIsImage ) ? (
						<MediaPlaceholder
							labels={ {
								title: __( 'Media Image', 'otter-blocks' )
							} }
							accept="image/*"
							allowedTypes={ [ 'image' ] }
							value={ attributes.defaultImage }
							onSelect={ value => setAttributes({ defaultImage: pick( value, [ 'id', 'alt', 'url' ]) }) }
						/>
					) : (
						<Suspense fallback={ <Placeholder><Spinner /></Placeholder> }>
							<IconPickerControl
								label={ __( 'Icon Picker', 'otter-blocks' ) }
								library={ attributes.defaultLibrary }
								prefix={ attributes.defaultPrefix }
								icon={ attributes.defaultIcon }
								changeLibrary={ changeLibrary }
								onChange={ changeIcon }
							/>
						</Suspense>
					)
				}

				<ToggleControl
					label={ __( 'Use images instead of icons.', 'otter-blocks' ) }
					checked={ attributes.defaultIsImage }
					onChange={ defaultIsImage => setAttributes({ defaultIsImage })}
				/>

				<RangeControl
					label={ __( 'Font Size', 'otter-blocks' ) }
					help={ __( 'The size of the font size of the content and icon.', 'otter-blocks' ) }
					value={ attributes.defaultSize }
					onChange={ onDefaultSizeChange }
					min={ 0 }
					max={ 60 }
					allowReset={true}
				/>

				<RangeControl
					label={ __( 'Gap', 'otter-blocks' ) }
					help={ __( 'The distance between the items.', 'otter-blocks' ) }
					value={ attributes.gap }
					onChange={ onGapChange }
					min={ 0 }
					max={ 60 }
					allowReset={ true }
				/>

				<ColorGradientControl
					label={ __( 'Content Color', 'otter-blocks' ) }
					colorValue={ attributes.defaultContentColor }
					onColorChange={ onDefaultContentColorChange }
				/>

				<ColorGradientControl
					label={ __( 'Icon Color', 'otter-blocks' ) }
					colorValue={ attributes.defaultIconColor }
					onColorChange={ onDefaultIconColorChange }
				/>
			</PanelBody>
		</InspectorControls>
	);
};

export default Inspector;
