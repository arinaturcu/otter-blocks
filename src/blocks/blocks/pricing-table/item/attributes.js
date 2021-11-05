import { __ } from '@wordpress/i18n';

const attributes = {
	id: {
		type: 'string'
	},
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
	price: {
		type: 'number',
		default: 4.99
	},
	isSale: {
		type: 'boolean'
	},
	oldPrice: {
		type: 'number',
		default: 9.99
	},
	currency: {
		type: 'string',
		default: '$'
	},
	period: {
		type: 'string',
		default: __( 'month', 'otter-blocks' )
	},
	ribbonText: {
		type: 'string',
		default: __( 'Best Value!', 'otter-blocks' )
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
	},
	oldPriceColor: {
		type: 'string'
	},
	ribbonColor: {
		type: 'string'
	},
	borderWidth: {
		type: 'string'
	},
	borderStyle: {
		type: 'string'
	},
	borderColor: {
		type: 'string'
	}
};

export default attributes;
