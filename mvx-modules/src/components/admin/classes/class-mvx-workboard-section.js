/* global appLocalizer */
import React, { Component } from 'react';
import axios from 'axios';
import Select from 'react-select';
import PageLoader from './class-mvx-page-loader.js';
import { css } from '@emotion/react';
import { BrowserRouter as Router, Link, useLocation } from 'react-router-dom';
import DynamicForm from '../../../DynamicForm';
import DataTable from 'react-data-table-component';
import TabSection from './class-mvx-page-tab';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
//import { __ } from '@wordpress/i18n';

const override = css`
	display: block;
	margin: 0 auto;
	border-color: green;
`;

class MVXworkboard extends Component {
	constructor(props) {
		super(props);
		this.state = {
			bulkselectlist: [],
			bulkselectreviewlist: [],
			show_vendor_name: '',
			display_announcement: [],
			display_pending_announcement: [],
			display_published_announcement: [],
			display_all_announcement: [],
			display_all_knowladgebase: [],
			display_publish_knowladgebase: [],
			display_pending_knowladgebase: [],
			edit_announcement_fileds: [],
			edit_knowledgebase_fileds: [],
			display_list_knowladgebase: [],
			list_of_pending_question: [],
			list_of_store_review: [],
			list_of_report_abuse: [],
			list_of_refund_request: [],
			list_of_refunded_order: [],
			columns_refunded_order : [],
			columns_announcement_new: [],
			columns_knowledgebase_new: [],
			columns_questions_new: [],
			columns_store_review: [],
			columns_report_abuse: [],
			columns_refund_request: [],
			list_of_publish_question: [],
			list_of_all_tabs: [],
			list_of_work_board_content: [],
			pending_individual_checkbox: [],
			open_dialog_popup_for_request_profile_deletion_vendor: [],
			open_dialog_popup_for_pending_product: [],
			open_dialog_popup_for_pending_verification: [],
			request_profile_deletion_vendor_list: [],
			pending_product_list: [],
			pending_verification_list: [],
			current_url: '',
			handle_rejected_vendor_product_description: '',
			workboard_list_announcement_status_all: false,
			workboard_list_announcement_status_approve: false,
			workboard_list_status_announcement_pending: false,
			workboard_list_knowledgebase_status_all: false,
			workboard_list_knowledgebase_status_publish: false,
			workboard_list_knowledgebase_status_pending: false,
			taskboard_loader_on: false,
			selected_option: false,
			selected_ids: [],
		};

		this.QueryParamsDemo = this.QueryParamsDemo.bind(this);
		this.useQuery = this.useQuery.bind(this);
		this.Child = this.Child.bind(this);
		this.handlePostRetriveStatus = this.handlePostRetriveStatus.bind(this);
		this.handlePostBulkStatus = this.handlePostBulkStatus.bind(this);
		this.onSelectedRowsChange = this.onSelectedRowsChange.bind(this);
		this.handleWorkBoardChenage = this.handleWorkBoardChenage.bind(this);
		this.handlePostDismiss = this.handlePostDismiss.bind(this);
		// individual checkbox trigger
		this.handleTodoCheckboxChenage =
			this.handleTodoCheckboxChenage.bind(this);
		this.handleTaskBoardBulkChenage =
			this.handleTaskBoardBulkChenage.bind(this);
		this.handleParentTodoCheckboxChenage =
			this.handleParentTodoCheckboxChenage.bind(this);
		this.handleQuestionSearch = this.handleQuestionSearch.bind(this);
		this.handleReviewDismiss = this.handleReviewDismiss.bind(this);
		this.handleReviewBulkStatus = this.handleReviewBulkStatus.bind(this);
		this.handleselectreviews = this.handleselectreviews.bind(this);
		this.handleSearchVendorReview =
			this.handleSearchVendorReview.bind(this);
		this.handleVendorSearchAbuse = this.handleVendorSearchAbuse.bind(this);
		this.handleProductSearchAbuse =
			this.handleProductSearchAbuse.bind(this);
		this.handleAbuseDismiss = this.handleAbuseDismiss.bind(this);
		this.handleQuestionDelete = this.handleQuestionDelete.bind(this);
		this.handleQuestionBulkStatusChange =
			this.handleQuestionBulkStatusChange.bind(this);
		this.handleClose_dynamic = this.handleClose_dynamic.bind(this);
		this.handleClose_verification_dynamic = this.handleClose_verification_dynamic.bind(this);
		this.handle_rejected_vendor_product_description = this.handle_rejected_vendor_product_description.bind(this);
		this.handle_Vendor_Product_Approve = this.handle_Vendor_Product_Approve.bind(this);
		this.handleChangeRadioButton = this.handleChangeRadioButton.bind(this);
		this.handleVendorDelete = this.handleVendorDelete.bind(this);
		this.handleClose_vendor_dynamic = this.handleClose_vendor_dynamic.bind(this);
		this.handleDeactiveModal = this.handleDeactiveModal.bind(this);
	}

	handleChangeRadioButton(e) {
		this.setState({
			selected_option: e.target.value,
		});
	}

	handleVendorDelete() {
		this.handleClose_vendor_dynamic();
		this.setState({
			taskboard_loader_on: true,
		});
		if (this.state.selected_ids) {
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/task_board_icons_triggers`,
				data: {
					value: this.state.selected_ids,
					key: this.state.selected_option,
				},
			}).then((response) => {
				if ( response && response.data ) {
					this.setState({
						list_of_work_board_content: response.data,
						taskboard_loader_on: false,
					});
				}
			});
		} 
	}

	handleClose_vendor_dynamic() {
		const default_vendor_delete_eye_popup = [];
		this.state.request_profile_deletion_vendor_list ? this.state.request_profile_deletion_vendor_list.map((data_ann, index_ann) => {
			default_vendor_delete_eye_popup[data_ann.id] = false;
		}) : '';
		this.setState({
			open_dialog_popup_for_request_profile_deletion_vendor: default_vendor_delete_eye_popup,
			taskboard_loader_on: false,	
		});
	}

	handleDeactiveModal(e) {
		if (e) {
			this.setState({
				taskboard_loader_on: true,	
			});
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/bulk_todo_request_profile_deletion`,
				data: {
					data_list: e,
				},
			}).then((responce) => {
				if ( responce && responce.data ) {
					let set_vendor_id_data = [];
					set_vendor_id_data = this.state.open_dialog_popup_for_request_profile_deletion_vendor,
					responce.data ? responce.data.map((data_ann, index_ann) => {
						set_vendor_id_data[data_ann] = true;
					}) : '';
					this.setState({
						selected_ids: responce.data,
						open_dialog_popup_for_request_profile_deletion_vendor: set_vendor_id_data,
					});
				}
			});
		}
	}

	handleClose_dynamic() {
		const default_vendor_eye_popup = [];
		this.state.pending_product_list.map((data_ann, index_ann) => {
			default_vendor_eye_popup[data_ann.id] = false;
		});
		this.setState({
			open_dialog_popup_for_pending_product: default_vendor_eye_popup,
		});
	}

	handleClose_verification_dynamic() {
		const default_verification_eye_popup = [];
		this.state.pending_verification_list.map((data_ann, index_ann) => {
			default_verification_eye_popup[data_ann.id] = false;
		});
		this.setState({
			open_dialog_popup_for_pending_verification: default_verification_eye_popup,
		});
	}

	

	handle_rejected_vendor_product_description(e, id) {
		this.setState({
			handle_rejected_vendor_product_description: e.target.value,
		});
	}

	handle_Vendor_Product_Approve(id) {
		this.handleClose_dynamic();
		this.setState({
			taskboard_loader_on: true,
		});
		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/task_board_icons_triggers`,
			data: {
				value: id,
				key: 'dismiss_product',
				reject_word: this.state.handle_rejected_vendor_product_description
			},
		}).then((responce) => {
			this.setState({
				list_of_work_board_content: responce.data,
				taskboard_loader_on: false,
			});
		})
	}

	handleQuestionBulkStatusChange(e) {
		if (e) {
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/list_of_bulk_change_status_question`,
				data: {
					value: e.value,
					product_ids: Array.isArray(e) ? e : '',
				},
			}).then((responce) => {
				this.setState({
					list_of_publish_question: responce.data,
				});
			});
		} else {
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/list_of_pending_question`,
					{
						params: { status: 'publish' },
					}
				)
				.then((response) => {
					this.setState({
						list_of_publish_question: response.data,
					});
				});
		}
	}

	handleQuestionDelete(e, questionId, productId, type, row) {
		if (type === 'rejected') {
			if (confirm(appLocalizer.global_string.confirm_dismiss)) {
				axios({
					method: 'post',
					url: `${appLocalizer.apiUrl}/mvx_module/v1/approve_dismiss_pending_question`,
					data: {
						questionId,
						productId,
						type,
					},
				}).then((responce) => {
					this.setState({
						list_of_publish_question: responce.data,
					});
				});
			}
		} else {
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/approve_dismiss_pending_question`,
				data: {
					questionId,
					productId,
					type,
				},
			}).then((responce) => {
				this.setState({
					list_of_publish_question: responce.data,
				});
			});
		}
	}

	handleAbuseDismiss(reason, product, vendor) {
		if (confirm(appLocalizer.global_string.confirm_delete)) {
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/report_abuse_delete`,
				data: {
					reason,
					product,
					vendor,
				},
			}).then((responce) => {
				this.setState({
					list_of_report_abuse: responce.data,
				});
			});
		}
	}

	handleVendorSearchAbuse(e) {
		if (e) {
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/report_abuse_details`,
					{
						params: { vendor_id: e.value },
					}
				)
				.then((response) => {
					this.setState({
						list_of_report_abuse: response.data,
					});
				});
		} else {
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/report_abuse_details`
				)
				.then((response) => {
					this.setState({
						list_of_report_abuse: response.data,
					});
				});
		}
	}

	handleProductSearchAbuse(e) {
		if (e) {
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/report_abuse_details`,
					{
						params: { product_id: e.value },
					}
				)
				.then((response) => {
					this.setState({
						list_of_report_abuse: response.data,
					});
				});
		} else {
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/report_abuse_details`
				)
				.then((response) => {
					this.setState({
						list_of_report_abuse: response.data,
					});
				});
		}
	}

	handleSearchVendorReview(e) {
		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/search_review`,
			data: {
				value: e.target.value,
			},
		}).then((responce) => {
			this.setState({
				list_of_store_review: responce.data,
			});
		});
	}

	handleReviewBulkStatus() {
		if (confirm(appLocalizer.global_string.confirm_delete)) {
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/delete_review`,
				data: {
					id: this.state.bulkselectreviewlist,
				},
			}).then((responce) => {
				this.setState({
					list_of_store_review: responce.data,
				});
			});
		}
	}

	handleselectreviews(e) {
		this.setState({
			bulkselectreviewlist: e.selectedRows,
		});
	}

	handleReviewDismiss(id) {
		if (confirm(appLocalizer.global_string.confirm_delete)) {
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/delete_review`,
				data: {
					id,
				},
			}).then((responce) => {
				this.setState({
					list_of_store_review: responce.data,
				});
			});
		}
	}

	handleQuestionSearch(e) {
		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/search_question_ans`,
			data: {
				value: e.target.value,
			},
		}).then((responce) => {
			this.setState({
				list_of_publish_question: responce.data,
			});
		});
	}

	handleTaskBoardBulkChenage(e, type) {
		if (type == 'requested_profile_deletion' && e.value == 'approve') {
			this.handleDeactiveModal(this.state.pending_individual_checkbox[type]);
		}
		axios({
			method: 'post',
			url: `${appLocalizer.apiUrl}/mvx_module/v1/bulk_todo_pending_product`,
			data: {
				data_list: this.state.pending_individual_checkbox[type],
				value: e.value,
				type: type,
			},
		}).then((responce) => {
			if (responce.data) {
				this.setState({
					list_of_work_board_content: responce.data
				});
			}
		});
	}

	handleParentTodoCheckboxChenage(e, data_key) {
		if (e.target.checked) {
			this.state.pending_individual_checkbox[data_key] = new Array(
				this.state.pending_individual_checkbox[data_key].length
			).fill(true);
		} else {
			this.state.pending_individual_checkbox[data_key] = new Array(
				this.state.pending_individual_checkbox[data_key].length
			).fill(false);
		}
		this.setState({
			pending_individual_checkbox: this.state.pending_individual_checkbox
		});
	}

	// individual checkbox trigger
	handleTodoCheckboxChenage(e, data_key, position) {
		this.state.pending_individual_checkbox[data_key] = this.state.pending_individual_checkbox[data_key].map(
			(item, index) => (index === position ? !item : item)
		);
		this.setState({
			pending_individual_checkbox: this.state.pending_individual_checkbox,
		});
	}

	handlePostDismiss(e, title) {
		if (confirm(appLocalizer.global_string.confirm_delete)) {
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/delete_post_details`,
				data: {
					ids: e,
					title,
				},
			}).then((responce) => {
				this.setState({
					display_announcement: responce.data,
				});
			});
		}
	}

	handleWorkBoardChenage(e, type) {
		if (type === 'announcement' && e) {
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/update_custom_post_status`,
				data: {
					ids: this.state.bulkselectlist,
					value: e.value,
				},
			}).then(() => {});
		}
	}

	onSelectedRowsChange(e) {
		this.setState({
			bulkselectlist: e.selectedRows,
		});
	}

	handlePostRetriveStatus(e, status, type) {
		if (type === 'announcement') {
			this.setState({
				workboard_list_announcement_status_all: status === 'all' ? true : false,
				workboard_list_announcement_status_approve: status === 'publish' ? true : false,
				workboard_list_status_announcement_pending: status === 'pending' ? true : false,
			});
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/display_announcement`,
					{
						params: { status },
					}
				)
				.then((response) => {
					this.setState({
						display_announcement: response.data,
					});
				});
		} else if (type === 'knowladgebase') {
			this.setState({
				workboard_list_knowledgebase_status_all: status === 'all' ? true : false,
				workboard_list_knowledgebase_status_publish: status === 'publish' ? true : false,
				workboard_list_knowledgebase_status_pending: status === 'pending' ? true : false,
			});
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/display_list_knowladgebase`,
					{
						params: { status },
					}
				)
				.then((response) => {
					this.setState({
						display_list_knowladgebase: response.data,
					});
				});
		}
	}

	handlePostBulkStatus(e, type) {
		if (type === 'announcement') {
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/search_announcement`,
				data: {
					ids: this.state.bulkselectlist,
					value: e.target.value,
				},
			}).then((responce) => {
				this.setState({
					display_announcement: responce.data,
				});
			});
		} else if (type === 'knowladgebase') {
			axios({
				method: 'post',
				url: `${appLocalizer.apiUrl}/mvx_module/v1/search_knowledgebase`,
				data: {
					value: e.target.value,
				},
			}).then((responce) => {
				this.setState({
					display_list_knowladgebase: responce.data,
				});
			});
		}
	}

	componentDidMount() {
		/***********  Announcement  ******************/
		// all announcement
		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/display_announcement`)
			.then((response) => {
				this.setState({
					display_announcement: response.data,
				});
			});

		// pending announcement
		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/display_announcement`, {
				params: { status: 'pending' },
			})
			.then((response) => {
				this.setState({
					display_pending_announcement: response.data,
				});
			});

		// published announcement
		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/display_announcement`, {
				params: { status: 'publish' },
			})
			.then((response) => {
				this.setState({
					display_published_announcement: response.data,
				});
			});

		// all announcement count
		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/display_announcement`, {
				params: { status: 'all' },
			})
			.then((response) => {
				this.setState({
					display_all_announcement: response.data,
				});
			});
		/***********  Announcement  ******************/

		/***********  Knowledgebase  **************/
		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/display_list_knowladgebase`
			)
			.then((response) => {
				this.setState({
					display_list_knowladgebase: response.data,
				});
			});

		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/display_list_knowladgebase`,
				{
					params: { status: 'all' },
				}
			)
			.then((response) => {
				this.setState({
					display_all_knowladgebase: response.data,
				});
			});

		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/display_list_knowladgebase`,
				{
					params: { status: 'publish' },
				}
			)
			.then((response) => {
				this.setState({
					display_publish_knowladgebase: response.data,
				});
			});

		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/display_list_knowladgebase`,
				{
					params: { status: 'pending' },
				}
			)
			.then((response) => {
				this.setState({
					display_pending_knowladgebase: response.data,
				});
			});

		/******** Knowledgebase end  ************/
		// pending details
		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/list_of_pending_question`
			)
			.then((response) => {
				const allPendingDataCheckbox = new Array(
					response.data.length
				).fill(false);
				this.setState({
					list_of_pending_question: response.data,
				});
			});

		// publish details
		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/list_of_pending_question`,
				{
					params: { status: 'publish' },
				}
			)
			.then((response) => {
				this.setState({
					list_of_publish_question: response.data,
				});
			});

		// fetch review
		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/list_of_store_review`)
			.then((response) => {
				this.setState({
					list_of_store_review: response.data,
				});
			});

		// fetch review
		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/report_abuse_details`)
			.then((response) => {
				this.setState({
					list_of_report_abuse: response.data,
				});
			});


		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/list_of_refund_request`)
			.then((response) => {
				this.setState({
					list_of_refund_request: response.data,
				});
			});

		axios
			.get(`${appLocalizer.apiUrl}/mvx_module/v1/list_of_refunded_orders`)
			.then((response) => {
				this.setState({
					list_of_refunded_order: response.data,
				});
			});

		// get vendor name on select
		axios({
			url: `${appLocalizer.apiUrl}/mvx_module/v1/show_vendor_name`,
		}).then((response) => {
			this.setState({
				show_vendor_name: response.data,
			});
		});

		// tab list
		axios({
			url: `${appLocalizer.apiUrl}/mvx_module/v1/list_of_all_tabs`,
		}).then((response) => {
			this.setState({
				list_of_all_tabs: response.data,
			});
		});

		// list of workboard data
		axios({
			url: `${appLocalizer.apiUrl}/mvx_module/v1/list_of_work_board_content`,
		}).then((response) => {

			response.data.map((data_parent, index_parent) => {
				this.state.pending_individual_checkbox[data_parent.key] = new Array(
					data_parent.content.length
				).fill(false)
			})
			this.setState({
				list_of_work_board_content: response.data,
			});
		});

		this.setState({
			workboard_list_knowledgebase_status_all: true,
			workboard_list_announcement_status_all: true
		});

		// request profile deletion vendor popup
		axios({
			url: `${appLocalizer.apiUrl}/mvx_module/v1/list_of_request_profile_deletion_vendor`,
		}).then((response) => {
			if ( response && response.data ) {
				const default_vendor_delete_popup = [];
				response.data ? response.data.map((data_ann, index_ann) => {
					default_vendor_delete_popup[data_ann.id] = false;
				}) : '';

				this.setState({
					open_dialog_popup_for_request_profile_deletion_vendor: default_vendor_delete_popup,
					request_profile_deletion_vendor_list: response.data,
				});
			}
		});

		// pending product rejection popup
		axios({
				url: `${appLocalizer.apiUrl}/mvx_module/v1/list_of_pending_vendor_product`,
		}).then((response) => {
			const default_vendor_popup = [];
			response.data.map((data_ann, index_ann) => {
				default_vendor_popup[data_ann.id] = false;
			});

			this.setState({
				open_dialog_popup_for_pending_product: default_vendor_popup,
				pending_product_list: response.data,
			});
		});

		// pending verification
		axios({
				url: `${appLocalizer.apiUrl}/mvx_module/v1/fetch_pending_verification_data`,
		}).then((response) => {
			const default_vendor_verification_popup = [];
			response.data.map((data_verifi, index_ann) => {
				default_vendor_verification_popup[data_verifi.id] = false;
			});
			this.setState({
				open_dialog_popup_for_pending_verification: default_vendor_verification_popup,
				pending_verification_list: response.data,
			});
		});

		
	}

	useQuery() {
		return new URLSearchParams(useLocation().hash);
	}

	QueryParamsDemo() {
		const use_query = this.useQuery();
		// update announcement table when clock on announcement tab
		if (
			new URLSearchParams(window.location.hash).get('name') ===
			'announcement'
		) {
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/display_announcement`
				)
				.then((response) => {
					this.state.display_announcement = response.data;
				});
		}
		// update announcement table end

		// update knowledgebase table when clock on knowledgebase tab
		if (
			new URLSearchParams(window.location.hash).get('name') ===
			'knowladgebase'
		) {
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/display_list_knowladgebase`
				)
				.then((response) => {
					this.state.display_list_knowladgebase = response.data;
				});
		}
		// update knowledgebase table end
		return Object.keys(this.state.list_of_all_tabs).length > 0 ? (
			<TabSection
				model={this.state.list_of_all_tabs['marketplace-workboard']}
				query_name={use_query.get('name')}
				funtion_name={this}
				horizontally
				no_banner
			/>
		) : (
			<PageLoader/>
		);
	}

	Child({ name }) {

		const get_current_name = this.useQuery();
		if (!get_current_name.get('AnnouncementID')) {
			this.state.edit_announcement_fileds = [];
		}

		if (!get_current_name.get('knowladgebaseID')) {
			this.state.edit_knowledgebase_fileds = [];
		}

		if (get_current_name.get('AnnouncementID')) {
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/update_announcement_display`,
					{
						params: {
							announcement_id:
								get_current_name.get('AnnouncementID'),
						},
					}
				)
				.then((response) => {
					if (
						response.data &&
						this.state.edit_announcement_fileds.length === 0
					) {
						this.setState({
							edit_announcement_fileds: response.data,
						});
					}
				});
		}

		if (get_current_name.get('knowladgebaseID')) {
			axios
				.get(
					`${appLocalizer.apiUrl}/mvx_module/v1/update_knowladgebase_display`,
					{
						params: {
							knowladgebase_id:
								get_current_name.get('knowladgebaseID'),
						},
					}
				)
				.then((response) => {
					if (
						response.data &&
						this.state.edit_knowledgebase_fileds.length === 0
					) {
						this.setState({
							edit_knowledgebase_fileds: response.data,
						});
					}
				});
		}

		{/** Display table column and row slection **/}
		if (
			this.state.columns_announcement_new.length === 0 &&
			new URLSearchParams(window.location.hash).get('name') ===
				'announcement'
		) {
			appLocalizer.columns_announcement.map((data_ann, index_ann) => {
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
								<a href={row.link}  data-title='Edit'>
									<i className="mvx-font icon-edit"></i>
								</a>
								<div
									onClick={() =>
										this.handlePostDismiss(row.id, row.type)
									}
									id={row.id}
									data-title='Delete'
								>
									<i className="mvx-font icon-no"></i>
								</div>
							</div>
					  ))
					: '';

				this.state.columns_announcement_new[index_ann] = data_ann;
				set_for_dynamic_column = this.state.columns_announcement_new;
				this.setState({
					columns_announcement_new: set_for_dynamic_column,
				});
			});
		}
		// Display table column and row slection end

		// Display table column and row slection for questions
		if (
			this.state.columns_questions_new.length === 0 &&
			new URLSearchParams(window.location.hash).get('name') ===
				'question-ans'
		) {
			appLocalizer.columns_questions.map((data_ques, index_ques) => {
				let data_selector_question = '';
				let set_for_dynamic_column_question = '';
				data_selector_question = data_ques.selector_choice;
				data_ques.selector = (row) => (
					<div
						dangerouslySetInnerHTML={{
							__html: row[data_selector_question],
						}}
					></div>
				);

				data_ques.cell
					? (data_ques.cell = (row) => (
							<div className="mvx-vendor-action-icon">
								<div
									onClick={(e) =>
										this.handleQuestionDelete(
											e,
											row.id,
											row.question_product_id,
											'verified',
											row
										)
									}
									id={row.id}
									data-title='approve'
								>
									<i className="mvx-font icon-approve"></i>
								</div>
								<div
									onClick={(e) =>
										this.handleQuestionDelete(
											e,
											row.id,
											row.question_product_id,
											'rejected',
											row
										)
									}
									id={row.id}
									data-title='Delete'
								>
									<i className="mvx-font icon-no"></i>
								</div>
							</div>
					  ))
					: '';

				this.state.columns_questions_new[index_ques] = data_ques;
				set_for_dynamic_column_question =
					this.state.columns_questions_new;
				this.setState({
					columns_questions_new: set_for_dynamic_column_question,
				});
			});
		}
		// Display table column and row slection for questions

		// Display table column and row slection knowladgebase
		if (
			this.state.columns_knowledgebase_new.length === 0 &&
			new URLSearchParams(window.location.hash).get('name') ===
				'knowladgebase'
		) {
			appLocalizer.columns_knowledgebase.map(
				(data_anno_knowl, index_knowledge) => {
					let data_knowledgebase_selector = '';
					let set_for_dynamic_column_know = '';
					data_knowledgebase_selector =
						data_anno_knowl.selector_choice;
					data_anno_knowl.selector = (row) => (
						<div
							dangerouslySetInnerHTML={{
								__html: row[data_knowledgebase_selector],
							}}
						></div>
					);

					data_anno_knowl.cell
						? (data_anno_knowl.cell = (row) => (
								<div className="mvx-vendor-action-icon">
									<a href={row.link} data-title="Edit">
										<i className="mvx-font icon-edit" ></i>
									</a>
									<div
										onClick={() =>
											this.handlePostDismiss(
												row.id,
												row.type
											)
										}
										id={row.id}
										data-title='Delete'
									>
										<i className="mvx-font icon-no"></i>
									</div>
								</div>
						  ))
						: '';

					this.state.columns_knowledgebase_new[index_knowledge] =
						data_anno_knowl;
					set_for_dynamic_column_know =
						this.state.columns_knowledgebase_new;
					this.setState({
						columns_knowledgebase_new: set_for_dynamic_column_know,
					});
				}
			);
		}

		// Display table column and row slection store review
		if (
			this.state.columns_store_review.length === 0 &&
			new URLSearchParams(window.location.hash).get('name') ===
				'store-review'
		) {
			appLocalizer.columns_store_review.map(
				(data_store_review_content, index_store_review) => {
					let data_store_review_selector = '';
					let set_for_dynamic_column_store_review = '';
					data_store_review_selector =
						data_store_review_content.selector_choice;
					data_store_review_content.selector = (row) => (
						<div
							dangerouslySetInnerHTML={{
								__html: row[data_store_review_selector],
							}}
						></div>
					);

					data_store_review_content.cell
						? (data_store_review_content.cell = (row) => (
								<div className="mvx-vendor-action-icon">
									<a href={row.link} data-title='Edit'>
										<i className="mvx-font icon-edit"></i>
									</a>
									<div
										onClick={() =>
											this.handleReviewDismiss(row.id)
										}
										id={row.id}
										data-title='Delete'
									>
										<i className="mvx-font icon-no"></i>
									</div>
								</div>
						  ))
						: '';

					this.state.columns_store_review[index_store_review] =
						data_store_review_content;
					set_for_dynamic_column_store_review =
						this.state.columns_store_review;
					this.setState({
						columns_store_review:
							set_for_dynamic_column_store_review,
					});
				}
			);
		}


		// Display table column and row slection refund request
		if (
			this.state.columns_refund_request.length === 0 &&
			new URLSearchParams(window.location.hash).get('name') ===
				'refund-request'
		) {
			appLocalizer.columns_refund_request.map(
				(data_store_refund_request_content, index_store_abuse) => {
					let data_refund_request_selector = '';
					let set_for_dynamic_column_store_review = '';
					data_refund_request_selector =
						data_store_refund_request_content.selector_choice;
					data_store_refund_request_content.selector = (row) => (
						<div
							dangerouslySetInnerHTML={{
								__html: row[data_refund_request_selector],
							}}
						></div>
					);

					this.state.columns_refund_request[index_store_abuse] =
						data_store_refund_request_content;
					set_for_dynamic_column_store_review =
						this.state.columns_refund_request;
					this.setState({
						columns_refund_request:
							set_for_dynamic_column_store_review,
					});
				}
			);
		}

		// Display table column and row slection refunded order
        if (
            this.state.columns_refunded_order.length === 0 &&
            new URLSearchParams(window.location.hash).get('name') ===
                'refunded-order'
        ) {
            appLocalizer.columns_refunded_order.map(
                (data_store_refunded_order_content, index_store_abuse) => {
                    let data_refunded_order_selector = '';
                    let set_for_dynamic_column_store_review = '';
                    data_refunded_order_selector =
                        data_store_refunded_order_content.selector_choice;
                    data_store_refunded_order_content.selector = (row) => (
                        <div
                            dangerouslySetInnerHTML={{
                                __html: row[data_refunded_order_selector],
                            }}
                        ></div>
                    );

                    this.state.columns_refunded_order[index_store_abuse] =
                        data_store_refunded_order_content;
                    set_for_dynamic_column_store_review =
                        this.state.columns_refunded_order;
                    this.setState({
                        columns_refunded_order:
                            set_for_dynamic_column_store_review,
                    });
                }
            );
        }


		// Display table column and row slection report abuse
		if (
			this.state.columns_report_abuse.length === 0 &&
			new URLSearchParams(window.location.hash).get('name') ===
				'report-abuse'
		) {
			appLocalizer.columns_report_abuse.map(
				(data_store_report_abuse_content, index_store_abuse) => {
					let data_report_abuse_selector = '';
					let set_for_dynamic_column_store_review = '';
					data_report_abuse_selector =
						data_store_report_abuse_content.selector_choice;
					data_store_report_abuse_content.selector = (row) => (
						<div
							dangerouslySetInnerHTML={{
								__html: row[data_report_abuse_selector],
							}}
						></div>
					);

					data_store_report_abuse_content.cell
						? (data_store_report_abuse_content.cell = (row) => (
								<div className="mvx-vendor-action-icon">
									<div
										onClick={() =>
											this.handleAbuseDismiss(
												row.reason,
												row.product,
												row.vendor
											)
										}
										id={row.reason}
										data-title='Delete'
									>
										<i className="mvx-font icon-no"></i>
									</div>
								</div>
						  ))
						: '';

					this.state.columns_report_abuse[index_store_abuse] =
						data_store_report_abuse_content;
					set_for_dynamic_column_store_review =
						this.state.columns_report_abuse;
					this.setState({
						columns_report_abuse:
							set_for_dynamic_column_store_review,
					});
				}
			);
		}
		let set_vendor_id_data = [];
		let set_vendors_id_data = [];
		let set_verification_id_data = [];
		
		return name === 'activity-reminder' ? (
		<div>{
			this.state.list_of_work_board_content.map(
				(taskboard_data, taskboard_index) => (
					<div className="mvx-todo-status-check">
						<div className="mvx-text-with-line-wrapper">

							<div className="mvx-text-with-right-side-line">
								{taskboard_data.header}
							</div>
							<hr role="presentation"></hr>

								
							<div className="mvx-select-all-bulk-wrap">
								<div className="mvx-select-all-checkbox">
									<input
										type="checkbox"
										className="mvx-select-all"
										onChange={(e) =>
											this.handleParentTodoCheckboxChenage(e, taskboard_data.key)
										}
									/>
									<span className="mvx-select-all-text">
										{appLocalizer.global_string.select_all}
									</span>
								</div>
								<Select
									placeholder={
										appLocalizer.global_string.bulk_action
									}
									options={appLocalizer.task_board_bulk_status}
									isClearable={true}
									className="mvx-wrap-bulk-action"
									onChange={(e) =>
										this.handleTaskBoardBulkChenage(
											e,
											taskboard_data.key
										)
									}
								/>
							</div>
						</div>
						<div className="mvx-product-box-sec">
							{this.state.taskboard_loader_on ?
								  <PageLoader/>
								:
							 taskboard_data.content.map(
								(task_lists_data, task_lists_index) => (
									<div className="mvx-all-product-box">
										<div className="mvx-white-box-header">
											{taskboard_data.header}
											<div className="pull-right">
												<input
													type="checkbox"
													className="mvx-workboard-checkbox"
													checked={
														this.state.pending_individual_checkbox[taskboard_data.key][task_lists_index]
													}
													onChange={(e) =>
														this.handleTodoCheckboxChenage(
															e,
															taskboard_data.key,
															task_lists_index
														)
													}
												/>

											</div>
										</div>

										<div className="mvx-white-box-body">
										{
											task_lists_data.list_datas.map((task_child_data, task_child_index) => (
												<div className="mvx-box-content">
													<div className="mvx-product-title">
														{
															task_child_data.label
														}
														:
													</div>

													<div className="mvx-product-name">
														<p
															dangerouslySetInnerHTML={{
																__html: task_child_data.value,
															}}
														></p>
													</div>
												</div>
											))
										}
										</div>
										<div className="mvx-white-box-footer">
											<div className="pull-right">
												{
													task_lists_data.left_icons ? task_lists_data.left_icons.map((icons_data, icons_index) => (
														icons_data.key == 'edit' ?

															<div className="link-icon" data-title='Edit' onClick={(e) =>
																	(
																		location.href = icons_data.link
																	)
																}>
																<i className="mvx-font icon-edit" ></i>
															</div>
														: 

														<div className="link-icon" data-title={icons_data.title}>
															<i
																className={`mvx-font ${icons_data.icon}`}
																onClick={(e) =>
																	(
																		icons_data.key === 'deactivate_vendor' ? (
																		set_vendor_id_data = this.state.open_dialog_popup_for_request_profile_deletion_vendor,
																		set_vendor_id_data[icons_data.value.id] = true,
																		this.setState({
																			selected_ids: icons_data.value.id,
																			open_dialog_popup_for_request_profile_deletion_vendor: set_vendor_id_data,
																		}))
																		: 
																		this.setState({
																			taskboard_loader_on: true,	
																		})
																		,

																		icons_data.key === 'dismiss_product' ? (
																		set_vendors_id_data = this.state.open_dialog_popup_for_pending_product,
																		set_vendors_id_data[icons_data.value.id] = true,
																		this.setState({
																			open_dialog_popup_for_pending_product: set_vendors_id_data,
																		}))
																		: 

																		this.setState({
																			taskboard_loader_on: true,	
																		})

																		,


																		icons_data.action === 'view' ? (
																		set_verification_id_data = this.state.open_dialog_popup_for_pending_verification,
																		set_verification_id_data[icons_data.value] = true,
																		this.setState({
																			open_dialog_popup_for_pending_verification: set_verification_id_data,
																		}))
																		: '',

																		icons_data.key === 'dismiss_product' ? '' :
																		axios({
																			method: 'post',
																			url: `${appLocalizer.apiUrl}/mvx_module/v1/task_board_icons_triggers`,
																			data: {
																				value: icons_data.value,
																				key: icons_data.key
																			},
																		}).then((responce) => {
																			this.setState({
																				list_of_work_board_content: responce.data,
																				taskboard_loader_on: false,
																			});
																		})

																	)
																}
															></i>
														</div>

													)) : ''
												}
											</div>
										</div>

									</div>	
								))
							}
						</div>
					</div>
				)
			)
			}



		{this.state.pending_verification_list.map((data_veri, index_veri) => (
			<Dialog
				open={this.state
						.open_dialog_popup_for_pending_verification[
						data_veri.id
					]}
				aria-labelledby="form-dialog-title"
				onClose={this.handleClose_verification_dynamic}
			>
				<DialogTitle id="form-dialog-title">
					<div className="mvx-module-dialog-title">
						Verification Details
					</div>
				</DialogTitle>
				<DialogContent>
					<DialogContentText>
						<div
							dangerouslySetInnerHTML={{ __html: data_veri.image }}
						></div>

					<table>
					  <tr>
					  	<th>Title</th>
					    <th>Type</th>
					    <th>Action</th>
					  </tr>

					  <tr>
					    <td>Address Verification</td>
					    <td>
						    <div
								dangerouslySetInnerHTML={{ __html: data_veri.address }}
							></div>
						</td>
					    <td>

					    	{data_veri.address_verified ? appLocalizer.workboard_string.workboard35 :
					    	<>
					    	<button className="mvx-back-btn" onClick={(e) =>
								(
								axios({
									method: 'post',
									url: `${appLocalizer.apiUrl}/mvx_module/v1/vendor_pending_verification_action`,
									data: {
										action: 'verified', id: data_veri.id, type: 'address_verification'
									},
								}).then((responce) => {
									location.reload();
								})

								)
							}>
								<i className="mvx-font icon-yes"></i>
							</button>
							<button className="mvx-back-btn" onClick={(e) =>
								(
									axios({
									method: 'post',
									url: `${appLocalizer.apiUrl}/mvx_module/v1/vendor_pending_verification_action`,
									data: {
										action: 'rejected', id: data_veri.id, type: 'address_verification'
									},
									}).then((responce) => {
										location.reload();
									})
								)
							}>
								<i className="mvx-font icon-no"></i>
							</button>
							</>
					    	}

					    </td>
					  </tr>

					  <tr>
					    <td>Id Verification</td>
					    <td>
					    	<div
								dangerouslySetInnerHTML={{ __html: data_veri.id_verification }}
							></div>
						</td>
					    <td>
					    	{data_veri.id_verified ? appLocalizer.workboard_string.workboard35 :
					    	<>
					    	<button className="mvx-back-btn" onClick={(e) =>
								(
									axios({
									method: 'post',
									url: `${appLocalizer.apiUrl}/mvx_module/v1/vendor_pending_verification_action`,
									data: {
										action: 'verified', id: data_veri.id, type: 'id_verification'
									},
									}).then((responce) => {
										location.reload();
									})
								)
							}>
								<i className="mvx-font icon-yes"></i>
							</button>
							<button className="mvx-back-btn" onClick={(e) =>
								(
									axios({
									method: 'post',
									url: `${appLocalizer.apiUrl}/mvx_module/v1/vendor_pending_verification_action`,
									data: {
										action: 'rejected', id: data_veri.id, type: 'id_verification'
									},
									}).then((responce) => {
										location.reload();
									})
								)
							}>
								<i className="mvx-font icon-no"></i>
							</button>
					    	</>
					    	}
					    </td>
					  </tr>

					  <tr>
					    <td>Social Verification</td>
					    <td>
					    	<div
								dangerouslySetInnerHTML={{ __html: data_veri.social }}
							></div>
						</td>
					    <td></td>
					  </tr>
					</table>

					</DialogContentText>
				</DialogContent>
				<DialogActions></DialogActions>
			</Dialog>
		))}


			{this.state.pending_product_list.map((data8, index8) => (
				<Dialog
					open={
						this.state
							.open_dialog_popup_for_pending_product[
							data8.id
						]
						
					}
					aria-labelledby="form-dialog-title"
					onClose={this.handleClose_dynamic}
				>
					<DialogTitle id="form-dialog-title">
						<div className="mvx-module-dialog-title">
							Reason for dismissal
						</div>
					</DialogTitle>
					<DialogContent>
						<DialogContentText>
							<textarea
								placeholder={
									appLocalizer
										.vendor_page_string
										.describe_yourself
								}
								onChange={(e) =>
									this.handle_rejected_vendor_product_description(
										e,
										data8.id
									)
								}
							></textarea>
							<button
								className="mvx-btn btn-red"
								onClick={() =>
									this.handle_Vendor_Product_Approve(
										data8
									)
								}
								color="primary"
							>
								{
									appLocalizer
										.workboard_string
										.workboard34
								}
							</button>
						</DialogContentText>
					</DialogContent>
					<DialogActions></DialogActions>
				</Dialog>
			))}

		{this.state.request_profile_deletion_vendor_list ? this.state.request_profile_deletion_vendor_list.map((data_vendor) => (
			<Dialog 
				className='deactivate-pop-up'
				open={
					this.state.open_dialog_popup_for_request_profile_deletion_vendor[data_vendor.id]
				}
				aria-labelledby="form-dialog-title"
				onClose={this.handleClose_vendor_dynamic}
			>
			<DialogTitle id="form-dialog-title">
				<div className="mvx-module-dialog-title">
					{appLocalizer.workboard_string.select_deletion_option}
				</div>
				<i className="mvx-font icon-no"
					onClick={this.handleClose_vendor_dynamic} >
				</i>
			</DialogTitle>
			<DialogContent>
				<DialogContentText>
					<div className="mvx-module-dialog-content">
						<div className="mvx-vendor-textarea-content">
							<div>
								<label>
									<input
									type="radio"
									value="product_draft"
									checked={this.state.selected_option === "product_draft"}
									onChange={this.handleChangeRadioButton}
									/>
									{appLocalizer.workboard_string.product_draft}
								</label>
							</div>
							<div>
								<label>
									<input
									type="radio"
									value="product_assigned_admin"
									checked={this.state.selected_option === "product_assigned_admin"}
									onChange={this.handleChangeRadioButton}
									/>
									{appLocalizer.workboard_string.product_assigned_to_admin}
								</label>
							</div>
						</div>

						<div className="mvx-vendor-multi-action-buttons">
							<button
								className="mvx-btn btn-red"
								onClick={() =>
									this.handleVendorDelete()
								}
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
		)) : ''}
		</div>

		) : name === 'announcement' ? (
			<div className="mvx-module-grid">
				<div className='mvx-back-btn-wrapper'>
				{(get_current_name &&
					get_current_name.get('create') === 'announcement') ||
				get_current_name.get('AnnouncementID') ? (

					<button className="mvx-back-btn" onClick={(e) =>
						(
							location.href = appLocalizer.announcement_back
						)
					}>
						<i className="mvx-font icon-back"></i>
						{appLocalizer.global_string.back}
					</button>

				) : (
					<button className="mvx-btn btn-purple" onClick={(e) =>
						(
							location.href = appLocalizer.add_announcement_link
						)
					}>
						<i className="mvx-font icon-add"></i>
						{appLocalizer.workboard_string.workboard25}
					</button>
				)}
			</div>
				{get_current_name &&
				get_current_name.get('create') === 'announcement' ? (
					<DynamicForm
						key={`dynamic-form-announcement-add-new`}
						className="mvx-announcement-add-new"
						title="Add new Announcement"
						model={appLocalizer.settings_fields.create_announcement}
						method="post"
						modulename="create_announcement"
						url="mvx_module/v1/create_announcement"
						submit_title={appLocalizer.global_string.publish}
					/>
				) : get_current_name.get('AnnouncementID') ? (
					this.state.edit_announcement_fileds &&
					Object.keys(this.state.edit_announcement_fileds).length >
						0 ? (
						<DynamicForm
							key={`dynamic-form-announcement-add-new`}
							className="mvx-announcement-add-new"
							title="Update Announcement"
							model={
								this.state.edit_announcement_fileds
									.update_announcement_display
							}
							method="post"
							announcement_id={get_current_name.get(
								'AnnouncementID'
							)}
							modulename="update_announcement"
							url="mvx_module/v1/update_announcement"
							submitbutton="false"
						/>
					) : (
						<PageLoader/>
					)
				) : (
					<div className="mvx-knowladgebase-different-funtionality">
						<div className="mvx-search-and-multistatus-wrap">
							<ul className="mvx-multistatus-ul">
								<li className={`mvx-multistatus-item ${this.state.workboard_list_announcement_status_all ? 'status-active' : ''}`}>
									<div
										className="mvx-multistatus-check-all"
										onClick={(e) =>
											this.handlePostRetriveStatus(
												e,
												'all',
												'announcement'
											)
										}
									>
										{appLocalizer.global_string.all} (
										{
											this.state.display_all_announcement
												.length
										}
										)
									</div>
								</li>
								<li className="mvx-multistatus-item mvx-divider"></li>
								<li className={`mvx-multistatus-item ${this.state.workboard_list_announcement_status_approve ? 'status-active' : ''}`}>
									<div
										className="mvx-multistatus-check-approve"
										onClick={(e) =>
											this.handlePostRetriveStatus(
												e,
												'publish',
												'announcement'
											)
										}
									>
										{appLocalizer.global_string.published} (
										{
											this.state
												.display_published_announcement
												.length
										}
										)
									</div>
								</li>
								<li className="mvx-multistatus-item mvx-divider"></li>
								<li className={`mvx-multistatus-item ${this.state.workboard_list_status_announcement_pending ? 'status-active' : ''}`}>
									<div
										className="mvx-multistatus-check-pending status-active"
										onClick={(e) =>
											this.handlePostRetriveStatus(
												e,
												'pending',
												'announcement'
											)
										}
									>
										{appLocalizer.global_string.pending} (
										{
											this.state
												.display_pending_announcement
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
									type="search"
									placeholder={
										appLocalizer.workboard_string
											.workboard26
									}
									onChange={(e) =>
										this.handlePostBulkStatus(
											e,
											'announcement'
										)
									}
								/>
							</div>
						</div>

						<div className="mvx-wrap-bulk-all-date">
							<Select
								placeholder={
									appLocalizer.global_string.bulk_action
								}
								options={appLocalizer.post_bulk_status}
								isClearable={true}
								className="mvx-wrap-bulk-action"
								onChange={(e) =>
									this.handleWorkBoardChenage(
										e,
										'announcement'
									)
								}
							/>
							<Select
								placeholder={
									appLocalizer.global_string.all_dates
								}
								className="mvx-wrap-bulk-action"
								options={this.state.details_vendor}
								isClearable={true}
								onChange={this.handleWorkBoardChenage}
							/>
						</div>

						{this.state.columns_announcement_new &&
						this.state.columns_announcement_new.length > 0 ? (
							<div className="mvx-backend-datatable-wrapper">
								<DataTable
									columns={
										this.state.columns_announcement_new
									}
									data={this.state.display_announcement}
									selectableRows
									onSelectedRowsChange={
										this.onSelectedRowsChange
									}
									pagination
								/>
							</div>
						) : (
							''
						)}
					</div>
				)}
			</div>
		) : name === 'knowladgebase' ? (
			<div className="mvx-module-grid">
				<div className='mvx-back-btn-wrapper'>
				{(get_current_name &&
					get_current_name.get('create') === 'knowladgebase') ||
				get_current_name.get('knowladgebaseID') ? (
					<button className="mvx-back-btn" onClick={(e) =>
						(
							location.href = appLocalizer.knowladgebase_back
						)
					}>
						<i className="mvx-font icon-back"></i>
						{appLocalizer.global_string.back}
					</button>
				) : (
					<button className="mvx-btn btn-purple" onClick={(e) =>
						(
							location.href = appLocalizer.add_knowladgebase_link
						)
					}>
						<i className="mvx-font icon-add"></i>
						{appLocalizer.workboard_string.workboard27}
					</button>
				)}
				</div>
				{get_current_name &&
				get_current_name.get('create') === 'knowladgebase' ? (
					<DynamicForm
						key={`dynamic-form-knowladgebase-add-new`}
						className="mvx-knowladgebase-add-new"
						title="Add new knowladgebase"
						model={
							appLocalizer.settings_fields.create_knowladgebase
						}
						method="post"
						modulename="create_knowladgebase"
						url="mvx_module/v1/create_knowladgebase"
						submit_title={appLocalizer.global_string.publish}
					/>
				) : get_current_name.get('knowladgebaseID') ? (
					this.state.edit_knowledgebase_fileds &&
					Object.keys(this.state.edit_knowledgebase_fileds).length >
						0 ? (
						<DynamicForm
							key={`dynamic-form-knowladgebase-add-new`}
							className="mvx-knowladgebase-add-new"
							title="Update Announcement"
							model={
								this.state.edit_knowledgebase_fileds
									.update_knowladgebase_display
							}
							method="post"
							knowladgebase_id={get_current_name.get(
								'knowladgebaseID'
							)}
							modulename="update_knowladgebase"
							url="mvx_module/v1/update_knowladgebase"
							submitbutton="false"
						/>
					) : (
						<PageLoader/>
					)
				) : (
					<div className="mvx-knowladgebase-different-funtionality">
						<div className="mvx-search-and-multistatus-wrap">
							<ul className="mvx-multistatus-ul">
								<li className={`mvx-multistatus-item ${this.state.workboard_list_knowledgebase_status_all ? 'status-active' : ''}`}>
									<div
										className="mvx-multistatus-check-all"
										onClick={(e) =>
											this.handlePostRetriveStatus(
												e,
												'all',
												'knowladgebase'
											)
										}
									>
										{appLocalizer.global_string.all} (
										{
											this.state.display_all_knowladgebase
												.length
										}
										)
									</div>
								</li>
								<li className="mvx-multistatus-item mvx-divider"></li>
								<li className={`mvx-multistatus-item ${this.state.workboard_list_knowledgebase_status_publish ? 'status-active' : ''}`}>
									<div
										className="mvx-multistatus-check-approve"
										onClick={(e) =>
											this.handlePostRetriveStatus(
												e,
												'publish',
												'knowladgebase'
											)
										}
									>
										{appLocalizer.global_string.published}(
										{
											this.state
												.display_publish_knowladgebase
												.length
										}
										)
									</div>
								</li>
								<li className="mvx-multistatus-item mvx-divider"></li>
								<li className={`mvx-multistatus-item ${this.state.workboard_list_knowledgebase_status_pending ? 'status-active' : ''}`}>
									<div
										className="mvx-multistatus-check-pending status-active"
										onClick={(e) =>
											this.handlePostRetriveStatus(
												e,
												'pending',
												'knowladgebase'
											)
										}
									>
										{appLocalizer.global_string.pending} (
										{
											this.state
												.display_pending_knowladgebase
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
										appLocalizer.workboard_string
											.workboard28
									}
									onChange={(e) =>
										this.handlePostBulkStatus(
											e,
											'knowladgebase'
										)
									}
								/>
							</div>
						</div>

						<div className="mvx-wrap-bulk-all-date">
							<Select
								placeholder={
									appLocalizer.global_string.bulk_action
								}
								options={appLocalizer.post_bulk_status}
								isClearable={true}
								className="mvx-wrap-bulk-action"
								onChange={this.handlePostBulkStatus}
							/>
							<Select
								placeholder={
									appLocalizer.global_string.all_dates
								}
								options={this.state.details_vendor}
								isClearable={true}
								className="mvx-wrap-bulk-action"
								onChange={this.handleWorkBoardChenage}
							/>
						</div>

						{this.state.columns_knowledgebase_new &&
						this.state.columns_knowledgebase_new.length > 0 ? (
							<div className="mvx-backend-datatable-wrapper">
								<DataTable
									columns={
										this.state.columns_knowledgebase_new
									}
									data={this.state.display_list_knowladgebase}
									selectableRows
									onSelectedRowsChange={
										this.onSelectedRowsChange
									}
									pagination
								/>
							</div>
						) : (
							''
						)}
					</div>
				)}
			</div>
		) : name === 'store-review' ? (
			<div className="mvx-module-grid">
				<div className="mvx-search-and-multistatus-wrap">
					<ul className="mvx-multistatus-ul">
						<li className="mvx-multistatus-item status-active">
							<div className="mvx-multistatus-check-all">
								{appLocalizer.global_string.all} (
								{this.state.list_of_store_review.length})
							</div>
						</li>
						<li clasName="mvx-multistatus-item mvx-divider"></li>
					</ul>
					<div className="mvx-header-search-section">
						<label>
							<i className="mvx-font icon-search"></i>
						</label>
						<input
							type="text"
							placeholder={
								appLocalizer.workboard_string.workboard8
							}
							onChange={(e) => this.handleSearchVendorReview(e)}
						/>
					</div>
				</div>
				<div className="mvx-wrap-bulk-all-date">
					<Select
						placeholder={appLocalizer.global_string.bulk_action}
						options={appLocalizer.select_option_delete}
						isClearable={true}
						className="mvx-wrap-bulk-action"
						onChange={this.handleReviewBulkStatus}
					/>
				</div>

				{this.state.columns_store_review &&
				this.state.columns_store_review.length > 0 ? (
					<div className="mvx-backend-datatable-wrapper">
						<DataTable
							columns={this.state.columns_store_review}
							data={this.state.list_of_store_review}
							selectableRows
							onSelectedRowsChange={this.handleselectreviews}
							pagination
						/>
					</div>
				) : (
					''
				)}
			</div>
		) : name === 'report-abuse' ? (
			<div className="mvx-module-grid">
				<div className="mvx-wrap-bulk-all-date">
					<Select
						placeholder={appLocalizer.workboard_string.workboard30}
						options={this.state.show_vendor_name}
						isClearable={true}
						className="mvx-wrap-bulk-action"
						onChange={this.handleVendorSearchAbuse}
					/>
					<Select
						placeholder={appLocalizer.workboard_string.workboard31}
						options={
							appLocalizer.question_product_selection_wordpboard
						}
						isClearable={true}
						className="mvx-wrap-bulk-action"
						onChange={this.handleProductSearchAbuse}
					/>
				</div>

				{this.state.columns_report_abuse &&
				this.state.columns_report_abuse.length > 0 ? (
					<div className="mvx-backend-datatable-wrapper">
						<DataTable
							columns={this.state.columns_report_abuse}
							data={this.state.list_of_report_abuse}
							selectableRows
							pagination
						/>
					</div>
				) : (
					''
				)}
			</div>
		) : name === 'refund-request' ? (
			<div className="mvx-module-grid">
				
				{this.state.columns_refund_request &&
				this.state.columns_refund_request.length > 0 ? (
					<div className="mvx-backend-datatable-wrapper">
						<DataTable
							columns={this.state.columns_refund_request}
							data={this.state.list_of_refund_request}
							selectableRows
							pagination
						/>
					</div>
				) : (
					''
				)}
			</div>
		) : name === 'refunded-order' ? (
			<div className="mvx-module-grid">
				
				{this.state.columns_refunded_order &&
				this.state.columns_refunded_order.length > 0 ? (
					<div className="mvx-backend-datatable-wrapper">
						<DataTable
							columns={this.state.columns_refunded_order}
							data={this.state.list_of_refunded_order}
							selectableRows
							pagination
						/>
					</div>
				) : (
					''
				)}
			</div>
		) : name === 'question-ans' ? (
			<div className="mvx-module-grid">
				<div className="mvx-search-and-multistatus-wrap">
					<ul className="mvx-multistatus-ul">
						<li className="mvx-multistatus-item">
							<div className="mvx-multistatus-check-all">
								{appLocalizer.global_string.all} (
								{this.state.list_of_publish_question.length})
							</div>
						</li>
						<li className="mvx-multistatus-item mvx-divider"></li>
						<li className="mvx-multistatus-item">
							<div className="mvx-multistatus-check-pending status-active">
								{appLocalizer.global_string.pending} (
								{this.state.list_of_pending_question.length})
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
								appLocalizer.workboard_string.workboard32
							}
							onChange={(e) => this.handleQuestionSearch(e)}
						/>
					</div>
				</div>

				<div className="mvx-wrap-bulk-all-date">
					<Select
						placeholder={appLocalizer.workboard_string.workboard33}
						options={appLocalizer.question_selection_wordpboard}
						isClearable={true}
						className="mvx-wrap-bulk-action"
						onChange={this.handleQuestionBulkStatusChange}
					/>
					<Select
						placeholder={appLocalizer.workboard_string.workboard31}
						isMulti
						options={
							appLocalizer.question_product_selection_wordpboard
						}
						isClearable={true}
						className="mvx-wrap-bulk-action"
						onChange={this.handleQuestionBulkStatusChange}
					/>
				</div>

				{this.state.columns_questions_new &&
				this.state.columns_questions_new.length > 0 ? (
					<div className="mvx-backend-datatable-wrapper">
						<DataTable
							columns={this.state.columns_questions_new}
							data={this.state.list_of_publish_question}
							selectableRows
							pagination
						/>
					</div>
				) : (
					''
				)}
			</div>
		) : (
			''
		);
	}

	render() {
		return (
				<this.QueryParamsDemo />
		);
	}
}
export default MVXworkboard;