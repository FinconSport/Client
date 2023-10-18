import React from 'react';
import Marquee from "react-fast-marquee";
import $ from 'jquery';
import { langText } from "../pages/LanguageContext";
import styled from '@emotion/styled';

const ToggleBtnStyle = {
    backgroundColor: 'rgb(65,91,90)',
    color: 'white',
    borderRadius: '25px',
    position: 'absolute',
    right: '0.5rem',
    transition: 'all 0.5s ease',
    width: '4.5rem',
    top: '0.5rem',
    height: '1.5rem',
    lineHeight: '1.5rem'
}

const ToggleDiv = styled.div`
    border-top: 1px solid rgb(65, 91, 90);
    height: auto;
`

const ToggleContainer = styled.div`
    transition: 0.5s ease;
    max-height: 0;
    overflow: hidden;
`

class CommonHistory extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            isOpen: false
        }
    }

    // 收合
    toggleCard = () => {
        // this.textOverFlow(this.props.data.id)
        // if( !this.state.isOpen ) this.textOverFlow(this.props.data.id)
        this.setState({
            isOpen: !this.state.isOpen
        })
    }

    // 偵測改變結算按鈕
	componentDidUpdate(prevProps) {
        // 恢復預設
		if (prevProps.data.id !== this.props.data.id) {
            this.setState({
                isOpen: false
            })
        }
	}

    render() {
        const val = this.props.data
        return (
            <>
                <div>
                    <div>
                        <p className='mb-0 textoverflow'>{ val.bet_data[0].league_name }</p>
                    </div>
                    <div className='row m-0'>
                        <div className='col-9 p-0'>
                            <p className='mb-0 textoverflow'>{ val.bet_data[0].home_team_name }&ensp;VS&ensp;{ val.bet_data[0].away_team_name }</p>
                        </div>
                    </div>
                    <div className='row m-0'>
                        <div className='col-9 p-0'>
                            <p className='mb-0 textoverflow'>{ val.bet_data[0].market_name }&ensp;({ val.bet_data[0].market_bet_name + val.bet_data[0].market_bet_line })</p>
                        </div>
                        <div className='col-3 p-0 text-right'>
                            {val.m_order === 0 || this.state.isOpen === true ? `@${val.bet_data[0]?.bet_rate}` : null}
                        </div>
                    </div>
                    
                    {
                        val.status === 4 &&
                        <div className='row m-0'>
                            <div className='col-9 p-0'>{langText.CommonHistory.result}</div>
                            <div className='col-3 p-0 text-right' style={
                                val.bet_data[0].status === 0 || val.bet_data[0].status === 3 ?
                                {color: 'green'} : (
                                    val.bet_data[0].status === 1 || val.bet_data[0].status === 2 ?
                                    {color: 'red'} : null
                                )
                            }>{langText.CommonHistory.detailStatusArr[val.bet_data[0].status]}</div>
                        </div>
                    }
                    {
                        val.bet_data[0].home_team_score && val.bet_data[0].away_team_score &&
                        <>
                            <div className='row m-0'>
                                <div className='col-9 p-0'>
                                    {val.bet_data[0].home_team_name}
                                </div>
                                <div className='col-3 p-0 text-right'>
                                    {val.bet_data[0].home_team_score}
                                </div>
                            </div>
                            <div className='row m-0'>
                                <div className='col-9 p-0'>
                                    {val.bet_data[0].away_team_name}
                                </div>
                                <div className='col-3 p-0 text-right'>
                                    {val.bet_data[0].away_team_score}
                                </div>
                            </div>
                        </>
                    }
                    <div className='row m-0'>
                        <div className='col-9 p-0'>
                            <p className='mb-0'>{ langText.CommonHistorySlideToggle.effectiveAmount }</p>
                        </div>
                        <div className='col-3 p-0 text-right'>
                            {val.bet_data[0].active_bet}
                        </div>
                    </div>
                </div>
                <ToggleContainer style={this.state.isOpen === true ? {maxHeight: val.bet_data.length * 6.5 + 'rem'} : null}>
                    {Object.entries(val.bet_data).map(([k, v]) =>
                        k !== '0' && (
                            <ToggleDiv key={k}>
                                <div>
                                    <p className='mb-0 textoverflow'>{v.league_name}</p>
                                </div>
                                <div className='row m-0'>
                                    <div className='col-9 p-0'>
                                        <p className='mb-0 textoverflow'>{v.home_team_name}&ensp;VS&ensp;{v.away_team_name}</p>
                                    </div>
                                </div>
                                <div className='row m-0'>
                                    <div className='col-9 p-0'>
                                       <p className='mb-0 textoverflow'>{ v.market_name } &ensp;({v.market_bet_name + v.market_bet_line})</p>
                                    </div>
                                    <div className='col-3 p-0 text-right'>
                                        <span style={this.state.isOpen === false ? {display: 'none'}:null}>
                                            {'@' + v.bet_rate}
                                        </span>
                                    </div>
                                </div>
                                {
                                    val.status === 4 &&
                                    <div className='row m-0'>
                                        <div className='col-9 p-0'>{langText.CommonHistory.result}</div>
                                        <div className='col-3 p-0 text-right' style={
                                            v.status === 0 || v.status === 3 ?
                                            {color: 'green'} : (
                                                v.status === 1 || v.status === 2 ?
                                                {color: 'red'} : null
                                            )
                                        }>{langText.CommonHistory.detailStatusArr[v.status]}</div>
                                    </div>
                                }
                                {
                                    v.home_team_score && v.away_team_score &&
                                    <>
                                        <div className='row m-0'>
                                            <div className='col-9 p-0'>
                                                { v.home_team_name }
                                            </div>
                                            <div className='col-3 p-0 text-right'>
                                                { v.home_team_score }
                                            </div>
                                        </div>
                                        <div className='row m-0'>
                                            <div className='col-9 p-0'>
                                                { v.away_team_name }
                                            </div>
                                            <div className='col-3 p-0 text-right'>
                                                { v.away_team_score }
                                            </div>
                                        </div>
                                    </>
                                }
                                <div className='row m-0'>
                                    <div className='col-9 p-0'>
                                        <p className='mb-0'>{ langText.CommonHistorySlideToggle.effectiveAmount }</p>
                                    </div>
                                    <div className='col-3 p-0 text-right'>
                                        {v.active_bet}
                                    </div>
                                </div>
                            </ToggleDiv>
                        )
                    )}
                    {/* The last element */}
                    {val.m_order === 1 && (
                        <div className='row m-0' onClick={this.toggleCard} key={val.m_order}>
                            <div className='col-9' style={{ height: 0}}></div>
                            <div style={ToggleBtnStyle} className='col-3 p-0 text-center'>
                                {this.state.isOpen === false ? langText.CommonHistorySlideToggle.open + '(' + val.bet_data.length + ')' : langText.CommonHistorySlideToggle.close}
                            </div>
                        </div>
                    )}
                </ToggleContainer>
            </>
        )
    }
}

export default CommonHistory;