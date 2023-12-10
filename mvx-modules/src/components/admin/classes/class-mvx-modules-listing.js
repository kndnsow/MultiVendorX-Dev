/* global appLocalizer */
import React, { Component } from 'react';
import axios from 'axios';
import Select from 'react-select';
import PageLoader from './class-mvx-page-loader.js';
import { css } from '@emotion/react';
import Button from '@mui/material/Button';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import HeaderSection from './class-mvx-page-header';
import BannerSection from './class-mvx-page-banner';

const override = css`
	display: block;
	margin: 0 auto;
	border-color: red;
`;

class MVX_Module_Listing extends Component {
	constructor(props) {
		super(props);
		this.state = {
			items: [],
			open_model: false,
			open_model_dynamic: [],
			total_number_of_module: 0,
			total_number_of_active_module: 0,
			total_number_of_deactive_module: 0,
			module_total_button: false,
			module_active_button: false,
			module_inactive_button: false,
		};
		// when click on checkbox
		this.handleOnChange = this.handleOnChange.bind(this);
		// popup close for paid module
		this.handleClose = this.handleClose.bind(this);
		// popup close for required plugin inactive popup
		this.handleClose_dynamic = this.handleClose_dynamic.bind(this);
		// search select module trigger
		this.handleModuleSearch = this.handleModuleSearch.bind(this);
		this.handleModuleSearchByCategory =
			this.handleModuleSearchByCategory.bind(this);
		this.mvx_search_different_module_status =
			this.mvx_search_different_module_status.bind(this);
	}

	mvx_search_different_module_status(status) {
		// multiple module status

		this.setState({
			module_total_button: status === 'all' ? true : false,
			module_active_button: status === 'active' ? true : false,
			module_inactive_button: status === 'inactive' ? true : false,
		});

		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/get_as_per_module_status`,
				{
					params: { module_status: status },
				}
			)
			.then((response) => {
				this.setState({
					items: response.data,
				});
			});
	}

	handleModuleSearch(e) {
		axios({
			url: `${appLocalizer.apiUrl}/mvx_module/v1/module_lists?module_id=${e.target.value}`,
		}).then((response) => {
			this.setState({
				items: response.data,
			});
		});
	}

	handleModuleSearchByCategory(e) {
		if (e) {
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/search_module_lists`,
					{
						params: { category: e.label },
					}
				)
				.then((response) => {
					this.setState({
						items: response.data,
					});
				});
		} else {
			Promise.all([
				fetch(
					`${appLocalizer.apiUrl}/mvx_module/v1/module_lists?module_id=all`
				).then((res) => res.json()),
			])
				.then(([product]) => {
					this.setState({
						items: product,
					});
				})
				.catch((error) => {});
		}
	}

	// popup close for paid module
	handleClose() {
		this.setState({
			open_model: false,
		});
	}
	// popup close for required plugin inactive popup
	handleClose_dynamic() {
		const add_module_false = new Array(this.state.items.length).fill(false);
		this.setState({
			open_model_dynamic: add_module_false,
		});
	}
	// when click on checkbox
	handleOnChange(
		event,
		tab,
		plan,
		is_plugin_active,
		doc_id,
		items,
		parent_index,
		sub_index,
		module_id
	) {
		if (plan === 'pro') {
			this.setState({
				open_model: true,
			});
		} else if (!is_plugin_active) {

		} else {
			// If everything works fine then checkbox trigger
			items[parent_index].options[sub_index].is_active =
				event.target.checked;

			this.setState({
				items,
			});

			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/checkbox_update`,
				data: {
					module_id,
					is_checked: event.target.checked,
				},
			}).then((res) => {
				if (res.data === 'reload') {
					location.reload();
				}
			});
		}
	}

	componentDidMount() {
		Promise.all([
			fetch(
				new URLSearchParams(window.location.hash).get('name') ? `${appLocalizer.apiUrl}/mvx_module/v1/module_lists?module_id=${new URLSearchParams(window.location.hash).get('name')}` : `${appLocalizer.apiUrl}/mvx_module/v1/module_lists?module_id=all`
			).then((res) => res.json()),
		])
			.then(([product]) => {
				this.setState({
					items: product,
				});
			})
			.catch((error) => {});
		// fetch total number of modules
		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/modules_count`)
			.then((response) => {
				this.setState({
					total_number_of_module: response.data,
				});
			});

		// fetch total number of active modules
		axios
		.get(
			`${appLocalizer.apiUrl}/mvx_module/v1/get_as_per_module_status`,
			{
				params: { module_status: 'active', count: 'yes' },
			}
		)
		.then((response) => {
			this.setState({
				total_number_of_active_module: response.data,
			});
		});

		// fetch total number of active modules
		axios
		.get(
			`${appLocalizer.apiUrl}/mvx_module/v1/get_as_per_module_status`,
			{
				params: { module_status: 'deactive', count: 'yes' },
			}
		)
		.then((response) => {
			this.setState({
				total_number_of_deactive_module: response.data,
			});
		});

		this.setState({
			module_total_button: true
		})
	}

	render() {
		return (
			<div className="mvx-general-wrapper mvx-modules">
				<HeaderSection />
				<div className="mvx-container">
					<div className="mvx-middle-container-wrapper">
						<div className="mvx-tab-description-start">
							<div className="mvx-tab-name">
								{appLocalizer.module_page_string.module1}
							</div>
							<p>{appLocalizer.module_page_string.module2}</p>
						</div>

						<div className="mvx-search-and-multistatus-wrap">
							<ul className="mvx-multistatus-ul">
								<li className={`mvx-multistatus-item ${this.state.module_total_button ? 'status-active' : ''}`}>
									<div
										className="mvx-total-module-name-and-count"
										onClick={(e) =>
											this.mvx_search_different_module_status(
												'all'
											)
										}
									>
										<span className="mvx-total-modules-name">
											{
												appLocalizer.module_page_string
													.module3
											} ({this.state.total_number_of_module})
										</span>
									</div>
								</li>
								<li className="mvx-multistatus-item mvx-divider"></li>
								<li className={`mvx-multistatus-item ${this.state.module_active_button ? 'status-active' : ''}`}>
									<button
										onClick={(e) =>
											this.mvx_search_different_module_status(
												'active'
											)
										}
									>
										{
											appLocalizer.module_page_string
												.module4
										} ({this.state.total_number_of_active_module})
									</button>
								</li>
								<li className="mvx-multistatus-item mvx-divider"></li>
								<li className={`mvx-multistatus-item ${this.state.module_inactive_button ? 'status-active' : ''}`}>
									<button
										onClick={(e) =>
											this.mvx_search_different_module_status(
												'inactive'
											)
										}
									>
										{
											appLocalizer.module_page_string
												.module5
										} ({this.state.total_number_of_deactive_module})
									</button>
								</li>
							</ul>
							<div className="mvx-header-search-section">
								<label>
									<i className="mvx-font icon-search"></i>
								</label>
								<input
									type="text"
									onChange={(e) => this.handleModuleSearch(e)}
									placeholder={
										appLocalizer.module_page_string.module6
									}
								/>
							</div>
						</div>

						<div className="mvx-wrap-bulk-all-date">
							<Select
								placeholder={
									appLocalizer.module_page_string.module7
								}
								options={
									appLocalizer.select_module_category_option
								}
								isClearable={true}
								className="mvx-wrap-bulk-action"
								onChange={(e) =>
									this.handleModuleSearchByCategory(e)
								}
							/>
						</div>

						{this.state.items.length === 0 ? (
							 <PageLoader/> 
						) : (
							this.state.items.map((student1, index1) => (
								<div className="mvx-module-list-start">
									<div className="mvx-module-list-container">
									<div className="mvx-text-with-right-side-line-wrapper">

										<div className="mvx-text-with-right-side-line">
											{student1.label}
										</div>
										
										<hr role="presentation"></hr>
									</div>
									
										<div className="mvx-module-option-row">
											{student1.options.map(
												(student, index) => (
													<div className="mvx-module-section-options-list">
														<div
															className={`mvx-module-settings-box ${
																student.is_active
																	? 'active'
																	: ''
															}`}
														>
															<div className="mvx-module-icon">
																<i
																	className={`mvx-font ${student.thumbnail_dir}`}
																></i>
															</div>

															<header>
																<div className="mvx-module-list-label-text">
																	{
																		student.name
																	}
																	{student.plan ===
																	'pro' ? (
																		<span className="mvx-module-section-pro-badge">
																			{
																				appLocalizer.pro_text
																			}
																		</span>
																	) : (
																		''
																	)}
																</div>
																<p>
																	{
																		student.description
																	}
																</p>
															</header>
															{student.required_plugin_list ? (
																<div className="mvx-module-require-name">
																	{
																		appLocalizer
																			.module_page_string
																			.module8
																	}
																</div>
															) : (
																''
															)}
															<ul>
																{student.required_plugin_list &&
																	student.required_plugin_list.map(
																		(
																			company,
																			index_req
																		) => (
																			<li>
																				{company.is_active ? (
																					<div className="mvx-module-active-plugin-class">
																					    <span className="mvx-font icon-yes"></span>

																					</div>
																				) : (
																					<div className="inactive-plugin-class">
																						<span className="mvx-font icon-no"></span>
																					</div>
																				)}
																				<a
																					href={
																						company.plugin_link
																					}
																					className="mvx-third-party-plugin-link-class"
																				>
																					{
																						company.plugin_name
																					}
																				</a>
																			</li>
																		)
																	)}
															</ul>
															<div className="mvx-module-current-status">
																{student.is_active &&
																student.mod_link ? (
																	<a
																		href={
																			student.mod_link
																		}
																		className="mvx-btn btn-border"
																	>
																		{
																			appLocalizer.settings_text
																		}
																	</a>
																) : (
																	''
																)}
																{student.doc_link ?
																<a
																	href={
																		student.doc_link
																	}
																	className="mvx-btn btn-border"
																>
																	{
																		appLocalizer.documentation_text
																	}
																</a> : ''}
																<div className={`mvx-toggle-checkbox-content ${student.plan && student.plan == 'pro' ? 'disabled' : ''}`}>
																	<input
																		type="checkbox"
																		className="mvx-toggle-checkbox"
																		id={`mvx-toggle-switch-${student.id}`}
																		name="modules[]"
																		value={
																			student.id
																		}
																		checked={
																			student.is_active
																				? true
																				: false
																		}
																		
																		onChange={(
																			e
																		) =>
																			this.handleOnChange(
																				e,
																				index,
																				student.plan,
																				student.active_status,
																				student.doc_id,
																				this
																					.state
																					.items,
																				index1,
																				index,
																				student.id
																			)
																		}
																	/>
																	<label
																		htmlFor={`mvx-toggle-switch-${student.id}`}
																	></label>
																</div>
															</div>
														</div>
													</div>
												)
											)}
										</div>
									</div>
								</div>
							))
						)}

						<Dialog
							open={this.state.open_model}
							onClose={this.handleClose}
							aria-labelledby="form-dialog-title"
						>
							<DialogContent>
								<DialogContentText>
									<div className="mvx-module-dialog-content">
										<div className="mvx-image-overlay">
											<div className="mvx-overlay-content">
												<h1 className="banner-header">
													{appLocalizer.module_page_string.module12}
													<span className="banner-pro-tag">
														{appLocalizer.module_page_string.module14}
													</span>
												</h1>
												<h2 className="mvx-banner-thrd">{appLocalizer.module_page_string.module16}</h2>
												<div className="mvx-banner-content">
													{appLocalizer.module_page_string.module17}
												</div>
												<div className="mvx-banner-offer">
													{appLocalizer.module_page_string.module18}
												</div>
												<div className="discount-tag">
													{appLocalizer.module_page_string.module15}
													<b>{appLocalizer.module_page_string.module20}</b>
												</div>
												<p>
													{appLocalizer.module_page_string.module19}
												</p>
												<a className="mvx-go-pro-btn" target="_blank" 
													href={appLocalizer.module_page_string.module21}>
														{appLocalizer.module_page_string.module13}
												</a>
											</div>
										</div>
									</div>
								</DialogContentText>
							</DialogContent>
						</Dialog>
					</div>

					<BannerSection />
				</div>
			</div>
		);
	}
}
export default MVX_Module_Listing;
