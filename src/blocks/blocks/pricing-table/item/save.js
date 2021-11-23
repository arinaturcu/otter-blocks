import { RichText, InnerBlocks } from '@wordpress/block-editor';
import classnames from 'classnames';

const Save = ({ attributes, className }) => {

	return (
		<div id={attributes.id} className={ classnames( className, { featured: attributes.isFeatured })}>
			<div className="o-pricing-table-wrap">
				<div className="o-pricing-header">
					{  attributes.isFeatured && (
						<span className="featured-badge">{ attributes.ribbonText }</span>
					) }
					{ ! RichText.isEmpty(  attributes.title ) && (
						<RichText.Content
							identifier="title"
							tagName="h3"
							className="o-pricing-title"
							value={  attributes.title }
						/>
					) }
					{ ! RichText.isEmpty(  attributes.description ) && (
						<RichText.Content
							identifier="description"
							tagName="p"
							className="o-pricing-description"
							value={  attributes.description }
						/>
					) }
					<div className="o-pricing-price">
						<h5>
							{
								attributes.isSale && (
									<del
										className="full-price"
									>
										<sup>{ attributes.currency }</sup>
										<span>{ attributes.oldPrice }</span>
									</del>
								)
							}
							<span
								className="price"
							>
								<sup>{ attributes.currency }</sup>
								<span>{ attributes.price }</span>
							</span>
							<sub className="period">/{ attributes.period }</sub>
						</h5>
					</div>
					<div
						className="o-pricing-action-wrap"
					>
						<a
							className="o-pricing-action"
							href={ attributes.buttonLink }
						>
							{ attributes.buttonText }
						</a>
					</div>
				</div>
				<InnerBlocks.Content />
			</div>
		</div>
	);
};

export default Save;
