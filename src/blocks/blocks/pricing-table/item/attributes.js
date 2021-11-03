import { __ } from '@wordpress/i18n';

const attributes = {
	title: {
		type: 'string'
	},
	description: {
		type: 'string'
	},
	buttonText: {
		type: 'string',
		default: __( 'Purchase', 'otter-blocks' )
	},
	buttonLink: {
		type: 'string'
	},
	variations: {
		type: 'array',
		default: []
	},
	isFeatured: {
		type: 'boolean',
		default: false
	},
	hasTableLink: {
		type: 'boolean',
		default: false
	},
	selector: {
		type: 'string'
	},
	linkText: {
		type: 'string',
		default: __( 'See all features', 'otter-blocks' )
	},
	buttonColor: {
		type: 'string'
	},
	backgroundColor: {
		type: 'string'
	},
	titleColorColor: {
		type: 'string'
	},
	descriptionColor: {
		type: 'string'
	},
	priceColor: {
		type: 'string'
	}
};

export default attributes;
