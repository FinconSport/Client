import React from "react";
import PullToRefresh from 'react-simple-pull-to-refresh';
import Cookies from 'js-cookie';
import pako from 'pako'
import MatchMenuNav from "../components/MatchMenuNav"
import CommonSliderUser from "../components/CommonSliderUser"
import CommonFooter from "../components/CommonFooter"
import MatchContent from '../components/MatchContent'
import GetIni from '../components/AjaxFunction'
import CommonLoader from '../components/CommonLoader';
import CommonCalculator from '../components/CommonCalculator';
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


class Match extends React.Component {
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
			betData: null,
			isOpenCal: false
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

		this.setState({
			[api_res]: json,
		})

		if(json.status === 0) {
			this.setState(prevState => ({
				toastMsg: [...prevState.toastMsg, json.message],
			}))
		}
	}

	componentWillUnmount() {
		clearInterval(this.renderInterval); // 清除定时器以防止内存泄漏
	}

	// 頁面初始
	componentDidMount() {
		clearInterval(this.renderInterval); // 清除定时器以防止内存泄漏

		setTimeout(() => { // 有時候loading太快，閃一下就過了畫面不好看，故至少執行一秒
			this.setState({
				ready: true
			})
		}, 1000);

		// 呼叫所需api
		this.caller(this.state.accout_api, 'account_res')
		this.caller(this.state.betRecord_api, 'betRecord_res')
		this.caller(this.state.indexMatchList_api, 'indexMatchList_res')


		this.renderInterval = setInterval(() => {
			this.caller(this.state.indexMatchList_api, 'indexMatchList_res')
		}, 5000);
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
			betData: null,
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
	}

	// 取得投注所需資料
	getBetData = (betData) => {
		this.setState({
			betData: betData,
			isOpenCal: true
		})
	}

	// 關閉計算機
	CloseCal = () => {
		this.setState({
			isOpenCal: false
		})
	}

	render() {
		return (
			this.state.ready === true ?
				<div style={GameWrapperStyle} id="MatchOuterContainer">
					<CommonSliderUser ref={(ref) => (this.sliderRef = ref)} api_res={this.state.account_res} isRefrehingBalance={this.state.isRefrehingBalance} callBack={this.refreshWallet} />
					<PullToRefresh onRefresh={this.handleRefresh} pullingContent={''} style={{ width: '74%' }}>
						<MatchMenuNav api_res={this.state.indexMatchList_res} callBack={this.changeTab} />
						<MatchContent apiUrl={this.state.baseApiUrl} menu_id={this.state.menu_id}  sport_id={this.state.sport_id} callBack={this.getBetData}/>
						<div onClick={this.handleCallBMethod} style={slideIconStyle}><GrMenu /></div>
					</PullToRefresh>
					<CommonCalculator isOpenCal={this.state.isOpenCal} data={this.state.betData} CloseCal={this.CloseCal} accountD={this.state.account_res} isRefrehingBalance={this.state.isRefrehingBalance} callBack={this.refreshWallet} />
					<CommonFooter index={5} />
				</div>
			:
			// 頁面loading樣式
			<CommonLoader />
		)
	}
};

export default Match;