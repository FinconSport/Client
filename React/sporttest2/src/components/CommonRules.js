import React from 'react';
import { langText } from "../pages/LanguageContext";
// import GetIni from './AjaxFunction'
// import styled from '@emotion/styled';
// import InfiniteScroll from 'react-infinite-scroll-component';
// import pako from 'pako'
import { AiFillCloseCircle } from "react-icons/ai";
import { TbArrowBigUpFilled } from 'react-icons/tb';

const ToTopStyle = {
	right: '0.5rem',
    bottom: '7rem',
	zIndex: 1,
	position: 'absolute',
	background: '#c79e42',
	color: 'white',
	borderRadius: '50%',
	fontSize: '2.5rem',
	padding: '0.3rem',
    opacity: 0.7
}

const RulesWrapper = {
    fontWeight: 600,
    backgroundColor: 'rgb(228 240 239 / 90%)',
    position: 'fixed',
    width: '100%',
    height: '100%',
    zIndex: 2,
    transition: 'all .5s ease 0s',
    MozTransition: 'all .5s ease 0s',
    WebkitTransition: 'all .5s ease 0s',
    OTransition: 'all .5s ease 0s',
    WebkitOverflowScrolling: 'touch',
    bottom: 'calc(-100%)'
}

const RulesWrapperOn = {
    bottom: '0'
};

const RulesBetWrapper = {
    width: '100%',
    height: '92%',
    bottom: 0,
    backgroundColor: 'rgb(65, 91, 90)',
    borderTopRightRadius: '35px',
    borderTopLeftRadius: '35px',
    position: 'absolute',
    padding: '1rem 1rem 0 1rem'
}
const PageContainer = {
    overflow: 'hidden',
    borderRadius: '15px',
    width: '100%',
    height: '100%',
    fontSize: '0.9rem',
    paddingBottom: '1rem,'
}

const RulesPageClose = {
    position: 'absolute',
    right: '1rem',
    top: '1rem',
    fontSize: '2rem'
}

const TabMenuWrapperCon = {
    padding: '0.5rem',
    columnGap: '0.5rem',
    flexWrap: 'nowrap',
    display: 'flex',
    overflowX: 'scroll',
}

const TabMenuBtn = {
    background: '#445a5a',
    color: '#c4d3d3',
    height: 'auto',
    lineHeight: '1rem',
    padding: '0.7rem',
    textAlign: 'center',
    fontWeight: '600',
    fontSize: '1rem',
    borderRadius: '15px',
    boxShadow: '#00000080 0 0 9px 0px',
    border:'none',
    minWidth: '100px',
}

const TabMenuBtnActive = {
    background: '#445a5a',
    color: '#c19e4f',
    height: 'auto',
    lineHeight: '1rem',
    padding: '0.7rem',
    textAlign: 'center',
    fontWeight: '600',
    fontSize: '1rem',
    borderRadius: '15px',
    boxShadow: '#00000080 0 0 9px 0px',
    border:'none',
    minWidth: '100px',
}

const HideTabContent = {
    display: 'none',
}

const ShowTabContent = {
    display: 'block',
}

const TabMainWrapperCon = {
    borderRadius: '10px',
    marginTop: '0.5rem',
    overflow: 'auto',
    height: 'calc(100% - 6rem)',
}

const TabWrapperTitle = {
    padding: '1rem',
    backgroundColor: '#c4d4d4',
    color: '#415b5a',
}

const TabWrapperContent = {
    padding: '1rem',
    backgroundColor: '#ffffff',
    color: '#415b5a',
    borderBottomRightRadius: '15px',
    borderBottomLeftRadius: '15px',
    lineHeight: '2rem',
    textAlign: 'left',
}

const LogsPageTitle = {
    position: 'absolute',
    left: '1rem',
    top: '1rem',
    fontSize: '1.2rem'
}

const h1 = {
    margin: '0px',
    textAlign: 'center',
    fontSize: '1.5rem',
    fontWeight: '600',
}

const h2 = {
    fontSize: '1.3rem',
    fontWeight: '600',
}

const h3 = {
    fontSize: '1.1rem',
    fontWeight: '600',
}

const h4 = {
    fontSize: '1.01rem',
    fontWeight: '600',
}


const numBullets = {
    listStyleType: 'number',
    paddingLeft: '15px',
}

const noBullets = {
    listStyleType: 'none',
    padding: '0px',
}

const alphaBullets = {
    listStyleType: 'lower-alpha',
    paddingLeft: '15px',
}

const upperAlphaBullets = {
    listStyleType: 'upper-alpha',
    paddingLeft: '15px',
}

const romanBullets = {
    listStyleType: 'lower-roman',
    padding: '0px',
}


class CommonRules extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            data:[],
            searchStatus: 0,
            activeTab: 1
        }
    }

    // 滑到最上面
	scrollToTop = () => {
		document.getElementById('TabMainWrapperCon').scrollTo({top: 0, behavior: 'smooth'});
	}

    // 關閉頁面
    closeGameRule = () => {
        this.props.callBack()
    }

    handleTabChange = ( sportCode ) => {
        this.scrollToTop()
        this.setState({
            activeTab: sportCode
        })
    }

    render() {

        return (
            <div style={{ ...RulesWrapper, ...(this.props.isGameRuleOpen === true && RulesWrapperOn) }}>
                <div style={LogsPageTitle}>{langText.CommonRulesTitles.gameRules}</div>
                <AiFillCloseCircle style={RulesPageClose} onClick={this.closeGameRule} />
                <div style={RulesBetWrapper}>
                    <div style={TabMenuWrapperCon}>
                        {Array.from({ length: 5 }, (_, index) => (
                            <button
                                key={index + 1}
                                onClick={() => this.handleTabChange(index + 1)}
                                style={this.state.activeTab === index + 1 ? TabMenuBtnActive : TabMenuBtn}
                            >
                                {langText.CommonRulesTitles.sportName[index + 1]}
                            </button>
                        ))}
                    </div>
                    <div id='GameRulesMain' style={PageContainer}>
                        <div id='TabMainWrapperCon' style={TabMainWrapperCon}>

                            {/* ---soccor */}
                            <div style={ this.state.activeTab === 1 ? ShowTabContent : HideTabContent }>
                                <div style={TabWrapperTitle}>
                                    <h1 style={h1}>{langText.CommonRulesTitles.sportName[1]}</h1>
                                </div>
                                <div style={TabWrapperContent}>
                                    <h2 style={h2}>{langText.CommonRulesTitles.generalrule}</h2>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_1}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_2}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_3}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_4}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_5}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_6}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_7}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_8}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_9}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_10}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_11}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_12}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_13}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_14}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_15}</li>
                                        <li>{langText.CommonRulesGeneralSoccor.grSoccor_16}</li>
                                    </ul>
                                    
                                    <h3 style={h2}>{langText.CommonRulesTitles.handicap}</h3>
                                    <h4 style={h3}>{langText.CommonRulesTitles.generalrule}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.handicap_1}</li>
                                        <li>{langText.CommonRulesSoccor.handicap_2}</li>
                                        <li>{langText.CommonRulesSoccor.handicap_3}</li>
                                        <li>{langText.CommonRulesSoccor.handicap_4}</li>
                                        <li>{langText.CommonRulesSoccor.handicap_5}</li>
                                        <ul style={alphaBullets}>
                                            <li>{langText.CommonRulesSoccor.handicap_5_1}</li>
                                            <li>{langText.CommonRulesSoccor.handicap_5_2}</li>
                                            <li>{langText.CommonRulesSoccor.handicap_5_3}</li>
                                        </ul>
                                        <li>{langText.CommonRulesSoccor.handicap_6}</li>
                                        <li>{langText.CommonRulesSoccor.handicap_7}</li>
                                        <li>{langText.CommonRulesSoccor.handicap_8}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.fulltimehandicapresult}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.fulltimehandicapresult_1}</li>
                                        <li>{langText.CommonRulesSoccor.fulltimehandicapresult_2}</li>
                                        <li>{langText.CommonRulesSoccor.fulltimehandicapresult_2}</li>
                                        <li>{langText.CommonRulesSoccor.fulltimehandicapresult_3}</li>
                                        <ul style={numBullets}>
                                            <li>{langText.CommonRulesSoccor.fulltimehandicapresult_4_1}</li>
                                            <li>{langText.CommonRulesSoccor.fulltimehandicapresult_4_2}</li>
                                            <li>{langText.CommonRulesSoccor.fulltimehandicapresult_4_3}</li>
                                            <li>{langText.CommonRulesSoccor.fulltimehandicapresult_4_4}</li>
                                            <li>{langText.CommonRulesSoccor.fulltimehandicapresult_4_5}</li>
                                        </ul>
                                        <li>{langText.CommonRulesSoccor.fulltimehandicapresult_5}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.handicapfirstHalf}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.handicapfirstHalf_1}</li>
                                        <li>{langText.CommonRulesSoccor.handicapfirstHalf_2}</li>
                                        <li>{langText.CommonRulesSoccor.handicapfirstHalf_3}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.letsroll}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.letsroll_1}</li>
                                        <li>{langText.CommonRulesSoccor.letsroll_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.othandicap}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.othandicap_1}</li>
                                        <li>{langText.CommonRulesSoccor.othandicap_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.otletfirstHalf}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.otletfirstHalf_1}</li>
                                        <li>{langText.CommonRulesSoccor.otletfirstHalf_2}</li>
                                        <li>{langText.CommonRulesSoccor.otletfirstHalf_3}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.bettingsizes}</h3>
                                    <h4 style={h4}>{langText.CommonRulesTitles.generalrule}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.bettingSizes_1}</li>
                                        <li>{langText.CommonRulesSoccor.bettingSizes_2}</li>
                                        <li>{langText.CommonRulesSoccor.bettingSizes_3}</li>
                                        <li>{langText.CommonRulesSoccor.bettingSizes_4}</li>
                                        <ul style={alphaBullets}>
                                            <li>{langText.CommonRulesSoccor.bettingSizes_4_1}</li>
                                            <li>{langText.CommonRulesSoccor.bettingSizes_4_2}</li>
                                            <li>{langText.CommonRulesSoccor.bettingSizes_4_3}</li>
                                        </ul>
                                        <li>{langText.CommonRulesSoccor.bettingSizes_5}</li>
                                        <ul style={alphaBullets}>
                                            <li>{langText.CommonRulesSoccor.bettingSizes_5_1}</li>
                                            <ul style={romanBullets}>
                                                <li>{langText.CommonRulesSoccor.bettingSizes_5_1_1}</li>
                                                <li>{langText.CommonRulesSoccor.bettingSizes_5_1_2}</li>
                                            </ul>   
                                            <li>{langText.CommonRulesSoccor.bettingSizes_5_2}</li> 
                                            <ul style={romanBullets}>
                                                <li>{langText.CommonRulesSoccor.bettingSizes_5_2_1}</li>
                                                <li>{langText.CommonRulesSoccor.bettingSizes_5_2_2}</li>
                                            </ul> 
                                            <li>{langText.CommonRulesSoccor.bettingSizes_5_3}</li>
                                            <ul style={romanBullets}>
                                                <li>{langText.CommonRulesSoccor.bettingSizes_5_3_1}</li>
                                                <li>{langText.CommonRulesSoccor.bettingSizes_5_3_2}</li>
                                            </ul>  
                                        </ul>
                                    </ul>
                                    <h4 style={h4}>{langText.CommonRulesTitles.goallargeSmall}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.goallargeSmall_1}</li>   
                                        <li>{langText.CommonRulesSoccor.goallargeSmall_2}</li> 
                                    </ul>
                                    <h4 style={h4}>{langText.CommonRulesTitles.goaloverUnderfirstHalf}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.goaloverUnderfirstHalf_1}</li>   
                                        <li>{langText.CommonRulesSoccor.goaloverUnderfirstHalf_2}</li>
                                        <li>{langText.CommonRulesSoccor.goaloverUnderfirstHalf_3}</li> 
                                    </ul>
                                    <h4 style={h4}>{langText.CommonRulesTitles.rollingballoverUnder}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor. rollingballoverUnder_1}</li>   
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.otgoaloverUnder}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.otgoaloverUnder_1}</li>   
                                        <li>{langText.CommonRulesSoccor.otgoaloverUnder_2}</li>
                                        <li>{langText.CommonRulesSoccor.otgoaloverUnder_3}</li> 
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.otgoalsoverUnderfirstHalf}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.otgoalsoverUnderfirstHalf_1}</li>   
                                        <li>{langText.CommonRulesSoccor.otgoalsoverUnderfirstHalf_2}</li>
                                        <li>{langText.CommonRulesSoccor.otgoalsoverUnderfirstHalf_3}</li> 
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.teamgoalsoverUnder}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.teamgoalsoverUnder_1}</li>   
                                        <li>{langText.CommonRulesSoccor.teamgoalsoverUnder_2}</li>
                                        <li>{langText.CommonRulesSoccor.teamgoalsoverUnder_3}</li> 
                                        <li>{langText.CommonRulesSoccor.teamgoalsoverUnder_4}</li> 
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.moneyline}</h3>
                                    <h4 style={h4}>{langText.CommonRulesTitles.generalrule}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.moneyline_1}</li>   
                                        <li>{langText.CommonRulesSoccor.moneyline_2}</li>
                                    </ul>
                                    <h4 style={h4}>{langText.CommonRulesTitles.winalone}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.winalone_1}</li>   
                                    </ul>
                                    <h4 style={h4}>{langText.CommonRulesTitles.winalonefirstHalf}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.winalonefirstHalf_1}</li>   
                                    </ul>
                                    <h4 style={h4}>{langText.CommonRulesTitles.scoregoal}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.scoregoal_1}</li>   
                                        <li>{langText.CommonRulesSoccor.scoregoal_2}</li>  
                                        <li>{langText.CommonRulesSoccor.scoregoal_3}</li>  
                                        <ul style={numBullets}>
                                            <li>{langText.CommonRulesSoccor.scoregoal_3_1}</li>  
                                            <li>{langText.CommonRulesSoccor.scoregoal_3_2}</li>  
                                            <li>{langText.CommonRulesSoccor.scoregoal_3_3}</li>   
                                            <ul style={upperAlphaBullets}>
                                                <li><h4 style={h4}>{langText.CommonRulesTitles.example1}</h4></li>  
                                                <ul style={romanBullets}>
                                                    <li>{langText.CommonRulesSoccor.scoregoal_3_1_1}</li>
                                                    <li>{langText.CommonRulesSoccor.scoregoal_3_1_2}</li>
                                                    <li>{langText.CommonRulesSoccor.scoregoal_3_1_3}</li>
                                                </ul>
                                                <li><h4 style={h4}>{langText.CommonRulesTitles.example2}</h4></li>  
                                                <ul style={romanBullets}>
                                                    <li>{langText.CommonRulesSoccor.scoregoal_3_2_1}</li>
                                                    <li>{langText.CommonRulesSoccor.scoregoal_3_2_2}</li>
                                                    <li>{langText.CommonRulesSoccor.scoregoal_3_2_3}</li>
                                                </ul>
                                            </ul>
                                        </ul>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.otwinalone}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.otwinalone_1}</li>
                                        <li>{langText.CommonRulesSoccor.otwinalone_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.otwinAlonefirstHalf}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.otwinAlonefirstHalf_1}</li>
                                        <li>{langText.CommonRulesSoccor.otwinAlonefirstHalf_2}</li>
                                        <li>{langText.CommonRulesSoccor.otwinAlonefirstHalf_3}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.crts}</h3>
                                    <h4 style={h4}>{langText.CommonRulesTitles.generalrule}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.crts_1}</li>
                                        <li>{langText.CommonRulesSoccor.crts_2}</li>
                                        <li>{langText.CommonRulesSoccor.crts_3}</li>
                                    </ul>
                                    <h4 style={h4}>{langText.CommonRulesTitles.crtsfirstHalf}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesSoccor.crtsfirstHalf_1}</li>
                                        <li>{langText.CommonRulesSoccor.crtsfirstHalf_2}</li>
                                        <li>{langText.CommonRulesSoccor.crtsfirstHalf_3}</li>
                                        <li>{langText.CommonRulesSoccor.crtsfirstHalf_4}</li>
                                    </ul>
                                </div>
                            </div>

                            {/* ---basketball */}
                            <div style={ this.state.activeTab === 2 ? ShowTabContent : HideTabContent }>
                                <div style={TabWrapperTitle}>
                                    <h1 style={h1}>{langText.CommonRulesTitles.sportName[2]}</h1>
                                </div>
                                <div style={TabWrapperContent}>
                                    <h2 style={h2}>{langText.CommonRulesTitles.generalrule}</h2>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesGeneralBasketball.grBasketball_1}</li>
                                        <li>{langText.CommonRulesGeneralBasketball.grBasketball_2}</li>
                                        <li>{langText.CommonRulesGeneralBasketball.grBasketball_3}</li>
                                        <li>{langText.CommonRulesGeneralBasketball.grBasketball_4}</li>
                                        <li>{langText.CommonRulesGeneralBasketball.grBasketball_5}</li>
                                        <li>{langText.CommonRulesGeneralBasketball.grBasketball_6}</li>
                                        <li>{langText.CommonRulesGeneralBasketball.grBasketball_7}</li>
                                        <li>{langText.CommonRulesGeneralBasketball.grBasketball_8}</li>
                                        <ul style={alphaBullets}>
                                            <li>{langText.CommonRulesGeneralBasketball.grBasketball_8_1}</li>
                                            <li>{langText.CommonRulesGeneralBasketball.grBasketball_8_2}</li>
                                            <li>{langText.CommonRulesGeneralBasketball.grBasketball_8_3}</li>
                                        </ul>
                                    </ul>
                                    
                                    <h3 style={h2}>{langText.CommonRulesTitles.bettingtype}</h3>
                                    <h4 style={h3}>{langText.CommonRulesTitles.moneyline}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBasketball.moneyline_1}</li>
                                        <li>{langText.CommonRulesBasketball.moneyline_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.gettheball}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBasketball.gettheball_1}</li>
                                        <li>{langText.CommonRulesBasketball.gettheball_2}</li>
                                        <li>{langText.CommonRulesBasketball.gettheball_3}</li>
                                        <li>{langText.CommonRulesBasketball.gettheball_4}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.letsroll}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBasketball.letsroll_1}</li>
                                        <li>{langText.CommonRulesBasketball.letsroll_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.totalscore}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBasketball.totalscore_1}</li>
                                        <li>{langText.CommonRulesBasketball.totalscore_2}</li>
                                        <li>{langText.CommonRulesBasketball.totalscore_3}</li>
                                        <li>{langText.CommonRulesBasketball.totalscore_4}</li>
                                        <li>{langText.CommonRulesBasketball.totalscore_5}</li>
                                        <li>{langText.CommonRulesBasketball.totalscore_6}</li>
                                        <li>{langText.CommonRulesBasketball.totalscore_7}</li>
                                        <ul style={alphaBullets}>
                                            <li>{langText.CommonRulesBasketball.totalscore_7_1}</li>
                                            <li>{langText.CommonRulesBasketball.totalscore_7_2}</li>
                                        </ul>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.rollingtotalscore}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBasketball.rollingtotalscore_1}</li>
                                        <li>{langText.CommonRulesBasketball.rollingtotalscore_2}</li>
                                        <li>{langText.CommonRulesBasketball.rollingtotalscore_3}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.teamscores}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBasketball.teamscores_1}</li>
                                        <li>{langText.CommonRulesBasketball.teamscores_2}</li>
                                        <li>{langText.CommonRulesBasketball.teamscores_3}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.totalpoints}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBasketball.totalpoints_1}</li>
                                        <li>{langText.CommonRulesBasketball.totalpoints_2}</li>
                                    </ul>
                                </div>
                            </div>

                            {/* ---baseball */}
                            <div style={ this.state.activeTab === 3 ? ShowTabContent : HideTabContent }>
                                <div style={TabWrapperTitle}>
                                    <h1 style={h1}>{langText.CommonRulesTitles.sportName[3]}</h1>
                                </div>
                                <div style={TabWrapperContent}>
                                    <h2 style={h2}>{langText.CommonRulesTitles.generalrule}</h2>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesGeneralBaseball.grBaseball_1}</li>
                                        <li>{langText.CommonRulesGeneralBaseball.grBaseball_2}</li>
                                        <li>{langText.CommonRulesGeneralBaseball.grBaseball_3}</li>
                                        <li>{langText.CommonRulesGeneralBaseball.grBaseball_4}</li>
                                        <li>{langText.CommonRulesGeneralBaseball.grBaseball_5}</li>
                                        <li>{langText.CommonRulesGeneralBaseball.grBaseball_6}</li>
                                        <li>{langText.CommonRulesGeneralBaseball.grBaseball_7}</li>
                                        <li>{langText.CommonRulesGeneralBaseball.grBaseball_8}</li>
                                        <li>{langText.CommonRulesGeneralBaseball.grBaseball_9}</li>
                                        <li>{langText.CommonRulesGeneralBaseball.grBaseball_10}</li>
                                        <li>{langText.CommonRulesGeneralBaseball.grBaseball_11}</li>
                                        <ul style={numBullets}>
                                            <li>{langText.CommonRulesGeneralBaseball.grBaseball_11_1}</li>
                                            <li>{langText.CommonRulesGeneralBaseball.grBaseball_11_2}</li>
                                            <ul style={noBullets}>
                                                <li>{langText.CommonRulesGeneralBaseball.grBaseball_11_2_1}</li>
                                                <li>{langText.CommonRulesGeneralBaseball.grBaseball_11_2_2}</li>
                                            </ul>
                                        </ul>
                                    </ul>
                                    
                                    <h2 style={h2}>{langText.CommonRulesTitles.bettingtype}</h2>
                                    <h3 style={h3}>{langText.CommonRulesTitles.moneyline}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBaseBall.moneyline_1}</li>
                                        <li>{langText.CommonRulesBaseBall.moneyline_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.gettheball}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBaseBall.gettheball_1}</li>
                                        <li>{langText.CommonRulesBaseBall.gettheball_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.letsroll}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBaseBall.letsroll_1}</li>
                                        <li>{langText.CommonRulesBaseBall.letsroll_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.totalscore}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBaseBall.totalscore_1}</li>
                                        <li>{langText.CommonRulesBaseBall.totalscore_2}</li>
                                        <li>{langText.CommonRulesBaseBall.totalscore_3}</li>
                                        <li>{langText.CommonRulesBaseBall.totalscore_4}</li>
                                        <li>{langText.CommonRulesBaseBall.totalscore_5}</li>
                                        <li>{langText.CommonRulesBaseBall.totalscore_6}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.rollingtotalscore}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBaseBall.rollingtotalscore_1}</li>
                                        <li>{langText.CommonRulesBaseBall.rollingtotalscore_2}</li>
                                        <li>{langText.CommonRulesBaseBall.rollingtotalscore_3}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.totalscoresd}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBaseBall.totalscoresd_1}</li>
                                        <li>{langText.CommonRulesBaseBall.totalscoresd_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.solowin}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBaseBall.solowin_1}</li>
                                        <li>{langText.CommonRulesBaseBall.solowin_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.teamscores}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBaseBall.teamscores_1}</li>
                                        <li>{langText.CommonRulesBaseBall.teamscores_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.overtime}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesBaseBall.overtime_1}</li>
                                        <li>{langText.CommonRulesBaseBall.overtime_2}</li>
                                        <li>{langText.CommonRulesBaseBall.overtime_3}</li>
                                    </ul>
                                </div>
                            </div>
                            
                            {/* ---Ice Hockey  */}
                            <div style={ this.state.activeTab === 4 ? ShowTabContent : HideTabContent }>
                                <div style={TabWrapperTitle}>
                                    <h1 style={h1}>{langText.CommonRulesTitles.sportName[4]}</h1>
                                </div>
                                <div style={TabWrapperContent}>
                                    <h2 style={h2}>{langText.CommonRulesTitles.generalrule}</h2>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesGeneralIceHockey.grIceHockey_1}</li>
                                        <li>{langText.CommonRulesGeneralIceHockey.grIceHockey_2}</li>
                                        <li>{langText.CommonRulesGeneralIceHockey.grIceHockey_3}</li>
                                        <li>{langText.CommonRulesGeneralIceHockey.grIceHockey_4}</li>
                                        <li>{langText.CommonRulesGeneralIceHockey.grIceHockey_5}</li>
                                        <li>{langText.CommonRulesGeneralIceHockey.grIceHockey_6}</li>
                                    </ul>
                                    
                                    <h2 style={h2}>{langText.CommonRulesTitles.bettingtype}</h2>
                                    <h3 style={h3}>{langText.CommonRulesTitles.moneyline}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesIceHockey.moneyline_1}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.gettheball}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesIceHockey.gettheball_1}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.letsroll}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesIceHockey.letsroll_1}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.overUnder} ({langText.CommonRulesTitles.ball})</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesIceHockey.overUnder_1}</li>
                                        <li>{langText.CommonRulesIceHockey.overUnder_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.inPlayOverUnder} ({langText.CommonRulesTitles.ball})</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesIceHockey.inPlayOverUnder_1}</li>
                                        <li>{langText.CommonRulesIceHockey.inPlayOverUnder_2}</li>
                                    </ul>
                                </div>
                            </div>

                            {/* ---American Football  */}
                            <div style={ this.state.activeTab === 5 ? ShowTabContent : HideTabContent }>
                                <div style={TabWrapperTitle}>
                                    <h1 style={h1}>{langText.CommonRulesTitles.sportName[5]}</h1>
                                </div>
                                <div style={TabWrapperContent}>
                                    <h2 style={h2}>{langText.CommonRulesTitles.generalrule}</h2>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesGeneralAmericanFootball.grAmericanFootball_1}</li>
                                        <li>{langText.CommonRulesGeneralAmericanFootball.grAmericanFootball_2}</li>
                                        <li>{langText.CommonRulesGeneralAmericanFootball.grAmericanFootball_3}</li>
                                        <li>{langText.CommonRulesGeneralAmericanFootball.grAmericanFootball_4}</li>
                                        <li>{langText.CommonRulesGeneralAmericanFootball.grAmericanFootball_5}</li>
                                        <li>{langText.CommonRulesGeneralAmericanFootball.grAmericanFootball_6}</li>
                                    </ul>
                                    
                                    <h2 style={h2}>{langText.CommonRulesTitles.bettingtype}</h2>
                                    <h3 style={h3}>{langText.CommonRulesTitles.moneyline}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsAmericanFootball.moneyline_1}</li>
                                        <li>{langText.CommonRulesContentsAmericanFootball.moneyline_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.gettheball}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsAmericanFootball.gettheball_1}</li>
                                        <li>{langText.CommonRulesContentsAmericanFootball.gettheball_2}</li>
                                        <li>{langText.CommonRulesContentsAmericanFootball.gettheball_3}</li>
                                        <li>{langText.CommonRulesContentsAmericanFootball.gettheball_4}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.letsroll}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsAmericanFootball.letsroll_1}</li>
                                        <li>{langText.CommonRulesContentsAmericanFootball.letsroll_2}</li>
                                        <li>{langText.CommonRulesContentsAmericanFootball.letsroll_3}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.overUnder} ({langText.CommonRulesTitles.totalPoints})</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsAmericanFootball.overUnder_1}</li>
                                        <li>{langText.CommonRulesContentsAmericanFootball.overUnder_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.inPlayOverUnder} ({langText.CommonRulesTitles.totalPoints})</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsAmericanFootball.inPlayOverUnder_1}</li>
                                        <li>{langText.CommonRulesContentsAmericanFootball.inPlayOverUnder_2}</li>
                                        <li>{langText.CommonRulesContentsAmericanFootball.inPlayOverUnder_3}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <TbArrowBigUpFilled onClick={this.scrollToTop} style={ ToTopStyle }/>
                </div>
            </div>
        )
    }
}

export default CommonRules;