// load base components
import './components/icons/index.js';
// load blocks
import './blocks/TopRatedVendors/block.js';
import './blocks/VendorTopProducts/block.js';
import './blocks/VendorsContact/block.js';
import './blocks/VendorCoupons/block.js';
import './blocks/VendorLocation/block.js';
import './blocks/VendorOnSellProducts/block.js';
import './blocks/VendorPolicies/block.js';

import './blocks/VendorsReview/block.js';
import './blocks/VendorsInfo/block.js';
import './blocks/VendorRecentProducts/block.js';
import './blocks/VendorProductsSearch/block.js';
import './blocks/VendorProductCategories/block.js';
import './blocks/VendorLists/block.js';

const { updateCategory } = wp.blocks;
const { SVG, G, Path, Polygon, Rect, Circle } = wp.components;

( function () {
	updateCategory( 'mvx', {
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
				<g fill="#181718" fill-rule="nonzero">
					<path
						d="M10.8,0H9.5C8,0,6.7,1.3,6.7,2.8V4C6.9,4,7,4,7.2,4.1V2.8c0-1.3,1-2.3,2.3-2.3h1.3
        c1.3,0,2.3,1,2.3,2.3v1.7c0.2,0,0.3,0,0.5,0V2.8C13.6,1.3,12.3,0,10.8,0z"
					/>
					<path
						d="M16.8,4.4C7.6,6.8,3.7,1.9,1.8,4.9c-1.1,1.7,0.3,7.6,1.2,13c2,1.3,4.4,2.1,7,2.1
        c2.7,0,5.2-0.8,7.3-2.3C18.6,12.3,19.5,3.7,16.8,4.4z M6.7,10.3V9.9h0.7v0.4v0.2H6.7V10.3z M5.6,8.9h0.3v0.3H5.6V8.9z M3.9,9.2h0.6
        v0.6H3.9V9.2z M5,10.8H4.3v-0.6H5V10.8z M5.1,9.9H4.7V9.5h0.3V9.9z M5.3,9.1H4.9V8.7h0.5V9.1z M5.4,9.4h0.7v0.7H5.4V9.4z
         M14.3,16.3h-0.6v-0.6h0.6V16.3z M13.9,15.4v-0.3h0.3v0.3H13.9z M11.7,14.2l1.4,1.6h-0.6v1h1v-0.6l0.8,0.9h-2.4l-1.6-1.7l-1.6,1.7
        H6.3l2.5-3l-2.2-2.6V11H6.1l-0.5-0.6h0.9v0.4h1.2v-0.4h0.3l2.3,2.6L13,9.8c0.4-0.5,1-0.8,1.7-0.8h1.4L11.7,14.2z M13.1,15.5V15h0.5
        v0.5H13.1z M13.2,16.1v0.5h-0.5v-0.5H13.2z M5.9,11.8v-0.5h0.2h0.3v0.3v0.2H5.9z"
					/>
				</g>
			</svg>
		),
	} );
} )();
