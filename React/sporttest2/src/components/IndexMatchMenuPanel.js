import React from "react";
import { Link } from "react-router-dom";
import { langText } from "../pages/LanguageContext";
import 'bootstrap/dist/css/bootstrap.css';

const MatchMenuPanelStyle = {
	width: '100%',
	color: 'rgb(197, 214, 213)',
	fontWeight: '600',
	backgroundColor: 'rgb(65, 91, 90)',
	borderRadius: '15px',
	height: '5.5rem',
	marginBottom: '0.5rem',
	padding: '0.5rem',
	backgroundPosition: 'right',
    backgroundRepeat: 'no-repeat',
    backgroundSize: '45%',
}

const PanelTextStyle = {
	fontSize: '1.2rem',
	marginLeft: '0.8rem',
	marginBottom: 0,
}

const PanelTextStyle2 = {
	marginLeft: '0.8rem',
	display: 'flex'
}

const PanelSpanStyle = {
	fontSize: '0.9rem',
	marginTop: '1rem',
	marginRight: '0.5rem'
}

const PanelSpanStyle2 = {
	fontSize: '2rem',
	color: '#c79e42 ',
	opacity: 0.8
}

class IndexMatchMenuPanel extends React.Component {
	render() {
		return(
			<Link to="/mobile/match">
				<div style={{ ...MatchMenuPanelStyle, backgroundImage: `url(${require(`../image/ball/ball-${this.props.sport}.png`)})` }}>
					<p style={PanelTextStyle}>{this.props.name}</p>
					<div style={PanelTextStyle2}>
						<div style={PanelSpanStyle}>{langText.IndexMatchMenuPanel.availlableCount}</div>
						<div style={PanelSpanStyle2}>{this.props.count}</div>
					</div>
				</div>
			</Link>
		)
	}
}

export default IndexMatchMenuPanel;