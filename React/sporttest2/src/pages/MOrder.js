import React from "react";
import PullToRefresh from 'react-simple-pull-to-refresh';
import Cookies from 'js-cookie';
import { langText } from "./LanguageContext";
import pako from 'pako'
import MatchMenuNav from "../components/MatchMenuNav"
import CommonSliderUser from "../components/CommonSliderUser"
import CommonFooter from "../components/CommonFooter"
import MatchContent from '../components/MatchContent_m'
import GetIni from '../components/AjaxFunction'
import CommonLoader from '../components/CommonLoader';
import CommonCalculator from '../components/CommonCalculator_m';
import MOrderDetail from '../components/MOrderDetail';
import { ToastContainer, toast, Slide } from 'react-toastify';
import { GrMenu } from "react-icons/gr";

const GameWrapperStyle = {
	height: '100%',
	overflow: 'hidden',
}

const slideIconStyle = {
    position: 'absolute',
    top: '0.3rem',
    right: '0.5rem',
    fontSize: '1.5rem',
}

const ToastStyle = {
    width: '60%',
    marginLeft: '20%',
    marginTop: '5rem',
}


class MOrder extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			baseApiUrl: 'https://sportc.asgame.net/api/v2/match_index?token=' + window.token+ '&player=' + window.player+ '',
			accout_api: 'https://sportc.asgame.net/api/v2/common_account?token=' + window.token+ '&player=' + window.player+ '',
			indexMatchList_api: 'https://sportc.asgame.net/api/v2/index_match_list?token=' + window.token+ '&player=' + window.player+ '',
			betRecord_api: 'https://sportc.asgame.net/api/v2/common_order?token=' + window.token+ '&player=' + window.player+ '&page=1',
			toastMsg: [],
			menu_id: window.menu,
			sport_id: window.sport,
			isRefrehingBalance: false,
			sendOrderData: { // Initialize sendOrderData
				bet_data: [], 
				bet_amount: 0,
				better_rate: 0,
			},
			isOpenCal: false,
			mOrderCount: 0
		};
	}

	async caller(apiUrl, api_res, type = 0) {

		const start = Date.now(); // 记录开始时间
		const elapsedTime = Date.now() - start; // 计算经过的时间
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

		// 餘額刷新功能 -> 有時ajax回傳太快導致icon旋轉會閃一下就結束 故至少執行一秒
		if( type === 1 ) {
			if (elapsedTime < 1000) { // 小于一秒延迟执行
				setTimeout(() => {
					this.setState({
						isRefrehingBalance: false,
					})
				}, 1000 - elapsedTime);
			} else { // 超过一秒立即执行
				this.setState({
					isRefrehingBalance: false,
				})
			}
		}

		if( api_res === 'indexMatchList_res') {
			delete json.data.living;
			window.menu = 0
		}

		this.setState({
			[api_res]: json,
		})

		if(json.status === 0) {
			this.setState(prevState => ({
				toastMsg: [...prevState.toastMsg, json.message],
			}))
		}
	}

	// 初始化
	componentDidMount() {
		setTimeout(() => { // 有時候loading太快，閃一下就過了畫面不好看，故至少執行一秒
			this.setState({
				ready: true
			})
		}, 1000);
		this.caller(this.state.accout_api, 'account_res')
		// this.caller(this.state.betRecord_api, 'betRecord_res')
		this.caller(this.state.indexMatchList_api, 'indexMatchList_res')
		

		// 連線
        // if( window.ws ) {
		// 	window.wsInt = null
		// 	window.ws.close()
		// 	window.ws = null
		// }
		// window.WebSocketDemo( window.sport );
	}

	// 刷新錢包餘額
	refreshWallet = () => {
		this.setState({
            isRefrehingBalance: true
        })
		this.caller(this.state.accout_api, 'account_res', 1)
	}

	// 下拉更新
	handleRefresh =() => {
		this.setState({
			toastMsg: [],
			menu_id: window.menu,
			sport_id: window.sport,
			isRefrehingBalance: false,
			sendOrderData: { // Initialize sendOrderData
				bet_data: [], 
				bet_amount: 0,
				better_rate: 0,
			},
			mOrderCount: 0,
			isOpenCal: false
		},() => {
			// 下拉刷新時，重新初始化頁面
			this.componentDidMount()
		})
		// 返回一個Promise對象，用於通知組件刷新操作已經完成
		return new Promise(resolve => setTimeout(resolve, 1000));
	}

	// 使用者資訊業
	handleCallBMethod = () => {
		// 調用slider中的方法
		this.sliderRef.handleMenuChange();
	}

	// 切換上方menu sport
	changeTab = (menu, sport) => {
		// if( this.state.sport_id !== sport ) {
		// 	// 連線
		// 	window.wsInt = null
		// 	window.ws.close()
		// 	window.ws = null
		// 	window.WebSocketDemo( sport );
		// } 

		this.setState({
			menu_id: menu,
			sport_id: sport
		})
		
		// 這個紀錄給game頁用
		Cookies.set('sport', sport, { path: '/' })
		this.clearOrder()
	}

	// 取得投注所需資料
	getBetData = (betData) => {
		var updatedBetData = []
		// 是否已經選過此玩法
		var existingItem = this.state.sendOrderData.bet_data.findIndex(function(data) {
			return data.fixture_id === betData.fixture_id && data.market_bet_id === betData.market_bet_id;
		});
		
		if(existingItem !== -1){
			// 已經選過這個玩法了 -> 取消選擇
			this.state.sendOrderData.bet_data.splice(existingItem, 1);
			updatedBetData = [...this.state.sendOrderData.bet_data];
			this.setState({
				sendOrderData: {
					bet_data: updatedBetData,
				},
				mOrderCount: updatedBetData.length
			});
		} else {
			// 判斷是否選過這場賽事
			var existingIndex = this.state.sendOrderData.bet_data.findIndex(function(data) {
				return data.fixture_id === betData.fixture_id;
			});
			if (existingIndex !== -1) {
				// 有選過 移除原本的資料
				this.state.sendOrderData.bet_data.splice(existingIndex, 1);
			}

			// 判斷有沒有超過上限10筆
			if(this.state.sendOrderData.bet_data.length + 1 > 10) {
				this.notifyError(langText.MOrder.maxten)
			} else {
				// 塞新的資料
				updatedBetData = [...this.state.sendOrderData.bet_data, betData];
				this.setState({
					sendOrderData: {
						bet_data: updatedBetData,
					},
					mOrderCount: updatedBetData.length
				});
			}
		}
	};
	  
	// 清除全部
	ClearAll = () => {
		this.CloseCal()
		this.clearOrder()
	}

	// 關閉計算機
	CloseCal = () => {
		this.setState({
			isOpenCal: false
		})
	}

	// 清除串關注單
	clearOrder = () => {
		this.setState({
			sendOrderData: { // Initialize sendOrderData
				bet_data: [], 
				bet_amount: 0,
				better_rate: 0,
			},
			mOrderCount: 0
		})
	}

	// 打開計算機
	openOrderDetail = () => {
		this.setState({
			isOpenCal: true
		})
	}

	// 訊息
	notifyError = msg => {
        toast(msg, {
            type: "error"
        })
    }

	render() {
		return (
			this.state.ready === true ?
				<div style={GameWrapperStyle} id="MatchOuterContainer">
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
					<CommonSliderUser ref={(ref) => (this.sliderRef = ref)} api_res={this.state.account_res} isRefrehingBalance={this.state.isRefrehingBalance} callBack={this.refreshWallet} />
					<PullToRefresh onRefresh={this.handleRefresh} pullingContent={''} style={{ width: '74%' }}>
						<MOrderDetail mOrderCount={this.state.mOrderCount} clearOrder={this.clearOrder} openOrderDetail={this.openOrderDetail} />
						<MatchMenuNav api_res={this.state.indexMatchList_res} callBack={this.changeTab} />
						<MatchContent apiUrl={this.state.baseApiUrl} menu_id={this.state.menu_id}  sport_id={this.state.sport_id} callBack={this.getBetData} sendOrderData={this.state.sendOrderData.bet_data} />
						<div onClick={this.handleCallBMethod} style={slideIconStyle}><GrMenu /></div>
					</PullToRefresh>
					<CommonCalculator isOpenCal={this.state.isOpenCal} data={this.state.sendOrderData} CloseCal={this.CloseCal} ClearAll={this.ClearAll} accountD={this.state.account_res} isRefrehingBalance={this.state.isRefrehingBalance} callBack={this.refreshWallet} />
					<CommonFooter index={4} />
				</div>
			:
			// 頁面loading樣式
			<CommonLoader />
		)
	}
};

export default MOrder;