import React from "react";
import Marquee from "react-fast-marquee";

const marqueeStyle = {
	color: 'white',
	lineHeight: '2rem',
	height: '2rem',
	fontSize: '1rem',
	width: 'calc(100% - 1rem)',
	backgroundColor: 'rgb(65, 91, 90)',
	borderRadius: '20px',
	marginBottom: '0.5rem',
	zIndex: 0,
	left: '0.5rem'
}

class IndexMarquee extends React.Component {
	render() {
		const res = this.props.api_res
        if( res !== undefined){
			return(
				<Marquee speed={50} style={marqueeStyle} gradient={false}>
					{res.data.map((text, i) => {     
						return (<p className="m-0 ms-4" key={i}>{text}</p>) 
					})}
				</Marquee>
			)
		}
	}
}

export default IndexMarquee;