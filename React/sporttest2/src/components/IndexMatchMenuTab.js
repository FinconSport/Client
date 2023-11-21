import React from "react";
import { langText } from "../pages/LanguageContext";
import { MdToday } from "react-icons/md";
import { CgMediaLive } from "react-icons/cg";
import { FaEarlybirds } from "react-icons/fa";
import { AiFillTrophy } from "react-icons/ai";

const MatchMenuTabStyle = {
	float: 'left',
	borderRadius: '10px',
	fontWeight: '600',
	textAlign: 'center',
	height: '5rem',
	lineHeight: '1.5rem',
	boxShadow: 'rgb(150,150,150) 0px 2px 3px 0px',
	marginBottom: '0.5rem',
	paddingTop: '0.3rem',
    width: '100%',
	background: '#445a5a',
    color: '#c4d3d3'
}

const iconStyle = {
	color: '#c4d3d3'
}

const TabSelected = {
	background: '#445a5a',
	color: '#c19e4f',
}

const iconStyleSelected = {
	color: '#c19e4f',
}

class IndexMatchMenuTab extends React.Component {
	render() {
		return(
			<div style={
				this.props.selected === true ?
				{...MatchMenuTabStyle, ...TabSelected}
				:
				MatchMenuTabStyle
			}>
				{
					this.props.text === langText.IndexMatchList.today ? (
						<MdToday style={ this.props.selected === true ? {...iconStyle, ...iconStyleSelected} : iconStyle } />
					) : this.props.text === langText.IndexMatchList.living ? (
						<CgMediaLive style={ this.props.selected === true ? {...iconStyle, ...iconStyleSelected} : iconStyle } />
					) : this.props.text === langText.IndexMatchList.early ? (
						<FaEarlybirds style={ this.props.selected === true ? {...iconStyle, ...iconStyleSelected} : iconStyle } />
					) : (
						<AiFillTrophy style={ this.props.selected === true ? {...iconStyle, ...iconStyleSelected} : iconStyle } />
					)
				}
				<div>{this.props.text}</div>
				<div style={{ fontSize: '0.9rem' }}>{this.props.total}</div>
			</div>
		)
	}
}
	



export default IndexMatchMenuTab;
