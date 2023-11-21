import Slider from "react-slick";
import React from "react";
import GetIni from './AjaxFunction'

import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import  "../css/IndexCarousel.css";


const imgStyle = {
    width: '100%',
    height: '10.5rem',
    margin : 'auto',
    borderRadius: '10px',
}


const teamTitle = {
    marginTop: '4rem',
    fontWeight: 600,
    fontSize: '1.2rem',
}



class IndexCarousel extends React.Component {
    constructor(props) {
		super(props);
		this.state = {
		};
	}

    // 日期格式
    formatDateTime = (dateTimeString) => {
        const dateTime = new Date(dateTimeString);
        const month = (dateTime.getMonth() + 1).toString().padStart(2, '0'); // Get month (0-based index), add 1, and pad with '0' if needed
        const day = dateTime.getDate().toString().padStart(2, '0'); // Get day and pad with '0' if needed
        const hour = dateTime.getHours().toString().padStart(2, '0'); // Get hours and pad with '0' if needed
        const minute = dateTime.getMinutes().toString().padStart(2, '0'); // Get minutes and pad with '0' if needed
        return `${month}-${day} ${hour}:${minute}`;
    }

    render() {
        var settings = {
            dots: true,
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 5000,
            cssEase: "linear",
        };

        const { data } = this.props.api_res
        if(data !== undefined){
            return (
                <div style={{ padding: '0 0.5rem', height: '10.5rem', marginBottom: '0.5rem' }}>
                    <Slider {...settings}>
                        <div>
                            <img style={imgStyle} alt="carousel_bg" src={require('../image/carousel_bg.png')} />
                        </div>
                        <div>
                            <img style={imgStyle} alt="sport" src={require('../image/sport.jpg')} />
                        </div>
                        {/* {
                            data.map( ele => {
                                return(
                                    <div className="IndexCarouselGame" key={ele} >
                                        <div className="CarouselGameDiv">
                                            <div style={teamTitle}>{ ele.home }</div>
                                        </div>
                                        <div className="CarouselGameDiv CarouselGameScore">
                                            <div>{ ele.home_score }&nbsp;-&nbsp;{ ele.away_score }</div>
                                            <div className="mt-1" style={{fontSize: '0.9rem'}}>{ this.formatDateTime(ele.match_time) }</div>
                                        </div>
                                        <div className="CarouselGameDiv">
                                            <div style={teamTitle}>{ ele.away }</div>
                                        </div>
                                    </div>
                                )
                            })
                        } */}
                    </Slider>
                </div>
            );

        }
    }
}

export default IndexCarousel;