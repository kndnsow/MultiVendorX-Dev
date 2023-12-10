/* global appLocalizer */
import React, { Component } from 'react';
class MVX_Banner_Adv extends Component {
	render() {
		return (
			<>
			{appLocalizer.dashboard_string.dashboard95 == 'free' ? 
			<div className="mvx-sidebar">
				
				<div className='mvx-banner-right'>
					<div className='mvx-logo-right'>	<img
							src={appLocalizer.multivendor_right_white_logo}
						/> </div>
					<p className='mvx-banner-description'>

					With MultiendorX Pro You Get More Control Over Every Aspect of Your Marketplace
					
					
					<span>1.All Product Type Support </span>			
					<span>2.Instant Payment to Vendors </span>					
					<span>3.Create Vendor Membership Plans</span>
					<span>4.Quick Real-Time Chat with Vendors</span>
					<span>5.Hassle-free Billing Invoice and Packing Slip</span>
					<span>6.Verify Vendors to Secure Marketplace</span>
					<span>7.Seamless Product Import and Export</span>
					<span>8.Advanced Sales and Tax Analytics</span>
					<span>9.Inventory Tracker</span>
					<span>10.Marketplace Vacation-Mode</span>

					Upgrade yourself to <a href="https://multivendorx.com/pricing/">MultiVendorX Pro</a> Today!!


					</p>
				</div>
			</div>
			: ''}
			</>
		);
	}
}
export default MVX_Banner_Adv;
