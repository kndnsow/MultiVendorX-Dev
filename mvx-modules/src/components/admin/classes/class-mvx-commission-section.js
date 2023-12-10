/* global appLocalizer */
import React, { Component } from 'react';
import axios from 'axios';
import Select from 'react-select';
import PageLoader from './class-mvx-page-loader.js';
import { css } from '@emotion/react';
import DataTable from 'react-data-table-component';
import { BrowserRouter as Router, Link } from 'react-router-dom';
import { CSVLink } from 'react-csv';
import HeaderSection from './class-mvx-page-header';
import BannerSection from './class-mvx-page-banner';
import DateRangePicker from 'rsuite/DateRangePicker';
const override = css`
	display: block;
	margin: 0 auto;
	border-color: green;
`;

class MVX_Backend_Commission extends Component {
	constructor(props) {
		super(props);
		this.state = {
			commission_select_option_open: false,
			commission_reload: false,
			commission_loading: false,
			commission_details: [],
			updated_commission_status: [],
			get_commission_id_status: [],
			columns_commission_list: [],
			datacommission: [],
			mvx_all_commission_list: [],
			data_paid_commission: [],
			data_unpaid_commission: [],
			data_refunded_commission: [],
			data_trash_commission: [],
			data_partial_refunded_commission: [],
			show_vendor_name: [],
			commisson_bulk_choose: [],
			commissiondata: [],
			commission_list_status_all: false,
			commission_list_status_paid: false,
			commission_list_status_unpaid: false,
			commission_list_status_refunded: false,
			commission_list_status_trash: false,
			date_range: ''
		};
		this.handleSelectRowsChange = this.handleSelectRowsChange.bind(this);
		this.handlecommissionsearch = this.handlecommissionsearch.bind(this);
		this.handlecommissionwork = this.handlecommissionwork.bind(this);
		this.handleupdatecommission = this.handleupdatecommission.bind(this);
		this.handleCommisssionDismiss =
			this.handleCommisssionDismiss.bind(this);
		this.handle_commission_live_search =
			this.handle_commission_live_search.bind(this);
		this.handlecommission_paid = this.handlecommission_paid.bind(this);
		this.handle_commission_status_check =
			this.handle_commission_status_check.bind(this);
		this.handleupdatereport = this.handleupdatereport.bind(this);
	}

	handleupdatereport(e) {
		this.setState({
			date_range: e,
		});

		axios
		.get(
			`${appLocalizer.apiUrl}/mvx_module/v1/all_commission`,
			{
				params: { date_range: e },
			}
		).then((response) => {
			this.setState({
				datacommission: response.data,
			});
		});
	}

	handle_commission_status_check(e, type) {
		if (type === 'paid') {

			this.setState({
				commission_list_status_all: false,
				commission_list_status_paid: true,
				commission_list_status_unpaid: false,
				commission_list_status_trash: false,
				commission_list_status_refunded: false,
			});
			// paid status
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/show_commission_from_status_list`,
					{
						params: { commission_status: 'paid', date_range: this.state.date_range },
					}
				)
				.then((response) => {
					this.setState({
						datacommission: response.data
					});
				});
		}

		if (type === 'unpaid') {
			// unpaid status
			this.setState({
				commission_list_status_all: false,
				commission_list_status_paid: false,
				commission_list_status_unpaid: true,
				commission_list_status_trash: false,
				commission_list_status_refunded: false,
			});
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/show_commission_from_status_list`,
					{
						params: { commission_status: 'unpaid', date_range: this.state.date_range },
					}
				)
				.then((response) => {
					this.setState({
						datacommission: response.data,
					});
				});
		}

		if (type === 'refunded') {
			// refunded status
			this.setState({
				commission_list_status_all: false,
				commission_list_status_paid: false,
				commission_list_status_unpaid: false,
				commission_list_status_refunded: true,
				commission_list_status_trash: false
			});
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/show_commission_from_status_list`,
					{
						params: { commission_status: 'refunded', date_range: this.state.date_range },
					}
				)
				.then((response) => {
					this.setState({
						datacommission: response.data,
					});
				});
		}

		if (type === 'trash') {
			// trash status
			this.setState({
				commission_list_status_all: false,
				commission_list_status_paid: false,
				commission_list_status_unpaid: false,
				commission_list_status_refunded: false,
				commission_list_status_trash: true
			});
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/show_commission_from_status_list`,
					{
						params: { commission_status: 'trash', date_range: this.state.date_range },
					}
				)
				.then((response) => {
					this.setState({
						datacommission: response.data,
					});
				});
		}
		

		if (type === 'all') {
			this.setState({
				commission_list_status_all: true,
				commission_list_status_paid: false,
				commission_list_status_unpaid: false,
				commission_list_status_trash: false,
				commission_list_status_refunded: false,
			});

			axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/all_commission`,
				{
					params: { date_range: this.state.date_range },
				}
			).then((response) => {
				this.setState({
					datacommission: response.data,
				});
			});
		}
	}

	handlecommission_paid(e) {
		this.setState({
			commission_select_option_open: true,
		});
	}

	handle_commission_live_search(e) {
		if (e.target.value) {
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/search_specific_commission`,
					{
						params: { commission_ids: e.target.value },
					}
				)
				.then((response) => {
					this.setState({
						datacommission: response.data,
					});
				});
		} else {
			axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/all_commission`,
				{
					params: { date_range: this.state.date_range },
				}
			).then((response) => {
				this.setState({
					datacommission: response.data,
				});
			});
		}
	}

	handleCommisssionDismiss(e) {
		if (confirm(appLocalizer.global_string.confirm_delete) === true) {
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/commission_delete`,
				data: {
					commission_ids: e,
				},
			}).then((response) => {
				this.setState({
					datacommission: response.data,
				});
			});
		}
	}

	handleupdatecommission(e) {
		this.setState({
			commission_reload: true,
		});

		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/update_commission_status`,
			data: {
				value: e.value,
				commission_id: new URLSearchParams(window.location.hash).get(
					'CommissionID'
				),
			},
		}).then((responce) => {
			const params = {
				commission_id: new URLSearchParams(window.location.hash).get(
					'CommissionID'
				),
			};
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/details_specific_commission`,
					{ params }
				)
				.then((responsenew) => {
					this.setState({
						commission_details: responsenew.data,
					});
				});

			this.setState({
				commission_select_option_open: false,
				commission_reload: false,
			});
		});
	}

	handlecommissionwork(e) {
		if (e) {
			if (this.state.commisson_bulk_choose.length > 0) {
				this.setState({
					commission_loading: false,
				});

				axios({
					method: 'post',
					url: `${appLocalizer.apiUrl}/mvx_module/v1/update_commission_bulk`,
					data: {
						value: e.value,
						commission_list: this.state.commisson_bulk_choose,
					},
				}).then((responce) => {
					this.setState({
						datacommission: responce.data,
						commission_loading: true,
					});
				});
			} else {
				alert('Please select commission');
			}
		} else {
			axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/all_commission`,
				{
					params: { date_range: this.state.date_range },
				}
			).then((response) => {
				this.setState({
					datacommission: response.data,
				});
			});
		}
		this.common_funtions('');
	}

	componentDidUpdate(prevProps) {
		if (new URLSearchParams(window.location.hash).get('CommissionID')) {
			let set_default_value = this.state.commission_details.length;
			set_default_value = 0;
			//complete commission details
			const params = {
				commission_id: new URLSearchParams(window.location.hash).get(
					'CommissionID'
				),
			};
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/details_specific_commission`,
					{ params }
				)
				.then((response) => {
					if (
						response.data &&
						this.state.commission_details.commission_id !=
							new URLSearchParams(window.location.hash).get(
								'CommissionID'
							)
					) {
						this.setState({
							commission_details: response.data,
						});
					}
				});
		} else {
			this.state.commission_details = [];
		}
	}

	common_funtions = (e) => {
		axios
		.get(
			`${appLocalizer.apiUrl}/mvx_module/v1/all_commission`,
			{
				params: { date_range: this.state.date_range },
			}
		).then((response) => {
			this.setState({
				datacommission: response.data,
				mvx_all_commission_list: response.data,
				commission_loading: true,
			});
		});

		// paid status
		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/show_commission_from_status_list`,
				{
					params: { commission_status: 'paid', date_range: this.state.date_range },
				}
			)
			.then((response) => {
				this.setState({
					data_paid_commission: response.data,
				});
			});

		// unpaid status
		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/show_commission_from_status_list`,
				{
					params: { commission_status: 'unpaid', date_range: this.state.date_range },
				}
			)
			.then((response) => {
				this.setState({
					data_unpaid_commission: response.data,
				});
			});

		// refunded status
		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/show_commission_from_status_list`,
				{
					params: { commission_status: 'refunded', date_range: this.state.date_range },
				}
			)
			.then((response) => {
				this.setState({
					data_refunded_commission: response.data,
				});
			});

		// trash status
		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/show_commission_from_status_list`,
				{
					params: { commission_status: 'trash', date_range: this.state.date_range },
				}
			)
			.then((response) => {
				this.setState({
					data_trash_commission: response.data,
				});
			});

		// partial refunded status
		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/show_commission_from_status_list`,
				{
					params: { commission_status: 'partial_refunded', date_range: this.state.date_range },
				}
			)
			.then((response) => {
				this.setState({
					data_partial_refunded_commission: response.data,
				});
			});
	};

	componentDidMount() {
		this.common_funtions('');
		// get vendor name on select
		axios({
			url: `${appLocalizer.apiUrl}/mvx_module/v1/show_vendor_name`,
		}).then((response) => {
			this.setState({
				show_vendor_name: response.data,
			});
		});

		//complete commission details
		const params = {
			commission_id: new URLSearchParams(window.location.hash).get(
				'CommissionID'
			),
		};
		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/details_specific_commission`,
				{ params }
			)
			.then((response) => {
				this.setState({
					commission_details: response.data,
				});
			});

		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/get_commission_id_status`,
				{ params }
			)
			.then((response) => {
				this.setState({
					get_commission_id_status: response.data,
				});
			});

		// Display table column and row slection
		if (
			this.state.columns_commission_list.length === 0 &&
			new URLSearchParams(window.location.hash).get('submenu') ==
				'commission'
		) {
			appLocalizer.columns_commission.map((data_ann, index_ann) => {
				let data_selector = '';
				let set_for_dynamic_column = '';
				data_selector = data_ann.selector_choice;
				data_ann.selector = (row) => (
					<div
						dangerouslySetInnerHTML={{ __html: row[data_selector] }}
					></div>
				);

				data_ann.cell
					? (data_ann.cell = (row) => (
							<div className="mvx-vendor-action-icon">
								<div data-title="Edit">
									<a href={row.link}>
										<i className="mvx-font icon-edit" ></i>
										
									</a>
								</div>

								<div
									onClick={() =>
										this.handleCommisssionDismiss(row.id)
									}
									id={row.id}
									data-title='Delete'
								>
									<i className="mvx-font icon-no"></i>
							
								</div>
							</div>
					  ))
					: '';

				this.state.columns_commission_list[index_ann] = data_ann;
				set_for_dynamic_column = this.state.columns_commission_list;
				this.setState({
					columns_commission_list: set_for_dynamic_column,
				});
			});
		}

		this.setState({
			commission_list_status_all: true
		});
	}

	handleSelectRowsChange(e) {
		const commission_list = [];
		e.selectedRows.map((data, index) => {
			commission_list[index] = data.id;
		});
		this.setState({
			commisson_bulk_choose: commission_list,
		});

		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/update_commission_bulk`,
			data: {
				value: 'export',
				commission_list,
			},
		}).then((response) => {
			this.setState({
				commissiondata: response.data,
			});
		});
	}

	handlecommissionsearch(e, status) {
		if (status === 'searchstatus') {
			if (e) {
				axios
					.get(
						`${appLocalizer.apiUrl}/mvx_module/v1/show_commission_from_status_list`,
						{
							params: { commission_status: e.value, date_range: this.state.date_range },
						}
					)
					.then((response) => {
						this.setState({
							datacommission: response.data,
						});
					});
			} else {
			axios
				.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/all_commission`,
				{
					params: { date_range: this.state.date_range },
				}
			).then((response) => {
					this.setState({
						datacommission: response.data,
					});
				});
			}
		} else if (status === 'showvendor') {
			if (e) {
				axios
					.get(
						`${appLocalizer.apiUrl}/mvx_module/v1/search_commissions_as_per_vendor_name`,
						{
							params: { vendor_name: e.value, date_range: this.state.date_range },
						}
					)
					.then((response) => {
						this.setState({
							datacommission: response.data,
						});
					});
			} else {
			
			axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/all_commission`,
				{
					params: { date_range: this.state.date_range },
				}
			).then((response) => {
					this.setState({
						datacommission: response.data,
					});
				});
			}
		}
	}

	render() {
		var total_net_earning_count = 0;
		return (
			<div className="mvx-general-wrapper mvx-commission">
				<HeaderSection />

				{new URLSearchParams(window.location.hash).get(
					'CommissionID'
				) ? (
					Object.keys(this.state.commission_details).length > 0 ? (
						<div className="mvx-container mvx-edit-commission-container">
							<div className="mvx-middle-container-wrapper">
								<div className="woocommerce-order-data">
									<div className="mvx-page-title">
										{
											appLocalizer.commission_page_string
												.edit_commission
										}
									</div>

									{/* Commission Details Start */}
									<div className="mvx-commission-details-section">
										<div className="woocommerce-order-data-heading">
											{this.state.commission_details
												.commission_type_object
												? this.state.commission_details
														.commission_type_object
														.labels.singular_name +
												  ' #' +
												  this.state.commission_details
														.commission_id +
												  ' ' +
												  appLocalizer
														.commission_page_string
														.details
												: ''}
										</div>

										<div className="mvx-edit-commission-status-wrapper">
											<div className="mvx-commission-wrap-vendor-order-status">
												<p
													className="commission-details-data-value"
													dangerouslySetInnerHTML={{
														__html: this.state
															.commission_details
															.meta_list_associate_vendor,
													}}
												></p>

												<p className="commission-details-data-value">
													<div className="mvx-commission-label-class">
														{
															appLocalizer
																.commission_page_string
																.associated_order
														}
														:
													</div>
													<div className="mvx-commission-value-class">
														<a
															href={
																this.state
																	.commission_details
																	.order_edit_link
															}
														>
															#
															{
																this.state
																	.commission_details
																	.commission_order_id
															}
														</a>
													</div>
												</p>

												<p className="commission-details-data-value">
													<div className="mvx-commission-label-class">
														{
															appLocalizer
																.commission_page_string
																.order_status
														}
														:
													</div>
													<div className="mvx-commission-value-class">
														{
															this.state
																.commission_details
																.order_status_display
														}
													</div>
												</p>
											</div>

											<div className="mvx-commission-wrap-amount-shipping-tax">
												<div className="mvx-commission-status-wrap">
													{this.state
														.commission_select_option_open ? (
														!this.state
															.commission_reload ? (
															<div className="commission-status-hide-and-show-wrap">
																<p className="commission-status-text-check">
																	{
																		appLocalizer
																			.commission_page_string
																			.commission_status
																	}
																	:{' '}
																</p>
																<Select
																	placeholder={
																		appLocalizer
																			.commission_page_string
																			.status
																	}
																	options={
																		appLocalizer.commission_status_list_action
																	}
																	defaultValue={
																		this
																			.state
																			.get_commission_id_status
																	}
																	className="mvx-wrap-bulk-action"
																	onChange={(
																		e
																	) =>
																		this.handleupdatecommission(
																			e
																		)
																	}
																/>
															</div>
														) : (
															<PageLoader/>     
														)
													) : (
														''
													)}
													{!this.state
														.commission_select_option_open ? (
														<div
															className="woocommerce-order-data-meta order_number"
															dangerouslySetInnerHTML={{
																__html: this
																	.state
																	.commission_details
																	.order_meta_details,
															}}
														></div>
													) : (
														''
													)}
													{!this.state
														.commission_select_option_open ? (
														<i
															className="mvx-font icon-edit"
															onClick={(e) =>
																this.handlecommission_paid(
																	e
																)
															}
														></i>
													) : (
														''
													)}
												</div>

												{/*<p className="woocommerce-order-data-meta order_number" dangerouslySetInnerHTML={{__html: this.state.commission_details.order_meta_details}} ></p> */}

												<p className="form-field form-field-wide mvx-commission-amount">
													<div className="mvx-commission-label-class">
														{
															appLocalizer
																.commission_page_string
																.commission_amount
														}
														:
													</div>
													<div className="mvx-commission-value-class">
														<p
															dangerouslySetInnerHTML={{
																__html:
																	this.state
																		.commission_details
																		.commission_amount !=
																	this.state
																		.commission_details
																		.commission_total_calculate
																		? this
																				.state
																				.commission_details
																				.commission_totals
																		: this
																				.state
																				.commission_details
																				.commission_total_calculate,
															}}
														></p>
													</div>
												</p>

												<p className="commission-details-data-value">
													<div className="mvx-commission-label-class">
														{
															appLocalizer
																.commission_page_string
																.shipping
														}
														:
													</div>
													<div className="mvx-commission-value-class">
														<p
															dangerouslySetInnerHTML={{
																__html:
																	this.state
																		.commission_details
																		.shipping_amount !=
																	this.state
																		.commission_details
																		.commission_shipping_totals
																		? this
																				.state
																				.commission_details
																				.commission_shipping_totals_output
																		: this
																				.state
																				.commission_details
																				.commission_shipping_totals,
															}}
														></p>
													</div>
												</p>

												<p className="commission-details-data-value">
													<div className="mvx-commission-label-class">
														{
															appLocalizer
																.commission_page_string
																.tax
														}
														:
													</div>
													<div className="mvx-commission-value-class">
														<p
															dangerouslySetInnerHTML={{
																__html:
																	this.state
																		.commission_details
																		.tax_amount !=
																	this.state
																		.commission_details
																		.commission_tax_total
																		? this
																				.state
																				.commission_details
																				.commission_tax_total_output
																		: this
																				.state
																				.commission_details
																				.commission_tax_total,
															}}
														></p>
													</div>
												</p>
												{
													this.state.commission_details.additional_datas ?
														<p
															dangerouslySetInnerHTML={{
																__html: this.state.commission_details.additional_datas
																	
															}}
														>
														</p>
													:

													''
												}

											</div>
										</div>
									</div>
									{/* Commission Details End */}

									{/* Commission vendor and order details start*/}
									<div className="mvx-order-details-vendor-details-wrap">
										{/* Commission order and others details start*/}
										<div className="mvx-order-details-wrap">
											{/* Commission order details start*/}
											<div className="mvx-box-background-white-wrapper">
												{
													appLocalizer
														.commission_page_string
														.order_details
												}
											</div>
											<div className="mvx-commission-order-data woocommerce_order_items_wrapper wc-order-items-editable">
												<table
													cellPadding="0"
													cellSpacing="0"
													className="woocommerce_order_items"
												>
													<thead>
														<tr>
															<th
																className="item sortable"
																colSpan="2"
															>
																Item
															</th>
															<th
																className="item_cost sortable"
																data-sort="float"
															>
																Cost
															</th>
															<th
																className="quantity sortable"
																data-sort="int"
															>
																Qty
															</th>
															<th
																className="line_cost sortable"
																data-sort="float"
															>
																Total
															</th>
														</tr>
													</thead>

													<tbody id="order_line_items">
														
															{this.state
																.commission_details
																.line_items ? (

																this.state.commission_details.line_items.map(
																(
																	item_value,
																	item_index
																) => (
																			<tr>
																				<td className="thumb">
																					<p
																						dangerouslySetInnerHTML={{
																							__html: item_value
																								.item_thunbail,
																						}}
																					></p>
																					<div className="mvx-customer-details">
																						<div
																							dangerouslySetInnerHTML={{
																								__html: item_value
																									.product_link_display,
																							}}
																						></div>
																						<div
																							dangerouslySetInnerHTML={{
																								__html: item_value
																									.product_sku,
																							}}
																						></div>
																						<div
																							dangerouslySetInnerHTML={{
																								__html: item_value
																									.check_variation_id
																									? item_value
																											.variation_id_text
																									: '',
																							}}
																						></div>

																						{item_value
																							.check_variation_id ? (
																							<div
																								dangerouslySetInnerHTML={{
																									__html:
																										item_value
																											.get_variation_post_type ===
																										'product_variation'
																											? item_value
																													.item_variation_display
																											: item_value
																													.no_longer_exist,
																								}}
																							></div>
																						) : (
																							''
																						)}

																						<div
																							dangerouslySetInnerHTML={{
																								__html: item_value
																									.close_div,
																							}}
																						></div>

																						<div className="view">
																							{item_value
																								.meta_format_data ? (
																								<table
																									cellSpacing="0"
																									className="display_meta"
																								>
																									{item_value.meta_data.map(
																										(
																											data,
																											index
																										) => (
																											<tr>
																												<th>
																													{
																														data.display_key
																													}

																													:
																												</th>
																												<td>
																													<div
																														dangerouslySetInnerHTML={{
																															__html: data.display_value,
																														}}
																													></div>
																												</td>
																											</tr>
																										)
																									)}
																								</table>
																							) : (
																								''
																							)}
																						</div>
																					</div>
																				</td>
																				<td></td>
																				<td className="item_cost">
																					<div className="view">
																						
																						<div
																							dangerouslySetInnerHTML={{
																								__html: item_value
																									? item_value
																											.item_cost
																									: '',
																							}}
																						></div>

																						<div
																							dangerouslySetInnerHTML={{
																								__html: item_value
																									? item_value
																											.line_cost_html
																									: '',
																							}}
																						></div>
																						
																					</div>
																				</td>

																				<td className="quantity">
																					<div className="view">
																						

																							<div
																								dangerouslySetInnerHTML={{
																									__html: item_value
																										? item_value
																												.quantity_1st
																										: '',
																								}}
																							></div>

																						
																						<div
																							dangerouslySetInnerHTML={{
																								__html: item_value
																									? item_value
																											.quantity_2nd
																									: '',
																							}}
																						></div>

																					</div>
																				</td>


																				<td className="line_cost">
																					<div className="view">

																				

																						<div
																							dangerouslySetInnerHTML={{
																								__html: item_value
																									? item_value
																											.line_cost
																									: '',
																							}}
																						></div>

																					
																						<div
																							dangerouslySetInnerHTML={{
																								__html: item_value
																									? item_value
																											.line_cost_1st
																									: '',
																							}}
																						></div>

																					
																						<div
																							dangerouslySetInnerHTML={{
																								__html: item_value
																									? item_value
																											.line_cost_2nd
																									: '',
																							}}
																						></div>

																					</div>
																				</td>


																				</tr>
																				))
															) : (
																''
															)}

															

														
													</tbody>
												</table>
											</div>
											{/* Commission order details end*/}

											{/* Commission order shipping details start*/}

											{this.state.commission_details
												.shipping_items_details ? (
												<div className="mvx-box-background-white-wrapper">
													{
														appLocalizer
															.commission_page_string
															.shipping
													}
												</div>
											) : (
												''
											)}
											{this.state.commission_details
												.shipping_items_details ? (
												<div className="mvx-commission-order-data woocommerce_order_items_wrapper wc-order-items-editable">
													
												<div className='woocommerce_order_items mvx-shipping-table-wrap'>
																
																{this
																				.state
																				.commission_details
																				.shipping_items_details
																				.meta_data ? (
																				<table
																					cellSpacing="0"
																					className="display_meta"
																				>
																				<thead>
																				<tr>
																					{this.state.commission_details.shipping_items_details.meta_data.map(
																						(
																							data,
																							index
																						) => (
																								<th>
																									{
																										data.display_key
																									}
																								</th>
																								
																						)
																					)}


																				</tr>
																				</thead>
																				<tbody>
																				<tr>
																					{this.state.commission_details.shipping_items_details.meta_data.map(
																						(
																							data,
																							index
																						) => (
																								
																								<td>
																									<div
																										dangerouslySetInnerHTML={{
																											__html: data.display_value,
																										}}
																									></div>
																								</td>
																						)
																					)}
																				</tr>
																				</tbody>
																				</table>
																			) : (
																				''
																			)}


														</div>
													
												</div>
											) : (
												''
											)}
											{/* Commission order shipping details end*/}

											<div className="wc-used-coupons">
												<ul className="wc_coupon_list"></ul>
											</div>

											<div className="mvx-wrap-table-commission-and-coupon-commission">
												<div className="mvx-coupon-shipping-tax">
													{/*<ul className="mvx-child-coupon-shipping-tax">
														{this.state
															.commission_details
															.order_total_discount >
															0 &&
														this.state
															.commission_details
															.commission_include_coupon ? (
															<li>
																<em>
																	*
																	{
																		appLocalizer
																			.commission_page_string
																			.calculated_coupon
																	}
																</em>
															</li>
														) : (
															''
														)}
														{this.state
															.commission_details
															.is_shipping > 0 &&
														this.state
															.commission_details
															.commission_total_include_shipping ? (
															<li>
																<em>
																	*
																	{
																		appLocalizer
																			.commission_page_string
																			.calculated_shipping
																	}
																</em>
															</li>
														) : (
															''
														)}
													
													</ul> */}
												</div>

												<table className="mvx-order-totals">
													<tbody>

														<tr>
															{this.state
																.commission_details
																.order_total_discount >
																0 &&
															this.state
																.commission_details
																.commission_include_coupon ? (
																<td className='cupon-ntc' colspan="3">
																	<em>
																		*
																		{
																			appLocalizer
																				.commission_page_string
																				.calculated_coupon
																		}
																	</em>
																</td>
															) : (
																<td colspan="3"></td>
															)}
															
															<td className="mvx-order-label-td">
																{this.state
																	.commission_details
																	.order_total_discount >
																	0 &&
																this.state
																	.commission_details
																	.commission_include_coupon
																	? '*'
																	: ''}
																{
																	appLocalizer
																		.commission_page_string
																		.commission
																}
																:
															</td>

															<td className="total">
																<div
																	dangerouslySetInnerHTML={{
																		__html: this
																			.state
																			.commission_details
																			.formated_commission_total,
																	}}
																></div>
															</td>
															
														</tr>




														{this.state
															.commission_details
															.get_shipping_method ? (
															<tr>
																{this.state
															.commission_details
															.is_shipping > 0 &&
														this.state
															.commission_details
															.commission_total_include_shipping ? (
																<td className='cupon-ntc' colspan="3">
																<em>
																	*
																	{
																		appLocalizer
																			.commission_page_string
																			.calculated_shipping
																	}
																</em>
															</td>
														) : (
															<td colspan="3"></td>
														)}

															<td className="mvx-order-label-td">
																{
																	appLocalizer
																		.commission_page_string
																		.shipping
																}
																:
															</td>
															<td className="total">
																<div
																	dangerouslySetInnerHTML={{
																		__html:
																			this
																				.state
																				.commission_details
																				.get_total_shipping_refunded >
																			0
																				? this
																						.state
																						.commission_details
																						.refund_shipping_display
																				: this
																						.state
																						.commission_details
																						.else_shipping,
																	}}
																></div>
															</td>

															</tr>
														) : (
															''
														)}



													{this.state
														.commission_details
														.tax_data &&
													Object.keys(
														this.state
															.commission_details
															.tax_data
													).length > 0
														? 
														<tr>
																			{this.state
																				.commission_details
																				.is_tax > 0 &&
																			this.state
																				.commission_details
																				.commission_total_include_tax ? (
																					<td className='cupon-ntc'>

																					<em>
																						*
																						{
																							appLocalizer
																								.commission_page_string
																								.calculated_tax
																						}
																					</em>
																				</td>
																			) : (
																				''
																			)}

																			<td/>
																			<td/>

																			<td className="mvx-order-label-td">
																					{this.state
																						.commission_details
																						.tax_data &&
																					Object.keys(
																						this.state
																							.commission_details
																							.tax_data
																					).length > 0 && Object.keys(
																					this.state
																					.commission_details
																					.tax_data
																					).map(
																					(
																					data,
																					index
																					) => (
																				<div
																					dangerouslySetInnerHTML={{
																						__html: data.tax_label,
																					}}
																				></div>
																				))}
																			</td>

																			<td className="total">
																					{this.state
																						.commission_details
																						.tax_data &&
																					Object.keys(
																						this.state
																							.commission_details
																							.tax_data
																					).length > 0 && Object.keys(
																					this.state
																					.commission_details
																					.tax_data
																					).map(
																					(
																					data,
																					index
																					) => (
																				<div
																					dangerouslySetInnerHTML={{
																						__html:
																							data.get_total_tax_refunded_by_rate_id >
																							0
																								? data.greater_zero
																								: data.else_output,
																					}}
																				></div>
																				))}
																			</td>
														</tr>
															: ''}



														{

															this.state.commission_details.additional_down_datas ? this.state.commission_details.additional_down_datas.map(
															(
																data,
																index_c
															) => (
																<tr
																	dangerouslySetInnerHTML={{
																		__html: data
																			
																	}}
																>
																</tr>
															)) : ''
														}


														<tr>
															<td/>
															<td/>
															<td/>
															<td className="mvx-order-label-td">
																**
																{
																	appLocalizer
																		.commission_page_string
																		.total
																}
																:
															</td>
															<td className="total">
																<div
																	dangerouslySetInnerHTML={{
																		__html:
																			!this
																				.state
																				.commission_details
																				.is_migration_order &&
																			this
																				.state
																				.commission_details
																				.commission_total !=
																				this
																					.state
																					.commission_details
																					.commission_total_edit
																				? this
																						.state
																						.commission_details
																						.commission_total_display
																				: this
																						.state
																						.commission_details
																						.commission_total_edit,
																	}}
																></div>
															</td>
														</tr>

														{this.state
															.commission_details
															.is_refuned ? (
															<tr>
																<td className="label refunded-total">
																	{
																		appLocalizer
																			.commission_page_string
																			.refunded
																	}
																	:
																</td>
																<td width="1%" />
																<td className="total refunded-total">
																	<div
																		dangerouslySetInnerHTML={{
																			__html: this
																				.state
																				.commission_details
																				.refunded_output,
																		}}
																	></div>
																</td>
															</tr>
														) : (
															''
														)}
													</tbody>
												</table>
											</div>
										</div>
										{/* Commission order and others details end*/}

										{/* Commission vendor and notes details start*/}
										<div className="mvx-vendor-notes-details-wrap">
											{/* Commission vendor details start*/}
											<div className="mvx-vendor-details-wrap">
												<div className="mvx-commission-vendor-details-class">
													{
														appLocalizer
															.commission_page_string
															.vendor_details
													}
												</div>
												{this.state.commission_details
													.vendor ? (
													<div className="mvx-child-vendor-details">
														<p className="commission-details-data-value">
															<div className="mvx-commission-label-class">
																<p
																	dangerouslySetInnerHTML={{
																		__html: this
																			.state
																			.commission_details
																			.avater_image,
																	}}
																></p>
															</div>
															<div className="mvx-commission-value-class">
																<a
																	href={
																		this
																			.state
																			.commission_details
																			.vendor_edit_link
																	}
																>
																	{
																		this
																			.state
																			.commission_details
																			.vendor
																			.user_data
																			.data
																			.display_name
																	}
																</a>
															</div>
														</p>

														<p className="commission-details-data-value">
															<div className="mvx-commission-label-class">
																{
																	appLocalizer
																		.commission_page_string
																		.email
																}
																:
															</div>
															<div className="mvx-commission-value-class">
																<a
																	href={`mailto:${this.state.commission_details.vendor.user_data.data.user_email}`}
																>
																	{
																		this
																			.state
																			.commission_details
																			.vendor
																			.user_data
																			.data
																			.user_email
																	}
																</a>
															</div>
														</p>

														<p className="commission-details-data-value">
															<div className="mvx-commission-label-class">
																{
																	appLocalizer
																		.commission_page_string
																		.payment_mode
																}
																:
															</div>
															<div className="mvx-commission-value-class">
																{
																	this.state
																		.commission_details
																		.payment_title
																}
															</div>
														</p>
													</div>
												) : (
													''
												)}
											</div>
											{/* Commission vendor details end*/}

											{/* Commission notes start*/}

											<div className="mvx-notes-details-wrap">
												<div className="mvx-commission-notes-details-class">
													{
														appLocalizer
															.commission_page_string
															.commission_notes
													}
												</div>

												{this.state.commission_details
													.notes_data &&
												this.state.commission_details
													.notes_data.length > 0
													? this.state.commission_details.notes_data.map(
															(
																data_com,
																index_com
															) => (
																<div className="mvx_commision_note_clm">
																	<p
																		dangerouslySetInnerHTML={{
																			__html: data_com.comment_content,
																		}}
																	></p>
																	<small
																		dangerouslySetInnerHTML={{
																			__html: data_com.comment_date,
																		}}
																	></small>
																</div>
															)
													  )
													: ''}
											</div>
										</div>
										{/* Commission notes end*/}
									</div>
									{/* Commission vendor and notes details end*/}
								</div>
								{/* Commission vendor and order details end*/}
							</div>

							<BannerSection />
						</div>
					) : (
						<PageLoader/>     
					)
				) : (
					<div className="mvx-container">
						<div className="mvx-middle-container-wrapper">
							<div className="mvx-page-title">
								<p>
								{appLocalizer.commission_page_string.commission}
								</p>
								<div className="pull-right">
									<CSVLink
										data={this.state.commissiondata}
										headers={appLocalizer.commission_header}
										filename={'Commissions.csv'}
										className="mvx-btn btn-purple"
									>
										<i className="mvx-font icon-download"></i>
										{
											appLocalizer.global_string
												.download_csv
										}
									</CSVLink>
								</div>
							</div>

							<div className="mvx-search-and-multistatus-wrap">


								<ul className="mvx-multistatus-ul">
									<li className={`mvx-multistatus-item ${this.state.commission_list_status_all ? 'status-active' : ''}`}>
										<div
											className="mvx-multistatus-check-all"
											onClick={(e) =>
												this.handle_commission_status_check(
													e,
													'all'
												)
											}
										>
											{
												appLocalizer
													.commission_page_string.all
											}{' '}
											(
											{
												this.state
													.mvx_all_commission_list
													.length
											}
											)
										</div>
									</li>
									<li className="mvx-multistatus-item mvx-divider"></li>
									<li className={`mvx-multistatus-item ${this.state.commission_list_status_paid ? 'status-active' : ''}`}>
										<div
											className="mvx-multistatus-check-paid status-active"
											onClick={(e) =>
												this.handle_commission_status_check(
													e,
													'paid'
												)
											}
										>
											{
												appLocalizer
													.commission_page_string.paid
											}{' '}
											(
											{
												this.state.data_paid_commission
													.length
											}
											)
										</div>
									</li>
									<li className="mvx-multistatus-item mvx-divider"></li>
									<li className={`mvx-multistatus-item ${this.state.commission_list_status_unpaid ? 'status-active' : ''}`}>
										<div
											className="mvx-multistatus-check-unpaid"
											onClick={(e) =>
												this.handle_commission_status_check(
													e,
													'unpaid'
												)
											}
										>
											{
												appLocalizer
													.commission_page_string
													.unpaid
											}{' '}
											(
											{
												this.state
													.data_unpaid_commission
													.length
											}
											)
										</div>
									</li>
									<li className="mvx-multistatus-item mvx-divider"></li>
									<li className={`mvx-multistatus-item ${this.state.commission_list_status_refunded ? 'status-active' : ''}`}>
										<div
											className="mvx-multistatus-check-unpaid"
											onClick={(e) =>
												this.handle_commission_status_check(
													e,
													'refunded'
												)
											}
										>
											{
												appLocalizer
													.commission_page_string
													.refunded
											}{' '}
											(
											{
												this.state
													.data_refunded_commission
													.length
											}
											)
										</div>
									</li>
									<li className="mvx-multistatus-item mvx-divider"></li>
									<li className={`mvx-multistatus-item ${this.state.commission_list_status_trash ? 'status-active' : ''}`}>
										<div
											className="mvx-multistatus-check-unpaid"
											onClick={(e) =>
												this.handle_commission_status_check(
													e,
													'trash'
												)
											}
										>
											{
												appLocalizer
													.commission_page_string
													.trash
											}{' '}
											(
											{
												this.state
													.data_trash_commission
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
											appLocalizer.commission_page_string
												.search_commission
										}
										onChange={
											this.handle_commission_live_search
										}
									/>
								</div>
							</div>

							<div className="mvx-wrap-bulk-all-date">
								<Select
									placeholder={
										appLocalizer.commission_page_string
											.show_commission_status
									}
									options={appLocalizer.commission_status_list_action}
									isClearable={true}
									className="mvx-wrap-bulk-action"
									onChange={(e) =>
										this.handlecommissionsearch(
											e,
											'searchstatus'
										)
									}
								/>
								<Select
									placeholder={
										appLocalizer.commission_page_string
											.show_all_vendor
									}
									options={this.state.show_vendor_name}
									isClearable={true}
									className="mvx-wrap-bulk-action"
									onChange={(e) =>
										this.handlecommissionsearch(
											e,
											'showvendor'
										)
									}
								/>
								<Select
									placeholder={
										appLocalizer.commission_page_string
											.bulk_action
									}
									options={
										appLocalizer.commission_bulk_list_option
									}
									isClearable={true}
									className="mvx-wrap-bulk-action"
									onChange={(e) =>
										this.handlecommissionwork(e)
									}
								/>

								<DateRangePicker
									onChange={(e) => this.handleupdatereport(e)}
								/>
							</div>


							{
								this.state.datacommission.length > 0 && appLocalizer.total_earning_visible ? this.state.datacommission.map((data_earning, index_earning) => {
									total_net_earning_count += data_earning.edit_net_earning;
								}) : '',
								total_net_earning_count > 0 && appLocalizer.total_earning_visible ? <div className="total-net-earning"> Total Net Earning: {total_net_earning_count} </div> : ''
							}

							{this.state.columns_commission_list &&
							this.state.columns_commission_list.length > 0 &&
							this.state.commission_loading ? (
								<div className="mvx-backend-datatable-wrapper">
									<DataTable
										columns={
											this.state.columns_commission_list
										}
										data={this.state.datacommission}
										selectableRows
										onSelectedRowsChange={
											this.handleSelectRowsChange
										}
										pagination
									/>
								</div>
							) : (
								<PageLoader/>     
							)}
						</div>

						<BannerSection />
					</div>
				)}
			</div>
		);
	}
}
export default MVX_Backend_Commission;
