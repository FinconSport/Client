import React from "react";
import { langText } from "../pages/LanguageContext";
import { Link } from "react-router-dom";
import { FaChevronLeft } from 'react-icons/fa';
import 'bootstrap/dist/css/bootstrap.min.css';
import  "../css/MatchMenuNav.css";
import styled from '@emotion/styled';


const MatchMenuBar = {
    display: 'flex',
	padding: '0 0.5rem 0 0.5rem',
    height: '3rem',
    position: 'relative',
    zIndex: 0
}

const MatchSportBar = {
    width: '100%',
    background: 'rgb(65, 91, 90)',
    borderTopRightRadius: '35px',
    borderTopLeftRadius: '35px',
    overflow: 'hidden scroll',
    padding: '0.5rem',
    marginTop: '1rem',
    display: 'flex',
    overflowX: 'scroll',
    height: '5rem',
    position: 'relative',
    zIndex: 0
}



const MatchMenuItem = styled.div`
    border-radius: 15px;
    box-shadow: rgba(65, 91, 90, 0.3) 0px 0px 5px 3px;
    width: 3.5rem;
    text-align: center;
    margin-left: 0.5rem;
    height: 2.2rem;
    margin-top: 0.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: rgb(196, 211, 211);
    background: rgb(65, 91, 90);
`

const MatchSportItem = styled.div`
	background: rgb(65, 91, 90);
    color: white;
    border-radius: 15px;
    box-shadow: rgba(0, 0, 0, 0.5) 0px 0px 5px 1px;
    width: 3.2rem;
    text-align: center;
    margin-right: 0.5rem;
    margin-left: 0.5rem;
    margin-top: 0.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    position: relative;
    padding-bottom: 0.2rem;

    p {
        font-size: 0.7rem;
        white-space: nowrap;
        display: flex;
        justify-content: center;
        align-items: center;
        transform: scale(0.8);
    }
`

const backIcon = {
    background: 'rgb(65, 91, 90)',
    borderRadius: '50%',
    color: 'white',
    width: '2rem',
    height: '2rem',
    padding: '0.2rem',
    marginTop: '0.6rem',
    marginRight: '1rem'
}

const totalSpan = {
    fontSize: '0.7rem',
    lineHeight: '0.7rem'
}

const MenuOn = {
    background: 'white',
    color: 'rgb(65, 91, 90)'
}
const SportOn = {
    color: 'rgb(65, 91, 90)',
    background: 'white',
}

const MatchSportImg = {
    width: '1.8rem',
    height: '1.8rem',
    marginTop: '0.5rem'
}

const CountSpan = {
    background: 'rgb(196, 211, 211)',
    borderRadius: '50%',
    width: '1.5rem',
    height: '1.5rem',
    textAlign: 'center',
    lineHeight: '1.5rem',
    position: 'absolute',
    top: '-0.5rem',
    right: '-0.6rem',
    fontSize: '0.7rem',
    color: 'rgb(65, 91, 90)'
}


const menuArr = ['early', 'living', 'about_to_start']
const mapping = {
    'living': [langText.MatchMenuNav.living, 1],
    'early': [langText.MatchMenuNav.early, 0],
    'about_to_start': ['about_to_start', 2]
}

class MatchMenuNav extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            menu_id: window.menu,
            sport_id: window.sport,
            objTage: menuArr[window.menu],
            isMenuOpen: false,
            api_res: this.props.api_res,
            isNoData: false
        }
    }

    componentDidMount() {
        let res = this.state.api_res
        
        for (const category in res.data) {
            if (res.data[category].items) {
                for (const key in res.data[category].items) {
                    if (res.data[category].items[key].count === 0) {
                        delete res.data[category].items[key];
                    }
                }
            }
        }


        // default menu
        let rKey = null
        for (const key in res.data) {
            if (res.data.hasOwnProperty(key) && res.data[key].total !== 0) {
                rKey = key
                if(window.menu === null || res.data[menuArr[window.menu]] === undefined || res.data[menuArr[window.menu]].total === 0) {
                    this.setState({
                        menu_id: mapping[key][1],
                        objTage: rKey
                    })
                    window.menu = mapping[key][1]
                }
                break; 
            }
        }


        if( rKey === null ) {
            this.setState({
                isNoData: true
            })

            return;
        }

        // default sport
        if(window.sport === null || res.data[rKey].items[window.sport] === undefined) {
            let itemData = res.data[rKey].items
            let keys = Object.keys(itemData);
            window.sport = parseInt(keys.find(key => itemData[key].count > 0))
            this.setState({
                sport_id: window.sport
            })
        }
        this.props.callBack(window.menu, window.sport)

        this.setState({
            api_res: res
        })
	}

    // 選擇分頁
    handleMenuChange = (menu_id, objTage) => {
        window.menu = menu_id
        // 如果分頁沒有球種 則預設第一筆數據
        // 例如 今日沒有棒球 但早盤有棒球  當tab從早盤跳到今日時，預設球類為資料第一筆
        let isSelectedSportIdExi = this.props.api_res.data[menuArr[menu_id]].items[this.state.sport_id]
		if(isSelectedSportIdExi === undefined) {
            window.sport = parseInt(Object.keys(this.props.api_res.data[menuArr[menu_id]].items)[0])
            this.setState({
                sport_id: parseInt(Object.keys(this.props.api_res.data[menuArr[menu_id]].items)[0])
            })
        }

        this.setState({
            menu_id: menu_id,
            objTage: objTage
        })

        this.props.callBack(window.menu, window.sport)
    };

    // 選擇球類
    handleSportChange = (sport_id) => {
        window.sport = sport_id
        this.setState({
            sport_id: sport_id
        })
        this.props.callBack(window.menu, window.sport)
    };

    render() {
        const res = this.state.api_res;
        if( res && window.menu !== null && window.sport !== null ){
            return (
                <>
                    <div style={MatchMenuBar}>
                        <Link to="/mobile">
                            <FaChevronLeft style={backIcon} />
                        </Link>
                        {   
                            !this.state.isNoData && 
                            Object.entries(res.data).map(([key, value], i) => {
                                return (
                                    value['total'] !== 0 &&
                                    <MatchMenuItem style={ this.state.menu_id === mapping[key][1] ? MenuOn : null } key={key} onClick={() => this.handleMenuChange(mapping[key][1], key)}>
                                        <p className="mb-0">{mapping[key][0]}</p>
                                        <p className="mb-0" style={totalSpan}>{value.total}</p>
                                    </MatchMenuItem>
                                )
                            })
                        }
                    </div>
                    <div style={MatchSportBar}>
                        {
                            !this.state.isNoData && 
                            Object.entries(res.data[this.state.objTage].items).map(([k, v],i) => {
                                return(
                                    <MatchSportItem key={k} style={ this.state.sport_id == k ? SportOn : null } onClick={() => this.handleSportChange(parseInt(k))}>
                                        <img style={MatchSportImg} alt={ v.name } src={ parseInt(this.state.sport_id) === parseInt(k) ? require('../image/ball/ball-' + k + '.png') : require('../image/ball/ball-' + k + '-white.png') } />
                                        <p className="mb-0">{ v.name }</p>
                                        <div style={CountSpan}>{ v.count }</div>
                                    </MatchSportItem>
                                )
                            })
                        }
                    </div>
                </>
            ) 
        }
    }
}

export default MatchMenuNav;