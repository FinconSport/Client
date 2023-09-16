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
    width: '4.5rem'
}

const ToggleDiv = styled.div`
    border-top: 1px solid rgb(65, 91, 90);
    height: 4.5rem;
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
        this.textOverFlow(this.props.data.id, 1)
    }

    // 文字太長變成跑馬燈
    textOverFlow = (id, type = 0) => {
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

    // 初始化
    componentDidMount() {
        this.textOverFlow(this.props.data.id)
        
    } 

    // 偵測改變結算按鈕
	componentDidUpdate(prevProps) {
        // 恢復預設
		if (prevProps.data.id !== this.props.data.id) {
            this.textOverFlow(this.props.data.match_id)
        }
	}

    render() {
        const val = this.props.data
        return (
            <>
                <div>
                    <div className='teamSpan'>
                        <div className="teamSpanMarquee">
                            <Marquee className='matchCardMarquee mt-1' speed={20} gradient={false}>
                                { val.bet_data[0].series_name }&emsp;&emsp;&emsp;
                            </Marquee>
                        </div>
                        <span className="teamSpanSpan">
                            { val.bet_data[0].series_name }
                        </span>
                    </div>
                    <div className='row m-0'>
                        <div className='col-9 p-0 teamSpan'>
                            <div className="teamSpanMarquee">
                                <Marquee className='matchCardMarquee mt-1' speed={20} gradient={false}>
                                    { val.bet_data[0].home_team_name }&ensp;VS&ensp;{ val.bet_data[0].away_team_name }&emsp;&emsp;&emsp;
                                </Marquee>
                            </div>
                            <span className="teamSpanSpan">
                                { val.bet_data[0].home_team_name }&ensp;VS&ensp;{ val.bet_data[0].away_team_name }
                            </span>
                        </div>
                    </div>
                    <div className='row m-0'>
                        <div className='col-9 p-0 teamSpan'>
                            <div className="teamSpanMarquee">
                                <Marquee className='matchCardMarquee mt-1' speed={20} gradient={false}>
                                    { val.bet_data[0].type_item_name }&ensp;({ val.bet_data[0].type_name })&emsp;&emsp;&emsp;
                                </Marquee>
                            </div>
                            <span className="teamSpanSpan">
                                { val.bet_data[0].type_item_name }&ensp;({ val.bet_data[0].type_name })
                            </span>
                        </div>
                        <div className='col-3 p-0 text-right'>
                            {val.m_order === 0 || this.state.isOpen === true ? `@${val.bet_data[0]?.bet_rate}` : null}
                        </div>

                    </div>
                    {/* 比分 */}
                    {
                        val.bet_data[0]?.home_team_score && val.bet_data[0]?.away_team_name && (
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
                        )
                    }
                    {/* 比分 */}
                </div>
                <ToggleContainer style={this.state.isOpen === true ? {maxHeight: val.bet_data.length * 4.5 + 'rem'} : null}>
                    {Object.entries(val.bet_data).map(([k, v]) =>
                        k !== '0' && (
                            <ToggleDiv key={k}>
                                <div className='teamSpan'>
                                    <div className="teamSpanMarquee">
                                        <Marquee className='matchCardMarquee mt-1' speed={20} gradient={false}>
                                            {v.series_name}&emsp;&emsp;&emsp;
                                        </Marquee>
                                    </div>
                                    <span className="teamSpanSpan">
                                        {v.series_name}
                                    </span>
                                </div>
                                <div className='row m-0'>
                                    <div className='col-9 p-0 teamSpan'>
                                        <div className="teamSpanMarquee">
                                            <Marquee className='matchCardMarquee mt-1' speed={20} gradient={false}>
                                                {v.home_team_name}&ensp;VS&ensp;{v.away_team_name}&emsp;&emsp;&emsp;
                                            </Marquee>
                                        </div>
                                        <span className="teamSpanSpan">
                                            {v.home_team_name}&ensp;VS&ensp;{v.away_team_name}
                                        </span>
                                    </div>
                                </div>
                                <div className='row m-0'>
                                    <div className='col-9 p-0 teamSpan'>
                                        <div className="teamSpanMarquee">
                                            <Marquee className='matchCardMarquee mt-1' speed={20} gradient={false}>
                                                {v.type_item_name}&ensp;({v.type_name})&emsp;&emsp;&emsp;
                                            </Marquee>
                                        </div>
                                        <span className="teamSpanSpan">
                                            {v.type_item_name}&ensp;({v.type_name})
                                        </span>
                                    </div>
                                    <div className='col-3 p-0 text-right'>
                                        <span style={this.state.isOpen === false ? {display: 'none'}:null}>
                                            {'@' + v.bet_rate}
                                        </span>
                                    </div>
                                </div>
                                {/* 比分 */}
                                {
                                    v?.home_team_score && v?.away_team_score && (
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
                                    )
                                }
                                {/* 比分 */}
                            </ToggleDiv>
                        )
                    )}
                    {/* The last element */}
                    {val.m_order === 1 && (
                        <div className='row m-0' onClick={this.toggleCard} key={val.m_order} style={{height: '2rem'}}>
                            <div className='col-9'></div>
                            <div style={{ ...ToggleBtnStyle, ...(this.state.isOpen === true ? { bottom: '0.5rem' } : {bottom: '1.5rem'}) }} className='col-3 p-0 text-center'>
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