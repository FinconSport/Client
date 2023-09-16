import React from "react";
import twTranslation from '../lang/tw';
import  "../css/ErrorsPage.css";


class Maintain extends React.Component {
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
					<img src={ require('../image/error_page/repair.png' ) }/>
					<p className="error-text">{inmaintainance}</p>
				</div>
			</div>
		);
	}
};

export default Maintain;