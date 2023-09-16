import React from "react";
import twTranslation from '../lang/tw';
import  "../css/ErrorsPage.css";


class E404 extends React.Component {
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
					<p className="error-num">404</p>
				</div>
			</div>
		);
	}
};

export default E404;