import React from "react";
import Marquee from "react-fast-marquee";
import { langText } from "../pages/LanguageContext";
import $ from 'jquery';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Controller, Pagination } from 'swiper';
import { IoIosArrowForward, IoIosArrowBack } from 'react-icons/io';
import styled from '@emotion/styled';
import 'bootstrap/dist/css/bootstrap.css';
import  "../css/ResultPage.css";
import 'swiper/css';

const ResultCard_item = { 
    position: 'relative',
    background: '#e2f0f0',
    borderRadius: '15px',
    marginBottom: '1rem',
    paddingTop: '1rem',
    zIndex: 1,
    transition: 'opacity 0.5s ease, max-height 0.5s ease, padding 0.5s ease, margin 0.5s ease', 
};
const ResultTeamIcon = {
	width: '1.3rem',
	height: '1.3rem',
    objectFit: "contain",
}
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

const stat = {
	display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
}

const SliderBrickHeight2 = styled.div`
	height: 3.3rem;
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

const SliderLeftArrow = {
	color: 'white',
	position: 'absolute',
	filter: 'drop-shadow(0px 2px 1px rgba(0,0,0,0.3))',
	top: '7rem',
	left: '39%',
	fontSize: '1.5rem'
}
const SliderRightArrow = {
	color: 'white',
	position: 'absolute',
	filter: 'drop-shadow(0px 2px 1px rgba(0,0,0,0.3))',
	top: '7rem',
	right: '-1%',
	fontSize: '1.5rem'
}

const TeamName = {
    lineHeight: '2rem',
}

const drfaultImg = 'https://sporta.asgame.net/uploads/default.png'

class ResultContentCard extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
            isOtherBetOpen: false,
            swiperIndex: 0,
            match_id: this.props.data.match_id,
            prevBtnOpacity: '0',
            nextBtnOpacity: '1',
		};
        this.slideToggleRef = React.createRef();
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
    
    handleOpacityClick = () => {
        const prevElement = document.querySelector('.swiper-slide-prev');
        if (!prevElement) {
            this.setState({ prevBtnOpacity: '0' });
        } else {
            this.setState({ prevBtnOpacity: '1' });
        }

        const nextElement = document.querySelector('.swiper-slide-next');
        if (!nextElement) {
            this.setState({ nextBtnOpacity: '0' });
        } else {
            this.setState({ nextBtnOpacity: '1' });
        }
    }

    // 圖片毀損
    handleError(event) {
        event.target.src = drfaultImg;
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
        const { prevBtnOpacity, nextBtnOpacity } = this.state;

        const sport = parseInt(window.sport)
        var fixedPriorityArr = []
        var gameTitle = []
        if(sport === 1) {
           fixedPriorityArr = [0, 1, 2]
           gameTitle = [langText.ResultTitle.fullTimeScore,langText.ResultTitle.firstHalfScore,langText.ResultTitle.secondHalfScore]
        }
        if(sport === 2) {
            fixedPriorityArr = [0, 1, 2, 3, 4, 5, 6]
            gameTitle = [langText.ResultTitle.fullTimeScore,langText.ResultTitle.firstHalfScore,langText.ResultTitle.secondHalfScore,langText.ResultTitle.firstQuarterScore,langText.ResultTitle.secondQuarterScore,langText.ResultTitle.thirdQuarterScore,langText.ResultTitle.fourthQuarterScore]
        }
        if(sport === 3) {
            fixedPriorityArr = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
            gameTitle = [langText.ResultTitle.fullTimeScore,langText.ResultTitle.firstRound,langText.ResultTitle.gameTwo,langText.ResultTitle.gameThree,langText.ResultTitle.gameFour,langText.ResultTitle.gameFive,langText.ResultTitle.gameSix,langText.ResultTitle.gameSeven,langText.ResultTitle.gameEight,langText.ResultTitle.gameNine,langText.ResultTitle.gameTen,langText.ResultTitle.gameEleven,langText.ResultTitle.gameTwelve,langText.ResultTitle.overtime,langText.ResultTitle.bttngAve]
        }
        
        if ( v !== undefined ){
            return (
                <div style={ ResultCard_item } cardid={v.match_id}>
                    <div>
                        <div className='row m-0'>
                            {/* left part */}
                            <div className='col-45' style={{ padding: '0 0rem 0 0.5rem'}}>
                                <div className='row m-0' style={rowHeight2}>
                                    <div className='col-10 p-0'>
                                        <p className='mb-0 mt-1'>{this.formatDateTime(v.start_time)}</p>
                                    </div>
                                </div>
                                <div className='row m-0' style={rowHeight2}>
                                    <div className='col-2 p-0'>
                                        {
                                            v?.home_team_logo && <img style={ResultTeamIcon} alt='home' src={v.home_team_logo}  onError={this.handleError}/>
                                        }
                                    </div>
                                    <div className='col-9 p-0 teamSpan' style={TeamName}>
                                        <div className="teamSpanMarquee">
                                            <Marquee className='ResultCard_itemMarquee mt-1' speed={20} gradient={false}>
                                                { v?.home_team_name && v.home_team_name}[{ langText.ResultTitle.hometag }]
                                            </Marquee>
                                        </div>
                                        <span className="teamSpanSpan">
                                            { v?.home_team_name && v.home_team_name }[{ langText.ResultTitle.hometag }]
                                        </span>
                                    </div>
                                </div>
                                <div className='row m-0' style={rowHeight2}>
                                    <div className='col-2 p-0'>
                                        {
                                            v?.away_team_logo && <img style={ResultTeamIcon} alt='away' src={v.away_team_logo}  onError={this.handleError}/>
                                        }
                                    </div>
                                    <div className='col-9 p-0 teamSpan' style={TeamName}>
                                        <div className="teamSpanMarquee">
                                            <Marquee className='ResultCard_itemMarquee mt-1' speed={20} gradient={false}>
                                                { v?.away_team_name && v.away_team_name }
                                            </Marquee>
                                        </div>
                                        <span className="teamSpanSpan">
                                            { v?.away_team_name && v.away_team_name }
                                        </span>
                                    </div>
                                </div>
                            </div>
                            {/* right part */}
                            <div className='col-55 text-center' style={{ paddingLeft: 0, paddingRight: "calc(var(--bs-gutter-x) * 0.7)"}}>
                            {
                                (fixedPriorityArr.length > 3 ) && (
                                    // this.state.swiperIndex === 0 ? (
                                    // <IoIosArrowForward onClick={() => { this.ResultCard_itemSwiper.slideNext() }} style={SliderRightArrow} />
                                    // ) : (
                                    // <IoIosArrowBack onClick={() => { this.ResultCard_itemSwiper.slidePrev() }} style={SliderLeftArrow} />
                                    // )
                                    <div>
                                        <div style={{ opacity: prevBtnOpacity }} onClick={this.handleOpacityClick}>
                                           <IoIosArrowBack onClick={() => { this.ResultCard_itemSwiper.slidePrev() }} style={SliderLeftArrow} />
                                        </div>
                                        <div style={{ opacity: nextBtnOpacity }} onClick={this.handleOpacityClick}>
                                           <IoIosArrowForward onClick={() => { this.ResultCard_itemSwiper.slideNext() }} style={SliderRightArrow} />
                                        </div>
                                    </div>
                                )
                            }
                                <Swiper
                                    slidesPerView={1}
                                    pagination={true}
                                    modules={[Controller, Pagination]}
                                    onSwiper={Swiper => (this.ResultCard_itemSwiper = Swiper)}
                                    className='ResultCard_itemSwiper'
                                    onSlideChange={(Swiper) => {this.swiperHandler(Swiper.activeIndex)}}
                                        style={{ position: 'relative', zIndex: 0}}
                                >
                                    {sport === 1 && 
                                    <div>
                                       <SwiperSlide className="sport1con">
                                            <div className='row m-0'>
                                                <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                    <div style={SliderTitleDiv}>{gameTitle[0]}</div>
                                                </div>
                                                <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                    <div style={SliderTitleDiv}>{gameTitle[1]}</div>
                                                </div>
                                                <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                    <div style={SliderTitleDiv}>{gameTitle[2]}</div>
                                                </div>
                                            </div>
                                            <div className='row m-0'>
                                                <div className='col-4' style={Padding01}>
                                                    <SliderBrickHeight2>
                                                        <div className="w-100 h-100" style={stat}>
                                                            {v?.stat.home_stat[fixedPriorityArr[0]] ? (<div>{v.stat.home_stat[fixedPriorityArr[0]]}</div>) : (<div>--</div>)}
                                                        </div>
                                                    </SliderBrickHeight2>
                                                    <SliderBrickHeight2>
                                                        <div className="w-100 h-100" style={stat}>
                                                        {v?.stat.away_stat[fixedPriorityArr[0]] ? (<div>{v.stat.away_stat[fixedPriorityArr[0]]}</div>) : (<div>--</div>)}
                                                        </div>
                                                    </SliderBrickHeight2>
                                                </div>
                                                <div className='col-4' style={Padding01}>
                                                    <SliderBrickHeight2>
                                                        <div className="w-100 h-100" style={stat}>
                                                            {v?.stat.home_stat[fixedPriorityArr[1]] ? (<div>{v.stat.home_stat[fixedPriorityArr[1]]}</div>) : (<div>--</div>)}
                                                        </div>
                                                    </SliderBrickHeight2>
                                                    <SliderBrickHeight2>
                                                        <div className="w-100 h-100" style={stat}>
                                                        {v?.stat.away_stat[fixedPriorityArr[1]] ? (<div>{v.stat.away_stat[fixedPriorityArr[1]]}</div>) : (<div>--</div>)}
                                                        </div>
                                                    </SliderBrickHeight2>
                                                </div>
                                                <div className='col-4' style={Padding01}>
                                                    <SliderBrickHeight2>
                                                        <div className="w-100 h-100" style={stat}>
                                                            {v?.stat.home_stat[fixedPriorityArr[2]] ? (<div>{v.stat.home_stat[fixedPriorityArr[2]]}</div>) : (<div>--</div>)}
                                                        </div>
                                                    </SliderBrickHeight2>
                                                    <SliderBrickHeight2>
                                                        <div className="w-100 h-100" style={stat}>
                                                        {v?.stat.away_stat[fixedPriorityArr[2]] ? (<div>{v.stat.away_stat[fixedPriorityArr[2]]}</div>) : (<div>--</div>)}
                                                        </div>
                                                    </SliderBrickHeight2>
                                                </div>
                                            </div>
                                        </SwiperSlide>
                                    </div>
                                    }
                                    {sport === 2 && 
                                    <div>
                                    <SwiperSlide className="sport2con">
                                         <div className='row m-0'>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[0]}</div>
                                             </div>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[1]}</div>
                                             </div>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[2]}</div>
                                             </div>
                                         </div>
                                         <div className='row m-0'>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[0]] ? (<div>{v.stat.home_stat[fixedPriorityArr[0]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[0]] ? (<div>{v.stat.away_stat[fixedPriorityArr[0]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[1]] ? (<div>{v.stat.home_stat[fixedPriorityArr[1]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[1]] ? (<div>{v.stat.away_stat[fixedPriorityArr[1]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[2]] ? (<div>{v.stat.home_stat[fixedPriorityArr[2]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[2]] ? (<div>{v.stat.away_stat[fixedPriorityArr[2]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                         </div>
                                     </SwiperSlide>
                                     <SwiperSlide>
                                         <div className='row m-0'>
                                             <div className='col-3' style={{ ...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[3]}</div>
                                             </div>
                                             <div className='col-3' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[4]}</div>
                                             </div>
                                             <div className='col-3' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[5]}</div>
                                             </div>
                                             <div className='col-3' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[6]}</div>
                                             </div>
                                         </div>
                                         <div className='row m-0'>
                                             <div className='col-3' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[3]] ? (<div>{v.stat.home_stat[fixedPriorityArr[3]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[3]] ? (<div>{v.stat.away_stat[fixedPriorityArr[3]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-3' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[4]] ? (<div>{v.stat.home_stat[fixedPriorityArr[4]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[4]] ? (<div>{v.stat.away_stat[fixedPriorityArr[4]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-3' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[5]] ? (<div>{v.stat.home_stat[fixedPriorityArr[5]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[5]] ? (<div>{v.stat.away_stat[fixedPriorityArr[5]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-3' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[6]] ? (<div>{v.stat.home_stat[fixedPriorityArr[6]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[6]] ? (<div>{v.stat.away_stat[fixedPriorityArr[6]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                         </div>
                                     </SwiperSlide>
                                    </div>
                                    }
                                    {sport === 3 && 
                                    <div>
                                    <SwiperSlide className="sport3con">
                                         <div className='row m-0'>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[0]}</div>
                                             </div>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[1]}</div>
                                             </div>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[2]}</div>
                                             </div>
                                         </div>
                                         <div className='row m-0'>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[0]] ? (<div>{v.stat.home_stat[fixedPriorityArr[0]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[0]] ? (<div>{v.stat.away_stat[fixedPriorityArr[0]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[1]] ? (<div>{v.stat.home_stat[fixedPriorityArr[1]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[1]] ? (<div>{v.stat.away_stat[fixedPriorityArr[1]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[2]] ? (<div>{v.stat.home_stat[fixedPriorityArr[2]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[2]] ? (<div>{v.stat.away_stat[fixedPriorityArr[2]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                         </div>
                                     </SwiperSlide>
                                     <SwiperSlide>
                                         <div className='row m-0'>
                                             <div className='col-4' style={{ ...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[3]}</div>
                                             </div>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[4]}</div>
                                             </div>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[5]}</div>
                                             </div>
                                         </div>
                                         <div className='row m-0'>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[3]] ? (<div>{v.stat.home_stat[fixedPriorityArr[3]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[3]] ? (<div>{v.stat.away_stat[fixedPriorityArr[3]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[4]] ? (<div>{v.stat.home_stat[fixedPriorityArr[4]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[4]] ? (<div>{v.stat.away_stat[fixedPriorityArr[4]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[5]] ? (<div>{v.stat.home_stat[fixedPriorityArr[5]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[5]] ? (<div>{v.stat.away_stat[fixedPriorityArr[5]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                         </div>
                                     </SwiperSlide>
                                     <SwiperSlide>
                                         <div className='row m-0'>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[6]}</div>
                                             </div>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[7]}</div>
                                             </div>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[8]}</div>
                                             </div>
                                         </div>
                                         <div className='row m-0'>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[6]] ? (<div>{v.stat.home_stat[fixedPriorityArr[6]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[6]] ? (<div>{v.stat.away_stat[fixedPriorityArr[6]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[7]] ? (<div>{v.stat.home_stat[fixedPriorityArr[7]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[7]] ? (<div>{v.stat.away_stat[fixedPriorityArr[7]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[8]] ? (<div>{v.stat.home_stat[fixedPriorityArr[8]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[8]] ? (<div>{v.stat.away_stat[fixedPriorityArr[8]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                         </div>
                                     </SwiperSlide>
                                     <SwiperSlide>
                                         <div className='row m-0'>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[9]}</div>
                                             </div>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[10]}</div>
                                             </div>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[11]}</div>
                                             </div>
                                         </div>
                                         <div className='row m-0'>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[9]] ? (<div>{v.stat.home_stat[fixedPriorityArr[9]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[9]] ? (<div>{v.stat.away_stat[fixedPriorityArr[9]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[10]] ? (<div>{v.stat.home_stat[fixedPriorityArr[10]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[10]] ? (<div>{v.stat.away_stat[fixedPriorityArr[10]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[11]] ? (<div>{v.stat.home_stat[fixedPriorityArr[11]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[11]] ? (<div>{v.stat.away_stat[fixedPriorityArr[11]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                         </div>
                                     </SwiperSlide>
                                     <SwiperSlide>
                                         <div className='row m-0'>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[12]}</div>
                                             </div>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[13]}</div>
                                             </div>
                                             <div className='col-4' style={{...Padding01, rowHeight2}}>
                                                 <div style={SliderTitleDiv}>{gameTitle[14]}</div>
                                             </div>
                                         </div>
                                         <div className='row m-0'>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[12]] ? (<div>{v.stat.home_stat[fixedPriorityArr[12]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[12]] ? (<div>{v.stat.away_stat[fixedPriorityArr[12]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[13]] ? (<div>{v.stat.home_stat[fixedPriorityArr[13]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[13]] ? (<div>{v.stat.away_stat[fixedPriorityArr[13]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                             <div className='col-4' style={Padding01}>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                         {v?.stat.home_stat[fixedPriorityArr[14]] ? (<div>{v.stat.home_stat[fixedPriorityArr[14]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                                 <SliderBrickHeight2>
                                                     <div className="w-100 h-100" style={stat}>
                                                     {v?.stat.away_stat[fixedPriorityArr[14]] ? (<div>{v.stat.away_stat[fixedPriorityArr[14]]}</div>) : (<div>--</div>)}
                                                     </div>
                                                 </SliderBrickHeight2>
                                             </div>
                                         </div>
                                     </SwiperSlide>
                                    </div>
                                    }
    
                                </Swiper>
                            </div>
                        </div>
                    </div>
                </div>	
            );
        }
	}
}


export default ResultContentCard;