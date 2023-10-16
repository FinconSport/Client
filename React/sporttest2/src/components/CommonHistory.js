import React from 'react';
import { langText } from "../pages/LanguageContext";
import GetIni from './AjaxFunction'
import styled from '@emotion/styled';
import InfiniteScroll from 'react-infinite-scroll-component';
import CommonHistorySlideToggle from './CommonHistorySlideToggle'
import pako from 'pako'
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

const HistoryWrapper = {
    fontWeight: 600,
    backgroundColor: 'rgba(255,255,255,0.9)',
    position: 'fixed',
    width: '100%',
    height: '100%',
    zIndex: 1,
    transition: 'all .5s ease 0s',
    MozTransition: 'all .5s ease 0s',
    WebkitTransition: 'all .5s ease 0s',
    OTransition: 'all .5s ease 0s',
    WebkitOverflowScrolling: 'touch',
    bottom: 'calc(-100%)'
}

const HistoryWrapperOn = {
    bottom: '0'
};

const HistoryBetWrapper = {
    width: '100%',
    height: '87%',
    bottom: 0,
    backgroundColor: 'rgb(65, 91, 90)',
    borderTopRightRadius: '35px',
    borderTopLeftRadius: '35px',
    position: 'absolute',
    padding: '1rem 1rem 0 1rem'
}
const PageContainer = {
    overflowY: 'auto',
    overflowX: 'hidden',
    borderRadius: '15px',
    width: '100%',
    height: '100%',
    fontSize: '0.9rem'
}

const HistoryPageTitle = {
    position: 'absolute',
    left: '1rem',
    top: '1rem',
    fontSize: '1.2rem'
}

const HistoryPageClose = {
    position: 'absolute',
    right: '1rem',
    top: '1rem',
    fontSize: '2rem'
}

const HistoryCard = {
    backgroundColor: 'rgb(226, 240, 240)',
    borderRadius: '10px',
    marginTop: '0.5rem',
    padding: '0.5rem 1rem',
    // width: 'calc(100% - 0.75rem)'
}

const HistoryCardMain = {
    backgroundColor: 'white',
    borderRadius: '10px',
    width: 'calc(100% + 1rem)',
    padding: '0.25rem',
    margin: '0.5rem 0 0 -0.5rem',
    position: 'relative'
}


const BetRecordBtn = styled.button`
    background-color: rgb(65,91,90);
    color: rgb(196, 211, 211);
    box-shadow: rgb(150, 150, 150) 0px 2px 3px 0px;
    border: none;
    width: 10rem;
    height: 2rem;
	border-radius: 15px;
    text-align: center;
    width: 8rem;
    margin: 0 0.5rem;
    border: none;
    font-weight: 600;
`

const BetRecordBtnOn = {
    backgroundColor: 'white',
    color: 'rgb(65,91,90)'
}


class CommonHistory extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            apiUrl: 'https://sportc.asgame.net/api/v2/common_order?token=' + window.token+ '&player=' + window.player+ '&page=',
            page:1,
            activeTab: 0,
            hasMore:true,
            data:[],
            searchStatus: 0,
            fetchMoreLock: 0
        }
    }

    async caller(apiUrl, callerType = 0, page=1) {
        if( callerType === 0 ) {
			// 先滑到最上面再撈資料
			this.scrollToTop()
		}

		const json = await GetIni(apiUrl);
        // 先判定要不要解壓縮
        if(json.gzip) {
            // 將字符串轉換成 ArrayBuffer
            const str = json.data;
            const bytes = atob(str).split('').map(char => char.charCodeAt(0));
            const buffer = new Uint8Array(bytes).buffer;
            // 解壓縮 ArrayBuffer
            const uncompressed = JSON.parse(pako.inflate(buffer, { to: 'string' }));
            json.data = uncompressed
        }
        const newData = json.data.list
        var data = []
		switch (callerType) {
			case 0:
				// 初始化
				data = [...Object.values(newData)]
				break;
			case 1:
				data = [...this.state.data, ...Object.values(newData)]
				break;
			default:
				break;
		}

		this.setState({
			status: json.status,
			message: json.message,
            data: data,
            page: page,
            fetchMoreLock: 0
		})
        if(Object.keys(newData).length === 0 || Object.keys(newData).length !== 20){
			this.setState({
				hasMore: false
			})
		} else {
			this.setState({
				hasMore: true
			})
		}
	}

    // 滑到最上面
	scrollToTop = () => {
		document.getElementById('HistoryMain').scrollTo({top: 0, behavior: 'smooth'});
	}


    // call api載入下一頁資料
	getNewPage = () => {
        if(this.state.fetchMoreLock === 0) {
            this.setState({
                fetchMoreLock: 1
            },() => {
                let nowPage = parseInt(this.state.page)
                let nextPage = nowPage + 1
                this.caller('https://sportc.asgame.net/api/v2/common_order?token=' + window.token+ '&player=' + window.player+ '&result=' + this.state.searchStatus + '&page=' + nextPage ,1 ,nextPage)
            })
        }
	}

    // 關閉頁面
    closeHistory = () => {
        this.props.callBack()
    }

    // 初始資料
    componentDidMount() {
        this.caller('https://sportc.asgame.net/api/v2/common_order?token=' + window.token+ '&player=' + window.player+ '&result=' + this.state.searchStatus + '&page=1')
	}

    // 每次點進來都撈一次
	componentDidUpdate(prevProps) {
        // console.log('componentDidUpdate')
		if (prevProps.isShow !== this.props.isShow ) {
            this.componentDidMount()
		}
	}

    // 以接算 未結算 切換
    StatusBtn = (status) => {
        this.setState({
            searchStatus: status
        })
        this.caller('https://sportc.asgame.net/api/v2/common_order?token=' + window.token+ '&player=' + window.player+ '&result=' + status + '&page=1')
    }

    // 圖片毀損
    handleError(event) {
        event.target.src = 'https://sporta.asgame.net/uploads/default.png';
    }

    render() {
        const { data, hasMore} = this.state
        return (
            <div style={{ ...HistoryWrapper, ...(this.props.isShow === true && HistoryWrapperOn) }}>
                <div style={HistoryPageTitle}>{langText.CommonHistory.record}</div>
                <AiFillCloseCircle style={HistoryPageClose} onClick={this.closeHistory} />
                <div className='text-center mt-5'>
                    <BetRecordBtn style={this.state.searchStatus === 0 ? BetRecordBtnOn: null} onClick={()=>this.StatusBtn(0)}>{langText.CommonHistory.notcheckout}</BetRecordBtn>
                    <BetRecordBtn style={this.state.searchStatus === 1 ? BetRecordBtnOn: null} onClick={()=>this.StatusBtn(1)}>{langText.CommonHistory.checkout}</BetRecordBtn>
                </div>
                <div style={HistoryBetWrapper}>
                    <div id='HistoryMain' style={PageContainer}>
                    <InfiniteScroll
                        dataLength={ data }
                        next={this.getNewPage}
                        hasMore={hasMore}
                        loader={<div className="loading loading04">
                            <span>L</span>
                            <span>O</span>
                            <span>A</span>
                            <span>D</span>
                            <span>I</span>
                            <span>N</span>
                            <span>G</span>
                            <span>.</span>
                            <span>.</span>
                            <span>.</span>
                        </div>}
                        scrollableTarget="HistoryMain">
                            {   
                                Object.entries(data).map(([key, val]) => 
                                    <div style={HistoryCard} key={key} historyid={val.id}>
                                        <div className='row m-0'>
                                            <div className='col-10 p-0'>
                                                {
                                                    val.m_order === 0 ?
                                                    langText.CommonHistory.sport
                                                    :
                                                    langText.CommonHistory.morder
                                                }
                                            </div>
                                            <div className='col-2 p-0 text-right'>
                                                {
                                                    val.m_order === 0 ?
                                                    val.id
                                                    :
                                                    val.m_id
                                                }
                                            </div>
                                        </div>
                                        <div style={HistoryCardMain}>
                                            <CommonHistorySlideToggle data={val}/>
                                        </div>
                                        <div className='row m-0'>
                                            <div className='col-4 p-0'>{langText.CommonHistory.betamount}</div>
                                            <div className='col-8 p-0 text-right'>{ val.bet_amount }</div>
                                            <div className='col-4 p-0'>{langText.CommonHistory.winamount}</div>
                                            <div className='col-8 p-0 text-right'>{ val.result_amount }</div>
                                            <div className='col-4 p-0'>{langText.CommonHistory.betstatus}</div>
                                            <div className='col-8 p-0 text-right'>{ langText.CommonHistory.statusArr[val.status] }</div>
                                            <div className='col-4 p-0'>{langText.CommonHistory.bettime}</div>
                                            <div className='col-8 p-0 text-right'>{ val.create_time }</div>
                                        </div>
                                    </div>
                                )
                            }
                            {!hasMore && <h5 className='mt-2 text-center fw-600' style={{ color: 'rgb(196, 211, 211)' }}>{langText.CommonHistory.nomoredata}</h5>}
                    </InfiniteScroll>
                    </div>
                    <TbArrowBigUpFilled onClick={this.scrollToTop} style={ ToTopStyle }/>
                </div>
            </div>
        )
    }
}

export default CommonHistory;