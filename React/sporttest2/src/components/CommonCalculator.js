import React from 'react';
import { langText } from "../pages/LanguageContext";
import GetIni from './AjaxFunction'
import styled from '@emotion/styled';
import { ToastContainer, toast, Slide } from 'react-toastify';
import { FaAngleDoubleDown } from 'react-icons/fa';
import { RiForbid2Line } from 'react-icons/ri';
import { MdAutorenew } from 'react-icons/md';
import 'react-toastify/dist/ReactToastify.css';
import  "../css/CommonCalculator.css";

const CalOuterWrapper = {
    position: 'fixed',
    top: 0,
    left: 0,
    zIndex: -1,
    opacity: 0,
    transition: 'all .5s ease 0s',
    MozTransition: 'all .5s ease 0s',
    WebkitTransition: 'all .5s ease 0s',
    OTransition: 'all .5s ease 0s',
    WebkitOverflowScrolling: 'touch',
}

const CalWrapper = {
    fontWeight: 600,
    backgroundColor: 'rgba(0,0,0,0.8)',
    position: 'fixed',
    width: '100%',
    height: '100%',
    zIndex: -1,
    transition: 'all .5s ease 0s',
    MozTransition: 'all .5s ease 0s',
    WebkitTransition: 'all .5s ease 0s',
    OTransition: 'all .5s ease 0s',
    WebkitOverflowScrolling: 'touch',
    // marginLeft: '-0.5rem',
    opacity: 0,
    top: 0
}

const CalWrapperOn = {
    opacity: 1,
    zIndex: 1,
}

const CalContainerOn = {
    bottom: '0',
};

const CalContainer = {
    width: '100%',
    height: '35.5rem',
    backgroundColor: 'white',
    borderTopRightRadius: '35px',
    borderTopLeftRadius: '35px',
    position: 'fixed',
    overflowY: 'scroll',
    overflowX: 'hidden',
    color: 'rgb(65, 91, 90)',
    transition: 'all .5s ease 0s',
    MozTransition: 'all .5s ease 0s',
    WebkitTransition: 'all .5s ease 0s',
    OTransition: 'all .5s ease 0s',
    WebkitOverflowScrolling: 'touch',
    bottom: 'calc(-100%)',
    zIndex: 2,
    // marginLeft: '-0.5rem',
    fontWeight: 600,
}

const BalanceStyle = {
    color: 'rgb(196, 152, 53)',
    fontSize: '1.5rem',
    // lineHeight: '1.6rem'
}

const CalHeight3 = styled.div`
	height: 3rem;
	line-height: 3rem;
`

const CalInfoCardWrapper = styled.div`
    height: 100%;
    width: 100%;
    padding-right: 0.5rem;
    overflow-y: auto;
    
`;
const CalHeight8 = styled.div`
    height: 10.5rem;
    line-height: 2rem;
    overflow-y: auto;
    padding: 0rem 0.5rem 0rem 1rem;
`;
const CalHeight12 = styled.div`
    border-top: 2px solid rgba(65, 91, 90, 0.5);
	height: 12rem;
	line-height: 2.5rem;
    text-align: center;
    font-size: 1.2rem;
    .quick {
        color: rgb(188, 213, 210);
    }
    div {
        border: 1px solid rgb(224, 234, 235);
    }
`
const BetCountStyle = {
    color: 'white',
    backgroundColor: 'rgb(65, 91, 90)',
    // width: '1.8rem',
    height: '1.8rem',
    lineHeight: '1.8rem',
    // marginTop: '0.6rem',
    // marginRight: '0.5rem',
    borderRadius: '50%',
}

const RefreshStyle = {
    // borderRight: '1px solid'
}
const BetterRateStyle = {
    width: '1.2rem',
    height: '1.2rem',
    // marginRight: '0.5rem',
    accentColor: 'rgb(65, 91, 90)',
}

const ClearAll = {
    background: 'rgb(65,91,90)',
    borderRadius: '25px',
    color: 'white',
    height: '2rem',
    lineHeight: '2rem',
    marginTop: '0.5rem'
}
const ClearAllStyle = {
    fontSize: '1.2rem',
    marginTop: '-0.2rem',
    marginRight: '0.2rem',
}

const MoneyInput = styled.input`
    border-radius: 5px;
    border: none;
    background: rgb(188, 213, 210);
    height: 2rem;
    line-height: 2rem;
    text-align: center;
    padding-right: 0px;
    color: rgb(65, 91, 90);
    font-weight: 600;
    font-size: 1.1rem;
    margin-top: 0.5rem;
    &::placeholder {
        color: rgb(127, 149, 146);
        font-size: 1rem;
    }
`;
const FooterLeftBtn = {
    background: 'white',
    borderRadius: '1rem',
    lineHeight: '2.5rem',
    height: '2.5rem',
    marginTop: '0.25rem'
}

const FooterRightBtn = {
    borderRadius: '1rem',
    color: 'white',
    lineHeight: '2.5rem',
    height: '2.5rem',
    marginTop: '0.25rem'
}

const ToastStyle = {
    width: '60%',
    marginLeft: '20%',
    marginTop: '5rem',
}

const CalInfoCard = {
    background: 'rgb(226, 240, 240)',
    borderRadius: '15px',
    padding: '0.5rem',
    margin: '0.5rem 0',
}

const BetItemStyle = {
    background: 'white',
    borderRadius: '20px'
}

const CalInfoCardIcon = {
    width: '1.5rem',
    height: '1.5rem',
    marginTop: '-0.25rem',
    marginRight: '0.25rem'
}


class CommonCalculator extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            accountApi: 'https://sportc.asgame.net/api/v2/common_account?token=' + window.token + '&player=' + window.player,
            game_bet: 'https://sportc.asgame.net/api/v2/game_bet',
            inputMoney: '',
            maxMoney: '0.00',
            isBetterRate: false,
            isPending: false
        }
    }

    async caller(apiUrl, api_res) {
		const json = await GetIni(apiUrl);

		this.setState({
			[api_res]: json,
		})

        // 訊息
        // if(api_res === 'afterBet') {
        //     json.message === 'SUCCESS_API_GAME_BET_01' ? this.notifySuccess(json.message) : this.notifyError(json.message)
        //     this.props.callBack() // 投注後餘額
        // }
	}

    componentDidMount() {
        // ws
        if(window.ws) window.ws.close()
        window.WebSocketDemo()

        window.wsStatus = setInterval(() => {
            if(window.socket_status) {
                window.ws.onmessage = (event) => {
                    const message = JSON.parse(event.data)
                    console.log(message)
                    if( message.action === 'delay_order' && this.state.isPending ) {
                        this.setState({
                            isPending: false
                        }, ()=>{
                            // 關閉計算機
                            this.CloseCal()
                            this.notifySuccess(message.order_id)
                            this.props.callBack() // 投注後餘額
                        })
                    }
                };
                clearInterval(window.wsStatus)
            }
        }, 500);
    }

    componentDidUpdate(prevProps) {
        if (prevProps.data !== this.props.data && this.props.data !== null ) {
            this.setState({
                minLimit: this.props.accountD.data.limit[this.props.cate][window.sport].min,
                maxLimit: this.props.accountD.data.limit[this.props.cate][window.sport].max
            })
        }
    }
    
    // 關閉計算機
    CloseCal = () => {
        if(this.state.isPending) return;
        this.setState({
            inputMoney: '',
            maxMoney: '0.00'
        })
        this.props.CloseCal()
    }

    // 餘額更新
    reFreshBalance = () => {
        this.caller(this.state.accountApi, 'accountApi_res', 1)
    }

    // 鍵盤
    CalMoneyBrick = (num, type = 0) => {
        if(this.state.isPending) return;

        var money = ''
        if( type === 1 ) {
            money = parseInt(this.state.inputMoney)
            if(isNaN(money)) {
                money = 0
            }
            money += num
        } else {
            if(this.state.inputMoney.toString() === '0' && num === 0) return
            if(this.state.inputMoney.toString() === '0') {
                money = num
            } else {
                money = this.state.inputMoney.toString().concat(num)
            }
        }
        this.setState({
            inputMoney: money,
            maxMoney: (this.props.data.bet_rate * money).toFixed(2)
        })
    }

    // 清除金額
    ClearMoney = () => {
        this.setState({
            inputMoney: '',
            maxMoney: '0.00'
        })
    }
    
    // 是否接受更加賠率
    handleBetterRate = () =>{
        if(this.state.isPending) return;

        this.setState({
            isBetterRate: !this.state.isBetterRate
        })
    }


    // 送出投注
    submitBet = () => {
        const money = parseInt(this.state.inputMoney)
        if ( !money ) {
            this.notifyError(langText.CommonCalculator.noinputmoney)
            this.ClearMoney()
            return;
        }

        if ( money < this.state.minLimit ) {
            this.notifyError(langText.CommonCalculator.tooless + this.state.minLimit)
            this.ClearMoney()
            return;
        }

        if ( money > this.state.maxLimit ) {
            this.notifyError(langText.CommonCalculator.toohigh + this.state.maxLimit)
            this.ClearMoney()
            return;
        }

        // 金額通過檢查 送出投注
        var betData = this.props.data
        betData.better_rate = this.state.isBetterRate ? 1 : 0
        betData.bet_amount = this.state.inputMoney
        betData.player = window.player
        betData.token = window.token
        

        this.setState({
            isPending: true
        })

        const queryParams = [];
        for (const key in betData) {
            if (betData.hasOwnProperty(key)) {
                queryParams.push(`${key}=${encodeURIComponent(betData[key])}`);
            }
        }
        const queryString = `${this.state.game_bet}?${queryParams.join('&')}`;
        this.caller(queryString , 'afterBet')

        setTimeout(() => {
            if( this.state.isPending ) {
                this.setState({
                    isPending: false,
                }, ()=>{
                    // 關閉計算機
                    this.CloseCal()
                    this.props.callBack() // 投注後餘額
                })
            }
        }, 10000);
    }

    notifySuccess = (msg) => {
        toast(msg, {
            type: "success"
        })
    }

    notifyError = msg => {
        toast(msg, {
            type: "error"
        })
    }

    render() {
        const sendOrderData = this.props.data
        const res = this.props.accountD
        if(res && sendOrderData) {
                return (
                    <>
                        <ToastContainer
                            position="top-center"
                            autoClose={1500}
                            hideProgressBar
                            newestOnTop={false}
                            closeOnClick
                            rtl={false}
                            draggable
                            pauseOnHover={false}
                            transition={Slide}
                            theme='colored'
                            style={ToastStyle}
                            limit={3}
                        />
                        <div style={{ ...CalOuterWrapper, ...(this.props.isOpenCal === true && CalWrapperOn) }}>
                            <div id='calwrapper' style={{ ...CalWrapper, ...(this.props.isOpenCal === true && CalWrapperOn) }} onClick={this.CloseCal}>
                            </div>
                            <div style={{...CalContainer, ...(this.props.isOpenCal === true && CalContainerOn)}}>
                                <CalHeight3 className='w-100 text-center' style={{padding: '0 1rem', display: 'flex', background: 'rgb(225, 235, 236)'}}>
                                    <div style={{ display: 'flex', width: '58%', marginLeft: '2%' }}>
                                        <span>Hi! { res.data.account }</span>
                                    </div>
                                    <p className='mb-0' style={BalanceStyle} onClick={this.props.callBack}>
                                        { res.data.balance }
                                    </p>
                                    <div style={{fontSize: '1.5rem', width: '15%'}}>
                                        <MdAutorenew style={RefreshStyle} className={this.props.isRefrehingBalance === true ? 'rotateRefresh' : ''} onClick={this.props.callBack} />
                                    </div>
                                    <div style={{fontSize: '1.5rem', width: '10%'}}>
                                        <FaAngleDoubleDown onClick={this.CloseCal} />
                                    </div>
                                </CalHeight3> 
                                <CalHeight3 className='row m-0' style={{borderBottom: '1px solid rgba(65, 91, 90, 0.5)'}}>
                                    <div className='col-8'>
                                        <input id='userInputMoney' type='checkbox' checked={this.state.isBetterRate} style={BetterRateStyle} onChange={this.handleBetterRate}/>
                                        <label htmlFor='userInputMoney'>{langText.CommonCalculator.betterrate}</label>
                                    </div>
                                    <div className='col-4 text-center'>
                                        <div style={ClearAll} onClick={this.CloseCal} >
                                            <RiForbid2Line style={ClearAllStyle}/>
                                            {langText.CommonCalculator.clearall}
                                        </div>
                                    </div>
                                </CalHeight3>
                                <CalHeight8>
                                    <CalInfoCardWrapper id='calInfoCardWrapper'>
                                        <div className='row' style={CalInfoCard}>
                                            <div>{ sendOrderData.series_name }</div>
                                            <div className='col-12'>
                                                { sendOrderData.home_team_name }
                                                <span style={{fontStyle: 'italic'}}>&ensp;VS&ensp;</span>
                                                { sendOrderData.away_team_name }
                                            </div>
                                            <div className='col-12'>{ sendOrderData.market_name }</div>
                                            <div style={BetItemStyle} className='row m-0'>
                                                <div className='col-10 p-0'>{ sendOrderData.bet_item_name }</div>
                                                <div className='col-2 p-0 text-center calCardInfo' market_bet_id={sendOrderData.market_bet_id}>
                                                    <span className='odd'>{ sendOrderData.bet_rate }</span>
                                                </div>
                                            </div>
                                        </div>
                                    </CalInfoCardWrapper>
                                </CalHeight8>
                                <CalHeight3 className='row' style={{ padding: '0 0.5rem', background: 'rgb(225, 235, 236)', borderTop: '2px solid rgba(65, 91, 90, 0.5)', boxShadow: 'rgba(65, 91, 90, 0.3) 0px 0px 5px 3px' }}>
                                    <div className='col-6 row m-0'>
                                        <div className='col-5 p-0'>{langText.CommonCalculator.maxwinning}</div>
                                        <div className='col-7 p-0' style={{ overflowX: 'hidden'}}>{this.state.maxMoney}</div>
                                    </div>
                                    <div className='col-6'>
                                        <MoneyInput readOnly value={this.state.inputMoney} className='w-100' placeholder={`${langText.CommonCalculator.limit} ${this.state.minLimit}-${this.state.maxLimit}`} />
                                    </div>
                                </CalHeight3>
                                <CalHeight12 className='row'>
                                    <div className='col-3 quick' onClick={()=>this.CalMoneyBrick(500,1)}>+500</div>
                                    <div className='col-3' onClick={()=>this.CalMoneyBrick(1)}>1</div>
                                    <div className='col-3' onClick={()=>this.CalMoneyBrick(2)}>2</div>
                                    <div className='col-3' onClick={()=>this.CalMoneyBrick(3)}>3</div>
                                    <div className='col-3 quick' onClick={()=>this.CalMoneyBrick(2000,1)}>+2000</div>
                                    <div className='col-3' onClick={()=>this.CalMoneyBrick(4)}>4</div>
                                    <div className='col-3' onClick={()=>this.CalMoneyBrick(5)}>5</div>
                                    <div className='col-3' onClick={()=>this.CalMoneyBrick(6)}>6</div>
                                    <div className='col-3 quick' onClick={()=>this.CalMoneyBrick(5000,1)}>+5000</div>
                                    <div className='col-3' onClick={()=>this.CalMoneyBrick(7)}>7</div>
                                    <div className='col-3' onClick={()=>this.CalMoneyBrick(8)}>8</div>
                                    <div className='col-3' onClick={()=>this.CalMoneyBrick(9)}>9</div>
                                    <div className='col-3'>MAX</div>
                                    <div className='col-3'>.</div>
                                    <div className='col-3' onClick={()=>this.CalMoneyBrick(0)}>0</div>
                                    <div className='col-3' onClick={this.ClearMoney}>AC</div>
                                </CalHeight12>
                                <div style={{ padding: '0.5rem 2rem', background: 'rgb(65, 91, 90)' }}>
                                    <div className='row' style={{ height: '3rem'}}>
                                        <div className='col-3 text-center' onClick={this.CloseCal} style={FooterLeftBtn}>{langText.CommonCalculator.cancel}</div>
                                        <div className='col-1'></div>
                                        {
                                            this.state.isPending ?
                                            <div className='col-8 text-center' style={{...FooterRightBtn, backgroundColor: '#978b8b'}}>
                                                {langText.CommonCalculator.pending}
                                                <div id='pendingLoad'></div>
                                            </div>
                                            :
                                            <div className='col-8 text-center' onClick={this.submitBet} style={{...FooterRightBtn, backgroundColor: 'rgb(196, 152, 53)'}}>{langText.CommonCalculator.submit}</div>
                                        }
                                    </div>
                                </div>   
                            </div>
                        </div>
                    </>
                )
            }
        }
    }

export default CommonCalculator;