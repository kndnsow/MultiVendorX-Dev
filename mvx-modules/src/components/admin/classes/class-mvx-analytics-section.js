/* global appLocalizer */
import React, { Component } from 'react';
import axios from 'axios';
import Select from 'react-select';
import PageLoader from './class-mvx-page-loader.js';
import { css } from '@emotion/react';
import { BrowserRouter as Router, Link, useLocation } from 'react-router-dom';
import DateRangePicker from 'rsuite/DateRangePicker';
import DataTable from 'react-data-table-component';
import TabSection from './class-mvx-page-tab';
import {
	LineChart,
	ResponsiveContainer,
	Legend,
	Tooltip,
	Line,
	XAxis,
	YAxis,
	CartesianGrid,
	BarChart,
	Bar,
} from 'recharts';
import { CSVLink } from 'react-csv';
const override = css`
	display: block;
	margin: 0 auto;
	border-color: green;
`;

class MVX_Analytics extends Component {
	constructor(props) {
		super(props);
		this.state = {
			report_overview_data: [],
			report_distribution_overview_data: [],
			vendor_loading: false,
			datacommission: [],
			details_vendor: [],
			dataproductchart: [],
			store_date: '',
			store_product_select: '',
			store_vendor_select: '',
			product_report_chart_data: [],
			vendor_report_chart_data: [],
			columns_sales_distribution_list: [],
			current_sales_distribution: [],
			columns_product: [
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics10}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.title }}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics11}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{
								__html: row.admin_earning,
							}}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics12}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{
								__html: row.vendor_earning,
							}}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics13}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.gross }}
						></div>
					),
					sortable: true,
				},
			],
			columns_vendor: [
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics14}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.title }}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics11}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{
								__html: row.admin_earning,
							}}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics12}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{
								__html: row.vendor_earning,
							}}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics13}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.gross }}
						></div>
					),
					sortable: true,
				},
			],
			columns_commission: [
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics15}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{
								__html: row.commission_id,
							}}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics16}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.order_id }}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics17}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.product }}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics6}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.vendor }}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics18}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.amount }}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics19}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{
								__html: row.net_earning,
							}}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics20}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.status }}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics9}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.date }}
						></div>
					),
					sortable: true,
				},
			],
			columns_transaction: [
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics20}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.status }}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics9}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.date }}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics21}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.type }}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics22}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{
								__html: row.reference_id,
							}}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics23}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.Credit }}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics24}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.Debit }}
						></div>
					),
					sortable: true,
				},
				{
					name: (
						<div className="mvx-datatable-header-text">
							{appLocalizer.analytics_page_string.analytics25}
						</div>
					),
					selector: (row) => (
						<div
							dangerouslySetInnerHTML={{ __html: row.balance }}
						></div>
					),
					sortable: true,
				},
			],
		};

		this.QueryParamsDemo = this.QueryParamsDemo.bind(this);
		this.useQuery = this.useQuery.bind(this);
		this.Child = this.Child.bind(this);
		this.handleupdatereport = this.handleupdatereport.bind(this);
		this.handleChangeproduct_char_list =
			this.handleChangeproduct_char_list.bind(this);
		this.handlevendorsearch = this.handlevendorsearch.bind(this);
		this.handleReportStatus = this.handleReportStatus.bind(this);
		this.handleproductsearch = this.handleproductsearch.bind(this);
		this.handleChangevendor_char_list =
			this.handleChangevendor_char_list.bind(this);
		this.handleSelectRowsChangeSales = this.handleSelectRowsChangeSales.bind(this);
	}

	handleSelectRowsChangeSales(e) {
		const sales_distribution_list = [];
		e.selectedRows.map((data, index) => {
			sales_distribution_list[index] = data.id;
		});
		
		axios
		.get(
			`${appLocalizer.apiUrl}/mvx_analytics/v1/fetch_csv_content`,
			{
				params: { sales_distribution_list: sales_distribution_list },
			}
		)
		.then((response) => {
			this.setState({
				current_sales_distribution: response.data,
			});
		});
	}

	handleproductsearch(e) {
		this.setState({
			store_product_select: e.value,
		});

		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/get_report_overview_data`,
			data: {
				value: this.state.store_date,
				product: e.value,
			},
		}).then((responce) => {
			this.setState({
				report_overview_data: responce.data,
			});
		});
	}

	handlevendorsearch(e) {
		this.setState({
			store_vendor_select: e.value,
		});

		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/get_report_overview_data`,
			data: {
				value: this.state.store_date,
				product: this.state.store_product_select,
				vendor: e.value,
			},
		}).then((responce) => {
			this.setState({
				report_overview_data: responce.data,
			});
		});
	}

	handleDistributionVendorsearch(e) {
		this.setState({
			store_vendor_select: e.value,
		});

		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/get_report_overview_data`,
			data: {
				value: this.state.store_date,
				product: this.state.store_product_select,
				vendor: e.value,
			},
		}).then((responce) => {
			this.setState({
				report_distribution_overview_data: responce.data,
			});
		});
	}

	handleReportStatus(e) {
		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/get_report_overview_data`,
			data: {
				value: this.state.store_date,
				product: this.state.store_product_select,
				vendor: this.state.store_vendor_select,
				status_sales: e.value,
			},
		}).then((responce) => {
			this.setState({
				report_distribution_overview_data: responce.data,
			});
		});
	}

	handleChangeproduct_char_list(e) {
		const list_product_chart_list = [];
		e.selectedRows.map((data, index) => {
			list_product_chart_list[index] = data;
		});

		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/export_csv_for_report_product_chart`,
			data: {
				product_list: list_product_chart_list,
			},
		}).then((response) => {
			this.setState({
				product_report_chart_data: response.data,
			});
		});
	}

	handleChangevendor_char_list(e) {
		const list_vendor_chart_list = [];
		e.selectedRows.map((data, index) => {
			list_vendor_chart_list[index] = data;
		});

		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/export_csv_for_report_vendor_chart`,
			data: {
				vendor_list: list_vendor_chart_list,
			},
		}).then((response) => {
			this.setState({
				vendor_report_chart_data: response.data,
			});
		});
	}

	handleupdatereport(e) {
		this.setState({
			store_date: e,
		});

		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/get_report_overview_data`,
			data: {
				value: e,
				product: this.state.store_product_select,
				vendor: this.state.store_vendor_select,
			},
		}).then((responce) => {
			this.setState({
				report_overview_data: responce.data,
			});
		});
	}

	componentDidMount() {
		const formatter = (value) => `$${value}`;
		this.setState({
			formatter,
		});

		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/fetch_report_overview_data`
			)
			.then((response) => {
				this.setState({
					report_overview_data: response.data,
					report_distribution_overview_data: response.data,
					vendor_loading: true,
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
			url: `${appLocalizer.apiUrl}/mvx_module/v1/all_commission`,
		}).then((response) => {
			this.setState({
				datacommission: response.data,
			});
		});
	}

	useQuery() {
		return new URLSearchParams(useLocation().hash);
	}

	QueryParamsDemo() {
		const use_query = this.useQuery();
		return (
			<TabSection
				model={
					appLocalizer.mvx_all_backend_tab_list[
						'marketplace-analytics'
					]
				}
				query_name={use_query.get('name')}
				funtion_name={this}
				tab_description="no"
				horizontally
			/>
		);
	}

	Child({ name }) {

		var admin_net = 0;
		var vendor_commission = 0;
		var vendor_net_commission = 0;
		var order_sub_total = 0;
		var shipping = 0;

		if (
			this.state.columns_sales_distribution_list.length === 0 &&
			new URLSearchParams(window.location.hash).get('name') ===
				'sales-distribution'
		) {
			appLocalizer.columns_sales_distribution ? appLocalizer.columns_sales_distribution.map((data_report, index_report) => {
				let data_selector_r = '';
				let set_for_dynamic_column_r = '';
				data_selector_r = data_report.selector_choice;
				data_report.selector = (row) => (
					<div
						dangerouslySetInnerHTML={{ __html: row[data_selector_r] }}
					></div>
				);

				this.state.columns_sales_distribution_list[index_report] = data_report;
				set_for_dynamic_column_r = this.state.columns_sales_distribution_list;
				this.setState({
					columns_sales_distribution_list: set_for_dynamic_column_r,
				});
			}) : '';
		}


		return (
			(name = !name
				? appLocalizer.mvx_all_backend_tab_list[
						'marketplace-analytics'
				  ][0].modulename
				: name),
			name ==
			appLocalizer.mvx_all_backend_tab_list['marketplace-analytics'][0]
				.modulename ? (
				<div className="mvx-report-start-content">
					<div className="mvx-wrapper-date-picker">
						<div className="mvx-date-range">
							{appLocalizer.analytics_page_string.analytics1}:
						</div>
						<div className="mvx-report-datepicker">
							<DateRangePicker
								onChange={(e) => this.handleupdatereport(e)}
							/>
						</div>
					</div>

					<div className="mvx-report-performance-content">
					<div className="mvx-text-with-right-side-line-wrapper">

						<div className="mvx-text-with-right-side-line">
							{appLocalizer.report_page_string.performance}
						</div>
						<hr role="presentation"></hr>
						</div>
						{this.state.vendor_loading ? (
							<div className="mvx-wrapper-performance-content">
								{this.state.report_overview_data.admin_overview
									? Object.entries(
											this.state.report_overview_data
												.admin_overview
									  ).map((data, index) =>
											data[0] &&
											data[0] != 'sales_data_chart' ? (
												<div className="mvx-performance-wrapper-content">
													<div className="mvx-labels">
														{data[1].label}
													</div>
													<div className="mvx-wrap-price-and-percent">
														<div
															className="mvx-price-display"
															dangerouslySetInnerHTML={{
																__html: data[1]
																	.value,
															}}
														></div>
													</div>
												</div>
											) : (
												''
											)
									  )
									: ''}
							</div>
						) : (
							<PageLoader/>    
						)}
					</div>

					{this.state.report_overview_data.admin_overview &&
					this.state.report_overview_data.admin_overview
						.sales_data_chart ? (
						<div className="mvx-charts-graph-content">
							<div className="mvx-chart-text-and-bar-line-wrap">
									<div className="mvx-text-with-right-side-line">
										{appLocalizer.report_page_string.charts}
									</div>
										<hr role="presentation"></hr>

									<div className="mvx-select-all-bulk-wrap">
										<div className="mvx-analytics-overview-link">
											<Link
												to={`?page=mvx#&submenu=analytics&name=admin-overview&type=bar`}
											>
												<i className="mvx-font icon-chart-bar"></i>
											</Link>
										</div>
										<div className="mvx-analytics-overview-link">
											<Link
												to={`?page=mvx#&submenu=analytics&name=admin-overview&type=line`}
											>
												<i className="mvx-font icon-chart-line"></i>
											</Link>
										</div>
									</div>
								<div className="mvx-bar-and-line-wrap hide"></div>
							</div>

							<div className="mvx-content-two-graph-wrap">
								<div className="mvx-header-and-graph-wrap">
									<div className="mvx-box-background-white-wrapper">
										{
											appLocalizer.analytics_page_string
												.analytics2
										}
									</div>
									<div className="mvx-chart-graph-visible">
										{!this.useQuery().get('type') ||
										this.useQuery().get('type') ==
											'line' ? (
											<ResponsiveContainer aspect={3}>
												<LineChart
													width={500}
													height={300}
													data={
														this.state
															.report_overview_data
															.admin_overview
															.sales_data_chart
													}
													margin={{
														top: 100,
														right: 30,
														left: 20,
														bottom: 5,
													}}
												>
													<CartesianGrid strokeDasharray="3 3" />
													<XAxis dataKey="name" />
													<YAxis
														tickFormatter={
															this.state.formatter
														}
													/>
													<Tooltip />
													<Legend />
													<Line
														type="monotone"
														dataKey={
															appLocalizer
																.analytics_page_string
																.analytics2
														}
														stroke="red"
														activeDot={{ r: 8 }}
													/>
												</LineChart>
											</ResponsiveContainer>
										) : (
											<ResponsiveContainer aspect={3}>
												<BarChart
													width={500}
													height={300}
													data={
														this.state
															.report_overview_data
															.admin_overview
															.sales_data_chart
													}
													margin={{
														top: 5,
														right: 30,
														left: 20,
														bottom: 5,
													}}
												>
													<CartesianGrid strokeDasharray="3 3" />
													<XAxis
														dataKey={
															appLocalizer
																.analytics_page_string
																.analytics9
														}
													/>
													<YAxis
														tickFormatter={
															this.state.formatter
														}
													/>
													<Tooltip />
													<Legend />
													<Bar
														dataKey={
															appLocalizer
																.analytics_page_string
																.analytics2
														}
														fill="red"
													/>
												</BarChart>
											</ResponsiveContainer>
										)}
									</div>
								</div>
							</div>
						</div>
					) : (
						''
					)}

					<div className="mvx-report-leaderboard-content">
					<div className="mvx-text-with-right-side-line-wrapper">
						<div className="mvx-text-with-right-side-line">
							{appLocalizer.report_page_string.leaderboards}
						</div>
						<hr role="presentation"></hr>
					</div>

						<div className="mvx-analytic-details-wrap">
							<div className="mvx-box-background-white-wrapper">
								{appLocalizer.analytics_page_string.analytics8}
							</div>
							{this.state.vendor_loading ? (
								<div className="mvx-backend-datatable-wrapper">
									<DataTable
										columns={this.state.columns_vendor}
										data={
											this.state.report_overview_data
												.vendor
												? this.state
														.report_overview_data
														.vendor
														.vendor_report_datatable
												: this.state.dataproductchart
										}
										selectableRows
										pagination
									/>
								</div>
							) : (
								<PageLoader/>    
							)}
						</div>

						<div className="mvx-analytic-details-wrap">
							<div className="mvx-box-background-white-wrapper">
								{appLocalizer.analytics_page_string.analytics7}
							</div>

							{this.state.vendor_loading ? (
								<div className="mvx-backend-datatable-wrapper">
									<DataTable
										columns={this.state.columns_commission}
										data={this.state.datacommission}
										selectableRows
										pagination
									/>
								</div>
							) : (
								<PageLoader/>    
							)}
						</div>
					</div>
				</div>
			) : name ==
			  appLocalizer.mvx_all_backend_tab_list['marketplace-analytics'][1]
					.modulename ? (
				<div className="mvx-report-start-content">
					<div className="mvx-date-and-show-wrapper">
						<div className="mvx-wrapper-date-picker">
							<div className="mvx-date-range">
								{appLocalizer.analytics_page_string.analytics1}:
							</div>
							<div className="mvx-report-datepicker">
								<DateRangePicker
									onChange={(e) => this.handleupdatereport(e)}
								/>
							</div>
						</div>
						<div className="mvx-vendor-wrapper-show-specific">
							<div className="mvx-date-range">
								{appLocalizer.analytics_page_string.analytics3}:
							</div>
							<Select
								placeholder={
									appLocalizer.report_page_string
										.choose_vendor
								}
								options={this.state.details_vendor}
								isClearable={true}
								className="mvx-wrap-bulk-action"
								onChange={(e) => this.handlevendorsearch(e)}
							/>
						</div>
					</div>

					<div className="mvx-report-performance-content">
					<div className="mvx-text-with-right-side-line-wrapper">
						<div className="mvx-text-with-right-side-line">
							{appLocalizer.report_page_string.performance}
						</div>
						<hr role="presentation"></hr>
						</div>

						<div className="mvx-wrapper-performance-content">
							{this.state.report_overview_data.admin_overview
								? Object.entries(
										this.state.report_overview_data.vendor
								  ).map((data, index) =>
										data[0] &&
										data[0] != 'sales_data_chart' ? (
											data[0] !=
											'vendor_report_datatable' ? (
												<div className="mvx-performance-wrapper-content">
													<div>{data[1].label}</div>
													<div className="mvx-wrap-price-and-percent">
														<div
															className="mvx-price-display"
															dangerouslySetInnerHTML={{
																__html: data[1]
																	.value,
															}}
														></div>
													</div>
												</div>
											) : (
												''
											)
										) : (
											''
										)
								  )
								: ''}
						</div>
					</div>

					{this.state.report_overview_data.vendor &&
					this.state.report_overview_data.vendor.sales_data_chart ? (
						<div className="mvx-charts-graph-content">
							<div className="mvx-chart-text-and-bar-line-wrap">
									<div className="mvx-text-with-right-side-line">
										{appLocalizer.report_page_string.charts}
									</div>
										<hr role="presentation"></hr>

									<div className="mvx-select-all-bulk-wrap">
										<div className="mvx-bar-chart">
											<Link
												to={`?page=mvx#&submenu=analytics&name=vendor&type=bar`}
											>
												<i className="mvx-font icon-chart-bar"></i>
											</Link>
										</div>
										<div className="mvx-line-chart">
											<Link
												to={`?page=mvx#&submenu=analytics&name=vendor&type=line`}
											>
												<i className="mvx-font icon-chart-line"></i>
											</Link>
										</div>
									</div>
							</div>

							<div className="mvx-chart-graph-visible">
								{!this.useQuery().get('type') ||
								this.useQuery().get('type') === 'line' ? (
									<ResponsiveContainer
										width="100%"
										height="100%"
										aspect={3}
									>
										<LineChart
											width={500}
											height={300}
											data={
												this.state.report_overview_data
													.vendor.sales_data_chart
											}
											margin={{
												top: 100,
												right: 30,
												left: 20,
												bottom: 5,
											}}
										>
											<CartesianGrid strokeDasharray="3 3" />
											<XAxis
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics9
												}
											/>
											<YAxis
												tickFormatter={
													this.state.formatter
												}
											/>
											<Tooltip />
											<Legend />
											<Line
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics2
												}
												stroke="red"
												activeDot={{ r: 8 }}
											/>

											<Line
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics4
												}
												stroke="black"
												activeDot={{ r: 8 }}
											/>

											<Line
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics5
												}
												stroke="green"
												activeDot={{ r: 8 }}
											/>
										</LineChart>
									</ResponsiveContainer>
								) : (
									<ResponsiveContainer
										width="100%"
										height="100%"
										aspect={3}
									>
										<BarChart
											width={500}
											height={300}
											data={
												this.state.report_overview_data
													.vendor.sales_data_chart
											}
											margin={{
												top: 5,
												right: 30,
												left: 20,
												bottom: 5,
											}}
										>
											<CartesianGrid strokeDasharray="3 3" />
											<XAxis
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics9
												}
											/>
											<YAxis
												tickFormatter={
													this.state.formatter
												}
											/>
											<Tooltip />
											<Legend />
											<Bar
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics2
												}
												fill="red"
											/>

											<Bar
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics4
												}
												fill="black"
											/>

											<Bar
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics5
												}
												fill="green"
											/>
										</BarChart>
									</ResponsiveContainer>
								)}
							</div>
						</div>
					) : (
						''
					)}

					<div className="mvx-report-csv-and-chart">
						{this.state.report_overview_data.vendor &&
						this.state.report_overview_data.vendor
							.vendor_report_datatable ? (
							<div className="mvx-text-with-line-wrapper">
								<div className="mvx-text-with-right-side-line">
									{
										appLocalizer.analytics_page_string
											.analytics6
									}
								</div>
						<hr role="presentation"></hr>

								<CSVLink
									data={this.state.vendor_report_chart_data}
									headers={appLocalizer.report_vendor_header}
									filename={'Report_vendor.csv'}
									className="mvx-btn btn-purple"
								>
									<i className="mvx-font icon-download"></i>
									{
										appLocalizer.report_page_string
											.download_csv
									}
								</CSVLink>
							</div>
						) : (
							''
						)}
						<div className="mvx-backend-datatable-wrapper">
							<DataTable
								columns={this.state.columns_vendor}
								data={
									this.state.report_overview_data.vendor
										? this.state.report_overview_data.vendor
												.vendor_report_datatable
										: this.state.dataproductchart
								}
								selectableRows
								onSelectedRowsChange={
									this.handleChangevendor_char_list
								}
								pagination
							/>
						</div>
					</div>
				</div>
			) : name ==
			  appLocalizer.mvx_all_backend_tab_list['marketplace-analytics'][2]
					.modulename ? (
				<div className="mvx-report-start-content">
					<div className="mvx-date-and-show-wrapper">
						<div className="mvx-wrapper-date-picker">
							<div className="mvx-date-range">
								{appLocalizer.analytics_page_string.analytics1}:
							</div>
							<div className="mvx-report-datepicker">
								<DateRangePicker
									onChange={(e) => this.handleupdatereport(e)}
								/>
							</div>
						</div>

						<div className="mvx-vendor-wrapper-show-specific">
							<div className="mvx-date-range">
								{appLocalizer.analytics_page_string.analytics3}:
							</div>
							<Select
								placeholder={
									appLocalizer.report_page_string
										.choose_product
								}
								options={appLocalizer.question_product_selection_wordpboard}
								isClearable={true}
								className="mvx-wrap-bulk-action"
								onChange={(e) => this.handleproductsearch(e)}
							/>
						</div>
					</div>

					<div className="mvx-report-performance-content">
					<div className="mvx-text-with-right-side-line-wrapper">
						<div className="mvx-text-with-right-side-line">
							{appLocalizer.report_page_string.performance}
						</div>
						<hr role="presentation"></hr>
									</div>

						<div className="mvx-wrapper-performance-content">
							{this.state.report_overview_data.admin_overview
								? Object.entries(
										this.state.report_overview_data.product
								  ).map((data, index) =>
										data && data[1].label ? (
											<div className="mvx-performance-wrapper-content">
												<div>{data[1].label}</div>
												<div className="mvx-wrap-price-and-percent">
													<div
														className="mvx-price-display"
														dangerouslySetInnerHTML={{
															__html: data[1]
																.value,
														}}
													></div>
												</div>
											</div>
										) : (
											''
										)
								  )
								: ''}
						</div>
					</div>

					{this.state.report_overview_data.product &&
					this.state.report_overview_data.product.sales_data_chart ? (
						<div className="mvx-charts-graph-content">
							<div className="mvx-chart-text-and-bar-line-wrap">
								<div className="mvx-text-with-right-side-line">
									{appLocalizer.report_page_string.charts}
								</div>
								<hr role="presentation"></hr>

								<div className="mvx-select-all-bulk-wrap">
									<div className="mvx-bar-chart">
										<Link
											to={`?page=mvx#&submenu=analytics&name=product&type=bar`}
										>
											<i className="mvx-font icon-chart-bar"></i>
										</Link>
									</div>
									<div className="mvx-line-chart">
										<Link
											to={`?page=mvx#&submenu=analytics&name=product&type=line`}
										>
											<i className="mvx-font icon-chart-line"></i>
										</Link>
									</div>
								</div>
							</div>

							<div className="mvx-chart-graph-visible">
								{!this.useQuery().get('type') ||
								this.useQuery().get('type') === 'line' ? (
									<ResponsiveContainer aspect={3}>
										<LineChart
											data={
												this.state.report_overview_data
													.product.sales_data_chart
											}
											margin={{
												top: 100,
												right: 30,
												left: 20,
												bottom: 5,
											}}
										>
											<CartesianGrid strokeDasharray="3 3" />
											<XAxis
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics9
												}
											/>
											<YAxis
												tickFormatter={
													this.state.formatter
												}
											/>
											<Tooltip />
											<Legend />
											<Line
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics2
												}
												stroke="red"
												activeDot={{ r: 8 }}
											/>

											<Line
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics4
												}
												stroke="black"
												activeDot={{ r: 8 }}
											/>

											<Line
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics5
												}
												stroke="green"
												activeDot={{ r: 8 }}
											/>
										</LineChart>
									</ResponsiveContainer>
								) : (
									<ResponsiveContainer
										width="100%"
										height="100%"
										aspect={3}
									>
										<BarChart
											width={500}
											height={300}
											data={
												this.state.report_overview_data
													.product.sales_data_chart
											}
											margin={{
												top: 5,
												right: 30,
												left: 20,
												bottom: 5,
											}}
										>
											<CartesianGrid strokeDasharray="3 3" />
											<XAxis
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics9
												}
											/>
											<YAxis
												tickFormatter={
													this.state.formatter
												}
											/>
											<Tooltip />
											<Legend />
											<Bar
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics2
												}
												fill="red"
											/>
											<Bar
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics4
												}
												fill="black"
											/>
											<Bar
												dataKey={
													appLocalizer
														.analytics_page_string
														.analytics5
												}
												fill="green"
											/>
										</BarChart>
									</ResponsiveContainer>
								)}
							</div>
						</div>
					) : (
						''
					)}

					<div className="mvx-report-csv-and-chart">
						{this.state.report_overview_data.product &&
						this.state.report_overview_data.product
							.product_report_datatable ? (
							<div className="mvx-text-with-line-wrapper">
								<div className="mvx-text-with-right-side-line">
									{
										appLocalizer.analytics_page_string
											.analytics26
									}
								</div>
						<hr role="presentation"></hr>

								<CSVLink
									data={this.state.product_report_chart_data}
									headers={appLocalizer.report_product_header}
									filename={'Report_product.csv'}
									className="mvx-btn btn-purple"
								>
									<i className="mvx-font icon-download"></i>
									{
										appLocalizer.report_page_string
											.download_csv
									}
								</CSVLink>
							</div>
						) : (
							''
						)}
						<div className="mvx-backend-datatable-wrapper">
							<DataTable
								columns={this.state.columns_product}
								data={
									this.state.report_overview_data.product
										? this.state.report_overview_data
												.product
												.product_report_datatable
										: this.state.dataproductchart
								}
								selectableRows
								onSelectedRowsChange={
									this.handleChangeproduct_char_list
								}
								pagination
							/>
						</div>
					</div>
				</div>
			) : name ==
			  appLocalizer.mvx_all_backend_tab_list['marketplace-analytics'][3]
					.modulename ? (
				<div className="mvx-report-start-content">
					<div className="mvx-date-and-show-wrapper">
						<div className="mvx-wrapper-date-picker">
							<div className="mvx-date-range">
								{appLocalizer.analytics_page_string.analytics1}:
							</div>
							<div className="mvx-report-datepicker">
								<DateRangePicker
									onChange={(e) => this.handleupdatereport(e)}
								/>
							</div>
						</div>

						<div className="mvx-vendor-wrapper-show-specific">
							<div className="mvx-date-range">
								{appLocalizer.report_page_string.vendor_select}
							</div>
							{this.state.details_vendor.length > 0 ?
								<Select
									placeholder={
										appLocalizer.report_page_string
											.choose_vendor
									}
									defaultValue={this.state.details_vendor[0]}
									options={this.state.details_vendor}
									isClearable={true}
									className="mvx-wrap-bulk-action"
									onChange={(e) => this.handlevendorsearch(e)}
								/>
							: ''}
						</div>	
					</div>

					<div className="mvx-backend-datatable-wrapper">
						<DataTable
							columns={this.state.columns_transaction}
							data={
								this.state.report_overview_data.banking_overview
									? this.state.report_overview_data
											.banking_overview
									: this.state.dataproductchart
							}
							selectableRows
							pagination
						/>
					</div>
				</div>
			) : name ==
			  appLocalizer.mvx_all_backend_tab_list['marketplace-analytics'][4]
					.modulename ? (
				<div className="mvx-report-start-content">
					{
						this.state.report_distribution_overview_data.sales_distribution && appLocalizer.total_sales_distribution_visible ? this.state.report_distribution_overview_data.sales_distribution.map((data_earning, index_earning) => {
							admin_net += data_earning.raw_data.admin_earning;
							vendor_commission += data_earning.raw_data.commission;
							order_sub_total += data_earning.raw_data.total_sales;
							shipping += data_earning.raw_data.shipping_amount;
							vendor_net_commission += data_earning.raw_data.v_net_commission;
						}) : ''
					}
					{
						appLocalizer.total_sales_distribution_visible ? <div className="total-net-earning"> 
						Total Admin Net Earning: {admin_net} 
						<br/>
						Total Vendor Commission: {vendor_commission}
						<br/>
						Total Vendor Net Commission: {vendor_net_commission}
						<br/>
						Total Sub Total: {order_sub_total}
						<br/>
						Shipping: {shipping}
						<br/>
						</div> 
						: ''
					}

					<div className="mvx-date-and-show-wrapper">
						<div className="mvx-wrapper-date-picker">
							<div className="mvx-date-range">
								{appLocalizer.analytics_page_string.analytics1}:
							</div>
							<div className="mvx-report-datepicker">
								<DateRangePicker
									onChange={(e) => this.handleupdatereport(e)}
								/>
							</div>
						</div>

						<div className="mvx-vendor-wrapper-show-specific">
							<div className="mvx-date-range">
								{appLocalizer.report_page_string.vendor_select}
							</div>
							{this.state.details_vendor.length > 0 ?
								<Select
									placeholder={
										appLocalizer.report_page_string
											.choose_vendor
									}
									defaultValue={this.state.details_vendor[0]}
									options={this.state.details_vendor}
									isClearable={false}
									className="mvx-wrap-bulk-action"
									onChange={(e) => this.handleDistributionVendorsearch(e)}
								/>
							: ''}
						</div>

						<div className="mvx-vendor-wrapper-show-specific">
							<div className="mvx-date-range">
								{appLocalizer.report_page_string.status_choose}
							</div>
							{appLocalizer.status_list.length > 0 ?
								<Select
									options={appLocalizer.status_list}
									isClearable={false}
									className="mvx-wrap-bulk-action"
									onChange={(e) => this.handleReportStatus(e)}
								/>
							: ''}
						</div>

						{this.state.current_sales_distribution ? 
						<CSVLink
							data={this.state.current_sales_distribution}
							headers={appLocalizer.sals_distribution_header}
							filename={'sales.csv'}
							className="mvx-btn btn-purple"
						>
							<i className="mvx-font icon-download"></i>
							{
								appLocalizer.global_string
									.download_csv
							}
						</CSVLink>	
						: '' }
					</div>

					<div className="mvx-backend-datatable-wrapper">
					{this.state.report_distribution_overview_data.sales_distribution ? 
						<DataTable
							columns={this.state.columns_sales_distribution_list}
							data={
								this.state.report_distribution_overview_data.sales_distribution
							}
							selectableRows
							onSelectedRowsChange={
								this.handleSelectRowsChangeSales
							}
							pagination
						/>
						: 'Not Enough Data' }
					</div>
				</div>

			) : (
				''
			)
		);
	}

	render() {
		return (
			<this.QueryParamsDemo />
		);
	}
}
export default MVX_Analytics;
