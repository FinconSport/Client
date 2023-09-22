import React from "react";

const logoImg = {
	position: 'absolute',
	width: '5rem',
	height: '5rem',
	left: 'calc(50% - 2.5rem)',
	top: 'calc(30%)'
}
const spinImg = {
	position: 'absolute',
	width: '8rem',
	height: '8rem',
	left: 'calc(50% - 4rem)',
	top: 'calc(30% - 2rem)',
	animation: 'rotate 5s linear infinite'

}
const titleImg = {
	position: 'absolute',
	width: '10rem',
	left: 'calc(50% - 5rem)',
	top: 'calc(30% + 6.5rem)'
}
const loadingStyle = {
	width: '100%',
	height: '100%',
	position: 'fixed',
	backgroundColor: 'rgba(0, 0, 0, 0.8)',
}

class CommonLoader extends React.Component {
	render() {
		return(
			<div style={loadingStyle}>
				<img alt="logo" style={logoImg} src={require('../image/loading-logo.png')} />
				<img alt="spin" style={spinImg} src={require('../image/loading-spin.png')} />
				<img alt="title" style={titleImg} src={require('../image/loading-title.png')} />
			</div>
		)
	}
}


export default CommonLoader;