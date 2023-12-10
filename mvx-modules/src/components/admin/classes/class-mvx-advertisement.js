/* global appLocalizer, location */
import React from "react";
import axios from "axios";
import Select from "react-select";
import DataTable from "react-data-table-component";
import { BrowserRouter as Router, useLocation } from "react-router-dom";
import Dialog from "@mui/material/Dialog";
import DialogActions from "@mui/material/DialogActions";
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";
import DialogTitle from "@mui/material/DialogTitle";
import HeaderSection from './class-mvx-page-header';
import BannerSection from './class-mvx-page-banner';
import PageLoader from './class-mvx-page-loader.js';

class Advertisement extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
        current_url: "",
        bulkselectlist: [],
        columns_adv_list: [],
        data_adv: [],
        data_all_adv: [],
        data_expire_adv: [],
        data_active_adv: [],
        data_product_id_adv: [],
        product_list_for_specific_id: [],
        data_add_adv: [],
        open_adv_model: false,
        adv_loading: false,
        adv_list_status_active: false,
        adv_list_status_expire: false,
        adv_list_status_all: false,
    };
    this.QueryParamsDemo = this.QueryParamsDemo.bind(this);
    this.handleBulkActions = this.handleBulkActions.bind(this);
    this.handleAdvertisementSearch = this.handleAdvertisementSearch.bind(this);
    this.handleChange = this.handleChange.bind(this);
    this.different_adv_status = this.different_adv_status.bind(this);
    this.handleAdvertisementFilter = this.handleAdvertisementFilter.bind(this);
    this.handleAdvertisementStoreFilter = this.handleAdvertisementStoreFilter.bind(this);
    this.handleStoreSelectBoxModal = this.handleStoreSelectBoxModal.bind(this);
    this.handleProductSelectBoxModal =
      this.handleProductSelectBoxModal.bind(this);
    this.handleAddAdvModal = this.handleAddAdvModal.bind(this);
    this.handleOpenDialog = this.handleOpenDialog.bind(this);
    this.handleCloseDialog = this.handleCloseDialog.bind(this);
    this.handleTableData = this.handleTableData.bind(this);
  }

  handleAdvertisementFilter(e, status) {
    if (status === "createdviafilter") {
      if (e) {
        axios
          .get(
            `${appLocalizer.apiUrl}/modules/advertisement/v1/advertisement_details`,
            {
              params: { create_filter: e.value },
            }
          )
          .then((response) => {
            this.setState({
              data_adv: response.data,
            });
          });
      } else {
        axios({
          url: `${appLocalizer.apiUrl}/modules/advertisement/v1/advertisement_details`,
        }).then((response) => {
            this.setState({
            data_adv: response.data,
          });
        });
      }
    }
  }

  handleAdvertisementStoreFilter(e, status) {
    if (status === "storefilter") {
      if (e) {
        axios
          .get(
            `${appLocalizer.apiUrl}/modules/advertisement/v1/advertisement_details`,
            {
              params: { store_filter: e.value },
            }
          )
          .then((response) => {
            this.setState({
              data_adv: response.data,
            });
          });
      } else {
        axios({
          url: `${appLocalizer.apiUrl}/modules/advertisement/v1/advertisement_details`,
        }).then((response) => {
          this.setState({
            data_adv: response.data,
          });
        });
      }
    }
  }

  different_adv_status(e, type) {
    this.setState({
      adv_loading: false,
    });
    if (type === "active") {
        this.setState({
        adv_list_status_active: true,
        adv_list_status_expire: false,
        adv_list_status_all: false,
      });
      axios
        .get(`${appLocalizer.apiUrl}/modules/advertisement/v1/advertisement_details`, {
          params: { status: type },
        })
        .then((response) => {
          this.setState({
            data_adv: response.data,
            adv_loading: true,
          });
        });
    } else if (type === "expire") {
      this.setState({
        adv_list_status_active: false,
        adv_list_status_expire: true,
        adv_list_status_all: false,
      });
      axios
        .get(`${appLocalizer.apiUrl}/modules/advertisement/v1/advertisement_details`, {
          params: { status: type },
        })
        .then((response) => {
          this.setState({
            data_adv: response.data,
            adv_loading: true,
          });
        });
    } else if (type === "all") {
      this.setState({
        adv_list_status_active: false,
        adv_list_status_expire: false,
        adv_list_status_all: true,
      });
      axios({
        url: `${appLocalizer.apiUrl}/modules/advertisement/v1/advertisement_details`,
      }).then((response) => {
        this.setState({
          data_adv: response.data,
          adv_loading: true,
        });
      });
    }
  }

  handleAdvertisementSearch(e) {
    if (e.target.value) {
      axios
        .get(`${appLocalizer.apiUrl}/modules/advertisement/v1/advertisement_details`, {
          params: { search: e.target.value },
        })
        .then((response) => {
          this.setState({
            data_adv: response.data,
          });
        });
    } else {
        axios({
            url: `${appLocalizer.apiUrl}/modules/advertisement/v1/advertisement_details`,
        }).then((response) => {
            this.setState({
                data_adv: response.data,
            });
        });
        }
  }

  handleBulkActions(e) {
    if (e) {
      if (confirm(appLocalizer.global_string.sure_text) === true) {
        axios({
          method: "post",
          url: `${appLocalizer.apiUrl}/modules/advertisement/v1/advertisement_bulk_actions`,
          data: {
            adv_ids: this.state.bulkselectlist,
            select_input: e.value,
          },
        }).then((response) => {
          this.setState({
            data_adv: response.data,
          });
        });
      }
    }
  }

  handleStoreSelectBoxModal(e) {
    if (e) {
      axios
        .get(
          `${appLocalizer.apiUrl}/modules/advertisement/v1/show_products_for_particular_store_id`,
          {
            params: { store_id: e.value },
          }
        )
        .then((response) => {
          this.setState({
            product_list_for_specific_id: response.data,
          });
        });
    }
  }

  handleProductSelectBoxModal(e) {
    if (e) {
      this.setState({
        data_product_id_adv: e.value,
      });
    }
  }

  handleAddAdvModal() {
    axios
      .get(
        `${appLocalizer.apiUrl}/modules/advertisement/v1/add_advertisement_form_admin`,
        {
          params: { product_id: this.state.data_product_id_adv },
        }
      )
      .then((response) => {
        alert(response.data.message);
        this.setState({
          open_adv_model: false,
        });
        this.handleTableData();
      });
  }

  handleOpenDialog() {
    this.setState({
      open_adv_model: true,
    });
  }

  handleCloseDialog() {
    this.setState({
      open_adv_model: false,
    });
  }

  useQuery() {
    return new URLSearchParams(useLocation().hash);
  }

  handleTableData() {
    axios({
      url: `${appLocalizer.apiUrl}/modules/advertisement/v1/advertisement_details`,
    }).then((response) => {
      this.setState({
        data_adv: response.data,
        data_all_adv: response.data,
        adv_loading: true,
        current_url: window.location.hash,
      });
    });
  }

  QueryParamsDemo(e) {
    if (
      window.location.hash !== this.state.current_url &&
      !this.useQuery().get("ID")
    ) {
      this.handleTableData();
    }
    if (
      this.state.columns_adv_list.length === 0 &&
      new URLSearchParams(window.location.hash).get("submenu") ===
        "advertisement"
    ) {
      appLocalizer.columns_advertisement.map((data_ann, index_ann) => {
        let data_selector = "";
        let set_for_dynamic_column = "";
        data_selector = data_ann.selector_choice;
        data_ann.selector = (row) => (
          <div dangerouslySetInnerHTML={{ __html: row[data_selector] }}></div>
        );

        this.state.columns_adv_list[index_ann] = data_ann;
        set_for_dynamic_column = this.state.columns_adv_list;
        this.setState({
          columns_adv_list: set_for_dynamic_column,
        });
      });
    }

    return (
      <div className="mvx-general-wrapper">
        <HeaderSection />
        <div className="mvx-container">
          <div className="mvx-middle-container-wrapper">
            <div className="mvx-page-title">
              <p>{appLocalizer.advertising_page_string.advertisements}</p>
              <div className="mvx-btn btn-purple"
                onClick={this.handleOpenDialog}>
                <i className="mvx-font icon-add"></i>
                {appLocalizer.advertising_page_string.add_advertisement}
              </div>
              <Dialog
                className="adv-modal-box"
                open={this.state.open_adv_model}
                onClose={this.handleCloseDialog}
                aria-labelledby="form-dialog-title"
              >
                <DialogTitle>
                  <div className="mvx-module-dialog-title">
                    {appLocalizer.advertising_page_string.add_new_advertisement}
                    <i className="mvx-font icon-no"
                      onClick={this.handleCloseDialog} ></i>
                  </div>
                </DialogTitle>
                <DialogContent>
                  <DialogContentText>
                    <div className="mvx-module-dialog-content">
                      <div className="mvx-modal-select-box">
                        <label>
                          {appLocalizer.advertising_page_string.show_all_stores}
                        </label>
                        <Select
                          placeholder={
                            appLocalizer.advertising_page_string.show_all_stores
                          }
                          options={
                            appLocalizer.advertisement_select_option_store
                          }
                          isClearable={true}
                          onChange={(e) => this.handleStoreSelectBoxModal(e)}
                        />
                      </div>
                      <div className="mvx-modal-select-box">
                        <label>
                          {
                            appLocalizer.advertising_page_string
                              .show_all_products
                          }
                        </label>
                        <Select
                          placeholder={
                            appLocalizer.advertising_page_string
                              .show_all_products
                          }
                          options={this.state.product_list_for_specific_id}
                          isClearable={true}
                          onChange={(e) => this.handleProductSelectBoxModal(e)}
                        />
                      </div>
                      <div className="mvx-vendor-multi-action-buttons">
                        <button
                          className="mvx-btn btn-red"
                          onClick={this.handleAddAdvModal}
                          color="primary"
                        >
                        {appLocalizer.advertising_page_string.add}
                        </button>
                      </div>
                    </div>
                  </DialogContentText>
                </DialogContent>
                <DialogActions></DialogActions>
              </Dialog>
            </div>
            <div className="mvx-search-and-multistatus-wrap">
              <ul className="mvx-multistatus-ul">
                <li
                  className={`mvx-multistatus-item ${
                    this.state.adv_list_status_all ? "status-active" : ""
                  }`}
                >
                  <div
                    className="mvx-multistatus-check-all"
                    onClick={(e) => this.different_adv_status(e, "all")}
                  >
                    {appLocalizer.global_string.all} (
                    {this.state.data_all_adv.length})
                  </div>
                </li>
                <li className="mvx-multistatus-item mvx-divider"></li>
                <li
                  className={`mvx-multistatus-item ${
                    this.state.adv_list_status_active ? "status-active" : ""
                  }`}
                >
                  <div
                    className="mvx-multistatus-check-approve"
                    onClick={(e) => this.different_adv_status(e, "active")}
                  >
                    {appLocalizer.advertising_page_string.active} (
                    {this.state.data_active_adv.length})
                  </div>
                </li>
                <li className="mvx-multistatus-item mvx-divider"></li>
                <li
                  className={`mvx-multistatus-item ${
                    this.state.adv_list_status_expire ? "status-active" : ""
                  }`}
                >
                  <div
                    className="mvx-multistatus-check-pending status-active"
                    onClick={(e) => this.different_adv_status(e, "expire")}
                  >
                    {appLocalizer.advertising_page_string.expire} (
                    {this.state.data_expire_adv.length})
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
                    appLocalizer.advertising_page_string.search_advertisement
                  }
                  onChange={this.handleAdvertisementSearch}
                />
              </div>
            </div>
            <div className="mvx-wrap-bulk-all-date">
              <Select
                placeholder={
                  appLocalizer.advertising_page_string.show_created_via_filter
                }
                options={appLocalizer.advertisement_created_via_action}
                isClearable={true}
                className="mvx-wrap-bulk-action"
                onChange={(e) =>
                  this.handleAdvertisementFilter(e, "createdviafilter")
                }
              />
              <Select
                placeholder={
                  appLocalizer.advertising_page_string.show_all_stores
                }
                options={appLocalizer.advertisement_select_option_store}
                isClearable={true}
                className="mvx-wrap-bulk-action"
                onChange={(e) =>
                  this.handleAdvertisementStoreFilter(e, "storefilter")
                }
              />
              <Select
                placeholder={appLocalizer.global_string.bulk_action}
                options={appLocalizer.advertisement_bulk_list_action}
                isClearable={true}
                className="mvx-wrap-bulk-action"
                onChange={this.handleBulkActions}
              />
            </div>
            {this.state.columns_adv_list &&
            this.state.columns_adv_list.length > 0 &&
            this.state.adv_loading ? (
              <div className="mvx-backend-datatable-wrapper">
                <DataTable
                  columns={this.state.columns_adv_list}
                  data={this.state.data_adv}
                  selectableRows
                  onSelectedRowsChange={this.handleChange}
                  pagination
                />
              </div>
            ) : (
                <PageLoader/>            
            )}
          </div>
          <BannerSection />
        </div>
      </div>
    );
  }

  handleChange(e) {
    this.setState({
      bulkselectlist: e.selectedRows,
    });
  }

  componentDidMount() {
    // active adv
    axios
      .get(`${appLocalizer.apiUrl}/modules/advertisement/v1/advertisement_details`, {
        params: { status: 'active' },
      })
      .then((response) => {
        this.setState({
          data_active_adv: response.data,
        });
      });

    // expire adv
    axios
      .get(`${appLocalizer.apiUrl}/modules/advertisement/v1/advertisement_details`, {
        params: { status: 'expire' },
      })
      .then((response) => {
        this.setState({
          data_expire_adv: response.data,
        });
      });

    // set adv list section top label status
    this.setState({
      adv_list_status_all: true,
    });
  }

  render() {
    return (
        <this.QueryParamsDemo />
    );
  }
}

export default Advertisement;
