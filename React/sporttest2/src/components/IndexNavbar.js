import React from "react";
import { langText } from "../pages/LanguageContext";
import  "../css/IndexNavbar.css";

class indexNavbar extends React.Component {
	constructor() {
		super();
		this.state = {
		  	activeTab: 0,
		};
	}
	
	// 切換上方分頁
	handleClick = (tabIndex) => {
		this.setState({ activeTab: tabIndex });
	};

	render() {
		const { activeTab } = this.state;
    	return (
			<div id="navBarContainer">
				<img id="logo" alt="logo" src={require('../image/logo.png')} />
				<div onClick={() => this.handleClick(0)} className={activeTab === 0 ? 'on' : ''}>
					{langText.IndexNavbar.home}
				</div>
				<div onClick={() => this.handleClick(1)} className={activeTab === 1 ? 'on' : ''}>
					{langText.IndexNavbar.hot}
				</div>
				<div onClick={() => this.handleClick(2)} className={activeTab === 2 ? 'on' : ''}>
					{langText.IndexNavbar.streamimg}
				</div>
			</div>
		)
  	}
}


export default indexNavbar;