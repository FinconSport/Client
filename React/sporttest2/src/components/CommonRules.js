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
    backgroundColor: 'rgba(255,255,255,0.9)',
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
    display: 'grid',
    gridTemplateColumns: '1fr 1fr 1fr',
    gridColumnGap:' 0.5rem',
}

const TabMenuBtn = {
    background: '#3f5a5c',
    color: '#ffffff',
    height: 'auto',
    lineHeight: '1rem',
    padding: '0.7rem',
    textAlign: 'center',
    fontWeight: '600',
    fontSize: '1rem',
    borderRadius: '15px',
    boxShadow: '#00000080 0 0 9px 0px',
    border:'none',
}

const TabMenuBtnActive = {
    background: '#ffffff',
    color: '#3f5a5c',
    height: 'auto',
    lineHeight: '1rem',
    padding: '0.7rem',
    textAlign: 'center',
    fontWeight: '600',
    fontSize: '1rem',
    borderRadius: '15px',
    boxShadow: '#00000080 0 0 9px 0px',
    border:'none',
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
    fontSize: '1.2rem',
    fontWeight: '600',
}

const h4 = {
    fontSize: '1.2rem',
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
    padding: '0px',
}

const upperAlphaBullets = {
    listStyleType: 'upper-alpha',
    padding: '0px',
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
                        <button onClick={()=> this.handleTabChange(1) } style={ this.state.activeTab === 1 ? TabMenuBtnActive : TabMenuBtn }>{langText.CommonRulesTitles.soccor}</button>
                        <button onClick={()=> this.handleTabChange(2) } style={ this.state.activeTab === 2 ? TabMenuBtnActive : TabMenuBtn }>{langText.CommonRulesTitles.basketball}</button>
                        <button onClick={()=> this.handleTabChange(3) } style={ this.state.activeTab === 3 ? TabMenuBtnActive : TabMenuBtn }>{langText.CommonRulesTitles.baseball}</button>
                    </div>
                    <div id='GameRulesMain' style={PageContainer}>
                        <div id='TabMainWrapperCon' style={TabMainWrapperCon}>

                            {/* ---soccor */}
                            <div style={ this.state.activeTab === 1 ? ShowTabContent : HideTabContent }>
                                <div style={TabWrapperTitle}>
                                    <h1 style={h1}>{langText.CommonRulesTitles.soccor}</h1>
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
                                    
                                    <h2 style={h2}>{langText.CommonRulesTitles.handicap}</h2>
                                    <h3 style={h3}>{langText.CommonRulesTitles.generalrule}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_1}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_2}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_3}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_4}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_5}</li>
                                        <ul style={alphaBullets}>
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_5_1}</li>
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_5_2}</li>
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_5_3}</li>
                                        </ul>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_6}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_7}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_8}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.fulltimehandicapresult}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_9}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_10}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_11}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_12}</li>
                                        <ul style={numBullets}>
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_12_1}</li>
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_12_2}</li>
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_12_3}</li>
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_12_4}</li>
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_12_5}</li>
                                        </ul>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_13}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.handicapfirstHalf}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_14}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_15}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_16}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.letsroll}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_17}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_18}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.othandicap}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_19}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_20}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.otletfirstHalf}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_21}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_22}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_23}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.betting_sizes}</h3>
                                    <h4 style={h4}>{langText.CommonRulesTitles.generalrule}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_24}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_25}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_26}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_27}</li>
                                        <ul style={alphaBullets}>
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_27_1}</li>
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_27_2}</li>
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_27_3}</li>
                                        </ul>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_28}</li>
                                        <ul style={alphaBullets}>
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_28_1}</li>
                                            <ul style={romanBullets}>
                                                <li>{langText.CommonRulesContentsSoccor.rcSoccor_28_1_1}</li>
                                                <li>{langText.CommonRulesContentsSoccor.rcSoccor_28_1_2}</li>
                                            </ul>   
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_28_2}</li> 
                                            <ul style={romanBullets}>
                                                <li>{langText.CommonRulesContentsSoccor.rcSoccor_28_2_1}</li>
                                                <li>{langText.CommonRulesContentsSoccor.rcSoccor_28_2_2}</li>
                                            </ul> 
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_28_3}</li>
                                            <ul style={romanBullets}>
                                                <li>{langText.CommonRulesContentsSoccor.rcSoccor_28_3_1}</li>
                                                <li>{langText.CommonRulesContentsSoccor.rcSoccor_28_3_2}</li>
                                            </ul>  
                                        </ul>
                                    </ul>
                                    <h4 style={h4}>{langText.CommonRulesTitles.goallargeSmall}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_29}</li>   
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_30}</li> 
                                    </ul>
                                    <h4 style={h4}>{langText.CommonRulesTitles.goaloverUnderfirstHalf}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_31}</li>   
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_32}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_33}</li> 
                                    </ul>
                                    <h4 style={h4}>{langText.CommonRulesTitles.rollingballoverUnder}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_34}</li>   
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.otgoaloverUnder}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_35}</li>   
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_36}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_37}</li> 
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.otgoalsoverUnderfirstHalf}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_38}</li>   
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_39}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_40}</li> 
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.teamgoalsoverUnder}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_41}</li>   
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_42}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_43}</li> 
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_44}</li> 
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.solowinners}</h3>
                                    <h4 style={h4}>{langText.CommonRulesTitles.generalrule}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_45}</li>   
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_46}</li>
                                    </ul>
                                    <h4 style={h4}>{langText.CommonRulesTitles.winalone}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_47}</li>   
                                    </ul>
                                    <h4 style={h4}>{langText.CommonRulesTitles.winalonefirstHalf}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_48}</li>   
                                    </ul>
                                    <h4 style={h4}>{langText.CommonRulesTitles.scoregoal}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_49}</li>   
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_50}</li>  
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_51}</li>  
                                        <ul style={numBullets}>
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_52_1}</li>  
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_52_2}</li>  
                                            <li>{langText.CommonRulesContentsSoccor.rcSoccor_52_3}</li>   
                                            <ul style={upperAlphaBullets}>
                                                <li><h4 style={h4}>{langText.CommonRulesTitles.example1}</h4></li>  
                                                <ul style={romanBullets}>
                                                    <li>{langText.CommonRulesContentsSoccor.rcSoccor_53_1}</li>
                                                    <li>{langText.CommonRulesContentsSoccor.rcSoccor_53_2}</li>
                                                    <li>{langText.CommonRulesContentsSoccor.rcSoccor_53_3}</li>
                                                </ul>
                                                <li><h4 style={h4}>{langText.CommonRulesTitles.example2}</h4></li>  
                                                <ul style={romanBullets}>
                                                    <li>{langText.CommonRulesContentsSoccor.rcSoccor_54_1}</li>
                                                    <li>{langText.CommonRulesContentsSoccor.rcSoccor_54_2}</li>
                                                    <li>{langText.CommonRulesContentsSoccor.rcSoccor_54_3}</li>
                                                </ul>
                                            </ul>
                                            <li>{langText.CommonRulesTitles.example2}</li>
                                        </ul>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.otwinalone}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_55}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_56}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.otwinAlonefirstHalf}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_57}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_58}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_59}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.crts}</h3>
                                    <h4 style={h4}>{langText.CommonRulesTitles.generalrule}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_60}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_61}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_62}</li>
                                    </ul>
                                    <h4 style={h4}>{langText.CommonRulesTitles.crtsfirstHalf}</h4>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_63}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_64}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_65}</li>
                                        <li>{langText.CommonRulesContentsSoccor.rcSoccor_66}</li>
                                    </ul>
                                </div>
                            </div>

                            {/* ---basketball */}
                            <div style={ this.state.activeTab === 2 ? ShowTabContent : HideTabContent }>
                                <div style={TabWrapperTitle}>
                                    <h1 style={h1}>{langText.CommonRulesTitles.basketball}</h1>
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
                                    
                                    <h2 style={h2}>{langText.CommonRulesTitles.bettingtype}</h2>
                                    <h3 style={h3}>{langText.CommonRulesTitles.solowinners}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_1}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.gettheball}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_3}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_4}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_5}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.letsroll}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_6}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.totalscore}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_7}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_2}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_8}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_9}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_4}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_10}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_11}</li>
                                        <ul style={alphaBullets}>
                                            <li>{langText.CommonRulesContentsBasketball.rcBasketball_11_1}</li>
                                            <li>{langText.CommonRulesContentsBasketball.rcBasketball_11_2}</li>
                                        </ul>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.rollingtotalscore}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_7}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_12}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_8}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.team_scores}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_13}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_14}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_15}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.totalpoints}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_16}</li>
                                        <li>{langText.CommonRulesContentsBasketball.rcBasketball_2}</li>
                                    </ul>
                                </div>
                            </div>

                            {/* ---baseball */}
                            <div style={ this.state.activeTab === 3 ? ShowTabContent : HideTabContent }>
                                <div style={TabWrapperTitle}>
                                    <h1 style={h1}>{langText.CommonRulesTitles.baseball}</h1>
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
                                    <h3 style={h3}>{langText.CommonRulesTitles.solowinners}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_1}</li>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.gettheball}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_3}</li>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.letsroll}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_3}</li>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.totalscore}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_4}</li>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_2}</li>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_5}</li>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_6}</li>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_7}</li>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_8}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.rollingtotalscore}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_4}</li>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_9}</li>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.totalscoresd}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_10}</li>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_2}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.solowin}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_11}</li>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_12}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.teamscores}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_13}</li>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_14}</li>
                                    </ul>
                                    
                                    <h3 style={h3}>{langText.CommonRulesTitles.overtime}</h3>
                                    <ul style={numBullets}>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_14}</li>
                                        <li>{langText.CommonRulesContentsBaseBall.rcBaseball_15}</li>
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