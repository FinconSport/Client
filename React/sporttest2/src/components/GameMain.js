import React from "react";
import { langText } from "../pages/LanguageContext";
import { AiFillLock } from 'react-icons/ai';
import Cookies from 'js-cookie';
import styled from '@emotion/styled';
import 'bootstrap/dist/css/bootstrap.css';
import 'swiper/css';
import "swiper/css/pagination";
import '../css/GameTopSlider.css'


const Tab = styled.div`
    height: 2rem;
    line-height: 2rem;
    text-align: center;
    font-weight: 600;
    background: rgb(184, 214, 212);
`

const TabLeft = styled(Tab)`
    border-radius: 0 10px 0 0;
`

const TabRight = styled(Tab)`
    border-radius: 10px 0 0 0;
`

const TabOnStyle = {
    background: 'rgb(65, 91, 90)',
    color: 'white',
}

const GameMainBodyStyle = {
    background: 'rgb(65, 91, 90)',
    padding: '0.5rem',
    height: 'calc(100% - 2rem)',
    color: 'white',
}

const GameBetBody = styled.div`
    width: 100%;
    height: calc(100% - 4rem);
    overflow-x: hidden;
    overflow-y: scroll;
    padding: 0 0.5rem 3rem 0.5rem;
`

const GameBetCard = styled.div`
    background: white;
    padding: 0.5rem;
    text-align: center;
    font-weight: 600;
    margin-top: 0.5rem;
    border-radius: 10px;
    color: rgb(65, 91, 90);
`
const GameBetCardBetName = styled.div`
    background: rgb(65, 91, 90);
    color: white;
    border-radius: 15px;
    width: fit-content;
    text-align: center;
    padding: 0 1rem;
    margin-bottom: 0.25rem;
`

const GameCat = styled.div`
    width: 100%;
    display: flex;
    height: 2.5rem;
    line-height: 2.5rem;
    margin-bottom:1rem;
    div {
        width: inherit;
        color: white;
        border: 1px solid rgb(184, 214, 212);
        text-align: center;
        font-weight: 600;
    }
    .catOn{
        background: rgb(184, 214, 212);
        color: rgb(65, 91, 90);
    }
`
const BetBrick = {
    height: '2.5rem',
    lineHeight: '2.5rem'
}

class GameMain extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            activeTab: 0,
            activeCat: 0,
        };
    }

    changeTab = (index) => {
        this.setState({
            activeTab: index
        })
    }

    changeCat = (index) => {
        this.setState({
            activeCat: index
        })
    }

    getBetData = (market_id, market_bet_id, price, market_name, home_team_name, away_team_name, bet_item_name, status) => {
        if( status !== 1 ) return;

        this.props.getBetDataCallBack(
            {
                sport_id: window.sport, 
                fixture_id: this.props.data.list.fixture_id, 
                market_id: market_id, 
                market_bet_id: market_bet_id, 
                bet_rate: price, 
                market_name: market_name, 
                series_name: this.props.data.list.league_name, 
                home_team_name: home_team_name, 
                away_team_name: away_team_name, 
                bet_item_name: bet_item_name, 
            }
        )
    }

    

    render() {
        const sport = parseInt(Cookies.get('sport', { path: '/' }))
        const data = this.props.data.list
        const GamePriorityArr = [[langText.MatchContent.allWinPriority], [langText.MatchContent.hcapPriority], [langText.MatchContent.sizePriority], [langText.MatchContent.oddEvenPriority]]

        return (
            <div style={{ height: '72%' }}>
                <div className="row m-0">
                    <TabLeft className="col-6" onClick={() => this.changeTab(0)} style={this.state.activeTab === 0 ? TabOnStyle : {}}>{langText.GameMain.bet}</TabLeft>
                    <TabRight className="col-6" onClick={() => this.changeTab(1)} style={this.state.activeTab === 1 ? TabOnStyle : {}}>{langText.GameMain.analyze}</TabRight>
                </div>
                <div style={GameMainBodyStyle}>
                    {
                        this.state.activeTab === 0 ?
                            <>
                                <GameCat>
                                    {
                                        langText.GameMain.GameCatArr[sport].map((t, i) => (
                                            <div key={i} onClick={() => this.changeCat(i)} className={this.state.activeCat === i ? 'catOn' : ''}>{t}</div>
                                        ))
                                    }
                                </GameCat>
                                <GameBetBody>
                                    {data.market.map((v, k) => {
                                        let t = v.priority
                                        if ( Object.keys(v.market_bet).length > 0 && (this.state.activeCat === 0 || GamePriorityArr[this.state.activeCat -1 ][0].indexOf(t) !== -1)) {
                                            return (
                                                <GameBetCard key={k}>
                                                    <GameBetCardBetName>{v.market_name}</GameBetCardBetName>
                                                    {
                                                        Object.keys(v.market_bet).sort((a, b) => parseFloat(a) - parseFloat(b)).map((key, v1) => (
                                                            <div key={key}>
                                                                {v.market_bet[key].map((v2, k2) => (
                                                                    <div key={k2}>
                                                                        <div className="row m-0" style={{ ...BetBrick, ...(k2 === 0 && v1 !== 0 ? { borderTop: '2px solid rgb(65, 91, 90)' } : null) }} key={k2}>
                                                                            <div className="col-5 row m-0 p-0">
                                                                                <div className="col-3 p-0" style={{ textAlign: 'right' }}>{v2.market_bet_name}</div>
                                                                                {
                                                                                    data.status === 2 && sport === 6046 && langText.MatchContent.hcapPriority.indexOf(t) !== -1 ?
                                                                                    <div className="col-9 p-0" style={{ textAlign: 'right' }}>{v2.line} (1-2)</div>
                                                                                    :
                                                                                    <div className="col-5 p-0" style={{ textAlign: 'right' }}>{v2.line}</div>
                                                                                }
                                                                            </div>
                                                                            <div className="col-7 row m-0 p-0">
                                                                                <div className="col-11" style={{ textAlign: 'right' }}>
                                                                                    <div market_bet_id={v2.market_bet_id}
                                                                                        onClick={() => this.getBetData(
                                                                                            v.market_id,
                                                                                            v2.market_bet_id,
                                                                                            v2.price,
                                                                                            v.market_name,
                                                                                            data.home_team_name,
                                                                                            data.away_team_name,
                                                                                            langText.MatchContent.allWinPriority.indexOf(t) !== -1 || langText.MatchContent.hcapPriority.indexOf(t) !== -1 ?
                                                                                                (v2.market_bet_name_en === "1" ? data.home_team_name : (v2.market_bet_name_en === '2' ? data.away_team_name : v2.market_bet_name)) + ' ' + v2.line
                                                                                                :
                                                                                                (v2.market_bet_name + ' ' + v2.line),
                                                                                            v2.status
                                                                                        )} style={{ width: '3rem', float: 'right', padding: '0 0.25rem' }} >
                                                                                        {v2.price}
                                                                                    </div>
                                                                                </div>
                                                                                <div className="col-1 p-0" style={{ textAlign: 'left' }}>
                                                                                    <AiFillLock style={v2.status === 1 ? { display: 'none' } : { display: 'initial' }} />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                ))}
                                                            </div>
                                                        ))
                                                    }
                                                </GameBetCard>
                                            );
                                        }
                                        return null;
                                    })}
                                </GameBetBody>
                            </>
                            :
                            langText.GameMain.analyze
                    }
                </div>
            </div>
        )

    }


}


export default GameMain;