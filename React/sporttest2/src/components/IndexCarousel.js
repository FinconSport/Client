import Slider from "react-slick";
import React from "react";
import GetIni from './AjaxFunction'

import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import  "../css/IndexCarousel.css";


const imgStyle = {
    width: 'inherit',
    margin : 'auto',
    borderRadius: '10px',
}


const teamTitle = {
    padding: '20px',
    position: 'absolute',
    translate: '-50%',
    left: '50%',
    top: '0',
}

class IndexCarousel extends React.Component {
    constructor(props) {
		super(props);
		this.state = {
            apiUrl: 'https://sportc.asgame.net/api/v1/index_carousel?token=' + window.token+ '&player=' + window.player,
		};
	}

	async caller() {
		const json = await GetIni(this.state.apiUrl); 
		this.setState({
			status: json.status,
			data: json.data,
			message: json.message
		})
	}

	componentDidMount() {
		this.caller()
	}


    render() {
        var settings = {
            dots: true,
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            cssEase: "linear",
        };
        const { data } = this.state
        
        if(data !== undefined){
            return (
                <div style={{ marginTop: '0.5rem', padding: '0 0.5rem' }}>
                    <Slider {...settings}>
                        <div>
                            <img style={imgStyle} alt="carousel_bg" src={require('../image/carousel_bg.png')} />
                        </div>
                        <div>
                            <img style={imgStyle} alt="sport" src={require('../image/sport.jpg')} />
                        </div>
                        <div style={teamTitle}>
                        <div className="IndexCarouselGame">
                            <div className="CarouselGameDiv">
                                <img alt='home' src={require('../image/icon/teamIcon/主隊1.png')} />
                                <div>主隊1</div>
                            </div>
                            <div className="CarouselGameDiv CarouselGameScore">
                                <div>2&nbsp;-&nbsp;3</div>
                                <div className="mt-1" style={{fontSize: '0.9rem'}}>2023-07-03</div>
                            </div>
                            <div className="CarouselGameDiv">
                                <img alt='away' src={require('../image/icon/teamIcon/客隊1.png')} />
                                <div>客隊1</div>
                            </div>
                        </div>
                    </div>
                    </Slider>
                </div>
            );

        }
    }
}

export default IndexCarousel;