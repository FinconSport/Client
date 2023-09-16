import React from "react";
import { langText } from "../pages/LanguageContext";
import { Link } from "react-router-dom";
import Cookies from 'js-cookie';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Navigation, Pagination,} from 'swiper';
import Marquee from "react-fast-marquee";
import { RxTriangleUp } from 'react-icons/rx';
import { MdAutorenew } from 'react-icons/md';
import { AiFillStar, AiOutlineStar } from 'react-icons/ai';
import { FaChevronLeft } from 'react-icons/fa';
import GameBg from '../image/gameBg.jpg'
import ScoreBoardBg from '../image/gameStatus.jpg'
import styled from '@emotion/styled';
import 'bootstrap/dist/css/bootstrap.css';
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
    padding: '0.5rem',
    background: '#172120a3',
    border: '0.5px solid #2c3032',
}

const scoreBoardseriesLogoCon = {
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'flex-start', 
    width: '66%',
}

const maintablebpard = {
    padding: '0.5rem 1.5rem 0.5rem 1.5rem',
}

class GameTopSlider extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
            isSetStar: Cookies.get(this.props.data.match_id, { path: '/' }) === 'true' || false,
        };
	}
    // 加入星號 並設定cookie
	setStarState = (matchId) => {
        Cookies.set(matchId, !this.state.isSetStar, { path: '/' })
		this.setState({
			isSetStar: !this.state.isSetStar
		})
	}

    refreshGame = () => {
        // console.log('refreshGame')
        this.props.refreshGame()
    }

    // 圖片毀損
    handleError(event) {
        event.target.src = 'https://sporta.asgame.net/uploads/default.png';
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
        var data = this.props.data
        // switch (window.sport) {
        //     case 1:case '1':
        //         data = {
        //             "teams": [
        //                 {
        //                     "index": 1,
        //                     "total_score": "3",
        //                     "scores": [
        //                         {
        //                             "stage": 1,
        //                             "score": "1"
        //                         },
        //                         {
        //                             "stage": 2,
        //                             "score": "2"
        //                         }
        //                     ],
        //                     "team": {
        //                         "id": 31048,
        //                         "game_id": 1,
        //                         "logo": "https://sporta.asgame.net/uploads/team_31048.png?v=1_2_34",
        //                         "name": "堡壘"
        //                     }
        //                 },
        //                 {
        //                     "index": 2,
        //                     "total_score": "1",
        //                     "scores": [
        //                         {
        //                             "stage": 1,
        //                             "score": "1"
        //                         },
        //                         {
        //                             "stage": 2,
        //                             "score": "0"
        //                         }
        //                     ],
        //                     "team": {
        //                         "id": 31179,
        //                         "game_id": 1,
        //                         "logo": "https://sporta.asgame.net/uploads/team_31179.png?v=1_2_34",
        //                         "name": "聯盟"
        //                     }
        //                 }
        //             ],
        //         }
        //         break;
        //     case 2:case '2':
        //         data = {
        //             "id": 65186,
        //             "match_id": 291821,
        //             "game_id": 2,
        //             "start_time": "2023-09-16 17:30:00",
        //             "end_time": "1970-01-01 08:00:00",
        //             "status": 1,
        //             "bo": 1,
        //             "win_team": 0,
        //             "live_status": null,
        //             "has_live": null,
        //             "has_animation": null,
        //             "series": {
        //                 "id": 161,
        //                 "game_id": 2,
        //                 "abbr": "澳篮联",
        //                 "logo": "https://sporta.asgame.net/uploads/series_161.png?v=1_2_34",
        //                 "name": "澳大利亞國家籃球聯賽"
        //             },
        //             "teams": [
        //                 {
        //                     "index": 1,
        //                     "total_score": "0",
        //                     "scores": [
        //                         {
        //                             "stage": 1,
        //                             "score": "1"
        //                         },
        //                         {
        //                             "stage": 2,
        //                             "score": "0"
        //                         }
        //                     ],
        //                     "team": {
        //                         "id": 4355,
        //                         "game_id": 2,
        //                         "logo": "https://sporta.asgame.net/uploads/team_4355.png?v=1_2_34",
        //                         "name": "布里斯班子彈"
        //                     }
        //                 },
        //                 {
        //                     "index": 2,
        //                     "total_score": "0",
        //                     "scores": [
        //                         {
        //                             "stage": 1,
        //                             "score": "1"
        //                         },
        //                         {
        //                             "stage": 2,
        //                             "score": "0"
        //                         }
        //                     ],
        //                     "team": {
        //                         "id": 3762,
        //                         "game_id": 2,
        //                         "logo": "https://sporta.asgame.net/uploads/team_3762.png?v=1_2_34",
        //                         "name": "墨爾本聯"
        //                     }
        //                 }
        //             ],
        //             "rate": {
        //                 "1": [
        //                     {
        //                         "id": 1819748,
        //                         "rate_id": 40732946,
        //                         "name": "全場大小",
        //                         "game_priority": 1,
        //                         "status": 1,
        //                         "rate_value": "161.5",
        //                         "rate": {
        //                             "103468504": {
        //                                 "id": 103468504,
        //                                 "limit": 0,
        //                                 "name": "大  161.5",
        //                                 "rate": "1.75",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "161.5"
        //                             },
        //                             "103468505": {
        //                                 "id": 103468505,
        //                                 "limit": 0,
        //                                 "name": "小  161.5",
        //                                 "rate": "1.95",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "161.5"
        //                             }
        //                         }
        //                     },
        //                     {
        //                         "id": 1805616,
        //                         "rate_id": 40674455,
        //                         "name": "全場大小",
        //                         "game_priority": 1,
        //                         "status": 1,
        //                         "rate_value": "162.5",
        //                         "rate": {
        //                             "103261236": {
        //                                 "id": 103261236,
        //                                 "limit": 0,
        //                                 "name": "大  162.5",
        //                                 "rate": "1.85",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "162.5"
        //                             },
        //                             "103261258": {
        //                                 "id": 103261258,
        //                                 "limit": 0,
        //                                 "name": "小  162.5",
        //                                 "rate": "1.85",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "162.5"
        //                             }
        //                         }
        //                     },
        //                     {
        //                         "id": 1819747,
        //                         "rate_id": 40732945,
        //                         "name": "全場大小",
        //                         "game_priority": 1,
        //                         "status": 1,
        //                         "rate_value": "163.5",
        //                         "rate": {
        //                             "103468500": {
        //                                 "id": 103468500,
        //                                 "limit": 0,
        //                                 "name": "大  163.5",
        //                                 "rate": "1.95",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "163.5"
        //                             },
        //                             "103468503": {
        //                                 "id": 103468503,
        //                                 "limit": 0,
        //                                 "name": "小  163.5",
        //                                 "rate": "1.75",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "163.5"
        //                             }
        //                         }
        //                     },
        //                     {
        //                         "id": 1842296,
        //                         "rate_id": 40810958,
        //                         "name": "全場大小",
        //                         "game_priority": 1,
        //                         "status": 1,
        //                         "rate_value": "164.5",
        //                         "rate": {
        //                             "103720369": {
        //                                 "id": 103720369,
        //                                 "limit": 0,
        //                                 "name": "大  164.5",
        //                                 "rate": "1.95",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "164.5"
        //                             },
        //                             "103720372": {
        //                                 "id": 103720372,
        //                                 "limit": 0,
        //                                 "name": "小  164.5",
        //                                 "rate": "1.75",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "164.5"
        //                             }
        //                         }
        //                     }
        //                 ],
        //                 "3": [
        //                     {
        //                         "id": 1805612,
        //                         "rate_id": 40674448,
        //                         "name": "上半場大小",
        //                         "game_priority": 3,
        //                         "status": 1,
        //                         "rate_value": "82.5",
        //                         "rate": {
        //                             "103261241": {
        //                                 "id": 103261241,
        //                                 "limit": 0,
        //                                 "name": "大  82.5",
        //                                 "rate": "1.83",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "82.5"
        //                             },
        //                             "103261245": {
        //                                 "id": 103261245,
        //                                 "limit": 0,
        //                                 "name": "小  82.5",
        //                                 "rate": "1.85",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "82.5"
        //                             }
        //                         }
        //                     },
        //                     {
        //                         "id": 1842297,
        //                         "rate_id": 40810960,
        //                         "name": "上半場大小",
        //                         "game_priority": 3,
        //                         "status": 1,
        //                         "rate_value": "83.5",
        //                         "rate": {
        //                             "103720365": {
        //                                 "id": 103720365,
        //                                 "limit": 0,
        //                                 "name": "大  83.5",
        //                                 "rate": "1.83",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "83.5"
        //                             },
        //                             "103720374": {
        //                                 "id": 103720374,
        //                                 "limit": 0,
        //                                 "name": "小  83.5",
        //                                 "rate": "1.85",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "83.5"
        //                             }
        //                         }
        //                     }
        //                 ],
        //                 "5": [
        //                     {
        //                         "id": 1805611,
        //                         "rate_id": 40674447,
        //                         "name": "全場獨贏",
        //                         "game_priority": 5,
        //                         "status": 1,
        //                         "rate_value": "",
        //                         "rate": {
        //                             "103261237": {
        //                                 "id": 103261237,
        //                                 "limit": 0,
        //                                 "name": "布里斯班子彈 ",
        //                                 "rate": "2.24",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": ""
        //                             },
        //                             "103261239": {
        //                                 "id": 103261239,
        //                                 "limit": 0,
        //                                 "name": "墨爾本聯 ",
        //                                 "rate": "1.57",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": ""
        //                             }
        //                         }
        //                     }
        //                 ],
        //                 "6": [
        //                     {
        //                         "id": 1805614,
        //                         "rate_id": 40674449,
        //                         "name": "上半場獨贏",
        //                         "game_priority": 6,
        //                         "status": 1,
        //                         "rate_value": "",
        //                         "rate": {
        //                             "103261243": {
        //                                 "id": 103261243,
        //                                 "limit": 0,
        //                                 "name": "布里斯班子彈 ",
        //                                 "rate": "2.09",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": ""
        //                             },
        //                             "103261254": {
        //                                 "id": 103261254,
        //                                 "limit": 0,
        //                                 "name": "墨爾本聯 ",
        //                                 "rate": "1.65",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": ""
        //                             }
        //                         }
        //                     }
        //                 ],
        //                 "9": [
        //                     {
        //                         "id": 1819746,
        //                         "rate_id": 40732943,
        //                         "name": "全場讓分",
        //                         "game_priority": 9,
        //                         "status": 1,
        //                         "rate_value": "2.5",
        //                         "rate": {
        //                             "103468499": {
        //                                 "id": 103468499,
        //                                 "limit": 0,
        //                                 "name": "布里斯班子彈  +2.5",
        //                                 "rate": "2.03",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "+2.5"
        //                             },
        //                             "103468501": {
        //                                 "id": 103468501,
        //                                 "limit": 0,
        //                                 "name": "墨爾本聯  -2.5",
        //                                 "rate": "1.73",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "-2.5"
        //                             }
        //                         }
        //                     },
        //                     {
        //                         "id": 1805617,
        //                         "rate_id": 40674454,
        //                         "name": "全場讓分",
        //                         "game_priority": 9,
        //                         "status": 1,
        //                         "rate_value": "3.5",
        //                         "rate": {
        //                             "103261246": {
        //                                 "id": 103261246,
        //                                 "limit": 0,
        //                                 "name": "布里斯班子彈  +3.5",
        //                                 "rate": "1.88",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "+3.5"
        //                             },
        //                             "103261257": {
        //                                 "id": 103261257,
        //                                 "limit": 0,
        //                                 "name": "墨爾本聯  -3.5",
        //                                 "rate": "1.88",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "-3.5"
        //                             }
        //                         }
        //                     },
        //                     {
        //                         "id": 1819745,
        //                         "rate_id": 40732944,
        //                         "name": "全場讓分",
        //                         "game_priority": 9,
        //                         "status": 1,
        //                         "rate_value": "4.5",
        //                         "rate": {
        //                             "103468498": {
        //                                 "id": 103468498,
        //                                 "limit": 0,
        //                                 "name": "布里斯班子彈  +4.5",
        //                                 "rate": "1.73",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "+4.5"
        //                             },
        //                             "103468502": {
        //                                 "id": 103468502,
        //                                 "limit": 0,
        //                                 "name": "墨爾本聯  -4.5",
        //                                 "rate": "2.03",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "-4.5"
        //                             }
        //                         }
        //                     }
        //                 ],
        //                 "10": [
        //                     {
        //                         "id": 1805615,
        //                         "rate_id": 40674453,
        //                         "name": "上半場讓分",
        //                         "game_priority": 10,
        //                         "status": 1,
        //                         "rate_value": "1.5",
        //                         "rate": {
        //                             "103261238": {
        //                                 "id": 103261238,
        //                                 "limit": 0,
        //                                 "name": "布里斯班子彈  +1.5",
        //                                 "rate": "1.86",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "+1.5"
        //                             },
        //                             "103261247": {
        //                                 "id": 103261247,
        //                                 "limit": 0,
        //                                 "name": "墨爾本聯  -1.5",
        //                                 "rate": "1.88",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "-1.5"
        //                             }
        //                         }
        //                     }
        //                 ],
        //                 "11": [
        //                     {
        //                         "id": 1805613,
        //                         "rate_id": 40674450,
        //                         "name": "第1節讓分",
        //                         "game_priority": 11,
        //                         "status": 1,
        //                         "rate_value": "0.5",
        //                         "rate": {
        //                             "103261240": {
        //                                 "id": 103261240,
        //                                 "limit": 0,
        //                                 "name": "布里斯班子彈  +0.5",
        //                                 "rate": "1.96",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "+0.5"
        //                             },
        //                             "103261249": {
        //                                 "id": 103261249,
        //                                 "limit": 0,
        //                                 "name": "墨爾本聯  -0.5",
        //                                 "rate": "1.78",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "-0.5"
        //                             }
        //                         }
        //                     },
        //                     {
        //                         "id": 1837740,
        //                         "rate_id": 40791091,
        //                         "name": "第1節讓分",
        //                         "game_priority": 11,
        //                         "status": 1,
        //                         "rate_value": "1.5",
        //                         "rate": {
        //                             "103648466": {
        //                                 "id": 103648466,
        //                                 "limit": 0,
        //                                 "name": "布里斯班子彈  +1.5",
        //                                 "rate": "1.75",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "+1.5"
        //                             },
        //                             "103648469": {
        //                                 "id": 103648469,
        //                                 "limit": 0,
        //                                 "name": "墨爾本聯  -1.5",
        //                                 "rate": "1.99",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "-1.5"
        //                             }
        //                         }
        //                     }
        //                 ],
        //                 "12": [
        //                     {
        //                         "id": 1805620,
        //                         "rate_id": 40674460,
        //                         "name": "第1節大小",
        //                         "game_priority": 12,
        //                         "status": 1,
        //                         "rate_value": "41.5",
        //                         "rate": {
        //                             "103261261": {
        //                                 "id": 103261261,
        //                                 "limit": 0,
        //                                 "name": "大  41.5",
        //                                 "rate": "1.85",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "41.5"
        //                             },
        //                             "103261268": {
        //                                 "id": 103261268,
        //                                 "limit": 0,
        //                                 "name": "小  41.5",
        //                                 "rate": "1.83",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": "41.5"
        //                             }
        //                         }
        //                     }
        //                 ],
        //                 "13": [
        //                     {
        //                         "id": 1805618,
        //                         "rate_id": 40674459,
        //                         "name": "第1節單雙",
        //                         "game_priority": 13,
        //                         "status": 1,
        //                         "rate_value": "",
        //                         "rate": {
        //                             "103261253": {
        //                                 "id": 103261253,
        //                                 "limit": 0,
        //                                 "name": "單 ",
        //                                 "rate": "1.94",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": ""
        //                             },
        //                             "103261260": {
        //                                 "id": 103261260,
        //                                 "limit": 0,
        //                                 "name": "雙 ",
        //                                 "rate": "1.94",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": ""
        //                             }
        //                         }
        //                     }
        //                 ],
        //                 "14": [
        //                     {
        //                         "id": 1805619,
        //                         "rate_id": 40674458,
        //                         "name": "第1節獨贏",
        //                         "game_priority": 14,
        //                         "status": 1,
        //                         "rate_value": "",
        //                         "rate": {
        //                             "103261256": {
        //                                 "id": 103261256,
        //                                 "limit": 0,
        //                                 "name": "布里斯班子彈 ",
        //                                 "rate": "2.01",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": ""
        //                             },
        //                             "103261265": {
        //                                 "id": 103261265,
        //                                 "limit": 0,
        //                                 "name": "墨爾本聯 ",
        //                                 "rate": "1.71",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769324,
        //                                 "value": ""
        //                             }
        //                         }
        //                     }
        //                 ]
        //             }
        //         }
        //         break;
        //     case 3:case '3':
        //         data = {
        //             "id": 66578,
        //             "match_id": 170925,
        //             "game_id": 3,
        //             "start_time": "2023-09-15 17:00:00",
        //             "end_time": "1970-01-01 08:00:00",
        //             "status": 2,
        //             "bo": 1,
        //             "win_team": 0,
        //             "live_status": null,
        //             "has_live": null,
        //             "has_animation": null,
        //             "series": {
        //                 "id": 23,
        //                 "game_id": 3,
        //                 "abbr": "NPB",
        //                 "logo": "https://sporta.asgame.net/uploads/series_23.png?v=1_2_34",
        //                 "name": "NPB 日本職業棒球"
        //             },
        //             "teams": [
        //                 {
        //                     "index": 2,
        //                     "total_score": "0",
        //                     "scores": [
        //                         {
        //                             "stage": 1,
        //                             "score": "0"
        //                         }
        //                     ],
        //                     "team": {
        //                         "id": 7367,
        //                         "game_id": 3,
        //                         "logo": "https://sporta.asgame.net/uploads/team_7367.png?v=1_2_34",
        //                         "name": "讀賣巨人"
        //                     }
        //                 },
        //                 {
        //                     "index": 1,
        //                     "total_score": "0",
        //                     "scores": [
        //                         {
        //                             "stage": 1,
        //                             "score": "0"
        //                         }
        //                     ],
        //                     "team": {
        //                         "id": 308,
        //                         "game_id": 3,
        //                         "logo": "https://sporta.asgame.net/uploads/team_308.png?v=1_2_34",
        //                         "name": "中日龍"
        //                     }
        //                 }
        //             ],
        //             "rate": {
        //                 "1": [
        //                     {
        //                         "id": 1862124,
        //                         "rate_id": 2341994,
        //                         "name": "全場大小",
        //                         "game_priority": 1,
        //                         "status": 2,
        //                         "rate_value": "4.5",
        //                         "rate": {
        //                             "5157204": {
        //                                 "id": 5157204,
        //                                 "limit": 0,
        //                                 "name": "大  4.5",
        //                                 "rate": "1.77",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769331,
        //                                 "value": "4.5"
        //                             },
        //                             "5157205": {
        //                                 "id": 5157205,
        //                                 "limit": 0,
        //                                 "name": "小  4.5",
        //                                 "rate": "1.89",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769331,
        //                                 "value": "4.5"
        //                             }
        //                         },
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "id": 1844464,
        //                         "rate_id": 2332811,
        //                         "name": "全場大小",
        //                         "game_priority": 1,
        //                         "status": 2,
        //                         "rate_value": "5.5",
        //                         "rate": {
        //                             "5136956": {
        //                                 "id": 5136956,
        //                                 "limit": 0,
        //                                 "name": "大  5.5",
        //                                 "rate": "2.03",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769331,
        //                                 "value": "5.5"
        //                             },
        //                             "5136960": {
        //                                 "id": 5136960,
        //                                 "limit": 0,
        //                                 "name": "小  5.5",
        //                                 "rate": "1.63",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769331,
        //                                 "value": "5.5"
        //                             }
        //                         }
        //                     },
        //                     {
        //                         "rate_id": 2341929,
        //                         "game_priority": 1,
        //                         "name": "全場大小",
        //                         "rate": {
        //                             "5157072": {
        //                                 "id": 5157072,
        //                                 "name_cn": "大  8.5",
        //                                 "rate": "1.8",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "大  8.5",
        //                                 "value": "大  8.5"
        //                             },
        //                             "5157075": {
        //                                 "id": 5157075,
        //                                 "name_cn": "小  8.5",
        //                                 "rate": "1.86",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "小  8.5",
        //                                 "value": "小  8.5"
        //                             }
        //                         },
        //                         "status": 1,
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "rate_id": 2332853,
        //                         "game_priority": 1,
        //                         "name": "全場大小",
        //                         "rate": {
        //                             "5137062": {
        //                                 "id": 5137062,
        //                                 "name_cn": "大  6.5",
        //                                 "rate": "1.72",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "大  6.5",
        //                                 "value": "大  6.5"
        //                             },
        //                             "5137066": {
        //                                 "id": 5137066,
        //                                 "name_cn": "小  6.5",
        //                                 "rate": "1.94",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "小  6.5",
        //                                 "value": "小  6.5"
        //                             }
        //                         },
        //                         "status": 2,
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "rate_id": 2341898,
        //                         "game_priority": 1,
        //                         "name": "全場大小",
        //                         "rate": {
        //                             "5157007": {
        //                                 "id": 5157007,
        //                                 "name_cn": "大  7.5",
        //                                 "rate": "1.74",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "大  7.5",
        //                                 "value": "大  7.5"
        //                             },
        //                             "5157012": {
        //                                 "id": 5157012,
        //                                 "name_cn": "小  7.5",
        //                                 "rate": "1.92",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "小  7.5",
        //                                 "value": "小  7.5"
        //                             }
        //                         },
        //                         "status": 2,
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "rate_id": 2341840,
        //                         "game_priority": 1,
        //                         "name": "全場大小",
        //                         "rate": {
        //                             "5156872": {
        //                                 "id": 5156872,
        //                                 "name_cn": "大  9.5",
        //                                 "rate": "1.94",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769342,
        //                                 "name": "大  9.5",
        //                                 "value": "大  9.5"
        //                             },
        //                             "5156873": {
        //                                 "id": 5156873,
        //                                 "name_cn": "小  9.5",
        //                                 "rate": "1.72",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769342,
        //                                 "name": "小  9.5",
        //                                 "value": "小  9.5"
        //                             }
        //                         },
        //                         "status": 2,
        //                         "updated_at": 1694769342
        //                     },
        //                     {
        //                         "rate_id": 2332960,
        //                         "game_priority": 1,
        //                         "name": "全場大小",
        //                         "rate": {
        //                             "5137344": {
        //                                 "id": 5137344,
        //                                 "name_cn": "大  7.5",
        //                                 "rate": "1.72",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769342,
        //                                 "name": "大  7.5",
        //                                 "value": "大  7.5"
        //                             },
        //                             "5137345": {
        //                                 "id": 5137345,
        //                                 "name_cn": "小  7.5",
        //                                 "rate": "1.94",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769342,
        //                                 "name": "小  7.5",
        //                                 "value": "小  7.5"
        //                             }
        //                         },
        //                         "status": 2,
        //                         "updated_at": 1694769342
        //                     }
        //                 ],
        //                 "2": [
        //                     {
        //                         "id": 1844460,
        //                         "rate_id": 2332808,
        //                         "name": "全場讓球",
        //                         "game_priority": 2,
        //                         "status": 2,
        //                         "rate_value": "1.5",
        //                         "rate": {
        //                             "5136953": {
        //                                 "id": 5136953,
        //                                 "limit": 0,
        //                                 "name": "中日龍  +1.5",
        //                                 "rate": "1.52",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769331,
        //                                 "value": "+1.5"
        //                             },
        //                             "5136957": {
        //                                 "id": 5136957,
        //                                 "limit": 0,
        //                                 "name": "讀賣巨人  -1.5",
        //                                 "rate": "2.19",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769331,
        //                                 "value": "-1.5"
        //                             }
        //                         },
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "rate_id": 2332851,
        //                         "game_priority": 2,
        //                         "name": "全場讓球",
        //                         "rate": {
        //                             "5137059": {
        //                                 "id": 5137059,
        //                                 "name_cn": "广岛鲤鱼  -1.5",
        //                                 "rate": "2.09",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "广岛鲤鱼  -1.5",
        //                                 "value": "广岛鲤鱼  -1.5"
        //                             },
        //                             "5137064": {
        //                                 "id": 5137064,
        //                                 "name_cn": "阪神虎  +1.5",
        //                                 "rate": "1.59",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "阪神虎  +1.5",
        //                                 "value": "阪神虎  +1.5"
        //                             }
        //                         },
        //                         "status": 2,
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "rate_id": 2341894,
        //                         "game_priority": 2,
        //                         "name": "全場讓球",
        //                         "rate": {
        //                             "5157004": {
        //                                 "id": 5157004,
        //                                 "name_cn": "广岛鲤鱼  +1.5",
        //                                 "rate": "1.74",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "广岛鲤鱼  +1.5",
        //                                 "value": "广岛鲤鱼  +1.5"
        //                             },
        //                             "5157005": {
        //                                 "id": 5157005,
        //                                 "name_cn": "阪神虎  -1.5",
        //                                 "rate": "1.94",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "阪神虎  -1.5",
        //                                 "value": "阪神虎  -1.5"
        //                             }
        //                         },
        //                         "status": 1,
        //                         "updated_at": 1694769341
        //                     }
        //                 ],
        //                 "5": [
        //                     {
        //                         "id": 1844463,
        //                         "rate_id": 2332809,
        //                         "name": "全場獨贏",
        //                         "game_priority": 5,
        //                         "status": 2,
        //                         "rate_value": "",
        //                         "rate": {
        //                             "5136954": {
        //                                 "id": 5136954,
        //                                 "limit": 0,
        //                                 "name": "中日龍 ",
        //                                 "rate": "2.24",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769331,
        //                                 "value": ""
        //                             },
        //                             "5136958": {
        //                                 "id": 5136958,
        //                                 "limit": 0,
        //                                 "name": "讀賣巨人 ",
        //                                 "rate": "1.52",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769331,
        //                                 "value": ""
        //                             }
        //                         },
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "rate_id": 2332850,
        //                         "game_priority": 5,
        //                         "name": "全場獨贏",
        //                         "rate": {
        //                             "5137060": {
        //                                 "id": 5137060,
        //                                 "name_cn": "广岛鲤鱼 ",
        //                                 "rate": "2.26",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "广岛鲤鱼 ",
        //                                 "value": "广岛鲤鱼 "
        //                             },
        //                             "5137063": {
        //                                 "id": 5137063,
        //                                 "name_cn": "阪神虎 ",
        //                                 "rate": "1.52",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769280,
        //                                 "name": "阪神虎 ",
        //                                 "value": "阪神虎 "
        //                             }
        //                         },
        //                         "status": 1,
        //                         "updated_at": 1694769341
        //                     }
        //                 ],
        //                 "27": [
        //                     {
        //                         "id": 1861883,
        //                         "rate_id": 2341682,
        //                         "name": "前5局大小",
        //                         "game_priority": 27,
        //                         "status": 2,
        //                         "rate_value": "2.5",
        //                         "rate": {
        //                             "5156536": {
        //                                 "id": 5156536,
        //                                 "limit": 0,
        //                                 "name": "大  2.5",
        //                                 "rate": "2.08",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769332,
        //                                 "value": "2.5"
        //                             },
        //                             "5156537": {
        //                                 "id": 5156537,
        //                                 "limit": 0,
        //                                 "name": "小  2.5",
        //                                 "rate": "1.58",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769332,
        //                                 "value": "2.5"
        //                             }
        //                         },
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "id": 1861834,
        //                         "rate_id": 2341634,
        //                         "name": "前5局大小",
        //                         "game_priority": 27,
        //                         "status": 2,
        //                         "rate_value": "3.5",
        //                         "rate": {
        //                             "5156420": {
        //                                 "id": 5156420,
        //                                 "limit": 0,
        //                                 "name": "大  3.5",
        //                                 "rate": "2.07",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769332,
        //                                 "value": "3.5"
        //                             },
        //                             "5156422": {
        //                                 "id": 5156422,
        //                                 "limit": 0,
        //                                 "name": "小  3.5",
        //                                 "rate": "1.59",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769332,
        //                                 "value": "3.5"
        //                             }
        //                         }
        //                     },
        //                     {
        //                         "rate_id": 2341700,
        //                         "game_priority": 27,
        //                         "name": "前5局 - 大小",
        //                         "rate": {
        //                             "5156571": {
        //                                 "id": 5156571,
        //                                 "name_cn": "大  3.5",
        //                                 "rate": "1.77",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "大  3.5",
        //                                 "value": "大  3.5"
        //                             },
        //                             "5156575": {
        //                                 "id": 5156575,
        //                                 "name_cn": "小  3.5",
        //                                 "rate": "1.89",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "小  3.5",
        //                                 "value": "小  3.5"
        //                             }
        //                         },
        //                         "status": 2,
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "rate_id": 2341931,
        //                         "game_priority": 27,
        //                         "name": "前5局 - 大小",
        //                         "rate": {
        //                             "5157077": {
        //                                 "id": 5157077,
        //                                 "name_cn": "大  5.5",
        //                                 "rate": "1.94",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "大  5.5",
        //                                 "value": "大  5.5"
        //                             },
        //                             "5157079": {
        //                                 "id": 5157079,
        //                                 "name_cn": "小  5.5",
        //                                 "rate": "1.72",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "小  5.5",
        //                                 "value": "小  5.5"
        //                             }
        //                         },
        //                         "status": 1,
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "rate_id": 2341896,
        //                         "game_priority": 27,
        //                         "name": "前5局 - 大小",
        //                         "rate": {
        //                             "5157006": {
        //                                 "id": 5157006,
        //                                 "name_cn": "大  4.5",
        //                                 "rate": "1.77",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "大  4.5",
        //                                 "value": "大  4.5"
        //                             },
        //                             "5157010": {
        //                                 "id": 5157010,
        //                                 "name_cn": "小  4.5",
        //                                 "rate": "1.89",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "小  4.5",
        //                                 "value": "小  4.5"
        //                             }
        //                         },
        //                         "status": 2,
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "rate_id": 2341841,
        //                         "game_priority": 27,
        //                         "name": "前5局 - 大小",
        //                         "rate": {
        //                             "5156874": {
        //                                 "id": 5156874,
        //                                 "name_cn": "大  5.5",
        //                                 "rate": "1.99",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769342,
        //                                 "name": "大  5.5",
        //                                 "value": "大  5.5"
        //                             },
        //                             "5156876": {
        //                                 "id": 5156876,
        //                                 "name_cn": "小  5.5",
        //                                 "rate": "1.67",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769342,
        //                                 "name": "小  5.5",
        //                                 "value": "小  5.5"
        //                             }
        //                         },
        //                         "status": 2,
        //                         "updated_at": 1694769342
        //                     }
        //                 ],
        //                 "28": [
        //                     {
        //                         "id": 1861835,
        //                         "rate_id": 2341635,
        //                         "name": "前5局獨贏",
        //                         "game_priority": 28,
        //                         "status": 2,
        //                         "rate_value": "",
        //                         "rate": {
        //                             "5156419": {
        //                                 "id": 5156419,
        //                                 "limit": 0,
        //                                 "name": "中日龍 ",
        //                                 "rate": "2.82",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769332,
        //                                 "value": ""
        //                             },
        //                             "5156423": {
        //                                 "id": 5156423,
        //                                 "limit": 0,
        //                                 "name": "平局 ",
        //                                 "rate": "3.20",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769332,
        //                                 "value": ""
        //                             },
        //                             "5156424": {
        //                                 "id": 5156424,
        //                                 "limit": 0,
        //                                 "name": "讀賣巨人 ",
        //                                 "rate": "2.25",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769332,
        //                                 "value": ""
        //                             }
        //                         },
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "rate_id": 2341701,
        //                         "game_priority": 28,
        //                         "name": "前5局 - 勝平負",
        //                         "rate": {
        //                             "5156570": {
        //                                 "id": 5156570,
        //                                 "name_cn": "广岛鲤鱼 ",
        //                                 "rate": "3.60",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769280,
        //                                 "name": "广岛鲤鱼 ",
        //                                 "value": "广岛鲤鱼 "
        //                             },
        //                             "5156574": {
        //                                 "id": 5156574,
        //                                 "name_cn": "平局 ",
        //                                 "rate": "6.60",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769280,
        //                                 "name": "平局 ",
        //                                 "value": "平局 "
        //                             },
        //                             "5156576": {
        //                                 "id": 5156576,
        //                                 "name_cn": "阪神虎 ",
        //                                 "rate": "1.46",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "阪神虎 ",
        //                                 "value": "阪神虎 "
        //                             }
        //                         },
        //                         "status": 1,
        //                         "updated_at": 1694769341
        //                     }
        //                 ],
        //                 "29": [
        //                     {
        //                         "id": 1844461,
        //                         "rate_id": 2332810,
        //                         "name": "前5局讓球",
        //                         "game_priority": 29,
        //                         "status": 2,
        //                         "rate_value": "0.5",
        //                         "rate": {
        //                             "5136955": {
        //                                 "id": 5136955,
        //                                 "limit": 0,
        //                                 "name": "中日龍  +0.5",
        //                                 "rate": "1.54",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769332,
        //                                 "value": "+0.5"
        //                             },
        //                             "5136959": {
        //                                 "id": 5136959,
        //                                 "limit": 0,
        //                                 "name": "讀賣巨人  -0.5",
        //                                 "rate": "2.16",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769332,
        //                                 "value": "-0.5"
        //                             }
        //                         },
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "rate_id": 2332852,
        //                         "game_priority": 29,
        //                         "name": "前5局 - 讓球",
        //                         "rate": {
        //                             "5137061": {
        //                                 "id": 5137061,
        //                                 "name_cn": "广岛鲤鱼  -0.5",
        //                                 "rate": "1.80",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "广岛鲤鱼  -0.5",
        //                                 "value": "广岛鲤鱼  -0.5"
        //                             },
        //                             "5137065": {
        //                                 "id": 5137065,
        //                                 "name_cn": "阪神虎  +0.5",
        //                                 "rate": "1.88",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "阪神虎  +0.5",
        //                                 "value": "阪神虎  +0.5"
        //                             }
        //                         },
        //                         "status": 2,
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "rate_id": 2341930,
        //                         "game_priority": 29,
        //                         "name": "前5局 - 讓球",
        //                         "rate": {
        //                             "5157074": {
        //                                 "id": 5157074,
        //                                 "name_cn": "广岛鲤鱼  +1.5",
        //                                 "rate": "1.77",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "广岛鲤鱼  +1.5",
        //                                 "value": "广岛鲤鱼  +1.5"
        //                             },
        //                             "5157078": {
        //                                 "id": 5157078,
        //                                 "name_cn": "阪神虎  -1.5",
        //                                 "rate": "1.91",
        //                                 "status": 1,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "阪神虎  -1.5",
        //                                 "value": "阪神虎  -1.5"
        //                             }
        //                         },
        //                         "status": 1,
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "rate_id": 2341895,
        //                         "game_priority": 29,
        //                         "name": "前5局 - 讓球",
        //                         "rate": {
        //                             "5157011": {
        //                                 "id": 5157011,
        //                                 "name_cn": "广岛鲤鱼  +0.5",
        //                                 "rate": "1.86",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "广岛鲤鱼  +0.5",
        //                                 "value": "广岛鲤鱼  +0.5"
        //                             },
        //                             "5157013": {
        //                                 "id": 5157013,
        //                                 "name_cn": "阪神虎  -0.5",
        //                                 "rate": "1.82",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769341,
        //                                 "name": "阪神虎  -0.5",
        //                                 "value": "阪神虎  -0.5"
        //                             }
        //                         },
        //                         "status": 2,
        //                         "updated_at": 1694769341
        //                     },
        //                     {
        //                         "rate_id": 2341685,
        //                         "game_priority": 29,
        //                         "name": "前5局 - 讓球",
        //                         "rate": {
        //                             "5156542": {
        //                                 "id": 5156542,
        //                                 "name_cn": "养乐多燕子  -0.5",
        //                                 "rate": "2.04",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769342,
        //                                 "name": "养乐多燕子  -0.5",
        //                                 "value": "养乐多燕子  -0.5"
        //                             },
        //                             "5156543": {
        //                                 "id": 5156543,
        //                                 "name_cn": "横滨DeNA湾星  +0.5",
        //                                 "rate": "1.64",
        //                                 "status": 2,
        //                                 "risk": 0,
        //                                 "updated_at": 1694769342,
        //                                 "name": "横滨DeNA湾星  +0.5",
        //                                 "value": "横滨DeNA湾星  +0.5"
        //                             }
        //                         },
        //                         "status": 2,
        //                         "updated_at": 1694769342
        //                     }
        //                 ]
        //             }
        //         }
        //         break;
        //     default:
        //         break;
        // }

        console.log(window.sport)
        const scoresLengths = data.teams.map((team) => team.scores.length);
        console.log(scoresLengths)
        // const scoresLengths = [0, 1, 2, 3]
        
        const homeData = data.teams.find(item => item.index === 1)
        const awayData = data.teams.find(item => item.index === 2)

        var baseballData = []
        if (scoresLengths.length < 6) {
            baseballData = [0, 1, 2, 3, 4, 5, 6]
        }
        if (scoresLengths.length >= 6) {
            baseballData = [4, 5, 6, 7, 8, 9, 10]
        }
        if (scoresLengths.length > 9) {
            baseballData = [7, 8, 9, 10, 11, 12, 13]
        }

        const sport = parseInt(window.sport)
        return (
            <>
                <Swiper navigation={true}  pagination={true} modules={[Navigation, Pagination]} style={{ color: 'white' }} slidesPerView={1} id='gameTopSlider' autoHeight={true}>
                    <SwiperSlide style={{ backgroundImage: `url(${GameBg})`, backgroundSize: '100% 100%', paddingBottom: '52px'}}>
                        <MainInfoSlider className='row m-0' style={{ height: '3rem', lineHeight: '3rem' }}>
                            <div className='col-2 gametopslider'>
                                <Link to="/mobile/match">
                                    <FaChevronLeft style={backIcon} />
                                </Link>
                            </div>
                            <div className='col-8 row m-0'>
                                <div className="col-11 p-0">
                                    {
                                        data.series.name.length > 8 ?
                                        <Marquee speed={20} gradient={false}>
                                            <p className="m-0">{data.series.name}</p>
                                        </Marquee>
                                        :
                                        data.series.name
                                    }
                                </div>
                            </div>
                            <div className={this.props.isGameRefreshing === true ? 'rotateRefresh col-2' : 'col-2'} onClick={this.refreshGame}>
                                <MdAutorenew className="fs-1"/>
                            </div>
                        </MainInfoSlider>
                        <MainInfoSlider className='row' style={{ margin:'1rem 0.5rem 0 0.5rem'}}>
                            <div className='col-4'>
                                {
                                    homeData?.team?.logo !== undefined && 
                                    <img style={teamIconStyle} src={homeData.team.logo} alt={'icon'}  onError={this.handleError} />
                                }
                                {   
                                    homeData?.team?.name !== undefined ?
                                        homeData?.team?.name.length > 5 ?
                                        <Marquee speed={20} gradient={false}>
                                            <p className="fs-6 mt-2 mb-0">{homeData.team.name}[{ langText.MatchContentCard.hometag }]</p>
                                        </Marquee>
                                        :
                                        <p className="fs-6 mt-2 mb-0">{homeData.team.name}[{ langText.MatchContentCard.hometag }]</p>
                                    :
                                    null
                                }
                            </div>
                            <div className='col-4'>
                                {
                                    homeData?.total_score !== undefined && 
                                    homeData?.total_score !== '' && 
                                    awayData?.total_score !== undefined && 
                                    awayData?.total_score !== '' ?
                                    <>
                                        <span scoretag={'home_score'} className="fs-1" style={{ position: 'relative' }}>
                                            <RxTriangleUp className="upIcon" style={UpIconStyle1}/>
                                            {data.teams.filter(obj => obj.index === 1)[0].total_score}
                                        </span>
                                        <span className="fs-1"> - </span>
                                        <span scoretag={'away_score'} className="fs-1" style={{ position: 'relative' }}>
                                            {data.teams.filter(obj => obj.index === 2)[0].total_score}
                                            <RxTriangleUp className="upIcon" style={UpIconStyle2}/>
                                        </span>
                                    </>
                                    :
                                    <span>{ data.start_time }</span>
                                }
                                <div onClick={() => this.setStarState(data.match_id)} className="mt-2">
                                    { this.state.isSetStar === true ?
                                        <AiFillStar/>
                                        :
                                        <AiOutlineStar/>
                                    }
                                    <small>{langText.GameTopSlider.collect}</small>
                                </div>
                            </div>
                            <div className='col-4'>
                                {
                                    awayData?.team?.logo !== undefined && 
                                    <img style={teamIconStyle} src={awayData.team.logo} alt={'icon'}  onError={this.handleError} />
                                }

                                {   
                                    awayData?.team?.name !== undefined ?
                                        awayData?.team?.name.length > 7 ?
                                        <Marquee speed={20} gradient={false}>
                                            <p className="fs-6 mt-2 mb-0">{awayData.team.name}</p>
                                        </Marquee>
                                        :
                                        <p className="fs-6 mt-2 mb-0">{awayData.team.name}</p>
                                    :
                                    null
                                }
                            </div>
                        </MainInfoSlider>
                    </SwiperSlide>
                    {data.status === 2 && 
                        <SwiperSlide id="scoreBoard" style={{ backgroundImage: `url(${ScoreBoardBg})`, backgroundSize: '100% 100%'}}>
                            <MainInfoSlider className='row m-0' style={{ height: '3rem', lineHeight: '3rem'}}>
                                <div className='col-2 gametopslider'>
                                    <Link to="/mobile/match">
                                        <FaChevronLeft style={backIcon} />
                                    </Link>
                                </div>
                                <div className='col-8 row m-0'>
                                    <div className="col-11 p-0">
                                        {
                                            data.series.name.length > 8 ?
                                            <Marquee speed={20} gradient={false}>
                                                <p className="m-0">{data.series.name}</p>
                                            </Marquee>
                                            :
                                            data.series.name
                                        }
                                    </div>
                                </div>
                                <div className={this.props.isGameRefreshing === true ? 'rotateRefresh col-2' : 'col-2'} onClick={this.refreshGame}>
                                    <MdAutorenew className="fs-1"/>
                                </div>
                            </MainInfoSlider>
                            <div style={maintablebpard}>
                                <div style={scoreBoardSeries}>
                                    <div style={scoreBoardseriesLogoCon}>
                                        <img style={scoreBoardSeriesLogo} src={data.series.logo} alt={'icon'}  onError={this.handleError} />
                                        {   
                                            data?.series.name !== undefined ?
                                                data?.series.name.length > 5 ?
                                                <Marquee speed={20} gradient={false}>
                                                    <p>{data.series.name}</p>
                                                </Marquee>
                                                :
                                                <p>{data.series.name}</p>
                                            :
                                            null
                                        }
                                    </div>
                                    <p>{this.formatDateTime(data.start_time)}</p>
                                </div>
                                {sport === 1 ||  sport === 2 && 
                                    <table className="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>{langText.scoreBoardTitle.fulltimescore}</th>
                                                <th>{langText.scoreBoardTitle.q1}</th>
                                                <th>{langText.scoreBoardTitle.q2}</th>
                                                <th>{langText.scoreBoardTitle.q3}</th>
                                                <th>{langText.scoreBoardTitle.q4}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th style={scoreBoardLogoCon}>
                                                    <div style={scoreBoardLogo}>
                                                        { homeData?.team?.logo !== undefined && <img style={teamIconStyle} src={homeData.team.logo} alt={'icon'}  onError={this.handleError} />}
                                                        {   
                                                            homeData?.team?.name !== undefined ? homeData?.team?.name.length > 5 ?
                                                            <Marquee speed={20} gradient={false}><p className="fs-6 mt-2 mb-0">{homeData.team.name}[{ langText.scoreBoardTitle.hometag }]</p></Marquee>
                                                            :
                                                            <p className="fs-6 mt-2 mb-0">{homeData.team.name}[{ langText.scoreBoardTitle.hometag }]</p>
                                                            :
                                                            null
                                                        }
                                                    </div>
                                                </th>
                                                {[...Array(5)].map((x, y) =>
                                                    <th>
                                                        {   
                                                            homeData?.team?.scores !== undefined ?
                                                            homeData?.team?.scores.length > 5 ?
                                                            <Marquee speed={20} gradient={false}><p className="fs-6 mt-2 mb-0">{homeData.team.scores[y]}[{ langText.scoreBoardTitle.hometag }]</p></Marquee>
                                                            :
                                                            <p className="fs-6 mt-2 mb-0">{homeData.team.scores[y]}[{ langText.scoreBoardTitle.hometag }]</p>
                                                            :
                                                            null
                                                        }
                                                    </th>
                                                )}
                                            </tr>
                                            <tr>
                                                <th style={scoreBoardLogoCon}>
                                                    <div style={scoreBoardLogo}>
                                                        { awayData?.team?.logo !== undefined && <img style={teamIconStyle} src={awayData.team.logo} alt={'icon'}  onError={this.handleError} />}
                                                        {   
                                                            awayData?.team?.name !== undefined ?
                                                            awayData?.team?.name.length > 5 ?
                                                            <Marquee speed={20} gradient={false}><p className="fs-6 mt-2 mb-0">{awayData.team.name}[{ langText.scoreBoardTitle.hometag }]</p></Marquee>
                                                            :
                                                            <p className="fs-6 mt-2 mb-0">{awayData.team.name}[{ langText.scoreBoardTitle.hometag }]</p>
                                                            :
                                                            null
                                                        }
                                                    </div>

                                                </th>
                                                {[...Array(5)].map((x, y) =>
                                                <th>
                                                    {   
                                                        awayData?.team?.scores !== undefined ?
                                                        awayData?.team?.scores.length > 5 ?
                                                        <Marquee speed={20} gradient={false}><p className="fs-6 mt-2 mb-0">{awayData.team.scores[y]}[{ langText.scoreBoardTitle.hometag }]</p></Marquee>
                                                        :
                                                        <p className="fs-6 mt-2 mb-0">{awayData.team.scores[y]}[{ langText.scoreBoardTitle.hometag }]</p>
                                                        :
                                                        null
                                                    }
                                                </th>
                                                )}

                                            </tr>
                                        </tbody>
                                    </table>
                                }
                                {sport === 3 && 
                                    <table className="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>{langText.scoreBoardTitle.firstRound}</th>
                                                <th>{langText.scoreBoardTitle.gameOne}</th>
                                                <th>{langText.scoreBoardTitle.gameTwo}</th>
                                                <th>{langText.scoreBoardTitle.gameThree}</th>
                                                <th>{langText.scoreBoardTitle.gameFour}</th>
                                                <th>{langText.scoreBoardTitle.gameFive}</th>
                                                <th>{langText.scoreBoardTitle.gameSix}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th style={scoreBoardLogoCon}>
                                                    <div style={scoreBoardLogo}>
                                                        {homeData?.team?.logo !== undefined && <img style={teamIconStyle} src={homeData.team.logo} alt={'icon'}  onError={this.handleError} />}
                                                        {   
                                                            homeData?.team?.name !== undefined ?
                                                            homeData?.team?.name.length > 5 ?
                                                            <Marquee speed={20} gradient={false}><p className="fs-6 mt-2 mb-0">{homeData.team.name}[{ langText.scoreBoardTitle.hometag }]</p></Marquee>
                                                            :
                                                            <p className="fs-6 mt-2 mb-0">{homeData.team.name}[{ langText.scoreBoardTitle.hometag }]</p>
                                                            :
                                                            null
                                                        }
                                                    </div>

                                                </th>
                                                {baseballData.map((y) =>
                                                <th>
                                                    {   
                                                        homeData?.team?.scores !== undefined ?
                                                        homeData?.team?.scores.length > 5 ?
                                                        <Marquee speed={20} gradient={false}><p className="fs-6 mt-2 mb-0">{homeData.team.scores[y]}[{ langText.scoreBoardTitle.hometag }]</p></Marquee>
                                                        :
                                                        <p className="fs-6 mt-2 mb-0">{homeData.team.scores[y]}[{ langText.scoreBoardTitle.hometag }]</p>
                                                        :
                                                        null
                                                    }
                                                </th>
                                                )}
                                            </tr>
                                            <tr>
                                                <th style={scoreBoardLogoCon}>
                                                    <div style={scoreBoardLogo}>
                                                        {awayData?.team?.logo !== undefined && <img style={teamIconStyle} src={awayData.team.logo} alt={'icon'}  onError={this.handleError} />}
                                                        {   
                                                            awayData?.team?.name !== undefined ?
                                                            awayData?.team?.name.length > 5 ?
                                                            <Marquee speed={20} gradient={false}><p className="fs-6 mt-2 mb-0">{awayData.team.name}[{ langText.scoreBoardTitle.hometag }]</p></Marquee>
                                                            :
                                                            <p className="fs-6 mt-2 mb-0">{awayData.team.name}[{ langText.scoreBoardTitle.hometag }]</p>
                                                            :
                                                            null
                                                        }
                                                    </div>
                                                </th>
                                                {baseballData.map((y) =>
                                                <th>
                                                    {   
                                                        awayData?.team?.scores !== undefined ?
                                                        awayData?.team?.scores.length > 5 ?
                                                        <Marquee speed={20} gradient={false}><p className="fs-6 mt-2 mb-0">{awayData.team.scores[y]}[{ langText.scoreBoardTitle.hometag }]</p></Marquee>
                                                        :
                                                        <p className="fs-6 mt-2 mb-0">{awayData.team.scores[y]}[{ langText.scoreBoardTitle.hometag }]</p>
                                                        :
                                                        null
                                                    }
                                                </th>
                                                )}
                                            </tr>
                                        </tbody>
                                    </table>
                                }
                            </div>
                        </SwiperSlide>
                    }
                </Swiper>
            </>
        )
		
	}
	
	
}


export default GameTopSlider;