import React, { Component } from 'react';
import { render } from 'react-dom';
import { BrowserRouter as Router, useLocation } from 'react-router-dom';
import VendorManage from './class-mvx-vendor-manage';
import WorkBoard from './class-mvx-workboard-section';
import PaymentSettings from './class-mvx-payemnt-section';
import CommissionSettings from './class-mvx-commission-section';
import AnalyticsSettings from './class-mvx-analytics-section';
import GESettings from './class-mvx-general-settings';
import Modules from './class-mvx-modules-listing';
import StatusTools from './class-mvx-status-tools';
import Dashboard from './class-mvx-dashboard-section';
import Membership from './class-mvx-membership';
import Advertisement from './class-mvx-advertisement';

class Mvx_Backend_Endpoints_Load extends Component {
	constructor(props) {
		super(props);
		this.state = {};
		this.QueryParamsDemo = this.QueryParamsDemo.bind(this);
	}

	QueryParamsDemo() {
		// For active submneu pages
		const $ = jQuery;
		const menuRoot = $('#toplevel_page_' + 'mvx');
		const currentUrl = window.location.href;
		const currentPath = currentUrl.substr(currentUrl.indexOf('admin.php'));

		menuRoot.on('click', 'a', function () {
			const self = $(this);

			$('ul.wp-submenu li', menuRoot).removeClass('current');

			if (self.hasClass('wp-has-submenu')) {
				$('li.wp-first-item', menuRoot).addClass('current');
			} else {
				self.parents('li').addClass('current');
			}
		});

		$('ul.wp-submenu a', menuRoot).each(function (index, el) {
			if ($(el).attr('href') === currentPath) {
				$(el).parent().addClass('current');
			} else {
				$(el).parent().removeClass('current');

				// if user enter page=mvx
				if (
					$(el).parent().hasClass('wp-first-item') &&
					currentPath === 'admin.php?page=mvx'
				) {
					$(el).parent().addClass('current');
				}
			}
		});
		// call every endpoint by checking from url #
		if (
			new URLSearchParams(useLocation().hash).get('submenu') &&
			new URLSearchParams(useLocation().hash).get('submenu') === 'vendor'
		) {
			return <VendorManage />;
		} else if (
			new URLSearchParams(useLocation().hash).get('submenu') &&
			new URLSearchParams(useLocation().hash).get('submenu') ==
				'commission'
		) {
			return <CommissionSettings />;
		} else if (
			new URLSearchParams(useLocation().hash).get('submenu') &&
			new URLSearchParams(useLocation().hash).get('submenu') ===
				'settings'
		) {
			return <GESettings />;
		} else if (
			new URLSearchParams(useLocation().hash).get('submenu') &&
			new URLSearchParams(useLocation().hash).get('submenu') === 'payment'
		) {
			return <PaymentSettings />;
		} else if (
			new URLSearchParams(useLocation().hash).get('submenu') &&
			new URLSearchParams(useLocation().hash).get('submenu') ==
				'analytics'
		) {
			return <AnalyticsSettings />;
		} else if (
			new URLSearchParams(useLocation().hash).get('submenu') &&
			new URLSearchParams(useLocation().hash).get('submenu') ==
				'work-board'
		) {
			return <WorkBoard />;
		} else if (
			new URLSearchParams(useLocation().hash).get('submenu') &&
			new URLSearchParams(useLocation().hash).get('submenu') ==
				'status-tools'
		) {
			return <StatusTools />;
		} else if (
			new URLSearchParams(useLocation().hash).get('submenu') &&
			new URLSearchParams(useLocation().hash).get('submenu') ==
				'membership'
		) {
			return <Membership />;
		} else if (
			new URLSearchParams(useLocation().hash).get('submenu') &&
			new URLSearchParams(useLocation().hash).get('submenu') === 'modules'
		) {
			return <Modules />;
		} else if (
			new URLSearchParams(useLocation().hash).get('submenu') &&
			new URLSearchParams(useLocation().hash).get('submenu') === 'advertisement'
		) {
			return <Advertisement />;
		}
		return <Dashboard />;
	}

	render() {
		return (
			<Router>
				<this.QueryParamsDemo />
			</Router>
		);
	}
}
export default Mvx_Backend_Endpoints_Load;
