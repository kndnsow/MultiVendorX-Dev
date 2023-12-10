/* global appLocalizer */
import React, { Component } from 'react';
import axios from 'axios';
class MVX_Header extends Component {
	constructor(props) {
		super(props);
		this.state = {
			fetch_all_settings_for_searching: [],
		};
		this.handleOnChange = this.handleOnChange.bind(this);
	}

	handleOnChange(event) {
		axios
			.get(
				`${appLocalizer.apiUrl}/mvx_module/v1/fetch_all_settings_for_searching`,
				{
					params: { value: event.target.value },
				}
			)
			.then((response) => {
				this.setState({
					fetch_all_settings_for_searching: response.data,
				});
			});
	}

	render() {
		return (
			<div className="mvx-header-wapper">
				<div className="mvx-header-nav-left-section">
					<div className="mvx-header-section-nav-child-data">
						<img
							src={appLocalizer.mvx_logo}
							className="mvx-section-img-fluid mvx-logo-img"
							width="50px"
							height="50px"
						/>
					</div>
					<div className="mvx-header-section-nav-child-data mvx-logo-title">
						{appLocalizer.marketplace_text}
					</div>
				</div>

				<div className="mvx-header-nav-right-section">
					<div className="mvx-header-search-section">
						<label>
							<i className="mvx-font icon-search"></i>
						</label>
						<input
							type="text"
							placeholder={appLocalizer.search_module}
							onChange={(e) => this.handleOnChange(e)}
						/>

						{this.state.fetch_all_settings_for_searching.length >
						0 ? (
							<div className="mvx-search-content">
								{this.state.fetch_all_settings_for_searching.map(
									(data, index) => (
										<div className="mvx-header-search-content-wrapper">
											<a onClick={(e) =>
												(
													window.location.href = data.link
													
												)
											}>
												<div className="mvx-header-search-contetnt-label">
													{data.label}
												</div>
												<p
													className="mvx-header-search-contetnt-desc"
													dangerouslySetInnerHTML={{
														__html: data.desc,
													}}
												></p>
												<p
													className="mvx-header-search-contetnt-desc"
													dangerouslySetInnerHTML={{
														__html: data.details,
													}}
												></p>
											</a>
										</div>
									)
								)}
							</div>
						) : (
							''
						)}
					</div>
					<a
						href={appLocalizer.knowledgebase}
						title={appLocalizer.knowledgebase_title}
						target="_blank"
						className="mvx-module-section-nav-child-data nav-child-right"
						rel="noreferrer"
					>
						<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyVpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDYuMC1jMDAyIDc5LjE2NDQ2MCwgMjAyMC8wNS8xMi0xNjowNDoxNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIDIxLjIgKE1hY2ludG9zaCkiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QjEwREJGNUZDRjlGMTFFQ0E1NTFGQTZDNTk5N0U4RUUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QjEwREJGNjBDRjlGMTFFQ0E1NTFGQTZDNTk5N0U4RUUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpCMTBEQkY1RENGOUYxMUVDQTU1MUZBNkM1OTk3RThFRSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpCMTBEQkY1RUNGOUYxMUVDQTU1MUZBNkM1OTk3RThFRSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pla3VCcAAAFTSURBVHjajNO/S8NAFMDxl6SpFlq1Kog//gAn/QMcBBfBVRB1EBxEJx2yiNbBTREnF3dxUdDJQZeOgg46CLoJIigogmiH0kb8nrxCGi6tDz70Lu/d9fKSOKPdgWQ8kZzvSLsv0pEWyetvmy9BNiXkw91WL5S0W/njI+VWxXOqQrph5JrkxY2MDzATy5/gNHZtGoe1SfQE2zjDA26QRR6hnuQLw9jBhG2DOwxp4SoKesIWlLGpiwdRst2CiU+sYA1zGMMlMtjSXCmpByZ4HrKOAOO6eCSSL2hN3QZF3GIeA+jCFRYsTe9Ev9aaNUXTg1m9zzc4qOBax/H4wQeO9I/LZoOXWNGxbmoL80i/dVyy9UC0UY+W609YavQi1eIdy5bri5qri6RX+Rz36NP5My5shUkbmEbuoUfnr9rAf29gYgO9kRPsN/uY4jGp34ExlVT0K8AA70JDMMPPpm8AAAAASUVORK5CYII=" />
					</a>
				</div>
			</div>
		);
	}
}
export default MVX_Header;
