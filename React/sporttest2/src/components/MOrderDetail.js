import React from 'react';
import { langText } from "../pages/LanguageContext";
import styled from '@emotion/styled';
import { AiFillFile, AiOutlineClose } from 'react-icons/ai';

import 'bootstrap/dist/css/bootstrap.css';

const DetailWrapper = {
    fontWeight: 600,
    backgroundColor: 'rgba(0,0,0,0.8)',
    position: 'fixed',
    width: '100%',
    height: '3rem',
    zIndex: 1,
    transition: 'all .5s ease 0s',
    MozTransition: 'all .5s ease 0s',
    WebkitTransition: 'all .5s ease 0s',
    OTransition: 'all .5s ease 0s',
    WebkitOverflowScrolling: 'touch',
    opacity: 0,
    top: '-4rem',
    padding: '0.5rem 0',
    textAlign: 'center',
}

const DetailWrapperOn = {
    top: 0,
    opacity: 1
}

const OpenDetailStyle = {
    background: 'rgb(196, 152, 53)',
    color: 'white'
}

const MOrderBtn = styled.button`
	height: 2rem;
	border-radius: 15px;
    text-align: center;
    font-size: 0.9rem;
    width: 8rem;
    margin: 0 0.5rem;
    border: none;
    background: white;
    color: rgb(65, 91, 90);
    font-weight: 600;
    position: relative;
`

class MOrderDetail extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            
        }
    }

    clearOrder = () => {
        this.props.clearOrder()
    }

    openOrderDetail = () => {
        this.props.openOrderDetail()
    }
   

    render() {
        return(
            <div style={{ ...DetailWrapper, ...(this.props.mOrderCount > 0 && DetailWrapperOn) }}>
                <MOrderBtn onClick={this.openOrderDetail} style={OpenDetailStyle}>
                    <AiFillFile/>
                    {langText.MOrderDetail.detail}
                    (<span>{ this.props.mOrderCount }</span>)
                </MOrderBtn>
                <MOrderBtn onClick={this.clearOrder}>{langText.MOrderDetail.clearbet}</MOrderBtn>
            </div>
        )
    }
}

export default MOrderDetail;