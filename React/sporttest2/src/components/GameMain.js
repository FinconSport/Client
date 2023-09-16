import React from "react";
import { langText } from "../pages/LanguageContext";
import { AiFillLock } from 'react-icons/ai';
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

const GameBetCardTableStyle = {
    color: 'rgb(65, 91, 90)',
    width: '100%',
    display: 'flex'
}
const GameBetCardTableAllWinStyle = {
    color: 'rgb(65, 91, 90)',
    width: '100%',
    display: 'inline-block'
}

const GameBetBody = styled.div`
    width: 100%;
    height: calc(100% - 4rem);
    overflow-x: hidden;
    overflow-y: scroll;
    padding: 0 0.5rem 3rem 0.5rem;
`
const GameBetCardBrick = styled.div`
    text-align: center;
    height: 3rem;
    line-height: 1.5rem;

    p:first-of-type {
        font-size: 0.9rem;
    }
`

const GameBetCardBrickBd = styled.div`
    text-align: center;
    height: 3.5rem;
    line-height: 1.75rem;
    p:first-of-type {
        font-size: 0.9rem;
    }
`
const GameBetCardBrickBdTitle = styled.div`
    text-align: center;
    height: 3rem;
    line-height: 3rem;
`
const GameBetCardBrickLeft = styled.div`
    height: 3rem;
    line-height: 3rem;
    text-align: left;
    padding-left: 1rem;
`
const GameBetCard = styled.div`
    background: white;
    padding: 0.5rem;
    text-align: center;
    font-weight: 600;
    margin-top: 0.5rem;
    border-radius: 10px;
`
const GameBetCardBetName = styled.div`
    background: rgb(65, 91, 90);
    color: white;
    border-radius: 15px;
    width: fit-content;
    text-align: center;
    padding: 0 1rem;
`

const GameCat = styled.div`
    width: 100%;
    display: flex;
    height: 3rem;
    line-height: 3rem;
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

const GameBetCardBrickAllWinLeft = styled.div`
    height: 3rem;
    line-height: 3rem;
    text-align: left;
    padding-left: 1rem;
    width: 30%;
    float: left;
`
const GameBetCardBrickAllWinRight = styled.div`
    width: 5rem;
    text-align: center;
    float: right;
    height: 3rem;
    line-height: 3rem;
`


var GamePriorityArr = [[5, 6, 14, 18, 22, 26, 28], [2, 4, 9, 10, 11, 15, 19, 23, 29], [1, 3, 12, 16, 20, 24, 27], [7, 8]]
var allIndexes = GamePriorityArr.flatMap(function(arr) {
    return arr;
});
GamePriorityArr = [allIndexes].concat(GamePriorityArr);

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


    render() {
        const sport = parseInt(window.sport)
        const data = this.props.data
        const homeData = data.teams.find(item => item.index === 1)
        const awayData = data.teams.find(item => item.index === 2)

        var GameCatArr = []
        if(sport === 1) GameCatArr = [langText.GameMain.all, langText.GameMain.win, langText.GameMain.hcap, langText.GameMain.size, langText.GameMain.bd]
        if(sport === 2) GameCatArr = [langText.GameMain.all, langText.GameMain.win, langText.GameMain.hcapS, langText.GameMain.size]
        if(sport === 3) GameCatArr = [langText.GameMain.all, langText.GameMain.win, langText.GameMain.hcap, langText.GameMain.size]

        return (
            <div style={{ height: '75%' }}>
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
                                        GameCatArr.map((t, i) => (
                                            <div key={i} onClick={() => this.changeCat(i)} className={this.state.activeCat === i ? 'catOn' : ''}>{t}</div>
                                        ))
                                    }
                                </GameCat>
                                <GameBetBody>
                                    {Object.entries(data.rate).map(([k, v]) => {
                                        k = parseInt(k)
                                        if (this.state.activeCat === 0 || GamePriorityArr[this.state.activeCat].indexOf(k) !== -1) {
                                            // 大小系列
                                            if (GamePriorityArr[3].indexOf(k) !== -1 && v.filter( item => item.status === 1).length > 0 ) {
                                                return (
                                                    <GameBetCard key={k}>
                                                        <GameBetCardBetName>{v[0].name}</GameBetCardBetName>
                                                        <div style={GameBetCardTableStyle}>
                                                            <div style={{ width: '30%' }}>
                                                                <GameBetCardBrickLeft>大</GameBetCardBrickLeft>
                                                                <GameBetCardBrickLeft style={{ borderTop: '2px solid rgb(65, 91, 90)' }}>
                                                                    小
                                                                </GameBetCardBrickLeft>
                                                            </div>
                                                            <div style={{ width: '70%', display: 'flex', overflowX: 'scroll' }}>
                                                                <div className="w-100">
                                                                    <GameBetCardBrick></GameBetCardBrick>
                                                                    <GameBetCardBrick style={{ borderTop: '2px solid rgb(65, 91, 90)' }}></GameBetCardBrick>
                                                                </div>
                                                                {Object.entries(v).map(([k2, v2]) => {
                                                                    if( v2.status === 1 ) {
                                                                        return (
                                                                            <div key={k2} style={{ float: 'right' }} >
                                                                                {Object.entries(v2.rate).map(([k3, v3], i) => (
                                                                                    <GameBetCardBrick
                                                                                    key={k3}
                                                                                    style={{
                                                                                        borderTop: i !== 0 ? '2px solid rgb(65, 91, 90)' : '',
                                                                                        width: '5rem'
                                                                                    }}
                                                                                    >
                                                                                    {
                                                                                        v2.status === 1 && v3.status === 1 && v3.risk === 0 && v3.rate !== undefined ?
                                                                                        <div
                                                                                            bet_match={data.match_id}
                                                                                            bet_type={v2.rate_id}
                                                                                            bet_type_item={v3.id}
                                                                                            className="w-100"
                                                                                            style={{
                                                                                                float: 'right',
                                                                                                border: '1px solid transparent',
                                                                                            }}
                                                                                            onClick={() => this.getBetData(data.match_id, v2.rate_id, v3.id, v3.rate, v2.name, v3.name, data.series.name,
                                                                                                homeData?.team?.name !== undefined ? homeData.team.name : langText.GameMain.home,
                                                                                                awayData?.team?.name !== undefined ? awayData.team.name : langText.GameMain.away,
                                                                                                homeData?.team?.logo !== undefined ? homeData?.team?.logo : 'sporta.asgame.net/uploads/default.png',
                                                                                                awayData?.team?.logo !== undefined ? awayData?.team?.logo : 'sporta.asgame.net/uploads/default.png',
                                                                                                data.start_time
                                                                                            )}
                                                                                        >
                                                                                        <p className="mb-0">{v3.name.split('  ')[1]}</p>
                                                                                        <p className="mb-0 odd">{v3.rate}</p>
                                                                                        </div>
                                                                                        :
                                                                                        <div style={{ width: '5rem', float: 'right' }}>
                                                                                        <p className="mb-0">{v3.name.split('  ')[1]}</p>
                                                                                        <AiFillLock className="mb-3" />
                                                                                        </div>
                                                                                    }
                                                                                    </GameBetCardBrick>
                                                                                ))}
                                                                            </div>
                                                                        );
                                                                    }
                                                                })}
                                                            </div>
                                                        </div>
                                                    </GameBetCard>
                                                );
                                            }
                                            // 讓球系列
                                            if (GamePriorityArr[2].indexOf(k) !== -1 && v.filter( item => item.status === 1).length > 0 ) {
                                                return (
                                                    <GameBetCard key={k}>
                                                        <GameBetCardBetName>{v[0].name}</GameBetCardBetName>
                                                        <div style={GameBetCardTableStyle}>
                                                            <div style={{ width: '30%' }}>
                                                                <GameBetCardBrickLeft className="teamname">
                                                                    {
                                                                        homeData?.team?.name !== undefined &&
                                                                        <span><span>{homeData.team.name}</span></span>  
                                                                    }
                                                                </GameBetCardBrickLeft>
                                                                <GameBetCardBrickLeft
                                                                    className="teamname" 
                                                                    style={{ borderTop: '2px solid rgb(65, 91, 90)' }}
                                                                >
                                                                    {
                                                                        awayData?.team?.name !== undefined &&
                                                                        <span><span>{awayData.team.name}</span></span>  
                                                                    }  
                                                                </GameBetCardBrickLeft>
                                                            </div>
                                                            <div style={{ width: '70%', display: 'flex', overflowX: 'scroll' }}>
                                                                <div className="w-100">
                                                                    <GameBetCardBrick></GameBetCardBrick>
                                                                    <GameBetCardBrick style={{ borderTop: '2px solid rgb(65, 91, 90)' }}></GameBetCardBrick>
                                                                </div>
                                                                {Object.entries(v).map(([k2, v2]) => (
                                                                    v2.status === 1 &&
                                                                    <div key={k2} style={{ float: 'right' }} >
                                                                        {Object.entries(v2.rate).map(([k3, v3], i) => {
                                                                        return (
                                                                            <GameBetCardBrick key={k3} style={{ borderTop: i !== 0 ? '2px solid rgb(65, 91, 90)' : '', width: '5rem' }} >
                                                                            {
                                                                                v2.status === 1 && v3.status === 1 && v3.risk === 0 && v3.rate !== undefined ?
                                                                                <div
                                                                                bet_match={data.match_id}
                                                                                bet_type={v2.rate_id}
                                                                                bet_type_item={v3.id}
                                                                                className="w-100"
                                                                                style={{
                                                                                    float: 'right',
                                                                                    border: '1px solid transparent',
                                                                                }}
                                                                                onClick={() => this.getBetData(data.match_id, v2.rate_id, v3.id, v3.rate, v2.name, v3.name, data.series.name,
                                                                                    homeData?.team?.name !== undefined ? homeData.team.name : langText.GameMain.home,
                                                                                    awayData?.team?.name !== undefined ? awayData.team.name : langText.GameMain.away,
                                                                                    homeData?.team?.logo !== undefined ? homeData?.team?.logo : 'sporta.asgame.net/uploads/default.png',
                                                                                    awayData?.team?.logo !== undefined ? awayData?.team?.logo : 'sporta.asgame.net/uploads/default.png',
                                                                                    data.start_time
                                                                                )}
                                                                                >
                                                                                <p className="mb-0">{v3.name.split('  ')[1]}</p>
                                                                                <p className="mb-0 odd">{v3.rate}</p>
                                                                                </div>
                                                                                :
                                                                                <div style={{ width: '5rem', float: 'right' }}>
                                                                                <p className="mb-0">{v3.name.split('  ')[1]}</p>
                                                                                <AiFillLock className="mb-3" />
                                                                                </div>
                                                                            }
                                                                            </GameBetCardBrick>
                                                                        );
                                                                        })}
                                                                    </div>
                                                                ))}

                                                            </div>
                                                        </div>
                                                    </GameBetCard>
                                                );
                                            }
                                            // 獨贏系列
                                            if (GamePriorityArr[1].indexOf(k) !== -1 && v.filter( item => item.status === 1).length > 0 ) {
                                                return (
                                                    <GameBetCard key={k}>
                                                        <GameBetCardBetName>{v[0].name}</GameBetCardBetName>
                                                        {
                                                            Object.entries(v[0].rate).map(([k2, v2], i) => {
                                                                const statusLock = 0; 
                                                                return (
                                                                    <div style={{ ...GameBetCardTableAllWinStyle, borderTop: i !== 0 ? '2px solid rgb(65, 91, 90)' : 'none' }} key={k2}>
                                                                        <GameBetCardBrickAllWinLeft className="teamname">
                                                                            <span><span>{v2.name}</span></span>
                                                                        </GameBetCardBrickAllWinLeft>
                                                                        {
                                                                            v[0].status === 1 && v2.status === 1 && v2.risk === 0 && v2.rate !== undefined ?
                                                                                <GameBetCardBrickAllWinRight
                                                                                    onClick={() => this.getBetData(data.match_id, v[0].rate_id, v2.id, v2.rate, v[0].name, v2.name, data.series.name, homeData?.team?.name !== undefined ? homeData.team.name : langText.GameMain.home, awayData?.team?.name !== undefined ? awayData.team.name : langText.GameMain.away, 
                                                                                    homeData?.team?.logo !== undefined ? homeData?.team?.logo : 'sporta.asgame.net/uploads/default.png', 
                                                                                    awayData?.team?.logo !== undefined ? awayData?.team?.logo : 'sporta.asgame.net/uploads/default.png',
                                                                                    data.start_time)}
                                                                                >
                                                                                    <div bet_match={data.match_id} bet_type={v[0].rate_id} bet_type_item={v2.id} className="w-100 h-100">
                                                                                        <p className="mb-0 odd">{v2.rate}</p>
                                                                                    </div>
                                                                                </GameBetCardBrickAllWinRight>
                                                                                :
                                                                                <GameBetCardBrickAllWinRight>
                                                                                    <AiFillLock  className="mb-3" />
                                                                                </GameBetCardBrickAllWinRight>
                                                                        }
                                                                    </div>
                                                                )
                                                            })
                                                        }
                                                    </GameBetCard>
                                                );
                                            }
                                            // 波膽系列
                                            if (GamePriorityArr[4].indexOf(k) !== -1 && v.filter( item => item.status === 1).length > 0 ) {
                                                return (
                                                    <GameBetCard key={k}>
                                                        <GameBetCardBetName>{v[0].name}</GameBetCardBetName>
                                                        {
                                                            Object.entries(v).map(([k2, v2], i2) => {
                                                                return(
                                                                    <div className='row m-0' style={{ color: 'rgb(65, 91, 90)' }} key={k2}>
                                                                        {
                                                                            Array.from({ length: 3 }).map((_, i) => (
                                                                                <React.Fragment key={i}>
                                                                                    <div className="col-4 p-0">
                                                                                        <GameBetCardBrickBdTitle>
                                                                                            { i === 0 && langText.GameMain.home }
                                                                                            { i === 1 && langText.GameMain.tie }
                                                                                            { i === 2 && langText.GameMain.away }
                                                                                        </GameBetCardBrickBdTitle>
                                                                                        {v2.rate[i].map(v3 => (
                                                                                            v[0].status === 1 && v3.status === 1 && v3.risk === 0 && v3.rate !== undefined ? (
                                                                                                <GameBetCardBrickBd key={v3.id} onClick={() => this.getBetData(data.match_id, v[0].rate_id, v3.id, v3.rate, v[0].name, v3.name, data.series.name, homeData?.team?.name !== undefined ? homeData.team.name : langText.GameMain.home, awayData?.team?.name !== undefined ? awayData.team.name : langText.GameMain.away, 
                                                                                                    homeData?.team?.logo !== undefined ? homeData?.team?.logo : 'sporta.asgame.net/uploads/default.png', 
                                                                                                    awayData?.team?.logo !== undefined ? awayData?.team?.logo : 'sporta.asgame.net/uploads/default.png',
                                                                                                    data.start_time)}>
                                                                                                    <div bet_match={data.match_id} bet_type={v[0].rate_id} bet_type_item={v3.id} className="w-100 h-100">
                                                                                                        <p className="mb-0">{v3.name}</p>
                                                                                                        <p className="mb-0 odd">{v3.rate}</p>
                                                                                                    </div>
                                                                                                </GameBetCardBrickBd>
                                                                                            ) : (
                                                                                                <GameBetCardBrickBd key={v3.id}>
                                                                                                    <p className="mb-0">{v3.name}</p>
                                                                                                    <AiFillLock className="mb-3" />
                                                                                                </GameBetCardBrickBd>
                                                                                            )
                                                                                        ))}
                                                                                    </div>
                                                                                </React.Fragment>
                                                                            ))
                                                                        }
                                                                    </div>
                                                                )
                                                            })
                                                        }
                                                    </GameBetCard>
                                                );
                                            }
                                        }
                                        return null;
                                    })}
                                </GameBetBody>
                            </>
                            :
                            '分析'
                    }
                </div>
            </div>
        )

    }


}


export default GameMain;