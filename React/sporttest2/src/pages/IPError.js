import React from "react";
import twTranslation from '../lang/tw';
import  "../css/ErrorsPage.css";


class IPError extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			
		};
	}

	render() {
		let iperror = twTranslation.ErrorPage.iplocationnotallowed;
		return (
			<div className="error-bg">
				<div className="error-image-container">
					<img src={ require('../image/error_page/ip-error.png' ) }/>
					<p className="ip-error-text">{iperror}</p>
				</div>
			</div>
		);
	}
};

export default IPError;