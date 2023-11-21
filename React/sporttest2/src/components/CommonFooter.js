import React from 'react';
import { langText } from "../pages/LanguageContext";
import { Link } from "react-router-dom";
import { Col } from 'reactstrap';
import { AiFillStar } from "react-icons/ai";
import { VscTasklist } from "react-icons/vsc";
import { IoHomeOutline } from "react-icons/io5";
import { MdSportsBaseball } from "react-icons/md";
import { SiTarget } from "react-icons/si";
import CommonHistory from './CommonHistory'
import styled from '@emotion/styled';
import 'bootstrap/dist/css/bootstrap.css';
import  "../css/CommonFooter.css";

const bottomNavRow = {
    width: '100%',
    textAlign: 'center',
    position: 'fixed',
    bottom: '0',
    display: 'flex',
    height: '5rem',
    zIndex: 0,
    justifyContent: 'space-around',
    background: 'rgb(65, 91, 90)',
    padding: '0 0.5rem'
}


const FooterIconTab = {
    borderRadius: '50%',
    background: '#8cb5b1',
    width: '2.5rem',
    height: '2.5rem',
    marginTop: '0.5rem',
    fontSize: '1.5rem',
    lineHeight: '2rem',
    color: '#c5d6d5',
    marginLeft: 'auto',
    marginRight: 'auto',
}
const FooterTab = styled.div`
    color: #8cb5b1;
    font-size: 1rem;
    width: 20%;
    font-weight: 600;
`

class CommonFooter extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            isShow: false,
            onIndex: this.props.index
        }
    }

    // 打開投注紀錄頁
    openHistory = () => {
        this.setState({
            isShow: true
        })
    };

    // 關閉投注紀錄頁
    closeHistory = () => {
        this.setState({
            isShow: false
        })
    }
    
    handleTabChange = (index) => {
        this.setState({
            onIndex: index
        })
    }

    render() {
        return(
            <>
                <Col style={bottomNavRow} id='footer'>
                    <FooterTab onClick={()=>this.handleTabChange(1)} className={this.state.onIndex === 1 ? 'commonFooterOn' : ''}>
                        <div style={FooterIconTab} >
                            <AiFillStar />
                        </div>
                        <div>{langText.CommonFooter.focus}</div>
                    </FooterTab>
                    <FooterTab onClick={() => { this.openHistory(); this.handleTabChange(2); }} className={this.state.onIndex === 2 ? 'commonFooterOn' : ''}>
                        <div style={FooterIconTab} >
                            <VscTasklist />
                        </div>
                        <div>{langText.CommonFooter.record}</div>
                    </FooterTab>
                    <FooterTab onClick={()=>this.handleTabChange(3)} className={this.state.onIndex === 3 ? 'commonFooterOn' : ''}>
                        <Link style={{color: '#8cb5b1'}} to="/mobile">
                            <div style={FooterIconTab} >
                                <IoHomeOutline />
                            </div>
                            <div>{langText.CommonFooter.home}</div>
                        </Link>
                    </FooterTab>
                    <FooterTab onClick={()=>this.handleTabChange(4)} className={this.state.onIndex === 4 ? 'commonFooterOn' : ''}>
                        <Link style={{color: '#8cb5b1'}} to="/mobile/m_order">
                            <div style={FooterIconTab} >
                                <SiTarget />
                            </div>
                            <div>{langText.CommonFooter.morder}</div>
                        </Link>
                    </FooterTab>
                    <FooterTab onClick={()=>this.handleTabChange(5)} className={this.state.onIndex === 5 ? 'commonFooterOn' : ''}>
                        <Link style={{color: '#8cb5b1'}} to="/mobile/match">
                            <div style={FooterIconTab} >
                                <MdSportsBaseball />
                            </div>
                            <div>{langText.CommonFooter.sport}</div>
                        </Link>
                    </FooterTab>
                </Col>
                <CommonHistory isShow={this.state.isShow} callBack={this.closeHistory} />
            </>
        )
    }
}

export default CommonFooter;