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
	height: '2.5rem',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'flex-start',
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
    
    // 文字太長變成跑馬燈
    textOverFlow = (id) => {
        $('div[cardid="' + id + '"] .teamSpan').each(function(){
            $(this).find('.teamSpanMarquee').hide()
            $(this).find('.teamSpanSpan').show()
            // 太長有換行
            if(this.clientHeight > 40) {
                $(this).find('.teamSpanMarquee').show()
                $(this).find('.teamSpanSpan').hide()
            }
        })
    }

    componentDidMount() {
        this.textOverFlow(this.props.data.fixture_id)
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
                        <div className='row m-0 p-1'>
                            {/* left part */}
                            <div className='col-45' style={{ padding: '0 0rem 0 0.5rem'}}>
                                <div style={rowHeight2}>
                                    <p className='mb-0 mt-1'>{this.formatDateTime(v.start_time)}</p>
                                </div>
                                <div style={rowHeight2}>
                                    <div className='p-0 teamSpan' style={TeamName}>
                                        <div className="teamSpanMarquee">
                                            <Marquee className='matchCardMarquee mt-1' speed={20} gradient={false}>
                                                { v.home_team_name }&emsp;&emsp;&emsp;
                                            </Marquee>
                                        </div>
                                        <span className="teamSpanSpan">
                                            { v.home_team_name }
                                        </span>
                                    </div>
                                </div>
                                <div style={rowHeight2}>
                                    <div className='p-0 teamSpan' style={TeamName}>
                                        <div className="teamSpanMarquee">
                                            <Marquee className='matchCardMarquee mt-1' speed={20} gradient={false}>
                                                { v.away_team_name }&emsp;&emsp;&emsp;
                                            </Marquee>
                                        </div>
                                        <span className="teamSpanSpan">
                                            { v.away_team_name }
                                        </span>
                                    </div>
                                </div>
                            </div>
                            {/* right part */}
                            <div className='col-55 text-center' style={{ paddingLeft: 0}}>
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