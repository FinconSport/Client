import React from "react";
import twTranslation from '../lang/tw';
import  "../css/ErrorsPage.css";


class E500 extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			
		};
	}

	render() {
		let inmaintainance = twTranslation.ErrorPage.inmaintenance;
		return (
			<div className="error-bg">
				<div className="error-image-container">
					<img src={ require('../image/error_page/error.png' ) }/>
					<p className="error-num">500</p>
				</div>
			</div>
		);
	}
};

export default E500;