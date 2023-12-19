/* global appLocalizer, location */
import React from 'react';
import axios from 'axios';
import Select from 'react-select';
import DataTable from 'react-data-table-component';
import PageLoader from './class-mvx-page-loader.js';
import { css } from '@emotion/react';
import { CSVLink } from 'react-csv';
import { BrowserRouter as Router, Link, useLocation } from 'react-router-dom';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
//import Button from '@mui/material/Button';
import DynamicForm from '../../../DynamicForm';
import HeaderSection from './class-mvx-page-header';
import BannerSection from './class-mvx-page-banner';
import TabSection from './class-mvx-page-tab';

const override = css`
	display: block;
	margin: 0 auto;
	border-color: green;
`;
class MVXBackendVendor extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			open_child_model: [],
			open_vendor_model_dynamic: [],
			open_vendor_delete_model_dynamic: [],
			handle_rejected_vendor_description: '',
			details_vendor: [],
			add_shipping_options_data: [],
			vendor_shipping_option_choice: '',
			bulkselectlist: [],
			data_setting_fileds: [],
			set_tab_name: '',
			set_tab_name_id: '',
			columns_vendor_list: [],
			columns_vendor_followers_list: [],
			columns_vendor_zone_list: [],
			list_vendor_application_data: '',
			list_vendor_application_raw_data: [],
			data_zone_shipping: [],
			datavendor: [],
			data_pending_vendor: [],
			data_all_vendor: [],
			data_approve_vendor: [],
			data_rejected_vendor: [],
			data_suspend_vendor: [],
			datafollowers: [],
			data_zone_in_shipping: [],
			list_vendor_roles_data: [],
			vendors_tab: [],
			current_url: '',
			open_model: false,
			datafollowers_loader: false,
			vendor_loading: false,
			vendor_list_status_approve: false,
			vendor_list_status_pending: false,
			vendor_list_status_rejected: false,
			vendor_list_status_suspended: false,
			vendor_list_status_all: false,
			selected_option: false,
			selected_ids: []
		};

		this.handleChange = this.handleChange.bind(this);
		this.QueryParamsDemo = this.QueryParamsDemo.bind(this);
		this.useQuery = this.useQuery.bind(this);
		this.Child = this.Child.bind(this);
		this.Childparent = this.Childparent.bind(this);
		this.handlevendorsearch = this.handlevendorsearch.bind(this);
		this.handleClose = this.handleClose.bind(this);
		this.handlechildClose = this.handlechildClose.bind(this);
		this.handleaddshipping_method =
			this.handleaddshipping_method.bind(this);
		this.onChangeshipping = this.onChangeshipping.bind(this);
		this.handle_different_shipping_add =
			this.handle_different_shipping_add.bind(this);
		this.handle_different_shipping_delete =
			this.handle_different_shipping_delete.bind(this);
		this.handleOnChange = this.handleOnChange.bind(this);
		this.toggle_shipping_method = this.toggle_shipping_method.bind(this);
		this.update_post_code = this.update_post_code.bind(this);
		this.onChangeshippingoption = this.onChangeshippingoption.bind(this);
		this.handledeletevendor = this.handledeletevendor.bind(this);
		this.handleVendorDismiss = this.handleVendorDismiss.bind(this);
		this.handlevendoractionsearch =
			this.handlevendoractionsearch.bind(this);
		this.different_vendor_status = this.different_vendor_status.bind(this);
		this.handleEyeIcon = this.handleEyeIcon.bind(this);
		this.handleClose_dynamic = this.handleClose_dynamic.bind(this);
		this.handle_rejected_vendor_description =
			this.handle_rejected_vendor_description.bind(this);
		this.handle_Vendor_Approve = this.handle_Vendor_Approve.bind(this);
		this.handle_Vendor_Reject = this.handle_Vendor_Reject.bind(this);
		this.handle_Vendor_Activate = this.handle_Vendor_Activate.bind(this);
		this.handle_Vendor_Edit = this.handle_Vendor_Edit.bind(this);
		this.handle_Vendor_Suspend = this.handle_Vendor_Suspend.bind(this);
		this.handleDeleteIcon = this.handleDeleteIcon.bind(this);
		this.handleClose_delete_modal = this.handleClose_delete_modal.bind(this);
		this.handleChangeRadioButton = this.handleChangeRadioButton.bind(this);
	}

	handle_rejected_vendor_description(e, vendorid) {
		this.setState({
			handle_rejected_vendor_description: e.target.value,
		});
	}

	handle_Vendor_Approve(e, reload = '') {
		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/approve_vendor`,
			data: {
				vendor_id: e,
				section: 'vendor_list',
			},
		}).then((response) => {
			if (reload) {
				location.reload();
			} else {
				this.handleClose_dynamic();
				this.setState({
					datavendor: response.data,
				});
			}
		});
	}

	handle_Vendor_Activate(e) {
		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/active_suspend_vendor`,
			data: {
				vendor_id: e,
				status: 'activate',
				section: 'vendor_list',
			},
		}).then((response) => {
			this.handleClose_dynamic();
			this.setState({
				datavendor: response.data,
			});
		});
	}
	
	handle_Vendor_Suspend(e) {
		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/active_suspend_vendor`,
			data: {
				vendor_id: e,
				status: 'suspend',
				section: 'vendor_list',
			},
		}).then((response) => {
			this.handleClose_dynamic();
			this.setState({
				datavendor: response.data,
			});
		});
	}

	handle_Vendor_Reject(e, reload = '') {
		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/reject_vendor`,
			data: {
				vendor_id: e,
				custom_note: this.state.handle_rejected_vendor_description,
			},
		}).then((response) => {
			if (reload) {
				location.reload();
			} else {
				this.handleClose_dynamic();
				this.setState({
					datavendor: response.data,
				});
			}
		});
	}

	handle_Vendor_Edit(e) {
		window.location.href = e.admin_link;
	}

	handleClose_dynamic() {
		const default_vendor_eye_popup = [];
		this.state.open_vendor_model_dynamic.map((data_ann, index_ann) => {
			default_vendor_eye_popup[data_ann.ID] = false;
		});
		this.setState({
			open_vendor_model_dynamic: default_vendor_eye_popup,
		});
	}

	handleEyeIcon(e) {
		let set_vendors_id_data = [];
		set_vendors_id_data = this.state.open_vendor_model_dynamic;
		set_vendors_id_data[e] = true;
		this.setState({
			open_vendor_model_dynamic: set_vendors_id_data,
		});
	}
	
	handleDeleteIcon(e) {
		let set_vendors_id_data = [];
		set_vendors_id_data = this.state.open_vendor_delete_model_dynamic;
		if (Array.isArray(e)) {
			e.forEach(item => {
			  if (item.ID) {
				set_vendors_id_data[item.ID] = true;
			  }
			});
		} else {
			set_vendors_id_data[e] = true;
		}
		this.setState({
			selected_ids: e,
			open_vendor_delete_model_dynamic: set_vendors_id_data,
		});
	}

	handleClose_delete_modal() {
		const default_vendor_delete_popup = [];
		{this.state.open_vendor_delete_model_dynamic ? (this.state.open_vendor_delete_model_dynamic.map((data_ann, index_ann) => {
			default_vendor_delete_popup[data_ann.ID] = false;
		})) : ('')
		}
		this.setState({
			open_vendor_delete_model_dynamic: default_vendor_delete_popup,
		});
	}

	handleChangeRadioButton(e) {
		this.setState({
			selected_option: e.target.value,
		});
	}

	different_vendor_status(e, type) {
		this.setState({
			vendor_loading: false
		});
		if (type === 'approve') {
			this.setState({
				vendor_list_status_approve: true,
				vendor_list_status_pending: false,
				vendor_list_status_rejected: false,
				vendor_list_status_suspended: false,
				vendor_list_status_all: false
			});
			axios
				.get(`${appLocalizer.apiUrl}/mvx_module/v1/all_vendors`, {
					params: { role: 'dc_vendor' },
				})
				.then((response) => {
					this.setState({
						datavendor: response.data,
						vendor_loading: true
					});
				});
		} else if (type === 'pending') {
			this.setState({
				vendor_list_status_approve: false,
				vendor_list_status_pending: true,
				vendor_list_status_rejected: false,
				vendor_list_status_suspended: false,
				vendor_list_status_all: false
			});
			axios
				.get(`${appLocalizer.apiUrl}/mvx_module/v1/all_vendors`, {
					params: { role: 'dc_pending_vendor' },
				})
				.then((response) => {
					this.setState({
						datavendor: response.data,
						vendor_loading: true
					});
				});
		} else if (type === 'rejected') {
			this.setState({
				vendor_list_status_approve: false,
				vendor_list_status_pending: false,
				vendor_list_status_rejected: true,
				vendor_list_status_suspended: false,
				vendor_list_status_all: false
			});
			axios
				.get(`${appLocalizer.apiUrl}/mvx_module/v1/all_vendors`, {
					params: { role: 'dc_rejected_vendor' },
				})
				.then((response) => {
					this.setState({
						datavendor: response.data,
						vendor_loading: true
					});
				});
		} else if (type === 'suspended') {
			this.setState({
				vendor_list_status_approve: false,
				vendor_list_status_pending: false,
				vendor_list_status_rejected: false,
				vendor_list_status_suspended: true,
				vendor_list_status_all: false
			});
			axios
				.get(`${appLocalizer.apiUrl}/mvx_module/v1/all_vendors`, {
					params: { role: 'suspended' },
				})
				.then((response) => {
					this.setState({
						datavendor: response.data,
						vendor_loading: true
					});
				});

		} else if (type === 'all') {
			this.setState({
				vendor_list_status_approve: false,
				vendor_list_status_pending: false,
				vendor_list_status_rejected: false,
				vendor_list_status_suspended: false,
				vendor_list_status_all: true
			});
			axios
				.get(`${appLocalizer.apiUrl}/mvx_module/v1/all_vendors`, {
					params: { role: '' },
				})
				.then((response) => {
					this.setState({
						datavendor: response.data,
						vendor_loading: true
					});
				});
		}
	}

	handlevendoractionsearch(e) {
		if (e) {
			if ( e.value == 'delete' ) {
				this.handleDeleteIcon(this.state.bulkselectlist);
			} else {
				if ( confirm(appLocalizer.global_string.sure_text) === true ) {
					axios({
						method: 'post',
						url: `${appLocalizer.apiUrl}/mvx_module/v1/vendor_delete`,
						data: {
							vendor_ids: this.state.bulkselectlist,
							select_input: e.value
						},
					}).then((response) => {
						if ( response && response.data ) {
							this.setState({
								datavendor: response.data,
							});
						}
					});
				}
			}
		}
	}

	handleVendorDismiss() {
		this.setState({
			vendor_loading: false
		});
		if (this.state.selected_option === 'delete_user') {
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/vendor_delete`,
				data: {
					vendor_ids: this.state.selected_ids,
					select_input: 'delete_user'
				},
			}).then((response) => {
				if ( response && response.data ) {
					this.handleClose_delete_modal();
					this.setState({
						datavendor: response.data,
						selected_option: false,
						vendor_loading: true
					});
				}
			});
		} 
		if (this.state.selected_option === 'delete_vendor') {
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/vendor_delete`,
				data: {
					vendor_ids: this.state.selected_ids,
					select_input: 'delete_vendor'
				},
			}).then((response) => {
				if ( response && response.data ) {
					this.handleClose_delete_modal();
					this.setState({
						datavendor: response.data,
						selected_option: false,
						vendor_loading: true
					});
				}
			});
		}
	}

	handledeletevendor(e) {
		if (confirm(appLocalizer.global_string.confirm_delete) === true) {
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/vendor_delete`,
				data: {
					vendor_ids: this.state.bulkselectlist,
				},
			}).then((responce) => {
				location.reload();
				if (responce.data.redirect_link) {
					window.location.href = responce.data.redirect_link;
				}
			});
		}
	}

	onChangeshippingoption(e, vendor_id) {
		this.setState({
			vendor_shipping_option_choice: e.value,
		});

		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/update_specific_vendor_shipping_option`,
			data: {
				value: e.value,
				vendor_id,
			},
		}).then(() => {});
	}

	update_post_code(e, zone_id, vendor_id, type) {
		let getvalue = '';
		if (type === 'select_state') {
			getvalue = e;
			this.state.data_zone_in_shipping.get_database_state_name = getvalue;
			this.setState({
				data_zone_in_shipping: this.state.data_zone_in_shipping,
			});
		} else {
			getvalue = e.target.value;
		}

		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/update_post_code`,
			data: {
				value: getvalue,
				vendor_id,
				zone_id,
				type,
			},
		}).then(() => {});
	}

	toggle_shipping_method(e, instance_id, zone_id, vendor_id) {
		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/toggle_shipping_method`,
			data: {
				value: e.target.checked,
				instance_id,
				vendor_id,
				zone_id,
			},
		}).then(() => {});

		const params = {
			vendor_id,
			zone_id,
		};
		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/specific_vendor_shipping_zone`,
				{ params }
			)
			.then((response) => {
				this.setState({
					data_zone_in_shipping: response.data,
				});
			});
	}

	handleOnChange(e, getdata, zoneid, vendorid, type) {
		if (type === 'title') {
			getdata.title = e.target.value;
			getdata.settings.title = e.target.value;
		} else if (type === 'cost') {
			getdata.settings.cost = e.target.value;
		} else if (type === 'tax') {
			getdata.settings.tax_status = e.target.value;
		} else if (type === 'min_cost') {
			getdata.settings.min_amount = e.target.value;
		}

		getdata.zone_id = zoneid;
		getdata.vendor_id = vendorid;
		getdata.method_id = getdata.id;
		getdata.instance_id = getdata.instance_id;

		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/update_vendor_shipping_method`,
			data: {
				data_details: getdata,
				change_value: e.target.value,
				vendorid,
				zoneid,
			},
		}).then(() => {});
	}

	handle_different_shipping_add(e, data, index) {
		const newModalShow = [...this.state.open_child_model];
		newModalShow[index] = true;
		this.setState({
			open_child_model: newModalShow,
		});
	}

	handle_different_shipping_delete(e, zone_id, instance_id, vendor_id) {
		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/delete_shipping_method`,
			data: {
				zone_id,
				instance_id,
				vendor_id,
			},
		}).then(() => {
			location.reload();
		});
	}

	handlechildClose() {
		const add_module_false = new Array(
			this.state.open_child_model.length
		).fill(false);
		this.setState({
			open_child_model: add_module_false,
		});
	}

	onChangeshipping(e, zoneid, vendorid) {
		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/add_vendor_shipping_method`,
			data: {
				method_id: e.value,
				vendorid,
				zoneid,
			},
		}).then((responce) => {
			location.reload();
		});
	}

	handleaddshipping_method(e) {
		this.setState({
			open_model: true,
		});
	}

	handleClose(e) {
		this.setState({
			open_model: false,
		});
	}

	handlevendorsearch(e) {
		if (e.target.value) {
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/specific_search_vendor`,
					{
						params: { vendor_id: e.target.value },
					}
				)
				.then((response) => {
					this.setState({
						datavendor: response.data,
					});
				});
		} else {
			axios({
				url: `${appLocalizer.apiUrl}/mvx_module/v1/all_vendors`,
			}).then((response) => {
				this.setState({
					datavendor: response.data,
				});
			});
		}
	}

	useQuery() {
		return new URLSearchParams(useLocation().hash);
	}

	QueryParamsDemo(e) {
		// fetch all vendor tab
		if (window.location.hash !== this.state.current_url && this.useQuery().get('ID')) {
			axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/find_individual_vendor_tabs`,
				{
					params: { vendor_id: this.useQuery().get('ID') },
				}
			)
			.then((response) => {
				if (response.data) {
					this.setState({
						vendors_tab: response.data,
						current_url: window.location.hash
					});
				}
			});
			/****	pending vendor application status	****/
			if (new URLSearchParams(window.location.hash).get('name') === 'vendor-application') {
				axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/list_vendor_application_data`,
					{
						params: { vendor_id: new URLSearchParams(window.location.hash).get('ID') },
					}
				)
				.then((response) => {
					this.setState({
						list_vendor_application_data: response.data,
						set_tab_name: new URLSearchParams(window.location.hash).get('name'),
					});
				});

				axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/list_vendor_application_raw_data`,
					{
						params: { vendor_id: new URLSearchParams(window.location.hash).get('ID') },
					}
				)
				.then((response) => {
					this.setState({
						list_vendor_application_raw_data: response.data
					});
				});

				axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/list_vendor_roles_data`,
					{
						params: { vendor_id: new URLSearchParams(window.location.hash).get('ID') },
					}
				)
				.then((response) => {
					this.setState({
						list_vendor_roles_data: response.data,
						set_tab_name: new URLSearchParams(window.location.hash).get('name'),
					});
				});
			}
			/****	pending vendor application status end	****/
		}

		/******** Vendor list **********/
		if (window.location.hash !== this.state.current_url && !this.useQuery().get('ID')) {
			axios({
				url: `${appLocalizer.apiUrl}/mvx_module/v1/all_vendors`,
			}).then((response) => {
				if (response && response.data) {
					//open_vendor_model_dynamic
					const default_vendor_eye_popup = [];
					const default_vendor_delete_popup = [];
					response.data.map((data_ann, index_ann) => {
						default_vendor_eye_popup[data_ann.ID] = false;
						default_vendor_delete_popup[data_ann.ID] = false;
					});
					this.setState({
						datavendor: response.data,
						data_all_vendor: response.data,
						open_vendor_model_dynamic: default_vendor_eye_popup,
						open_vendor_delete_model_dynamic: default_vendor_delete_popup,
						vendor_loading: true,
						current_url: window.location.hash
					});
				}
			});
		}

		if (!this.useQuery().get('ID')) {
			this.state.data_setting_fileds = [];
			this.state.data_zone_shipping = [];
			this.state.vendors_tab = [];
		}

		// Display vendor list table column and row slection
		if (
			this.state.columns_vendor_list.length === 0 &&
			new URLSearchParams(window.location.hash).get('submenu') ===
				'vendor'
		) {
			appLocalizer.columns_vendor.map((data_ann, index_ann) => {
				let data_selector = '';
				let set_for_dynamic_column = '';
				data_selector = data_ann.selector_choice;
				data_ann.selector = (row) => (
					<div
						dangerouslySetInnerHTML={{ __html: row[data_selector] }}
					></div>
				);

				data_ann.last_action === 'last_action_trigger'
					? (data_ann.cell = (row) => (
							<div className="mvx-vendor-action-icon">
								<div>
									<a href={row.link_shop} data-title={appLocalizer.global_string.shop}>
										<i className="mvx-font icon-shop"></i>
									</a>
								</div>
								<div>
									<a href={row.link} data-title={appLocalizer.global_string.edit}>
										<i className="mvx-font icon-edit"></i>
									</a>
								</div>
								<div
									onClick={() =>
										this.handleDeleteIcon(row.ID)
									}
									id={row.ID}
									data-title={appLocalizer.global_string.delete}
								>
									<i className="mvx-font icon-no"></i>
								</div>
							</div>
					  ))
					: '';

				data_ann.last_action === 'eyeicon_trigger'
					? (data_ann.cell = (row) => (
							<div className="mvx-vendor-action-icon">
								
								<div
									onClick={() => this.handleEyeIcon(row.ID)}
									id={row.ID}
								>
									<i className="mvx-font icon-eye-preview"></i>
								</div>
							</div>
					  ))
					: '';

				this.state.columns_vendor_list[index_ann] = data_ann;
				set_for_dynamic_column = this.state.columns_vendor_list;
				this.setState({
					columns_vendor_list: set_for_dynamic_column,
				});
			});
		}
		// Display vendor list table column and row slection end

		{
			/* column list followers start */
		}
		if (this.useQuery().get('name') === 'vendor-followers') {
			appLocalizer.columns_followers.map((data_follow, index_follow) => {
				let data_selector_followers = '';
				let set_for_dynamic_column_followers = '';
				data_selector_followers = data_follow.selector_choice;
				data_follow.selector = (row) => (
					<div
						dangerouslySetInnerHTML={{
							__html: row[data_selector_followers],
						}}
					></div>
				);

				this.state.columns_vendor_followers_list[index_follow] =
					data_follow;
				set_for_dynamic_column_followers =
					this.state.columns_vendor_followers_list;
				this.state.columns_vendor_followers_list =
					set_for_dynamic_column_followers;
			});
		}
		{
			/* column list followers end */
		}

		{
			/* column zone list start */
		}
		if (
			new URLSearchParams(window.location.hash).get('submenu') ===
			'vendor'
		) {
			appLocalizer.columns_zone_shipping.map((data_zone, index_zone) => {
				let data_selector_zone = '';
				let set_for_dynamic_column_zone = '';
				data_selector_zone = data_zone.selector_choice;
				data_zone.selector = (row) => (
					<div
						dangerouslySetInnerHTML={{
							__html: row[data_selector_zone],
						}}
					></div>
				);

				this.state.columns_vendor_zone_list[index_zone] = data_zone;
				set_for_dynamic_column_zone =
					this.state.columns_vendor_zone_list;
				this.state.columns_vendor_zone_list =
					set_for_dynamic_column_zone;
			});
		}
		{
			/* column zone list end */
		}

		if (this.useQuery().get('ID')) {
			this.state.datavendor = [];
			this.state.vendor_loading = false;
		}

		const user_query = this.useQuery();

		return user_query.get('ID') ? (
		this.state.vendors_tab.length > 0 ? 
			<TabSection
				model={
					this.state.vendors_tab
				}
				query_name={user_query}
				funtion_name={this}
				vendor
			/>
			:   <PageLoader/>
		) : user_query.get('name') === 'add-new' ? (
			<TabSection
				model={
					appLocalizer.mvx_all_backend_tab_list[
						'marketplace-new-vendor'
					]
				}
				query_name={user_query.get('name')}
				funtion_name={this}
				default_vendor_funtion
				no_tabs
			/>
		) : (
			<div className="mvx-general-wrapper mvx-vendor">
				<HeaderSection />
				<div className="mvx-container">
					{!user_query.get('ID') ? (
						user_query.get('name') === 'add-new' ? (
							''
						) : (
							<div className="mvx-middle-container-wrapper">
								<div className="mvx-page-title">
									<p>
										{
											appLocalizer.vendor_page_string
												.vendors
										}
									</p>
									<Link
										to={`?page=mvx#&submenu=vendor&name=add-new`}
										className="mvx-btn btn-purple"
									>
										<i className="mvx-font icon-add"></i>
										{
											appLocalizer.vendor_page_string
												.add_vendor
										}
									</Link>
								</div>

								<div className="mvx-search-and-multistatus-wrap">
									<ul className="mvx-multistatus-ul">
										<li className={`mvx-multistatus-item ${this.state.vendor_list_status_all ? 'status-active' : ''}`}>
											<div
												className="mvx-multistatus-check-all"
												onClick={(e) =>
													this.different_vendor_status(
														e,
														'all'
													)
												}
											>
												{appLocalizer.global_string.all}{' '}
												(
												{
													this.state.data_all_vendor
														.length
												}
												)
											</div>
										</li>
										<li className="mvx-multistatus-item mvx-divider"></li>
										<li className={`mvx-multistatus-item ${this.state.vendor_list_status_approve ? 'status-active' : ''}`}>
											<div
												className="mvx-multistatus-check-approve"
												onClick={(e) =>
													this.different_vendor_status(
														e,
														'approve'
													)
												}
											>
												{
													appLocalizer
														.vendor_page_string
														.approve
												}{' '}
												(
												{
													this.state
														.data_approve_vendor
														.length
												}
												)
											</div>
										</li>
										<li className="mvx-multistatus-item mvx-divider"></li>
										<li className={`mvx-multistatus-item ${this.state.vendor_list_status_pending ? 'status-active' : ''}`}>
											<div
												className="mvx-multistatus-check-pending status-active"
												onClick={(e) =>
													this.different_vendor_status(
														e,
														'pending'
													)
												}
											>
												{
													appLocalizer.global_string
														.pending
												}{' '}
												(
												{
													this.state
														.data_pending_vendor
														.length
												}
												)
											</div>
										</li>
										<li className="mvx-multistatus-item mvx-divider"></li>
										<li className={`mvx-multistatus-item ${this.state.vendor_list_status_rejected ? 'status-active' : ''}`}>
											<div
												className="mvx-multistatus-check-rejected"
												onClick={(e) =>
													this.different_vendor_status(
														e,
														'rejected'
													)
												}
											>
												{
													appLocalizer
														.vendor_page_string
														.reject
												}{' '}
												(
												{
													this.state
														.data_rejected_vendor
														.length
												}
												)
											</div>
										</li>
										<li className="mvx-multistatus-item mvx-divider"></li>
										<li className={`mvx-multistatus-item ${this.state.vendor_list_status_suspended ? 'status-active' : ''}`}>
											<div
												className="mvx-multistatus-check-rejected"
												onClick={(e) =>
													this.different_vendor_status(
														e,
														'suspended'
													)
												}
											>
												{
													appLocalizer
														.vendor_page_string
														.suspend
												}{' '}
												(
												{
													this.state
														.data_suspend_vendor
														.length
												}
												)
											</div>
										</li>
									</ul>
									<div className="mvx-header-search-section">
										<label>
											<i className="mvx-font icon-search"></i>
										</label>
										<input
											type="text"
											placeholder={
												appLocalizer.vendor_page_string
													.search_vendor
											}
											onChange={this.handlevendorsearch}
										/>
									</div>
								</div>

								<div className="mvx-wrap-bulk-all-date">
									<Select
										placeholder={
											appLocalizer.global_string
												.bulk_action
										}
										options={
											appLocalizer.select_option_delete_for_vendor
										}
										isClearable={true}
										className="mvx-wrap-bulk-action"
										onChange={this.handlevendoractionsearch}
									/>
								</div>

								{this.state.columns_vendor_list &&
								this.state.columns_vendor_list.length > 0 &&
								this.state.vendor_loading ? (
									<div className="mvx-backend-datatable-wrapper">
										<DataTable
											columns={
												this.state.columns_vendor_list
											}
											data={this.state.datavendor}
											selectableRows
											onSelectedRowsChange={
												this.handleChange
											}
											pagination
										/>
									</div>
								) : (
									<PageLoader/>
								)}

								{this.state.datavendor.map((data8, index8) => (
									<Dialog
										open={
											this.state
												.open_vendor_model_dynamic[
												data8.ID
											]
										}
										onClose={this.handleClose_dynamic}
										aria-labelledby="form-dialog-title"
									>
										<DialogTitle id="form-dialog-title">
											<div className="mvx-module-dialog-title">
												<div
													className="mvx-vendor-title"
													dangerouslySetInnerHTML={{
														__html: data8.name,
													}}
												></div>
												<i
													className="mvx-font icon-no"
													onClick={
														this.handleClose_dynamic
													}
												></i>
											</div>
										</DialogTitle>
										<DialogContent>
											<DialogContentText>
												<div className="mvx-module-dialog-content">
													<div className="mvx-email-content-and-value-wrap">
														<div className="mvx-content-email">
															{
																appLocalizer
																	.vendor_page_string
																	.email
															}{' '}
															:
														</div>
														<div className="mvx-content-email-value" dangerouslySetInnerHTML={{ __html: data8.email }} >
														</div>
													</div>

													<div className="mvx-vendor-textarea-content">
														<textarea
															placeholder={
																appLocalizer
																	.vendor_page_string
																	.describe_yourself
															}
															onChange={(e) =>
																this.handle_rejected_vendor_description(
																	e,
																	data8.ID
																)
															}
														></textarea>
													</div>

													<div className="mvx-vendor-multi-action-buttons">
														{data8.status_raw_text !== 'Rejected' ? 
															(data8.status_raw_text !== 'Approved' ? (
																data8.status_raw_text == 'Pending' ? (
																<button
																	className="mvx-btn btn-purple hellof"
																	onClick={() =>
																		this.handle_Vendor_Approve(
																			data8.ID
																		)
																	}
																	color="primary"
																>
																	{
																		appLocalizer
																			.vendor_page_string
																			.approve
																	}
																</button>)
																:(
																<button
																	className="mvx-btn btn-purple hellof"
																	onClick={() =>
																		this.handle_Vendor_Activate(
																			data8.ID
																		)
																	}
																	color="primary"
																>
																	{
																		appLocalizer
																			.vendor_page_string
																			.approve
																	}
																</button>))
															: (
															<button
																className="mvx-btn btn-purple"
																onClick={() =>
																	this.handle_Vendor_Suspend(
																		data8.ID
																	)
																}
																color="primary"
															>
																{
																	appLocalizer
																		.vendor_page_string
																		.suspend
																}
															</button>
															))
														:
															(
															' '	
															)	

														}
														{data8.status_raw_text !== 'Rejected' ? 
															(<button
																className="mvx-btn btn-red"
																onClick={() =>
																	this.handle_Vendor_Reject(
																		data8.ID
																	)
																}
																color="primary"
															>
																{
																	appLocalizer
																		.vendor_page_string
																		.reject
																}
															</button>)
														:
															(
															' '	
															)	

														}
														<button
															className="mvx-btn btn-border"
															onClick={() =>
																this.handle_Vendor_Edit(
																	data8
																)
															}
															color="primary"
														>
															{
																appLocalizer
																	.vendor_page_string
																	.edit_vendor
															}
														</button>
													</div>
												</div>
											</DialogContentText>
										</DialogContent>
										<DialogActions></DialogActions>
									</Dialog>
								))}

								{this.state.datavendor ? (this.state.datavendor.map((vendor_data, vendor_index) => (
									<Dialog
										open={
											this.state
												.open_vendor_delete_model_dynamic[
												vendor_data.ID
											]
										}
										onClose={this.handleClose_delete_modal}
										aria-labelledby="form-dialog-title"
									>
										<DialogTitle id="form-dialog-title">
											<div className="mvx-module-dialog-title">
												<div className="mvx-vendor-title">
													{appLocalizer.vendor_page_string.select_option}
												</div>
												<i
													className="mvx-font icon-no"
													onClick={
														this.handleClose_delete_modal
													}
												></i>
											</div>
										</DialogTitle>
										<DialogContent>
											<DialogContentText>
												<div className="mvx-module-dialog-content">
													<div className="mvx-vendor-textarea-content">
														<div>
															<label>
																<input
																type="radio"
																value="delete_user"
																checked={this.state.selected_option === "delete_user"}
																onChange={this.handleChangeRadioButton}
																/>
																{appLocalizer.vendor_page_string.delete_user}
															</label>
														</div>
														<div>
															<label>
																<input
																type="radio"
																value="delete_vendor"
																checked={this.state.selected_option === "delete_vendor"}
																onChange={this.handleChangeRadioButton}
																/>
																{appLocalizer.vendor_page_string.delete_vendor}
															</label>
														</div>
													</div>

													<div className="mvx-vendor-multi-action-buttons">
														<button
															className="mvx-btn btn-red"
															onClick={this.handleVendorDismiss}
															color="primary"
															>
															{appLocalizer.vendor_page_string.modal_button_text}
														</button>
													</div>
												</div>
											</DialogContentText>
										</DialogContent>
										<DialogActions></DialogActions>
									</Dialog>
								))) : ('')
								}
							</div>
						)
					) : (
						''
					)}

					<BannerSection />
				</div>
			</div>
		);
	}

	Childparent({ name }) {
		return (
			<div className="mvx-dynamic-form-content">
				<div className="mvx-back-btn-wrapper">
				<Link className="mvx-back-btn" to={`?page=mvx#&submenu=vendor`}>
					<i className="mvx-font icon-back"></i>Back
				</Link>
				</div>
				{name ? (
					<DynamicForm
						key={`dynamic-form-add-new`}
						className="mvx-vendor-add-new"
						model={appLocalizer.settings_fields['add-new']}
						method="post"
						location={useLocation().search}
						modulename="vendor_add_personal"
						url="mvx_module/v1/create_vendor"
						submit_title={appLocalizer.vendor_page_string.add_new}
					/>
				) : (
					''
				)}
			</div>
			
		);
	}

	Child({ name }) {
		if (!this.useQuery().get('zone_id')) {
			this.state.data_zone_in_shipping = [];
		}

		if (name.get('ID') && name.get('ID') != this.state.set_tab_name_id) {
			this.state.data_setting_fileds = [];
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/list_of_all_tab_based_settings_field`,
					{
						params: { vendor_id: this.useQuery().get('ID') },
					}
				)
				.then((response) => {
					if (response.data) {
						this.setState({
							data_setting_fileds: response.data,
							vendor_shipping_option_choice:
								response.data.vendor_default_shipping_options
									.value,
							set_tab_name_id: name.get('ID'),
						});
					}
				});

		}

		if (name.get('ID') && name.get('name') != this.state.set_tab_name) {
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/list_of_all_tab_based_settings_field`,
					{
						params: { vendor_id: name.get('ID') },
					}
				)
				.then((response) => {
					if (response.data) {
						this.setState({
							data_setting_fileds: response.data,
							vendor_shipping_option_choice:
								response.data.vendor_default_shipping_options
									.value,
							set_tab_name: name.get('name'),
						});
					}
				});

			if (
				this.useQuery().get('ID') &&
				this.useQuery().get('name') === 'vendor-followers'
			) {
				this.state.datafollowers_loader = false;
				axios
					.get(
						`${appLocalizer.apiUrl}/mvx_module/v1/all_vendor_followers`,
						{
							params: { vendor_id: this.useQuery().get('ID') },
						}
					)
					.then((response) => {
						this.setState({
							datafollowers: response.data,
							datafollowers_loader: true,
						});
					});
			}

			
		}

		if (name.get('name') === 'vendor-shipping') {
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/specific_vendor_shipping`,
					{
						params: { vendor_id: name.get('ID') },
					}
				)
				.then((response) => {
					if (
						response.data &&
						this.state.data_zone_shipping.length === 0
					) {
						this.setState({
							data_zone_shipping: response.data,
						});
					}
				});
		}

		if (name.get('zone_id')) {
			const params = {
				vendor_id: name.get('ID'),
				zone_id: name.get('zone_id'),
			};

			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/specific_vendor_shipping_zone`,
					{ params }
				)
				.then((response) => {
					const add_module_false = response.data
						.vendor_shipping_methods
						? new Array(
								Object.keys(
									response.data.vendor_shipping_methods
								).length
						  ).fill(false)
						: '';
					if (
						response.data &&
						this.state.data_zone_in_shipping.length === 0
					) {
						this.setState({
							data_zone_in_shipping: response.data,
							open_child_model: add_module_false,
						});
					}
				});
		}

		return this.state.vendors_tab.map(
			(data, index) =>
				data.modulename === name.get('name') ? (
					name.get('name') === 'vendor-application' ? (
						<div className="mvx-vendor-application-content">
							{this.state.list_vendor_application_data ? (
								<div>
								<div className="pull-right">
									{
									this.state.list_vendor_application_raw_data ? 
										<CSVLink
										data={this.state.list_vendor_application_raw_data}
										headers={appLocalizer.mvx_vendor_application_header}
										filename={'Vendor_application_data.csv'}
										className="mvx-btn btn-purple"
										>
										<i className="mvx-font icon-download"></i>
										{
											appLocalizer.global_string
												.download_csv
										}
										</CSVLink>
									: ''
									}
									
								</div>
								<div
									dangerouslySetInnerHTML={{
										__html: this.state
											.list_vendor_application_data,
									}}
								></div>
							</div>
							) : (
								<PageLoader/>
							)}

							{this.state.list_vendor_roles_data &&
							this.state.list_vendor_roles_data ===
								'pending_vendor' ? (
								<div className="mvx-vendor-modal-main">
									<textarea
										className="pending-vendor-note form-control"
										placeholder={
											appLocalizer.vendor_page_string
												.optional_note
										}
										onChange={(e) =>
											this.handle_rejected_vendor_description(
												e,
												name.get('ID'),
												'reload'
											)
										}
									/>

									<div>
										<button
											onClick={() =>
												this.handle_Vendor_Reject(
													name.get('ID'),
													'reload'
												)
											}
											className="mvx-btn btn-red"
										>
											{
												appLocalizer.vendor_page_string
													.reject
											}
										</button>
										<button
											onClick={() =>
												this.handle_Vendor_Approve(
													name.get('ID'),
													'reload'
												)
											}
											className="mvx-btn btn-purple"
										>
											{
												appLocalizer.vendor_page_string
													.approve
											}
										</button>
									</div>
								</div>
							) : (
								''
							)}
						</div>
					) : name.get('name') === 'vendor-followers' ? (
						this.state.datafollowers_loader ? (
							<div className="mvx-backend-datatable-wrapper">
								<DataTable
									columns={
										this.state.columns_vendor_followers_list
									}
									data={this.state.datafollowers}
									selectableRows
									pagination
								/>
							</div>
						) : (
							<PageLoader/>
						)
					) : name.get('name') === 'vendor-shipping' ? (
						name.get('zone_id') ? (
							this.state.data_zone_in_shipping &&
							Object.keys(this.state.data_zone_in_shipping)
								.length > 0 ? (
								<div>
									<Link className="mvx-back-btn" to={`?page=mvx#&submenu=vendor&ID=${name.get('ID')}&name=vendor-shipping`}>
										<i className="mvx-font icon-back"></i>{appLocalizer.vendor_page_string.back}
									</Link>
									<table className="form-table mvx-shipping-zone-settings wc-shipping-zone-settings">
										<tbody>
											<tr>
												<th scope="row">
													<label>
														{
															appLocalizer
																.vendor_page_string
																.zone_name
														}
													</label>
												</th>
												<td>
													{this.state
														.data_zone_in_shipping
														.zones
														? this.state
																.data_zone_in_shipping
																.zones.data
																.zone_name
														: ''}
												</td>
											</tr>

											<tr>
												<th scope="row">
													<label>
														{
															appLocalizer
																.vendor_page_string
																.zone_region
														}
													</label>
												</th>
												<td>
													{this.state
														.data_zone_in_shipping
														.zones
														? this.state
																.data_zone_in_shipping
																.zones
																.formatted_zone_location
														: ''}
												</td>
											</tr>

											<tr></tr>

											<tr>
												<th scope="row">
													<label>
														{
															appLocalizer
																.vendor_page_string
																.specific_state
														}
													</label>
												</th>
												<td>
													<Select
														value={
															this.state
																.data_zone_in_shipping
																.get_database_state_name
																? this.state
																		.data_zone_in_shipping
																		.get_database_state_name
																: ''
														}
														isMulti
														options={
															this.state
																.data_zone_in_shipping
																.state_select
														}
														onChange={(e) =>
															this.update_post_code(
																e,
																name.get(
																	'zone_id'
																),
																name.get('ID'),
																'select_state'
															)
														}
													></Select>
												</td>
											</tr>

											<tr>
												<th>
													<label>
														{
															appLocalizer
																.vendor_page_string
																.postcode
														}
													</label>
												</th>
												<td className="mvx-settings-basic-input-class">
													<input
														className="mvx-setting-form-input"
														type="text"
														defaultValue={
															this.state
																.data_zone_in_shipping
																.postcodes
														}
														placeholder={
															appLocalizer
																.vendor_page_string
																.comma_separated
														}
														onChange={(e) =>
															this.update_post_code(
																e,
																name.get(
																	'zone_id'
																),
																name.get('ID'),
																'postcode'
															)
														}
													/>
												</td>
											</tr>

											<tr>
												<th>
													<label>
														{
															appLocalizer
																.vendor_page_string
																.shipping_methods
														}
													</label>
												</th>
												<td>
													<table className="mvx-shipping-zone-methods">
														<thead>
															<tr>
																<th className="mvx-title wc-shipping-zone-method-title">
																	{
																		appLocalizer
																			.vendor_page_string
																			.title
																	}
																</th>
																<th className="mvx-enabled wc-shipping-zone-method-enabled">
																	{
																		appLocalizer
																			.vendor_page_string
																			.enabled
																	}
																</th>
																<th className="mvx-description wc-shipping-zone-method-description">
																	{
																		appLocalizer
																			.vendor_page_string
																			.description
																	}
																</th>
																<th className="mvx-action">
																	Action
																</th>
															</tr>
														</thead>

														<tfoot>
															<tr>
																<td>
																	<button
																		onClick={(
																			e
																		) =>
																			this.handleaddshipping_method(
																				e
																			)
																		}
																		className="mvx-btn btn-purple"
																	>
																		{
																			appLocalizer
																				.vendor_page_string
																				.shipping_methods
																		}
																	</button>
																</td>
															</tr>
														</tfoot>

														<tbody>
															{this.state
																.data_zone_in_shipping
																.vendor_shipping_methods ? (
																Object.entries(
																	this.state
																		.data_zone_in_shipping
																		.vendor_shipping_methods
																).map(
																	(
																		data,
																		index
																	) => (
																		<tr className="mvx-shipping-zone-method">
																			<td>
																				{
																					data[1]
																						.title
																				}
																			</td>
																			<td className="mvx-shipping-zone-method-enabled wc-shipping-zone-method-enabled">
																				<span>
																					<input
																						className="inputcheckbox"
																						defaultChecked={
																							data[1]
																								.enabled &&
																							data[1]
																								.enabled ===
																								'yes'
																								? true
																								: false
																						}
																						type="checkbox"
																						name="method_status"
																						onChange={(
																							e
																						) =>
																							this.toggle_shipping_method(
																								e,
																								data[1]
																									.instance_id,
																								name.get(
																									'zone_id'
																								),
																								name.get(
																									'ID'
																								)
																							)
																						}
																					/>
																				</span>
																			</td>

																			<td>
																				{
																					data[1]
																						.settings
																						.description
																				}
																			</td>

																			<td>
																				<div className="mvx-actions">
																					<span className="edit">
																						<button
																							onClick={(
																								e
																							) =>
																								this.handle_different_shipping_add(
																									e,
																									data[1],
																									index
																								)
																							}
																							className="mvx-btn btn-purple"
																						>
																							{
																								appLocalizer
																									.vendor_page_string
																									.edit
																							}
																						</button>
																					</span>
																					<span className="delete">
																						<button
																							onClick={(
																								e
																							) =>
																								this.handle_different_shipping_delete(
																									e,
																									name.get(
																										'zone_id'
																									),
																									data[1]
																										.instance_id,
																									name.get(
																										'ID'
																									)
																								)
																							}
																							className="mvx-btn btn-purple"
																						>
																							{
																								appLocalizer
																									.vendor_page_string
																									.delete
																							}
																						</button>
																					</span>
																				</div>
																			</td>

																			<Dialog
																				open={
																					this
																						.state
																						.open_child_model[
																						index
																					]
																				}
																				onClose={
																					this
																						.handlechildClose
																				}
																				aria-labelledby="form-dialog-title"
																			>
																				<DialogTitle id="form-dialog-title">
																					<div className="mvx-module-dialog-title">
																						{
																							appLocalizer
																								.vendor_page_string
																								.differnet_method
																						}
																					</div>
																				</DialogTitle>
																				<DialogContent>
																					<DialogContentText>
																						{data[1]
																							.id &&
																						data[1]
																							.id ===
																							'flat_rate' ? (
																							<div className="mvx-vendor-shipping-method-details">
																								<label>
																									{
																										appLocalizer
																											.vendor_page_string
																											.method_title
																									}
																								</label>
																								<input
																									type="text"
																									defaultValue={
																										data[1]
																											.title
																									}
																									onChange={(
																										e
																									) =>
																										this.handleOnChange(
																											e,
																											data[1],
																											name.get(
																												'zone_id'
																											),
																											name.get(
																												'ID'
																											),
																											'title'
																										)
																									}
																								/>

																								<label>
																									{
																										appLocalizer
																											.vendor_page_string
																											.cost
																									}
																								</label>
																								<input
																									className="form-control"
																									type="number"
																									defaultValue={
																										data[1]
																											.settings
																											.cost
																									}
																									placeholder="0.00"
																									onChange={(
																										e
																									) =>
																										this.handleOnChange(
																											e,
																											data[1],
																											name.get(
																												'zone_id'
																											),
																											name.get(
																												'ID'
																											),
																											'cost'
																										)
																									}
																								/>

																								<select
																									defaultValue={
																										data[1]
																											.settings
																											.tax_status
																									}
																									onChange={(
																										e
																									) =>
																										this.handleOnChange(
																											e,
																											data[1],
																											name.get(
																												'zone_id'
																											),
																											name.get(
																												'ID'
																											),
																											'tax'
																										)
																									}
																								>
																									<option value="none">
																										None
																									</option>
																									<option value="taxable">
																										{
																											appLocalizer
																												.vendor_page_string
																												.taxable
																										}
																									</option>
																								</select>
																							</div>
																						) : data[1]
																								.id ===
																						  'local_pickup' ? (
																							<div>
																								<label>
																									{
																										appLocalizer
																											.vendor_page_string
																											.method_title
																									}
																								</label>
																								<input
																									type="text"
																									defaultValue={
																										data[1]
																											.title
																									}
																									onChange={(
																										e
																									) =>
																										this.handleOnChange(
																											e,
																											data[1],
																											name.get(
																												'zone_id'
																											),
																											name.get(
																												'ID'
																											),
																											'title'
																										)
																									}
																								/>

																								<label>
																									{
																										appLocalizer
																											.vendor_page_string
																											.cost
																									}
																								</label>
																								<input
																									className="form-control"
																									type="number"
																									defaultValue={
																										data[1]
																											.settings
																											.cost
																									}
																									placeholder="0.00"
																									onChange={(
																										e
																									) =>
																										this.handleOnChange(
																											e,
																											data[1],
																											name.get(
																												'zone_id'
																											),
																											name.get(
																												'ID'
																											),
																											'cost'
																										)
																									}
																								/>

																								<select
																									defaultValue={
																										data[1]
																											.settings
																											.tax_status
																									}
																									onChange={(
																										e
																									) =>
																										this.handleOnChange(
																											e,
																											data[1],
																											name.get(
																												'zone_id'
																											),
																											name.get(
																												'ID'
																											),
																											'tax'
																										)
																									}
																								>
																									<option value="none">
																										{
																											appLocalizer
																												.vendor_page_string
																												.none
																										}
																									</option>
																									<option value="taxable">
																										{
																											appLocalizer
																												.vendor_page_string
																												.taxable
																										}
																									</option>
																								</select>
																							</div>
																						) : data[1]
																								.id ===
																						  'free_shipping' ? (
																							<div>
																								<label>
																									{
																										appLocalizer
																											.vendor_page_string
																											.method_title
																									}
																								</label>
																								<input
																									type="text"
																									defaultValue={
																										data[1]
																											.title
																									}
																									onChange={(
																										e
																									) =>
																										this.handleOnChange(
																											e,
																											data[1],
																											name.get(
																												'zone_id'
																											),
																											name.get(
																												'ID'
																											),
																											'title'
																										)
																									}
																								/>

																								<label>
																									{
																										appLocalizer
																											.vendor_page_string
																											.cost
																									}
																								</label>
																								<input
																									className="form-control"
																									type="number"
																									defaultValue={
																										data[1]
																											.settings
																											.min_amount
																									}
																									placeholder="0.00"
																									onChange={(
																										e
																									) =>
																										this.handleOnChange(
																											e,
																											data[1],
																											name.get(
																												'zone_id'
																											),
																											name.get(
																												'ID'
																											),
																											'min_cost'
																										)
																									}
																								/>
																							</div>
																						) : (
																							''
																						)}
																					</DialogContentText>
																				</DialogContent>
																				<DialogActions>
																					<button
																						onClick={
																							this
																								.handlechildClose
																						}
																						className="mvx-btn btn-purple"
																					>
																						{
																							appLocalizer
																								.global_string
																								.save_changes
																						}
																					</button>
																				</DialogActions>
																			</Dialog>
																		</tr>
																	)
																)
															) : (
																<tr>
																	<td colSpan="4">
																		{
																			appLocalizer
																				.vendor_page_string
																				.shipping3
																		}
																	</td>
																</tr>
															)}
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>

									<Dialog
										open={this.state.open_model}
										onClose={this.handleClose}
										aria-labelledby="form-dialog-title"
									>
										<DialogTitle id="form-dialog-title">
											<div className="mvx-module-dialog-title">
												{
													appLocalizer
														.vendor_page_string
														.add_shipping_methods
												}
											</div>
										</DialogTitle>
										<DialogContent>
											<DialogContentText>
												<p>
													{
														appLocalizer
															.vendor_page_string
															.shipping1
													}
												</p>

												<Select
													className="shipping_method"
													options={
														this.state
															.add_shipping_options_data
													}
													onChange={(e) => {
														this.onChangeshipping(
															e,
															name.get('zone_id'),
															name.get('ID')
														);
													}}
												></Select>
												<div className="wc-shipping-zone-method-description">
													{
														appLocalizer
															.vendor_page_string
															.shipping2
													}
												</div>
											</DialogContentText>
										</DialogContent>
										<DialogActions>
											<button
												onClick={this.handleClose}
												className="mvx-btn btn-purple"
											>
												{
													appLocalizer
														.vendor_page_string
														.add_shipping_methods
												}
											</button>
										</DialogActions>
									</Dialog>
								</div>
							) : (
								<PageLoader/>
							)
						) : this.state.data_setting_fileds.shipping_options ? (
							<div>
								<Select
									className="shipping_choice"
									options={
										this.state.data_setting_fileds
											.shipping_options
									}
									defaultValue={
										this.state.data_setting_fileds
											.vendor_default_shipping_options
									}
									onChange={(e) => {
										this.onChangeshippingoption(
											e,
											name.get('ID')
										);
									}}
								/>

								{this.state.vendor_shipping_option_choice ===
								'distance_by_zone' ? (
									this.state.columns_vendor_zone_list &&
									this.state.columns_vendor_zone_list.length >
										0 ? (
										<div className="mvx-backend-datatable-wrapper">
											<DataTable
												columns={
													this.state
														.columns_vendor_zone_list
												}
												data={
													this.state
														.data_zone_shipping
												}
												selectableRows
												pagination
											/>
										</div>
									) : (
										''
									)
								) : this.state.vendor_shipping_option_choice ===
								  'distance_by_shipping' ? (
									<DynamicForm
										key={`dynamic-form`}
										title="distance wise shipping"
										model={
											this.state.data_setting_fileds[
												'distance-shipping'
											]
										}
										method="post"
										modulename="distance-shipping"
										vendor_id={name.get('ID')}
										url="mvx_module/v1/update_vendor"
										submitbutton="false"
									/>
								) : this.state.vendor_shipping_option_choice ===
								  'shipping_by_country' ? (
									<DynamicForm
										key={`dynamic-form`}
										title="country wise shipping"
										model={
											this.state.data_setting_fileds[
												'country-shipping'
											]
										}
										method="post"
										modulename="country-shipping"
										vendor_id={name.get('ID')}
										url="mvx_module/v1/update_vendor"
										submitbutton="false"
									/>
								) : (
									''
								)}
							</div>
						) : (
							''
						)
					) : this.state.data_setting_fileds &&
					  Object.keys(this.state.data_setting_fileds).length > 0 ? (
						<DynamicForm
							key={`dynamic-form-${data.modulename}`}
							className={data.classname}
							title={data.tablabel}
							model={
								this.state.data_setting_fileds[data.modulename]
							}
							method="post"
							vendor_id={name.get('ID')}
							modulename={data.modulename}
							url={data.apiurl}
							submitbutton="false"
						/>
					) : (
						<PageLoader/>
					)
				) : (
					''
				)
		);
	}

	handleChange(e) {
		this.setState({
			bulkselectlist: e.selectedRows,
		});
	}

	componentDidMount() {
		// approve vendor
		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/all_vendors`, {
				params: { role: 'dc_vendor' },
			})
			.then((response) => {
				this.setState({
					data_approve_vendor: response.data,
				});
			});

		// pending vendor
		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/all_vendors`, {
				params: { role: 'dc_pending_vendor' },
			})
			.then((response) => {
				this.setState({
					data_pending_vendor: response.data,
				});
			});

		// rejected vendor
		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/all_vendors`, {
				params: { role: 'dc_rejected_vendor' },
			})
			.then((response) => {
				this.setState({
					data_rejected_vendor: response.data,
				});
			});

		// suspended vendor
		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/all_vendors`, {
				params: { role: 'suspended' },
			})
			.then((response) => {
				this.setState({
					data_suspend_vendor: response.data,
				});
			});

		axios({
			url: `${appLocalizer.apiUrl}/mvx_module/v1/vendor_list_search`,
		}).then((response) => {
			this.setState({
				details_vendor: response.data,
			});
		});

		axios({
			url: `${appLocalizer.apiUrl}/mvx_module/v1/add_shipping_option`,
		}).then((response) => {
			this.setState({
				add_shipping_options_data: response.data,
			});
		});

		// set vendor list section top label status
		this.setState({
			vendor_list_status_all: true
		});
	}

	render() {
		return (
			<this.QueryParamsDemo />
		);
	}
}
export default MVXBackendVendor;
