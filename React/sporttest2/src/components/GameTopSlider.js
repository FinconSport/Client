import React from "react";
import { langText } from "../pages/LanguageContext";
import { Link } from "react-router-dom";
import Cookies from 'js-cookie';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Navigation, Pagination,} from 'swiper';
import { RxTriangleUp } from 'react-icons/rx';
import { MdAutorenew } from 'react-icons/md';
import { AiFillStar, AiOutlineStar } from 'react-icons/ai';
import { IoIosArrowForward, IoIosArrowBack } from 'react-icons/io';
import { FaChevronLeft } from 'react-icons/fa';
import GameBg from '../image/gameBg.jpg'
import ScoreBoardBg from '../image/gameStatus.jpg'
import styled from '@emotion/styled';
import 'bootstrap/dist/css/bootstrap.css';
import $ from 'jquery';
import 'swiper/css';
import 'swiper/css/navigation';
import "swiper/css/pagination";
import '../css/GameTopSlider.css'


const teamIconStyle = {
    width: '2.5rem',
    height: '2.5rem',
    marginRight: '5px',
}

const MainInfoSlider = styled.div`
    text-align: center;
    
	> div {
        padding: 0
    }
`

const UpIconStyle1 = {
    color: 'red',
    display: 'none',
    position: 'absolute',
    left: '-30px',
    top: '5px',
}

const UpIconStyle2 = {
    color: 'red',
    display: 'none',
    position: 'absolute',
    right: '-30px',
    top: '5px',
}

const backIcon = {
    background: 'white',
    borderRadius: '50%',
    color: 'rgb(65, 91, 90)',
    width: '2rem',
    height: '2rem',
    padding: '0.2rem',
    marginRight: '1rem'
}

const scoreBoardLogo = {
    display: 'flex',
    alignitems: 'center',
    justifyContent: 'flex-start',
    width: '90%',
}

const scoreBoardLogoCon = {
    width: '40%',
}

const scoreBoardSeriesLogo = {
    width: '2rem',
    height: '2rem',
    marginRight: '5px',
}

const scoreBoardSeries = {
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: '0.25rem 0.5rem',
    background: '#172120a3',
    border: '0.5px solid #2c3032',
}

const scoreBoardseriesLogoCon = {
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'flex-start', 
    width: '75%',
}

const maintablebpard = {
    padding: '0 1rem',
}

class GameTopSlider extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
            isSetStar: Cookies.get(this.props.fixtureId, { path: '/' }) === 'true' || false,
            scoreboardIndex: 0,
            scoreboardSliderCount: 0,
            scColumnCount: 1
        };
	}
    // 加入星號 並設定cookie
	setStarState = (fixture_id) => {
        Cookies.set(fixture_id, !this.state.isSetStar, { path: '/' })
		this.setState({
			isSetStar: !this.state.isSetStar
		})
	}

    prevSB = () => {
        if( this.state.scoreboardIndex -1 > 0 ) {
            this.setState({
                scoreboardIndex: this.state.scoreboardIndex -1
            })
        }
    }

    nextSB = () => {
        if( this.state.scoreboardIndex +1 <= this.state.scoreboardSliderCount ) {
            this.setState({
                scoreboardIndex: this.state.scoreboardIndex +1
            })
        }
    }

    componentDidUpdate(prevProps) {
		if (prevProps.data !== this.props.data) {
            this.componentDidMount(1)
		}
	}


    componentDidMount = ( isupdate = 0 ) => {
        if( isupdate === 0 ) {
            switch (parseInt(Cookies.get('sport', { path: '/' }))) {
                case 154914:
                    this.setState({
                        scColumnCount: 7
                    })
                    break;
                case 48242:case 131506:
                    this.setState({
                        scColumnCount: 5
                    })
                    break;
                case 6046:
                    this.setState({
                        scColumnCount: 3
                    })
                    break;
                case 35232:
                    this.setState({
                        scColumnCount: 4
                    })
                    break;
                default:
                    break;
            }
        }


        if( !this.props.data?.list?.[this.props.fixtureId]?.scoreboard ) return
        const data = this.props.data.list[this.props.fixtureId].scoreboard
        const scbLen = data[1].length - 1;
        switch (true) {
            case scbLen < 6:
                this.setState({
                    scoreboardIndex: 1,
                    scoreboardSliderCount: 1
                })
            break;
            case scbLen >= 6 && scbLen <= 9:
                this.setState({
                    scoreboardIndex: 2,
                    scoreboardSliderCount: 2
                })
            break;
            case scbLen > 9:
                this.setState({
                    scoreboardIndex: 3,
                    scoreboardSliderCount: 3
                })
            break;
            default:
            break;
        }
        
    } 

    refreshGame = () => {
        this.props.refreshGame()
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
        const data = this.props.data
        const sport = parseInt(Cookies.get('sport', { path: '/' }))
        const fixture = data.list[this.props.fixtureId]
        let scTitle = null
        if( sport === 48242 ) {
            scTitle = this.props.league_id === 4045 ? langText.GameTopSlider.scoreBoardTitle[sport][4045] : langText.GameTopSlider.scoreBoardTitle[sport].common
        } else {
            scTitle = langText.GameTopSlider.scoreBoardTitle[sport]
        }

        if( data ) {
            return (
                <div style={{ height: '25%' }}>
                    <Swiper navigation={true}  pagination={true} modules={[Navigation, Pagination]} style={{ color: 'white', fontSize: '0.8rem' }} slidesPerView={1} id='gameTopSlider' className="h-100">
                        <SwiperSlide style={{ backgroundImage: `url(${GameBg})`, backgroundSize: '100% 100%'}}>
                            <MainInfoSlider className='row m-0' style={{ height: '2.5rem', lineHeight: '2.5rem' }}>
                                <div className='col-2 gametopslider'>
                                    <Link to="/mobile/match">
                                        <FaChevronLeft style={backIcon} />
                                    </Link>
                                </div>
                                <div className='col-8 row m-0'>
                                    <div className="p-0">
                                        <p target='league'>{data.league_name}</p>
                                    </div>
                                </div>
                                <div className='col-2' onClick={this.refreshGame}>
                                    <MdAutorenew className={this.props.isGameRefreshing === true ? 'rotateRefresh fs-1' : 'fs-1'}/>
                                </div>
                            </MainInfoSlider>
                            <MainInfoSlider className='row' style={{ margin:'1rem 0.5rem 0 0.5rem'}}>
                                <div className='col-4'>
                                    <p className="fs-6 mt-2 mb-0" target='teamName'>{fixture.home_team_name}</p>
                                </div>
                                <div className='col-4'>
                                    {
                                        fixture.status === 2 && fixture.scoreboard ?
                                        <>
                                            <span className="fs-1" style={{ position: 'relative' }}>
                                                <RxTriangleUp className="upIcon" style={UpIconStyle1}/>
                                                {fixture.scoreboard[1][0]}
                                            </span>
                                            <span className="fs-1"> - </span>
                                            <span className="fs-1" style={{ position: 'relative' }}>
                                                {fixture.scoreboard[2][0]}
                                                <RxTriangleUp className="upIcon" style={UpIconStyle2}/>
                                            </span>
                                        </>
                                        :
                                        <span>{this.formatDateTime(fixture.start_time)}</span>
                                    }
                                    <div onClick={() => this.setStarState(fixture.fixture_id)} className="mt-2">
                                        { this.state.isSetStar === true ?
                                            <AiFillStar/>
                                            :
                                            <AiOutlineStar/>
                                        }
                                        <small>{langText.GameTopSlider.collect}</small>
                                    </div>
                                </div>
                                <div className='col-4'>
                                    <p className="fs-6 mt-2 mb-0" target='teamName'>{fixture.away_team_name}</p>
                                </div>
                            </MainInfoSlider>
                        </SwiperSlide>
                        {(fixture.status === 2 || fixture.status === 9) && fixture.scoreboard && 
                            <SwiperSlide id="scoreBoard" style={{ backgroundImage: `url(${ScoreBoardBg})`, backgroundSize: '100% 100%', paddingBottom: '52px'}}>
                                <MainInfoSlider className='row m-0' style={{ height: '2.5rem', lineHeight: '2.5rem' }}>
                                    <div className='col-2 gametopslider'>
                                        <Link to="/mobile/match">
                                            <FaChevronLeft style={backIcon} />
                                        </Link>
                                    </div>
                                    <div className='col-8 row m-0'>
                                        <div className="col-11 p-0">
                                            <p target='league'>{data.league_name}</p>
                                        </div>
                                    </div>
                                    <div className='col-2' onClick={this.refreshGame}>
                                        <MdAutorenew className={this.props.isGameRefreshing === true ? 'rotateRefresh fs-1' : 'fs-1'}/>
                                    </div>
                                </MainInfoSlider>
                                <div style={maintablebpard}>
                                    <div style={scoreBoardSeries}>
                                        <div style={scoreBoardseriesLogoCon}>
                                            {data.league_name}
                                        </div>
                                        <div className="text-right" style={{ width: '25%'}}>
                                            {sport === 154914 ? 
                                                langText.GameTopSlider.stageStr[sport][fixture.periods.period] + langText.GameTopSlider.baseballPeriod[fixture.periods.Turn]
                                                : 
                                                sport === 48242 ?
                                                this.props.league_id === 4045 ?
                                                langText.GameTopSlider.stageStr[sport][4045][fixture.periods.period]  
                                                :
                                                langText.GameTopSlider.stageStr[sport].common[fixture.periods.period]  
                                                :
                                                langText.GameTopSlider.stageStr[sport][fixture.periods.period]  
                                            }
                                        </div>
                                    </div>
                                    
                                    <table className="table table-bordered">
                                        {(() => {
                                            if( !fixture.scoreboard ) return
                                            switch (this.state.scoreboardIndex) {
                                                case 1:
                                                    this.baseballShowStage = [0, 1, 2, 3, 4, 5, 6];
                                                break;
                                                case 2:
                                                    this.baseballShowStage = [0, 4, 5, 6, 7, 8, 9];
                                                break;
                                                case 3:
                                                    this.baseballShowStage = [0, 7, 8, 9, 10, 11, 12];
                                                break;
                                                default:
                                                    this.baseballShowStage = []
                                                break;
                                            }
                                        })()}
                                        <thead>
                                            <tr>
                                                <th style={{ width: '20%'}}></th>
                                                {
                                                    scTitle.map((v, k) => {
                                                        return(
                                                            <th key={k}style={{
                                                                width: `${80 / this.state.scColumnCount}%`,
                                                                ...(sport === 154914 && this.baseballShowStage.indexOf(k) === -1
                                                                ? { display: 'none' }: null),}}
                                                            >{v}</th>
                                                        )
                                                    })
                                                }
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style={{textOverflow: 'ellipsis',whiteSpace: 'nowrap',overflow: 'hidden',maxWidth: '5rem'}}>
                                                    {fixture.home_team_name}
                                                </td>
                                                {
                                                    scTitle.map((v, k) => {
                                                        return(
                                                            <td style={ sport === 154914 && this.baseballShowStage.indexOf(k) === -1 ? {display: 'none'} : null } key={k}>
                                                                {Array.from(Object.values(fixture.scoreboard[1]))[k] === undefined ? '-' : Array.from(Object.values(fixture.scoreboard[1]))[k]}

                                                                {/* arrow */}
                                                                {
                                                                    k === 0 && this.state.scoreboardIndex -1 > 0 ? <IoIosArrowBack onClick={this.prevSB} style={{ 
                                                                        position: 'absolute', 
                                                                        left: '20%', 
                                                                        bottom: '1rem',
                                                                        fontSize: '1.5rem'
                                                                    }} /> : null
                                                                }
                                                                {
                                                                    k === 0 && this.state.scoreboardIndex + 1 <= this.state.scoreboardSliderCount ? <IoIosArrowForward onClick={this.nextSB}  style={{ 
                                                                        position: 'absolute', 
                                                                        right: '-0.5rem', 
                                                                        bottom: '1rem',
                                                                        fontSize: '1.5rem'
                                                                    }} /> : null
                                                                }
                                                            </td>
                                                        )
                                                    })
                                                }
                                            </tr>
                                            <tr>
                                                <td style={{textOverflow: 'ellipsis',whiteSpace: 'nowrap',overflow: 'hidden',maxWidth: '5rem'}}>
                                                    {fixture.away_team_name}
                                                </td>
                                                {
                                                    scTitle.map((v, k) => {
                                                        return(
                                                            <td style={ sport === 154914 && this.baseballShowStage.indexOf(k) === -1 ? {display: 'none'} : null } key={k}>{Array.from(Object.values(fixture.scoreboard[2]))[k] === undefined ? '-' : Array.from(Object.values(fixture.scoreboard[2]))[k]}</td>
                                                        )
                                                    })
                                                }
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </SwiperSlide>
                        }
                    </Swiper>
                </div>
            )
        }
		
	}
	
	
}


export default GameTopSlider;
