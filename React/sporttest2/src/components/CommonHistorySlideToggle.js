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
        this.setState({
            isOpen: !this.state.isOpen
        })
    }

    componentDidMount() {
        this.textOverFlow(this.props.data.id)
    }


    // 偵測改變結算按鈕
	componentDidUpdate(prevProps) {
        // 恢復預設
		if (prevProps.data.id !== this.props.data.id) {
            this.setState({
                isOpen: false
            },() => {
                this.textOverFlow(this.props.data.id)
            })
        }
	}

    // 文字太長變成跑馬燈
    textOverFlow = (id) => {
        $('div[historyid="' + id + '"] .teamSpan').each(function(){
            $(this).find('.teamSpanMarquee').hide()
            $(this).find('.teamSpanSpan').show()
            // 太長有換行
            if(this.clientHeight > 22) {
                $(this).find('.teamSpanMarquee').show()
                $(this).find('.teamSpanSpan').hide()
            }
        })
    }

    render() {
        const val = this.props.data
        return (
            <>
                <div>
                    <div className='teamSpan'>
                        <div className="teamSpanMarquee">
                            <Marquee className='matchCardMarquee' speed={20} gradient={false}>
                            { val.bet_data[0].league_name }&emsp;&emsp;&emsp;
                            </Marquee>
                        </div>
                        <span className="teamSpanSpan">
                            { val.bet_data[0].league_name }
                        </span>
                    </div>

                    <div className='row m-0'>
                        <div className='col-9 p-0'>
                            <div className='teamSpan'>
                                <div className="teamSpanMarquee">
                                    <Marquee className='matchCardMarquee' speed={20} gradient={false}>
                                        { val.bet_data[0].home_team_name }&ensp;VS&ensp;{ val.bet_data[0].away_team_name }&emsp;&emsp;&emsp;
                                    </Marquee>
                                </div>
                                <span className="teamSpanSpan">
                                    { val.bet_data[0].home_team_name }&ensp;VS&ensp;{ val.bet_data[0].away_team_name }
                                </span>
                            </div>
                        </div>
                    </div>


                    <div className='row m-0'>
                        <div className='col-9 p-0'>
                            <div className='teamSpan'>
                                <div className="teamSpanMarquee">
                                    <Marquee className='matchCardMarquee' speed={20} gradient={false}>
                                        { val.bet_data[0].market_name }&ensp;({ val.bet_data[0].market_bet_name + val.bet_data[0].market_bet_line })&emsp;&emsp;&emsp;
                                    </Marquee>
                                </div>
                                <span className="teamSpanSpan">
                                    { val.bet_data[0].market_name }&ensp;({ val.bet_data[0].market_bet_name + val.bet_data[0].market_bet_line })
                                </span>
                            </div>
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
                                val.bet_data[0].result_percent === 0 || val.bet_data[0].result_percent === 3 ?
                                {color: 'green'} : (
                                    val.bet_data[0].result_percent === 1 || val.bet_data[0].result_percent === 2 ?
                                    {color: 'red'} : null
                                )
                            }>{langText.CommonHistory.detailStatusArr[val.bet_data[0].result_percent]}</div>
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

                                <div className='teamSpan'>
                                    <div className="teamSpanMarquee">
                                        <Marquee className='matchCardMarquee' speed={20} gradient={false}>
                                            {v.league_name}&emsp;&emsp;&emsp;
                                        </Marquee>
                                    </div>
                                    <span className="teamSpanSpan">
                                        {v.league_name}
                                    </span>
                                </div>
                                <div className='row m-0'>
                                    <div className='col-9 p-0'>
                                        <div className='teamSpan'>
                                            <div className="teamSpanMarquee">
                                                <Marquee className='matchCardMarquee' speed={20} gradient={false}>
                                                    {v.home_team_name}&ensp;VS&ensp;{v.away_team_name}&emsp;&emsp;&emsp;
                                                </Marquee>
                                            </div>
                                            <span className="teamSpanSpan">
                                                {v.home_team_name}&ensp;VS&ensp;{v.away_team_name}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div className='row m-0'>
                                    <div className='col-9 p-0'>
                                        <div className='teamSpan'>
                                            <div className="teamSpanMarquee">
                                                <Marquee className='matchCardMarquee' speed={20} gradient={false}>
                                                    { v.market_name } &ensp;({v.market_bet_name + v.market_bet_line})&emsp;&emsp;&emsp;
                                                </Marquee>
                                            </div>
                                            <span className="teamSpanSpan">
                                                { v.market_name } &ensp;({v.market_bet_name + v.market_bet_line})
                                            </span>
                                        </div>
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
                                            v.result_percent === 0 || v.result_percent === 3 ?
                                            {color: 'green'} : (
                                                v.result_percent === 1 || v.result_percent === 2 ?
                                                {color: 'red'} : null
                                            )
                                        }>{langText.CommonHistory.detailStatusArr[v.result_percent]}</div>
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