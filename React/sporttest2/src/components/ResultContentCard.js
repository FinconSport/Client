import React from "react";
import Marquee from "react-fast-marquee";
import { langText } from "../pages/LanguageContext";
import $ from 'jquery';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Controller, Pagination } from 'swiper';
import styled from '@emotion/styled';
import 'bootstrap/dist/css/bootstrap.css';
import  "../css/ResultPage.css";
import 'swiper/css';

const ResultCard_item = { 
    position: 'relative',
    background: '#e2f0f0',
    borderRadius: '15px',
    marginBottom: '1rem',
    zIndex: 1,
    transition: 'opacity 0.5s ease, max-height 0.5s ease, padding 0.5s ease, margin 0.5s ease', 
};

const Padding01 = {
	padding: '0.1rem',
}
const SliderTitleDiv = {
	background: 'rgb(65, 91, 90)',
	color: 'white',
	fontSize: '0.7rem',
	borderRadius: '5px',
	marginBottom: '0.3rem',
    textAlign: 'center'
}
const rowHeight2 = {
    alignItems: 'center',
    justifyContent: 'flex-start',
    textOverflow: 'ellipsis',
    whiteSpace: 'nowrap',
    overflow: 'hidden'
}

const rowHeight15 = {
    height: '1.5rem',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'flex-start',
}

const rowHeight1 = {
    height: '1rem',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'flex-start',
    fontSize: '0.8rem',
    color: 'red'
}

const SliderBrickHeight2 = styled.div`
	height: 3rem;
    line-height: 3rem;
	background: white;
	margin-bottom: 0.3rem;
	border-radius: 5px;
    border: 1px solid transparent;

	p {
		margin-bottom: 0;
	}
	.SliderBrickTitle{
		font-size: 0.8rem;
		line-height: 2rem;
	}
	.SliderBrickOdd{
		font-size: 0.9rem;
		line-height: 1rem;
	}
`

const TeamName = {
    lineHeight: '2rem',
}


class ResultContentCard extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
            scoreData: [],
            v: this.props.data
		};
	}
    
    componentDidMount() {
        const data = this.props.data
        const sport = parseInt(window.sport)
        let gameTitle = []
        if( sport === 48242 ) {
            gameTitle = [[langText.ResultTitle.fullTimeScore,langText.ResultTitle.firstQuarterScore,langText.ResultTitle.secondQuarterScore], 
            [langText.ResultTitle.thirdQuarterScore,langText.ResultTitle.fourthQuarterScore,langText.ResultTitle.overtime]]
        }
        if( sport === 6046 ) {
            gameTitle = [[langText.ResultTitle.fullTimeScore,langText.ResultTitle.firstHalfScore,langText.ResultTitle.secondHalfScore,langText.ResultTitle.overtime]]
        }
        if( sport === 154914 ) {
            gameTitle = [[langText.ResultTitle.fullTimeScore,langText.ResultTitle.firstRound,langText.ResultTitle.gameTwo], 
            [langText.ResultTitle.gameThree,langText.ResultTitle.gameFour,langText.ResultTitle.gameFive],
            [langText.ResultTitle.gameSix,langText.ResultTitle.gameSeven,langText.ResultTitle.gameEight],
            [langText.ResultTitle.gameNine,langText.ResultTitle.overtime]]
        }
        if( sport === 35232 ) {
            gameTitle = [[langText.ResultTitle.fullTimeScore,langText.ResultTitle.firstRound,langText.ResultTitle.gameTwo], 
            [langText.ResultTitle.gameThree,langText.ResultTitle.overtime]]
        }

        let scores = data.scoreboard
        let scoreData = scores.reduce((acc, currentValue, currentIndex) => {
            if (currentIndex % 3 === 0) {
                acc.push([]);
            }
            acc[acc.length - 1].push(currentValue);
            return acc;
        }, []);

        this.setState({
            scoreData: scoreData,
            gameTitle: gameTitle
        })

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
        const { v, scoreData, gameTitle} = this.state
        if ( v !== undefined ){
            return (
                <div style={ ResultCard_item } cardid={v.fixture_id}>
                    <div>
                        <div className='row m-0' style={{ padding: '0.25rem 1rem'}}>
                            {/* left part */}
                            <div className='col-45' style={{ padding: '0 0.25rem 0 0'}}>
                                <div style={rowHeight15}>
                                    <p className='mb-0 mt-1'>{this.formatDateTime(v.start_time)}</p>
                                </div>
                                <div style={rowHeight1}>
                                    <p className='mb-0 mt-1'>
                                        {
                                            v.status > 3 && v.status < 9 &&
                                            v.status_name
                                        }
                                    </p>
                                </div>

                                <div style={rowHeight2}>
                                    { v.home_team_name }
                                </div>
                                <div style={{...rowHeight2, marginTop: '1.5rem'}}>
                                    { v.away_team_name }
                                </div>
                            </div>
                            {/* right part */}
                            <div className='col-55 text-center' style={{ padding: '0 0.25rem'}}>
                                {
                                    gameTitle &&
                                    <Swiper
                                        slidesPerView={1}
                                        pagination={true}
                                        modules={[Controller, Pagination]}
                                        onSwiper={Swiper => (this.matchCardSwiper = Swiper)}
                                        className='matchCardSwiper'
                                        style={{ position: 'relative', zIndex: 0}}
                                    >
                                        {
                                            gameTitle.map((v1, k1) => {
                                                return(
                                                    <SwiperSlide key={k1}>
                                                        <div className='row m-0'>
                                                            {
                                                                v1.map((v2, k2) => {
                                                                    return(
                                                                        <div className='col' style={Padding01} key={k2}>
                                                                            <div style={SliderTitleDiv}>{ v2 }</div>
                                                                            <SliderBrickHeight2>
                                                                                <div className="w-100 h-100">
                                                                                    <p>
                                                                                        {
                                                                                            scoreData[k1] && scoreData[k1][k2] ?
                                                                                            scoreData[k1][k2][0]
                                                                                            :
                                                                                            '-'
                                                                                        }
                                                                                    </p>
                                                                                </div>
                                                                            </SliderBrickHeight2>
                                                                            <SliderBrickHeight2>
                                                                                <div className="w-100 h-100">
                                                                                    <p>
                                                                                        {
                                                                                            scoreData[k1] && scoreData[k1][k2] ?
                                                                                            scoreData[k1][k2][1]
                                                                                            :
                                                                                            '-'
                                                                                        }
                                                                                    </p>
                                                                                </div>
                                                                            </SliderBrickHeight2>
                                                                        </div>
                                                                    )
                                                                })
                                                            }
                                                        </div>
                                                    </SwiperSlide>
                                                )
                                            })
                                        }
                                    </Swiper>

                                }
                               
                            </div>
                        </div>
                    </div>
                </div>	
            );
        }
	}
}


export default ResultContentCard;