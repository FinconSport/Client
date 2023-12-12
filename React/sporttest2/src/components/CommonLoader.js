import React from "react";


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

const logoImg = {
	position: 'relative',
	width: '20%',
	margin: '0 -0.45rem'
}

class CommonLoader extends React.Component {
	render() {
		return(
			<div style={loadingStyle}>
				<div className="loading loading04" style={{ width: '60%', margin: '90% auto', position: 'relative', textAlign: 'center' }}>
					<img alt="logo" style={logoImg} src={require('../image/loading/f.png')} />
					<img alt="logo" style={logoImg} src={require('../image/loading/t.png')} />
					<img alt="logo" style={logoImg} src={require('../image/loading/3.png')} />
					<img alt="logo" style={logoImg} src={require('../image/loading/6.png')} />
					<img alt="logo" style={logoImg} src={require('../image/loading/5.png')} />
				</div>
			</div>
		)
	}
}


export default CommonLoader;