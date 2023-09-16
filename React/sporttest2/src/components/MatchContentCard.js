import React from "react";
import Marquee from "react-fast-marquee";
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
const MatchTeamIcon = {
	width: '1.3rem',
	height: '1.3rem',
}
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
    border: 1px solid transparent;
	p {
		margin-bottom: 0;
	}
	.SliderBrickTitle{
		font-size: 0.8rem;
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

const SliderLeftArrow = {
	color: 'white',
	position: 'absolute',
	filter: 'drop-shadow(0px 2px 1px rgba(0,0,0,0.3))',
	top: '7rem',
	left: '40%',
	fontSize: '1.5rem'
}
const SliderRightArrow = {
	color: 'white',
	position: 'absolute',
	filter: 'drop-shadow(0px 2px 1px rgba(0,0,0,0.3))',
	top: '7rem',
	right: '-2%',
	fontSize: '1.5rem'
}

const OtherBetDiv = {
    width: '100%',
    marginBottom: '0.5rem',
    display: 'flex'
}

const OtherBetConainer ={
    borderRadius: '15px',
    background: 'white',
    width: '100%',
    margin: '0.5rem 0',
    display: 'none'
}

const OtherBetTitle = {
    fontSize: '0.9rem',
    textAlign: 'center',
    padding: '0.5rem'
}
const OtherBetBrick = {
    textAlign: 'center',
    fontSize: '0.9rem',
    padding: '0.5rem'
}
const OtherBetBrickName = {
    fontSize: '0.5rem'
}

const OtherBetBtn = {
    background: 'white',
    padding: '0.2rem',
    fontSize: '0.9rem',
    borderRadius: '5px',
    marginRight: '0.5rem',
    minWidth: '6rem',
    textAlign: 'center'
}
const TeamName = {
    lineHeight: '2rem',
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

const drfaultImg = 'https://sporta.asgame.net/uploads/default.png'

class MatchContentCard extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
            isOtherBetOpen: false,
            swiperIndex: 0,
            isSetStar: Cookies.get(this.props.data.match_id, { path: '/' }) === 'true' || false,
            match_id: this.props.data.match_id
		};
        this.slideToggleRef = React.createRef();
	}

    // 加入星號 並設定cookie
	setStarState = (matchId) => {
        Cookies.set(matchId, !this.state.isSetStar, { path: '/' })
		this.setState({
			isSetStar: !this.state.isSetStar
		})
	}

    setGameMatchId = (matchId) => {
        Cookies.set('GameMatchId', matchId, { path: '/' })
    }


    
    // 右邊滑動
    swiperHandler = (swiperIndex) => {
        this.setState({
            swiperIndex: swiperIndex
        })
        this.props.swiperTabCallBack(swiperIndex)
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
        this.textOverFlow(this.props.data.match_id)
    } 


    getBetData = (match_id, bet_type, bet_type_item, bet_rate, type_name, type_item_name, series_name, home_name, away_name, home_logo, away_logo, start_time) => {
        this.props.getBetDataCallBack(
            {
                bet_match: match_id, 
                bet_type: bet_type, 
                bet_type_item: bet_type_item, 
                bet_rate: bet_rate, 
                type_name: type_name, 
                type_item_name: type_item_name, 
                series_name: series_name, 
                home_name: home_name, 
                away_name: away_name,
                home_logo: home_logo,
                away_logo: away_logo,
                start_time: start_time
            }
        )
    }

    // 圖片毀損
    handleError(event) {
        event.target.src = drfaultImg;
    }

    // 其他
    openOtherBet = (id) => {
        $('.otherBetArea').hide(300)
        $('span[dir="dirIcon"]').html('▸')
        if(!$('#' + id).is(':visible')) {
            $('#' + id).show(300)
            $('#' + id + '_icon').html('▾')
        }
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
        const AllWinObj = sport === 1 ? [
            {
                name: langText.MatchContentCard.homewin,
                rateKey: 0
            },
            {
                name: langText.MatchContentCard.awaywin,
                rateKey: 2
            },
            {
                name: langText.MatchContentCard.tie,
                rateKey: 1
            }
        ] : [
            {
                name: langText.MatchContentCard.homewin,
                rateKey: 0
            },
            {
                name: langText.MatchContentCard.awaywin,
                rateKey: 1
            }
        ]


        // 棒球  前五局獨贏  有平局 處理
        var HalfWinObj = null
        switch (sport) {
            case 1:
                HalfWinObj = [
                    {
                        name: langText.MatchContentCard.homewin,
                        rateKey: 0
                    },
                    {
                        name: langText.MatchContentCard.awaywin,
                        rateKey: 2
                    },
                    {
                        name: langText.MatchContentCard.tie,
                        rateKey: 1
                    }
                ]
                break;
            case 2:
                HalfWinObj = [
                    {
                        name: langText.MatchContentCard.homewin,
                        rateKey: 0
                    },
                    {
                        name: langText.MatchContentCard.awaywin,
                        rateKey: 1
                    }
                ]
                break;
            case 3:
                HalfWinObj = [
                    {
                        name: langText.MatchContentCard.homewin,
                        rateKey: 0
                    },
                    {
                        name: langText.MatchContentCard.awaywin,
                        rateKey: 2
                    }
                ]
                break;
            default:
                break;
        }

        var fixedPriorityArr = []
        var gameTitle = []
        if(sport === 1) {
            fixedPriorityArr = [5, 2, 1, 6, 4, 3]
            gameTitle = [langText.MatchContentCard.allwin,langText.MatchContentCard.allhcap,langText.MatchContentCard.allsize,langText.MatchContentCard.halfwin,langText.MatchContentCard.halfhcap,langText.MatchContentCard.halfsize, langText.MatchContentCard.all, langText.MatchContentCard.win, langText.MatchContentCard.hcap, langText.MatchContentCard.size]
        }
        if(sport === 2) {
            fixedPriorityArr = [5, 9, 1, 6, 10, 3]
            gameTitle = [langText.MatchContentCard.allwin,langText.MatchContentCard.allhcapS,langText.MatchContentCard.allsize,langText.MatchContentCard.firsthalfwin,langText.MatchContentCard.firsthalfhacpS,langText.MatchContentCard.firsthalfsize, langText.MatchContentCard.all, langText.MatchContentCard.win, langText.MatchContentCard.hcapS, langText.MatchContentCard.size]
        }
        if(sport === 3) {
            fixedPriorityArr = [5, 2, 1, 28, 29, 27]
            gameTitle = [langText.MatchContentCard.allwin,langText.MatchContentCard.allhcap,langText.MatchContentCard.allsize,langText.MatchContentCard.firstfivewin,langText.MatchContentCard.firstfivehcap,langText.MatchContentCard.firstfivesize, langText.MatchContentCard.all, langText.MatchContentCard.win, langText.MatchContentCard.hcap, langText.MatchContentCard.size]
        }

        if ( v !== undefined ){
            const homeData = v.teams.find(item => item.index === 1)
            const awayData = v.teams.find(item => item.index === 2)


            var result = null
            var betData = null

            var resultObj = {}
            // 排序
            Object.entries(v.rate).map(([k1, v1]) => {
                
                // 尋找有效盤口
                result = v1.filter( item => item.status === 1 )
               
                
                if( result.length > 0 ) {
                    result.forEach(item => {
                        const rateStatus1Count = Object.values(item.rate).filter(rateItem => rateItem.status === 1).length;
                        item.rateStatus1Count = rateStatus1Count;
                    });
                    result = result.filter(item => item.rateStatus1Count > 0) // rate_item_status 至少有一個為1

                    if( result.length > 0 ) {
                        // 按照时间戳从大到小排序
                        result = result.sort((a, b) => b.updated_at - a.updated_at);
                        // 按照rate_id排序
                        result = result.sort((a, b) => {
                            if (b.updated_at === a.updated_at) {
                                return b.rate_id - a.rate_id;
                            }
                            return b.updated_at - a.updated_at;
                        });
                        // 移除添加的临时字段
                        result.forEach(item => {
                            delete item.rateStatus1Count;
                        });

                        resultObj[v1[0].game_priority] = result[0]
                    }
                }
            })

            return (
                <div style={{ ...MatchCard, ...(this.props.isOpen ? CardShow : CardHide) }} cardid={v.match_id}>
                    <SlideToggle duration={500} ref={this.slideToggleRef} collapsed={false} >
                        {({ toggle, setCollapsibleElement }) => (
                            <div>
                                <div className='row m-0' ref={setCollapsibleElement}>
                                    <div className='col-45' style={{ padding: '0 0.5rem'}}>
                                        <div className='row m-0' style={rowHeight2}>
                                            <div className='col-2 p-0'>
                                                {
                                                    this.state.isSetStar === true ?
                                                    <IoIosStar onClick={()=>this.setStarState(v.match_id)} style={{ fontSize: '1.1rem' }} />
                                                    :
                                                    <IoIosStarOutline onClick={()=>{this.setStarState(v.match_id)}} style={{ fontSize: '1.1rem' }} />
                                                }
                                            </div>
                                            <div className='col-10 p-0'>
                                                {
                                                    langText.MatchContentCard.stage[sport][homeData.scores.length - 1] ?
                                                        <p className='mb-0 mt-1'>{ langText.MatchContentCard.stage[sport][homeData.scores.length - 1] }</p>
                                                    :
                                                    <p className='mb-0 mt-1'>{this.formatDateTime(v.start_time)}</p>
                                                }
                                            </div>
                                        </div>
                                        <Link to="/mobile/game" style={{color: 'inherit'}} onClick={()=>this.setGameMatchId(v.match_id)} >
                                            <div className='row m-0' style={rowHeight2}>
                                                <div className='col-2 p-0'>
                                                    {
                                                        homeData?.team?.logo && <img style={MatchTeamIcon} alt='home' src={homeData.team.logo}  onError={this.handleError}/>
                                                    }
                                                </div>
                                                <div className='col-8 p-0 teamSpan' style={TeamName}>
                                                    <div className="teamSpanMarquee">
                                                        <Marquee className='matchCardMarquee mt-1' speed={20} gradient={false}>
                                                            { homeData?.team?.name !== undefined && homeData.team.name }[{ langText.MatchContentCard.hometag }]&emsp;&emsp;&emsp;
                                                        </Marquee>
                                                    </div>
                                                    <span className="teamSpanSpan">
                                                    {
                                                        homeData?.team?.name !== undefined && homeData.team.name 
                                                    }
                                                    [{ langText.MatchContentCard.hometag }]
                                                    </span>
                                                </div>
                                                <div className='col-2 p-0 text-center teamScore' index={1} style={{ lineHeight: '2rem'}}>
                                                    {
                                                        homeData?.total_score !== undefined && homeData.total_score
                                                    }
                                                </div>
                                            </div>
                                            <div className='row m-0' style={rowHeight2}>
                                                <div className='col-2 p-0'>
                                                    {
                                                        awayData?.team?.logo !== undefined && <img style={MatchTeamIcon} alt='home' src={awayData.team.logo}  onError={this.handleError}/>
                                                    }
                                                </div>
                                                <div className='col-8 p-0 teamSpan' style={TeamName}>
                                                    <div className="teamSpanMarquee">
                                                        <Marquee className='matchCardMarquee mt-1' speed={20} gradient={false}>
                                                            { awayData?.team?.name !== undefined && awayData.team.name }&emsp;&emsp;&emsp;
                                                        </Marquee>
                                                    </div>
                                                    <span className="teamSpanSpan">
                                                    {
                                                        awayData?.team?.name !== undefined && awayData.team.name
                                                    }
                                                    </span>
                                                </div>
                                                <div className='col-2 p-0 text-center teamScore' index={2} style={{ lineHeight: '2rem'}}>
                                                    {
                                                        awayData?.total_score !== undefined && awayData.total_score
                                                    }
                                                </div>
                                            </div>
                                        </Link>
                                        
                                        <div style={rowHeight2}>
                                            <RiVideoLine style={{ fontSize: '1.5rem'}} />
                                            <AiOutlineAreaChart style={{ fontSize: '1.5rem', marginLeft: '0.5rem'}} />
                                        </div>
                                    </div>
                                    <div className='col-55 text-center' style={{ paddingLeft: 0}}>
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
                                            <SwiperSlide>
                                                <div className='row m-0'>
                                                    <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                        <div style={SliderTitleDiv}>{ gameTitle[0] }</div>
                                                    </div>
                                                    <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                        <div style={SliderTitleDiv}>{ gameTitle[1] }</div>
                                                    </div>
                                                    <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                        <div style={SliderTitleDiv}>{ gameTitle[2] }</div>
                                                    </div>
                                                </div>
                                                <div className='row m-0'>
                                                    <div className='col-4' style={Padding01}>
                                                        {
                                                            AllWinObj.map((item, index) => (
                                                                (() => {
                                                                    const filteredData = resultObj[fixedPriorityArr[0]]
                                                                    if( filteredData!== undefined ) {
                                                                        const rateKeys = Object.keys(filteredData.rate);
                                                                        const targetRate = filteredData.rate[rateKeys[item.rateKey]];
                                                                       
                                                                        if(targetRate !== undefined && targetRate.id) {
                                                                            if( sport === 1 ) {
                                                                                return(
                                                                                    <SliderBrickHeight3 key={index} onClick={()=>this.getBetData(
                                                                                        v.match_id, 
                                                                                        filteredData.rate_id, 
                                                                                        targetRate.id, 
                                                                                        targetRate.rate, 
                                                                                        filteredData.name, 
                                                                                        targetRate.name, 
                                                                                        v.series.name, 
                                                                                        homeData?.team?.name !== undefined ? homeData.team.name : langText.MatchContentCard.home, 
                                                                                        awayData?.team?.name !== undefined ? awayData.team.name : langText.MatchContentCard.away, 
                                                                                        homeData?.team?.logo !== undefined ? homeData?.team?.logo : drfaultImg, 
                                                                                        awayData?.team?.logo !== undefined ? awayData?.team?.logo : drfaultImg,
                                                                                        v.start_time
                                                                                    )}>
                                                                                        <div className="w-100 h-100" bet_match={v.match_id} bet_type={filteredData.rate_id} bet_type_item={targetRate.id}>
                                                                                            <p className='SliderBrickTitle'>{item.name}</p>
                                                                                            {
                                                                                                
                                                                                                filteredData.status === 1 && targetRate.status === 1 && targetRate.risk === 0 && targetRate.rate !== undefined ?
                                                                                                <p className='SliderBrickOdd odd'>
                                                                                                    { targetRate.rate }
                                                                                                </p>
                                                                                                :
                                                                                                <p className='SliderBrickOdd'>
                                                                                                    <AiFillLock />
                                                                                                </p>
                                                                                            }
                                                                                        </div>
                                                                                    </SliderBrickHeight3>
                                                                                )
                                                                            } else {
                                                                                return(
                                                                                    <SliderBrickHeight2 key={index} onClick={()=>this.getBetData(
                                                                                        v.match_id, 
                                                                                        filteredData.rate_id, 
                                                                                        targetRate.id, 
                                                                                        targetRate.rate, 
                                                                                        filteredData.name, 
                                                                                        targetRate.name, 
                                                                                        v.series.name, 
                                                                                        homeData?.team?.name !== undefined ? homeData.team.name : langText.MatchContentCard.home, 
                                                                                        awayData?.team?.name !== undefined ? awayData.team.name : langText.MatchContentCard.away, 
                                                                                        homeData?.team?.logo !== undefined ? homeData?.team?.logo : drfaultImg, 
                                                                                        awayData?.team?.logo !== undefined ? awayData?.team?.logo : drfaultImg,
                                                                                        v.start_time
                                                                                    )}>
                                                                                        <div className="w-100 h-100" bet_match={v.match_id} bet_type={filteredData.rate_id} bet_type_item={targetRate.id}>
                                                                                            <p className='SliderBrickTitle'>{item.name}</p>
                                                                                            {
                                                                                                
                                                                                                filteredData.status === 1 && targetRate.status === 1 && targetRate.risk === 0 && targetRate.rate !== undefined ?
                                                                                                <p className='SliderBrickOdd odd'>
                                                                                                    { targetRate.rate }
                                                                                                </p>
                                                                                                :
                                                                                                <p className='SliderBrickOdd'>
                                                                                                    <AiFillLock />
                                                                                                </p>
                                                                                            }
                                                                                        </div>
                                                                                    </SliderBrickHeight2>
                                                                                )
                                                                            }
                                                                        }
                                                                        
                                                                    } else {
                                                                        if( sport === 1 ) {
                                                                            return(
                                                                                <SliderBrickHeight3 key={index}>
                                                                                    <p className='SliderBrickTitle'>{item.name}</p>
                                                                                    <p className='SliderBrickOdd'>
                                                                                        <AiFillLock />
                                                                                    </p>
                                                                                </SliderBrickHeight3>
                                                                            )
                                                                        } else {
                                                                            return(
                                                                                <SliderBrickHeight2 key={index}>
                                                                                    <p className='SliderBrickTitle'>{item.name}</p>
                                                                                    <p className='SliderBrickOdd'>
                                                                                        <AiFillLock />
                                                                                    </p>
                                                                                </SliderBrickHeight2>
                                                                            )
                                                                        }
                                                                    }
                                                                })()
                                                            ))
                                                        }
                                                    </div>
                                                    {/* 讓分 */}
                                                    <div className='col-4' style={Padding01}>
                                                        {
                                                            [0, 1].map(e => (
                                                            <React.Fragment key={e}>
                                                                {
                                                                    (() => {
                                                                        const filteredData = resultObj[fixedPriorityArr[1]]
                                                                        if (filteredData !== undefined ) {
                                                                            const rateKeys = Object.keys(filteredData.rate);
                                                                            const targetRate = filteredData.rate[rateKeys[e]];
                                                                            if(targetRate !== undefined && targetRate.id) {
                                                                                return (
                                                                                    <SliderBrickHeight2 key={e} onClick={()=>this.getBetData(
                                                                                        v.match_id, 
                                                                                        filteredData.rate_id, 
                                                                                        targetRate.id, 
                                                                                        targetRate.rate, 
                                                                                        filteredData.name, 
                                                                                        targetRate.name, 
                                                                                        v.series.name, 
                                                                                        homeData?.team?.name !== undefined ? homeData.team.name : langText.MatchContentCard.home, 
                                                                                        awayData?.team?.name !== undefined ? awayData.team.name : langText.MatchContentCard.away, 
                                                                                        homeData?.team?.logo !== undefined ? homeData?.team?.logo : drfaultImg, 
                                                                                        awayData?.team?.logo !== undefined ? awayData?.team?.logo : drfaultImg,
                                                                                        v.start_time
                                                                                    )}>
                                                                                        <div className="w-100 h-100" bet_match={v.match_id} bet_type={filteredData.rate_id} bet_type_item={targetRate.id}>
                                                                                            {
                                                                                                
                                                                                                filteredData.status === 1 && targetRate.status === 1 && targetRate.risk === 0 && targetRate.rate !== undefined ?
                                                                                                <>
                                                                                                    <p className='SliderBrickTitle'>{targetRate.value}</p>
                                                                                                    <p className='SliderBrickOdd odd'>{targetRate.rate}</p>
                                                                                                </>
                                                                                                :
                                                                                                <AiFillLock style={{ marginTop: '1.2rem'}} />
                                                                                            }
                                                                                        </div>
                                                                                    </SliderBrickHeight2>
                                                                                );
                                                                            }
                                                                        } else {
                                                                            return (
                                                                                <SliderBrickHeight2 key={e}>
                                                                                    <AiFillLock style={{ marginTop: '1.2rem'}} />
                                                                                </SliderBrickHeight2>
                                                                            );
                                                                        }
                                                                    })()
                                                                }
                                                            </React.Fragment>
                                                            ))
                                                        }
                                                    </div>
                                                    {/* 大小 */}
                                                    <div className='col-4' style={Padding01}>
                                                        {
                                                            [langText.MatchContentCard.big, langText.MatchContentCard.small].map((e, i) => {
                                                            const filteredData = resultObj[fixedPriorityArr[2]]
                                                            if (filteredData !== undefined ) {
                                                                const rateKeys = Object.keys(filteredData.rate);
                                                                const targetRate = filteredData.rate[rateKeys[i]];
                                                                if(targetRate !== undefined && targetRate.id) {
                                                                    return (
                                                                        <SliderBrickHeight2 onClick={()=>this.getBetData(
                                                                            v.match_id, 
                                                                            filteredData.rate_id, 
                                                                            targetRate.id, 
                                                                            targetRate.rate, 
                                                                            filteredData.name, 
                                                                            targetRate.name, 
                                                                            v.series.name, 
                                                                            homeData?.team?.name !== undefined ? homeData.team.name : langText.MatchContentCard.home, 
                                                                            awayData?.team?.name !== undefined ? awayData.team.name : langText.MatchContentCard.away, 
                                                                            homeData?.team?.logo !== undefined ? homeData?.team?.logo : drfaultImg, 
                                                                            awayData?.team?.logo !== undefined ? awayData?.team?.logo : drfaultImg,
                                                                            v.start_time
                                                                        )}  key={i}>
                                                                            <div className="w-100 h-100" bet_match={v.match_id} bet_type={filteredData.rate_id} bet_type_item={targetRate.id}>
                                                                                <p className='SliderBrickTitle'>{targetRate.name}</p>
                                                                                {
                                                                                    
                                                                                    filteredData.status === 1 && targetRate.status === 1 && targetRate.risk === 0 && targetRate.rate !== undefined ?
                                                                                    <p className='SliderBrickOdd odd'>{targetRate.rate}</p>
                                                                                    :
                                                                                    <AiFillLock style={{ marginTop: '-1.2rem' }} />
                                                                                }
                                                                            </div>
                                                                    </SliderBrickHeight2>
                                                                    );
                                                                }
                                                               
                                                            } else {
                                                                return (
                                                                    <SliderBrickHeight2 key={i}>
                                                                        <p className='SliderBrickTitle'>{e}</p>
                                                                        <AiFillLock style={{ marginTop: '-1.2rem' }} />
                                                                    </SliderBrickHeight2>
                                                                );
                                                            }
                                                            })
                                                        }
                                                    </div> 
                                                </div>

                                            </SwiperSlide>
                                            <SwiperSlide>
                                                <div className='row m-0'>
                                                    <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                        <div style={SliderTitleDiv}>{ gameTitle[3] }</div>
                                                    </div>
                                                    <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                        <div style={SliderTitleDiv}>{ gameTitle[4] }</div>
                                                    </div>
                                                    <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                        <div style={SliderTitleDiv}>{ gameTitle[5] } </div>
                                                    </div>
                                                </div>
                                                <div className='row m-0'>
                                                    <div className='col-4' style={Padding01}>
                                                        {
                                                            HalfWinObj.map((item, index) => (
                                                                (() => {
                                                                    const filteredData = resultObj[fixedPriorityArr[3]]
                                                                    if( filteredData!== undefined) {
                                                                        const rateKeys = Object.keys(filteredData.rate);
                                                                        const targetRate = filteredData.rate[rateKeys[item.rateKey]];
                                                                        if(targetRate !== undefined && targetRate.id) {
                                                                            if( sport === 1 ) {
                                                                                return(
                                                                                    <SliderBrickHeight3 key={index} onClick={()=>this.getBetData(
                                                                                        v.match_id, 
                                                                                        filteredData.rate_id, 
                                                                                        targetRate.id, 
                                                                                        targetRate.rate, 
                                                                                        filteredData.name, 
                                                                                        targetRate.name, 
                                                                                        v.series.name, 
                                                                                        homeData?.team?.name !== undefined ? homeData.team.name : langText.MatchContentCard.home, 
                                                                                        awayData?.team?.name !== undefined ? awayData.team.name : langText.MatchContentCard.away, 
                                                                                        homeData?.team?.logo !== undefined ? homeData?.team?.logo : drfaultImg, 
                                                                                        awayData?.team?.logo !== undefined ? awayData?.team?.logo : drfaultImg,
                                                                                        v.start_time
                                                                                    )} >
                                                                                        <div className="w-100 h-100" bet_match={v.match_id} bet_type={filteredData.rate_id} bet_type_item={targetRate.id}>
                                                                                            <p className='SliderBrickTitle'>{item.name}</p>
                                                                                            {
                                                                                                
                                                                                                filteredData.status === 1 && targetRate.status === 1 && targetRate.risk === 0 && targetRate.rate !== undefined ?
                                                                                                <p className='SliderBrickOdd odd'>
                                                                                                    { targetRate.rate }
                                                                                                </p>
                                                                                                :
                                                                                                <p className='SliderBrickOdd'>
                                                                                                    <AiFillLock />
                                                                                                </p>
                                                                                            }
                                                                                        </div>
                                                                                    </SliderBrickHeight3>
                                                                                )
                                                                            } else {
                                                                                return(
                                                                                    <SliderBrickHeight2 key={index} onClick={()=>this.getBetData(
                                                                                        v.match_id, 
                                                                                        filteredData.rate_id, 
                                                                                        targetRate.id, 
                                                                                        targetRate.rate, 
                                                                                        filteredData.name, 
                                                                                        targetRate.name, 
                                                                                        v.series.name, 
                                                                                        homeData?.team?.name !== undefined ? homeData.team.name : langText.MatchContentCard.home, 
                                                                                        awayData?.team?.name !== undefined ? awayData.team.name : langText.MatchContentCard.away, 
                                                                                        homeData?.team?.logo !== undefined ? homeData?.team?.logo : drfaultImg, 
                                                                                        awayData?.team?.logo !== undefined ? awayData?.team?.logo : drfaultImg
                                                                                    )} >
                                                                                        <div className="w-100 h-100" bet_match={v.match_id} bet_type={filteredData.rate_id} bet_type_item={targetRate.id}>
                                                                                            <p className='SliderBrickTitle'>{item.name}</p>
                                                                                            {
                                                                                                
                                                                                                filteredData.status === 1 && targetRate.status === 1 && targetRate.rate !== undefined ?
                                                                                                <p className='SliderBrickOdd odd'>
                                                                                                    { targetRate.rate }
                                                                                                </p>
                                                                                                :
                                                                                                <p className='SliderBrickOdd'>
                                                                                                    <AiFillLock />
                                                                                                </p>
                                                                                            }
                                                                                        </div>
                                                                                    </SliderBrickHeight2>
                                                                                )
                                                                            }
                                                                        }
                                                                       
                                                                    } else {
                                                                        if( sport === 1 ) {
                                                                            return(
                                                                                <SliderBrickHeight3 key={index}>
                                                                                    <p className='SliderBrickTitle'>{item.name}</p>
                                                                                    <p className='SliderBrickOdd'>
                                                                                        <AiFillLock />
                                                                                    </p>
                                                                                </SliderBrickHeight3>
                                                                            )
                                                                        } else {
                                                                            return(
                                                                                <SliderBrickHeight2 key={index}>
                                                                                    <p className='SliderBrickTitle'>{item.name}</p>
                                                                                    <p className='SliderBrickOdd'>
                                                                                        <AiFillLock />
                                                                                    </p>
                                                                                </SliderBrickHeight2>
                                                                            )
                                                                        }
                                                                        
                                                                    }
                                                                })()
                                                            ))
                                                        }
                                                    </div>
                                                    <div className='col-4' style={Padding01}>
                                                        {
                                                            [0, 1].map(e => (
                                                            <React.Fragment key={e}>
                                                                {
                                                                    (() => {
                                                                        const filteredData = resultObj[fixedPriorityArr[4]]

                                                                        if (filteredData !== undefined ) {
                                                                        const rateKeys = Object.keys(filteredData.rate);
                                                                        const targetRate = filteredData.rate[rateKeys[e]];
                                                                            if(targetRate !== undefined && targetRate.id) {
                                                                                return (
                                                                                    <SliderBrickHeight2 key={e} onClick={()=>this.getBetData(
                                                                                        v.match_id, 
                                                                                        filteredData.rate_id, 
                                                                                        targetRate.id, 
                                                                                        targetRate.rate, 
                                                                                        filteredData.name, 
                                                                                        targetRate.name, 
                                                                                        v.series.name, 
                                                                                        homeData?.team?.name !== undefined ? homeData.team.name : langText.MatchContentCard.home, 
                                                                                        awayData?.team?.name !== undefined ? awayData.team.name : langText.MatchContentCard.away, 
                                                                                        homeData?.team?.logo !== undefined ? homeData?.team?.logo : drfaultImg, 
                                                                                        awayData?.team?.logo !== undefined ? awayData?.team?.logo : drfaultImg,
                                                                                        v.start_time
                                                                                    )} >
                                                                                        <div className="w-100 h-100" bet_match={v.match_id} bet_type={filteredData.rate_id} bet_type_item={targetRate.id}>
                                                                                            {
                                                                                                
                                                                                                filteredData.status === 1 && targetRate.status === 1 && targetRate.risk === 0 && targetRate.rate !== undefined ?
                                                                                                <>
                                                                                                    <p className='SliderBrickTitle'>{targetRate.value}</p>
                                                                                                    <p className='SliderBrickOdd odd'>{targetRate.rate}</p>
                                                                                                </>
                                                                                                :
                                                                                                <AiFillLock style={{ marginTop: '1.2rem'}} />
                                                                                            }
                                                                                        </div>
                                                                                    </SliderBrickHeight2>
                                                                                );
                                                                            }
                                                                       
                                                                        } else {
                                                                        return (
                                                                            <SliderBrickHeight2 key={e}>
                                                                            <AiFillLock style={{ marginTop: '1.2rem'}} />
                                                                            </SliderBrickHeight2>
                                                                        );
                                                                        }
                                                                    })()
                                                                }
                                                            </React.Fragment>
                                                            ))
                                                        }
                                                    </div>

                                                    <div className='col-4' style={Padding01}>
                                                        {
                                                            [langText.MatchContentCard.big, langText.MatchContentCard.small].map((e, i) => {
                                                            const filteredData = resultObj[fixedPriorityArr[5]]
                                                            if (filteredData !== undefined ) {
                                                                const rateKeys = Object.keys(filteredData.rate);
                                                                const targetRate = filteredData.rate[rateKeys[i]];
                                                                if(targetRate !== undefined && targetRate.id) {
                                                                    return (
                                                                        <SliderBrickHeight2 onClick={()=>this.getBetData(
                                                                            v.match_id, 
                                                                            filteredData.rate_id, 
                                                                            targetRate.id, 
                                                                            targetRate.rate, 
                                                                            filteredData.name, 
                                                                            targetRate.name, 
                                                                            v.series.name, 
                                                                            homeData?.team?.name !== undefined ? homeData.team.name : langText.MatchContentCard.home, 
                                                                            awayData?.team?.name !== undefined ? awayData.team.name : langText.MatchContentCard.away, 
                                                                            homeData?.team?.logo !== undefined ? homeData?.team?.logo : drfaultImg, 
                                                                            awayData?.team?.logo !== undefined ? awayData?.team?.logo : drfaultImg,
                                                                            v.start_time
                                                                        )}  key={i}>
                                                                            <div className="w-100 h-100" bet_match={v.match_id} bet_type={filteredData.rate_id} bet_type_item={targetRate.id}>
                                                                                <p className='SliderBrickTitle'>{targetRate.name}</p>
                                                                                {
                                                                                    
                                                                                    filteredData.status === 1 && targetRate.status === 1 && targetRate.risk === 0 && targetRate.rate !== undefined ?
                                                                                    <p className='SliderBrickOdd odd'>{targetRate.rate}</p>
                                                                                    :
                                                                                    <AiFillLock style={{ marginTop: '-1.2rem' }} />
                                                                                }
                                                                            </div>
                                                                    </SliderBrickHeight2>
                                                                    );
                                                                }
                                                                
                                                            } else {
                                                                return (
                                                                    <SliderBrickHeight2 key={i}>
                                                                        <p className='SliderBrickTitle'>{e}</p>
                                                                        <AiFillLock style={{ marginTop: '-1.2rem' }} />
                                                                    </SliderBrickHeight2>
                                                                );
                                                            }
                                                            })
                                                        }
                                                    </div>
                                                </div>

                                            </SwiperSlide>
                                        </Swiper>
                                    </div>
                                    {/* 其他玩法 */}
                                    <div style={OtherBetDiv}>
                                        {
                                            Object.entries(v.rate).map(([k1, v1], index) => {
                                                // 最多顯示三個 不然會超出畫面
                                                // if( fixedPriorityArr.indexOf( parseInt(k1) ) === -1 && v1.filter(item => item.status === 1).length > 0 ) {
                                                if( fixedPriorityArr.indexOf( parseInt(k1) ) === -1) {
                                                    return(
                                                        <div key={k1} style={OtherBetBtn} onClick={()=>this.openOtherBet(v1[0].rate_id)} >
                                                            { v1[0].name }
                                                            <span id={v1[0].rate_id + '_icon'} dir='dirIcon'>▸</span>
                                                        </div>
                                                    )
                                                }
                                            })
                                        }
                                    </div>
                                    <div>
                                        {
                                            Object.entries(v.rate).map(([k1, v1], index) => {
                                                if( fixedPriorityArr.indexOf( parseInt(k1) ) === -1 && index < 9 ) {
                                                    return(
                                                        <div key={k1} className='row otherBetArea' style={OtherBetConainer} id={v1[0].rate_id}> 
                                                            <div className="row m-0">
                                                                {
                                                                    v1.map(ele => {
                                                                        return(
                                                                            Object.entries(ele.rate).map(([k2, v2]) => {
                                                                                if(k1 == 7 || k1 == 8) {
                                                                                    return(
                                                                                        <div className="col-4" key={k2}>
                                                                                            <div style={OtherBetTitle}>
                                                                                                {k2 == 0 && langText.MatchContentCard.home}
                                                                                                {k2 == 1 && langText.MatchContentCard.tie}
                                                                                                {k2 == 2 && langText.MatchContentCard.away}
                                                                                            </div>
                                                                                            {
                                                                                                Object.entries(v2).map(([k3, v3]) => {
                                                                                                    return(
                                                                                                        <div style={OtherBetBrick} key={v3.id} onClick={()=>this.getBetData(v.match_id, ele.rate_id, v3.id, v3.rate, ele.name, v3.name, v.series.name, 
                                                                                                            homeData?.team?.name !== undefined ? homeData.team.name : langText.MatchContentCard.home, 
                                                                                                            awayData?.team?.name !== undefined ? awayData.team.name : langText.MatchContentCard.away,
                                                                                                            homeData?.team?.logo !== undefined ? homeData.team.logo : drfaultImg, 
                                                                                                            awayData?.team?.logo !== undefined ? awayData.team.logo : drfaultImg,
                                                                                                            v.start_time
                                                                                                            )}>
                                                                                                                <div className="w-100 h-100" bet_match={v.match_id} bet_type={ele.rate_id} bet_type_item={v3.id}>
                                                                                                                    <p style={OtherBetBrickName} className="mb-0">{v3.name}</p>
                                                                                                                    {
                                                                                                                        ele.status === 1 && v3.status === 1 && v3.risk === 0 && v3.rate !== undefined ?
                                                                                                                        <p className="mb-0 odd">{v3.rate}</p>
                                                                                                                        :
                                                                                                                        <AiFillLock />
                                                                                                                    }
                                                                                                                </div>
                                                                                                        </div>
                                                                                                    )
                                                                                                })
                                                                                            }
                                                                                        </div>
                                                                                    )
                                                                                } else {
                                                                                    // if( ele.status === 1 ) {
                                                                                        return(
                                                                                            <div className="col-6" key={k2}>
                                                                                                <div style={OtherBetBrick} key={v2.id} onClick={()=>this.getBetData(v.match_id, ele.rate_id, v2.id, v2.rate, ele.name, v2.name, v.series.name, 
                                                                                                    homeData?.team?.name !== undefined ? homeData.team.name : langText.MatchContentCard.home, 
                                                                                                    awayData?.team?.name !== undefined ? awayData.team.name : langText.MatchContentCard.away,
                                                                                                    homeData?.team?.logo !== undefined ? homeData.team.logo : drfaultImg, 
                                                                                                    awayData?.team?.logo !== undefined ? awayData.team.logo : drfaultImg,
                                                                                                    v.start_time
                                                                                                    )}>
                                                                                                        <div className="w-100 h-100" bet_match={v.match_id} bet_type={ele.rate_id} bet_type_item={v2.id}>
                                                                                                            <p style={OtherBetBrickName} className="mb-0">{v2.name}</p>
                                                                                                            {
                                                                                                                ele.status === 1 && v2.status === 1 && v2.risk === 0 && v2.rate !== undefined ?
                                                                                                                <p className="mb-0 odd">{v2.rate}</p>
                                                                                                                :
                                                                                                                <AiFillLock />
                                                                                                            }
                                                                                                        </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        )
                                                                                    // }
                                                                                }
                                                                            })
                                                                        )
                                                                    })
                                                                }
                                                            </div> 
                                                        </div>
                                                    )
                                                }
                                            })
                                        }
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