/* global appLocalizer */
import React, { Component } from 'react';
import axios from 'axios';
import { css } from '@emotion/react';
import PageLoader from './class-mvx-page-loader.js';
import { BrowserRouter as Router, useLocation } from 'react-router-dom';
import TabSection from './class-mvx-page-tab';
const override = css`
	display: block;
	margin: 0 auto;
	border-color: green;
`;
class MVX_Status_Tools extends Component {
	constructor(props) {
		super(props);
		this.state = {
			list_of_system_info: [],
			list_of_system_info_copy_data: '',
			store_index_data: [],
		};
		this.QueryParamsDemo = this.QueryParamsDemo.bind(this);
		this.useQuery = this.useQuery.bind(this);
		this.Child = this.Child.bind(this);
		this.handle_tools_triggers = this.handle_tools_triggers.bind(this);
		this.open_closed_system_info = this.open_closed_system_info.bind(this);
	}

	open_closed_system_info(e, index, parent_index) {
		const set_index_data = this.state.store_index_data;
		set_index_data[parent_index] =
			set_index_data[parent_index] === 'false' ? 'true' : 'false';

		this.setState({
			store_index_data: set_index_data,
		});
	}

	handle_tools_triggers(e, type) {
		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/tools_funtion`,
			data: {
				type,
			},
		}).then((responce) => {
			if (responce.data.redirect_link) {
				window.location.href = responce.data.redirect_link;
			}
		});
	}

	componentDidMount() {
		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/fetch_system_info`)
			.then((response) => {
				const store_index_data = [];
				if (response.data) {
					Object.entries(response.data).map(
						(list_data, index_data) =>
							(store_index_data[index_data] = 'false')
					);
				}
				this.setState({
					list_of_system_info: response.data,
					store_index_data,
				});
			});

		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/system_info_copy_data`)
			.then((responsecopy) => {
				this.setState({
					list_of_system_info_copy_data: responsecopy.data,
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
				model={appLocalizer.mvx_all_backend_tab_list['status-tools']}
				query_name={use_query.get('name')}
				funtion_name={this}
				tab_description="no"
				horizontally
			/>
		);
	}

	Child({ name }) {
		return (
			(name = !name
				? appLocalizer.mvx_all_backend_tab_list['status-tools'][0]
						.modulename
				: name),
			name === 'version-control' ? (
				''
			) : name ===
			  appLocalizer.mvx_all_backend_tab_list['status-tools'][0]
					.modulename ? (
				<div className="mvx-status-database-tools-content">
					{appLocalizer.status_and_tools_string['database-tools'].map(
						(list_tools, index_tools) => (
							<div className="mvx-vendor-transients">
								<div className="mvx-text-with-line-wrapper">
									<div className="mvx-text-with-right-side-line">
										{list_tools.headline_text}
									</div>

								</div>
								<div className="mvx-vendor-transients-description">
									{list_tools.description_text}
								</div>
								<div className="mvx-vendor-transients-button">
									<button
										type="button"
										className="mvx-btn btn-border"
										onClick={(e) =>
											this.handle_tools_triggers(
												e,
												list_tools.key
											)
										}
									>
										{list_tools.name}
									</button>
								</div>
							</div>
						)
					)}
				</div>
			) : name ===
			  appLocalizer.mvx_all_backend_tab_list['status-tools'][1]
					.modulename ? (
				<div className="mvx-status-tools-content">
				
					<header>
						<h3>
							{
								appLocalizer.status_and_tools_string[
									'system-info'
								]
							}
						</h3>
					</header>

					{this.state.list_of_system_info_copy_data ? (
						<div className="site-health-copy-buttons">
							<button
								type="button"
								className="mvx-btn btn-border copy-button"
								data-clipboard-text={
									this.state.list_of_system_info_copy_data
								}
							>
								{
									appLocalizer.status_and_tools_string[
										'copy-system-info'
									]
								}
							</button>
							<span className="success hidden" aria-hidden="true">
								{appLocalizer.status_and_tools_string.copied}
							</span>
						</div>
					) : (
						<PageLoader/>     
					)}

					{Object.entries(this.state.list_of_system_info).length > 0
						? Object.entries(this.state.list_of_system_info).map(
								(list_data, index_data) => (
									<div
										id="health-check-debug"
										className="health-check-accordion"
									>
										<h3 className="health-check-accordion-heading">
											<button
												aria-expanded={
													this.state.store_index_data
														.length > 0 &&
													this.state.store_index_data[
														index_data
													] === 'false'
														? 'false'
														: 'true'
												}
												className="health-check-accordion-trigger"
												aria-controls={`health-check-accordion-block-${list_data[0]}`}
												type="button"
												onClick={(e) =>
													this.open_closed_system_info(
														e,
														list_data[0],
														index_data
													)
												}
											>
												<span className="title">
													{list_data[1].label}
													{list_data[1].show_count ? (
														<span>
															(
															{
																Object.entries(
																	list_data[1]
																		.fields
																).length
															}
															)
														</span>
													) : (
														''
													)}
												</span>
												<span className="icon" />
											</button>
										</h3>

										<div
											id={`health-check-accordion-block-${list_data[0]}`}
											className="health-check-accordion-panel"
											hidden={
												this.state.store_index_data
													.length > 0 &&
												this.state.store_index_data[
													index_data
												] === 'false'
													? 'hidden'
													: ''
											}
										>
											{list_data[1].description
												? list_data[1].description
												: ''}
											<table
												className="widefat striped health-check-table"
												role="presentation"
											>
												<tbody>
													{Object.entries(
														list_data[1].fields
													).map(
														(
															list_data1,
															index_data1
														) => (
															<tr>
																<td>
																	{
																		list_data1[1]
																			.label
																	}
																</td>
																<td>
																	{
																		list_data1[1]
																			.value
																	}
																</td>
															</tr>
														)
													)}
												</tbody>
											</table>
										</div>
									</div>
								)
						  )
						: ''}

					<br/>
					<header>
						<h3>
							{appLocalizer.status_and_tools_string['error-log']}
						</h3>
					</header>

					<p>{appLocalizer.status_and_tools_string['copied-text']}</p>
						{ appLocalizer.errors_log ? 
							appLocalizer.errors_log == 1 ? 
							<p>
								<h4>{appLocalizer.status_and_tools_string.large_size_log}</h4>
							</p>
							:
							<>
								<div className="site-health-copy-buttons">
									<button
										type="button"
										className="mvx-btn btn-border copy-button"
										data-clipboard-text={appLocalizer.errors_log}
									>
										{
											appLocalizer.status_and_tools_string['copy-log-clipboard']
										}
									</button>
									<span className="success hidden" aria-hidden="true">
										{appLocalizer.status_and_tools_string.copied}
									</span>
								</div>
								<textarea
									name="name"
									rows="10"
									cols="80"
									disabled="disabled"
								>
									{appLocalizer.errors_log}
								</textarea>
							</>
						: ''											
					}
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
export default MVX_Status_Tools;