import React from 'react';
import { BrowserRouter as Router, Link } from 'react-router-dom';
import HeaderSection from './class-mvx-page-header';
import BannerSection from './class-mvx-page-banner';

export default class TabSection extends React.Component {
	state = {};
	constructor(props) {
		super(props);
		this.state = {};
	}

	renderTab = () => {
		const model = this.props.model;
		const funtion_name = this.props.funtion_name;
		const horizontally = this.props.horizontally;
		const no_banner = this.props.no_banner;
		const no_header = this.props.no_header;
		const query_name = this.props.query_name;
		const query_name_modified = this.props.vendor
			? query_name.get('name')
			: query_name;
		const TabUI = model.map((m, index) => {
			return query_name_modified === m.modulename ? (
				<div className="mvx-tab-description-start">
					<div className="mvx-tab-name">{m.tablabel}</div>
					<p>{m.description}</p>
				</div>
			) : (
				''
			);
		});

		const TabUIContent = (
			<div className={`mvx-general-wrapper mvx-${query_name_modified}`}>
				{no_header ? '' : <HeaderSection />}
				<div className="mvx-container">
					<div
						className={`mvx-middle-container-wrapper ${
							horizontally
								? 'mvx-horizontal-tabs'
								: 'mvx-vertical-tabs'
						}`}
					>
						{this.props.tab_description &&
						this.props.tab_description === 'no'
							? ''
							: TabUI}
						<div className="mvx-middle-child-container">
							{this.props.no_tabs ? (
								''
							) : (
								<div className="mvx-current-tab-lists">
									{model.map((m, index) => {
										return m.link ? (
											
												<a href={m.link}>
													{m.icon ? (
														<i
															className={`mvx-font ${m.icon}`}
														></i>
													) : (
														''
													)}
													{m.tablabel}
												</a>
											
										) : (
											
												<Link

													className={
													query_name_modified ===
													m.modulename
														? 'active-current-tab'
														: ''
												}

													to={
														this.props.vendor
															? `?page=mvx#&submenu=${
																	m.submenu
															  }&ID=${query_name.get(
																	'ID'
															  )}&name=${
																	m.modulename
															  }`
															: `?page=mvx#&submenu=${m.submenu}&name=${m.modulename}`
													}
												>
													{m.icon ? (
														<i
															className={`mvx-font ${m.icon}`}
														></i>
													) : (
														''
													)}
													{m.tablabel}
												</Link>
											
										);
									})}
								</div>
							)}
							<div className="mvx-tab-content">
								{this.props.default_vendor_funtion ? (
									<funtion_name.Childparent
										name={query_name}
									/>
								) : (
									<funtion_name.Child name={query_name} />
								)}
							</div>
						</div>
					</div>
					{no_banner ? '' : <BannerSection />}
				</div>
			</div>
		);
		return TabUIContent;
	};

	render() {
		return this.renderTab();
	}
}
