import React from "react";
import { langText } from "../pages/LanguageContext";
import SlideToggle from "react-slide-toggle";
import { Link } from "react-router-dom";
import $ from 'jquery';
import Cookies from 'js-cookie';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Controller, Pagination } from 'swiper';
import { RiVideoLine } from 'react-icons/ri';
import { AiOutlineAreaChart, AiFillLock } from 'react-icons/ai';
import { IoIosStarOutline, IoIosStar, IoIosArrowForward, IoIosArrowBack, IoIosArrowDown } from 'react-icons/io';
import styled from '@emotion/styled';
import 'bootstrap/dist/css/bootstrap.css';
import  "../css/MatchContentCard.css";
import 'swiper/css';

const MatchCard = {
    position: 'relative',
    background: '#e2f0f0',
    borderRadius: '15px',
    marginBottom: '0.5rem',
    paddingTop: '0.5rem',
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
	marginBottom: '0.1rem',
    textAlign: 'center'
}
const rowHeight2 = {
	height: '2.5rem',
}
const SliderBrickHeight3 = styled.div`
	height: 2.5rem;
	background: white;
	margin-bottom: 0.2rem;
	border-radius: 5px;
	p {
		margin-bottom: 0;
	}
	.SliderBrickTitle{
		font-size: 0.7rem;
		line-height: 1.5rem;
	}
	.SliderBrickOdd{
		font-size: 0.9rem;
		line-height: 0.6rem;
	}
`

const SliderBrickHeight2 = styled.div`
	height: 3.85rem;
	background: white;
	margin-bottom: 0.2rem;
	border-radius: 5px;

	p {
		margin-bottom: 0;
	}
	.SliderBrickTitle{
		font-size: 0.8rem;
		line-height: 2rem;
	}
	.SliderBrickOdd{
		font-size: 0.9rem;
		line-height: 1.5rem;
	}
`

const SliderLeftArrow = {
	color: 'white',
	position: 'absolute',
	filter: 'drop-shadow(0px 2px 1px rgba(0,0,0,0.3))',
	top: '5rem',
	left: '40%',
	fontSize: '1.5rem'
}
const SliderRightArrow = {
	color: 'white',
	position: 'absolute',
	filter: 'drop-shadow(0px 2px 1px rgba(0,0,0,0.3))',
	top: '5rem',
	right: '-0.25rem',
	fontSize: '1.5rem'
}

const TeamName = {
    lineHeight: '2rem',
    paddingLeft: 0,
    overflow: 'hidden',
    textOverflow: 'ellipsis',
    whiteSpace: 'nowrap'
}

const CardShow = {
    opacity: 1,
    maxHeight: '500px', // 设置展开时的 max-height
    marginBottom: '0.5rem',
    paddingTop: '0.5rem',
};
  
const CardHide = {
    opacity: 0,
    maxHeight: '0', // 设置收缩时的 max-height
    overflow: 'hidden', // 隐藏溢出内容
    marginBottom: 0,
    paddingTop: 0
};

const selectedOn = {
    background: 'rgba(65, 91, 90, 0.8)',
    color: 'white',
}


class MatchContentCard extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
            isOtherBetOpen: false,
            swiperIndex: 0,
            isSetStar: Cookies.get(this.props.data.fixture_id, { path: '/' }) === 'true' || false,
            fixture_id: this.props.data.fixture_id
		};
        this.slideToggleRef = React.createRef();
	}

    // 加入星號 並設定cookie
	setStarState = (fixture_id) => {
        Cookies.set(fixture_id, !this.state.isSetStar, { path: '/' })
		this.setState({
			isSetStar: !this.state.isSetStar
		})
	}

    setGameMatchId = (matchId) => {
        Cookies.set('GameMatchId', matchId, { path: '/' })
        Cookies.set('sport', window.sport, { path: '/' })
    }


    
    // 右邊滑動
    swiperHandler = (swiperIndex) => {
        this.setState({
            swiperIndex: swiperIndex
        })
        this.props.swiperTabCallBack(swiperIndex)
    }



    getBetData = (sport, fixture_id, market_id, market_bet_id, price, market_name, home_team_name, away_team_name, bet_item_name, status) => {

        if( status !== 1 ) return;
        this.props.getBetDataCallBack(
            {
                sport_id: sport, 
                fixture_id: fixture_id, 
                market_id: market_id, 
                market_bet_id: market_bet_id, 
                bet_rate: price, 
                market_name: market_name, 
                series_name: this.props.series_name, 
                home_team_name: home_team_name, 
                away_team_name: away_team_name, 
                bet_item_name: bet_item_name, 
            }
        )
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
		const v = this.props.data
        const sport = parseInt(window.sport)
        const gameTitle = langText.MatchContentCard.gameTitle[window.sport]
        if ( v !== undefined && gameTitle ){
            let hcapTeam = null
            if( v?.list && Object.keys(v.list).length > 0 ) {
                let h = Object.values(v.list).find(e => e.priority === gameTitle[0][1])?.list
                let a = Object.values(v.list).find(e => e.priority === gameTitle[0][1])?.list
                if( h && a) {
                    h = h[0]?.line
                    a = a[1]?.line

                    if( h !== a ) {
                        hcapTeam = h < 0 ? 1 : 2
                    }
                }
            }

            return (
                <div style={{ ...MatchCard, ...(this.props.isOpen ? CardShow : CardHide) }} cardid={v.fixture_id}>
                    <SlideToggle duration={500} ref={this.slideToggleRef} collapsed={false} >
                        {({ toggle, setCollapsibleElement }) => (
                            <div>
                                <div className='row m-0' ref={setCollapsibleElement}>
                                    <div className='col-45' style={{ padding: '0 0.5rem 0 0'}}>
                                        <div className='row m-0' style={rowHeight2}>
                                            <div className='col-2 p-0'>
                                                {
                                                    this.state.isSetStar === true ?
                                                    <IoIosStar onClick={()=>this.setStarState(v.fixture_id)} style={{ fontSize: '1.1rem' }} />
                                                    :
                                                    <IoIosStarOutline onClick={()=>{this.setStarState(v.fixture_id)}} style={{ fontSize: '1.1rem' }} />
                                                }
                                            </div>
                                            <div className='col-10 p-0'>
                                            <p className='mb-0 mt-1'>
                                            {
                                                        v.status === 1 ?
                                                        this.formatDateTime(v.start_time)
                                                        :
                                                        v.status === 9 ? langText.MatchContentCard.readyToStart :
                                                        (
                                                            sport === 154914 ? 
                                                            langText.GameTopSlider.stageStr[sport][v.periods.period] + langText.GameTopSlider.baseballPeriod[v.periods.Turn]
                                                            : 
                                                            langText.GameTopSlider.stageStr[sport][v?.periods?.period] || this.formatDateTime(v.start_time)
                                                        )
                                                    }
                                                </p>
                                            </div>
                                        </div>
                                        <Link to="/mobile/game" style={{color: 'inherit'}} onClick={()=>this.setGameMatchId(v.fixture_id)} >
                                            <div className='row m-0' style={rowHeight2}>
                                                <div className='col-10 p-0' style={{...TeamName, color:hcapTeam === 1 ? 'darkred' : null}}>
                                                    {v.home_team_name}
                                                </div>
                                                <div className='col-2 p-0 text-center' style={{ lineHeight: '2rem', color: '#ff7000'}}>
                                                    { v?.scoreboard && v.scoreboard[1][0] }
                                                </div>
                                            </div>
                                            <div className='row m-0' style={rowHeight2}>
                                                <div className='col-10 p-0' style={{...TeamName, color:hcapTeam === 2 ? 'darkred' : null}}>
                                                    { v.away_team_name }
                                                </div>
                                                <div className='col-2 p-0 text-center' style={{ lineHeight: '2rem', color: '#ff7000'}}>
                                                    { v?.scoreboard && v.scoreboard[2][0] }
                                                </div>
                                            </div>
                                            <div className="col p-0">+{v.market_bet_count}</div>
                                        </Link>
                                        <div className="row m-0" style={rowHeight2}>
                                        </div>
                                    </div>
                                    <div className='col-55 text-center' style={{ padding: 0 }}>
                                        {
                                            this.state.swiperIndex === 0 ?
                                            <IoIosArrowForward onClick={()=>{this.matchCardSwiper.slideNext()}} style={SliderRightArrow}/>
                                            :
                                            <IoIosArrowBack onClick={()=>{this.matchCardSwiper.slidePrev()}} style={SliderLeftArrow}/>
                                        }
                                        <Swiper
                                            slidesPerView={1}
                                            pagination={true}
                                            modules={[Controller, Pagination]}
                                            onSwiper={Swiper => (this.matchCardSwiper = Swiper)}
                                            className='matchCardSwiper'
                                            onSlideChange={(Swiper) => {this.swiperHandler(Swiper.activeIndex)}}
                                             style={{ position: 'relative', zIndex: 0}}
                                        >
                                            {
                                                gameTitle.map((m, n) => {
                                                    return(
                                                        <SwiperSlide key={n}>
                                                            <div className='row m-0'>
                                                                {
                                                                    gameTitle[n].map(k => {
                                                                        let tt = null
                                                                        if( v.list ) tt = Object.values(v.list).find(m => m.priority === k)
                                                                        return(
                                                                            <div className='col-4' style={Padding01} key={k}>
                                                                                <div style={SliderTitleDiv}>{ langText.MatchContent.game_priority[window.sport][k] }</div>
                                                                                {
                                                                                    tt && tt.list && Object.keys(tt.list).length > 0 ? 
                                                                                    Object.entries(tt.list).map(([r,s]) => {
                                                                                        // s.line = '-0.25 (0-0)'
                                                                                        return(
                                                                                            k === 201 || k === 202 ?
                                                                                            <SliderBrickHeight3 key={r} onClick={()=>this.getBetData(
                                                                                                sport, 
                                                                                                v.fixture_id,
                                                                                                tt.market_id,
                                                                                                s.market_bet_id,
                                                                                                s.price,
                                                                                                tt.market_name,
                                                                                                v.home_team_name,
                                                                                                v.away_team_name,
                                                                                                langText.MatchContent.allWinPriority.indexOf(k) !== -1 ?
                                                                                                s.market_bet_name_en === "1" ? v.home_team_name : (
                                                                                                    s.market_bet_name_en === "2" ?
                                                                                                    v.away_team_name
                                                                                                    :
                                                                                                    langText.MatchContentCard.tie
                                                                                                )
                                                                                                :
                                                                                                s.market_bet_name + ' ' + s.line
                                                                                                ,
                                                                                                s.status
                                                                                            )}>
                                                                                                <div className="w-100 h-100" market_bet_id={s.market_bet_id} 
                                                                                                style={ this.props?.selectedM?.market_bet_id === s.market_bet_id ? selectedOn : null}
                                                                                                >
                                                                                                <p className='SliderBrickTitle'>
                                                                                                    {s.market_bet_name}
                                                                                                </p>
                                                                                                    {
                                                                                                        s.status === 1 ?
                                                                                                        <p className='SliderBrickOdd odd'>
                                                                                                            { s.price }
                                                                                                        </p>
                                                                                                        :
                                                                                                        <p className='SliderBrickOdd'>
                                                                                                            <AiFillLock />
                                                                                                        </p>
                                                                                                    }
                                                                                                </div>
                                                                                            </SliderBrickHeight3>
                                                                                            :
                                                                                            <SliderBrickHeight2 key={r} onClick={()=>this.getBetData(
                                                                                                sport, 
                                                                                                v.fixture_id,
                                                                                                tt.market_id,
                                                                                                s.market_bet_id,
                                                                                                s.price,
                                                                                                tt.market_name,
                                                                                                v.home_team_name,
                                                                                                v.away_team_name,
                                                                                                langText.MatchContent.allWinPriority.indexOf(k) !== -1 || langText.MatchContent.hcapPriority.indexOf(k) !== -1  ?
                                                                                                (s.market_bet_name_en === "1" ? v.home_team_name : v.away_team_name) + ' ' + s.line
                                                                                                :
                                                                                                (s.market_bet_name + ' ' + s.line)
                                                                                                ,
                                                                                                s.status
                                                                                            )}>
                                                                                                <div className="w-100 h-100" market_bet_id={s.market_bet_id} 
                                                                                                style={ this.props?.selectedM?.market_bet_id === s.market_bet_id ? selectedOn : null}
                                                                                                >
                                                                                                <p className='SliderBrickTitle' style={{lineHeight: (s.line).indexOf('(') !== -1 ? '1rem' : null}}>
                                                                                                {langText.MatchContent.sizePriority.indexOf(k) !== -1 &&
                                                                                                        s.market_bet_name
                                                                                                    }
                                                                                                    {
                                                                                                        (s.line).indexOf('(') !== -1 ?
                                                                                                        <span style={{color: '#ff7000'}}>
                                                                                                            {(s.line).split(' ')[0]}
                                                                                                            <br/>
                                                                                                            {(s.line).split(' ')[1]}
                                                                                                        </span>
                                                                                                        :
                                                                                                        <span style={{color: '#ff7000'}}>
                                                                                                            {s.line}
                                                                                                        </span>
                                                                                                    }
                                                                                                </p>
                                                                                                    {
                                                                                                        s.status === 1 ?
                                                                                                        <p className='SliderBrickOdd odd' style={window.sport !== 6046 && langText.MatchContent.allWinPriority.indexOf(k) !== -1 ? {lineHeight: '3.5rem'} : null}>
                                                                                                            { s.price }
                                                                                                        </p>
                                                                                                        :
                                                                                                        <p className='SliderBrickOdd' style={window.sport !== 6046 && langText.MatchContent.allWinPriority.indexOf(k) !== -1 ? {lineHeight: '3.5rem'} : null}>
                                                                                                            <AiFillLock />
                                                                                                        </p>
                                                                                                    }
                                                                                                </div>
                                                                                            </SliderBrickHeight2>
                                                                                        )
                                                                                    })
                                                                                    :
                                                                                    k === 201 || k === 202 ?
                                                                                    <div key={k}>
                                                                                        <SliderBrickHeight3>
                                                                                            <AiFillLock style={{ marginTop:'1rem'}} />
                                                                                        </SliderBrickHeight3>
                                                                                        <SliderBrickHeight3>
                                                                                            <AiFillLock style={{ marginTop:'1rem'}} />
                                                                                        </SliderBrickHeight3>
                                                                                        <SliderBrickHeight3>
                                                                                            <AiFillLock style={{ marginTop:'1rem'}} />
                                                                                        </SliderBrickHeight3>
                                                                                    </div>
                                                                                    :
                                                                                    <div key={k}>
                                                                                        <SliderBrickHeight2>
                                                                                            <AiFillLock style={{ marginTop:'1rem'}} />
                                                                                        </SliderBrickHeight2>
                                                                                        <SliderBrickHeight2>
                                                                                            <AiFillLock style={{ marginTop:'1rem'}} />
                                                                                        </SliderBrickHeight2>
                                                                                    </div>
                                                                                }
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
                                    </div>
                                </div>
                            </div>
                        )}
                    </SlideToggle>
                </div>	
            );
        }
	}
}


export default MatchContentCard;