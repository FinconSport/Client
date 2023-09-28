import React from "react";
import PullToRefresh from 'react-simple-pull-to-refresh';
import pako from 'pako'
import ResultMenuNav from "../components/ResultMenuNav"
import CommonSliderUser from "../components/CommonSliderUser"
import CommonFooter from "../components/CommonFooter"
import ResultContent from '../components/ResultContent'
import GetIni from '../components/AjaxFunction'
import CommonLoader from '../components/CommonLoader';
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


class Result extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			accout_api: 'https://sportc.asgame.net/api/v2/common_account?token=' + window.token+ '&player=' + window.player,
			match_sport: 'https://sportc.asgame.net/api/v2/match_sport?token=' + window.token+ '&player=' + window.player,
			sport_id: window.sport,
			isRefrehingBalance: false,
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

	// 初始化
	componentDidMount() {
		setTimeout(() => { // 有時候loading太快，閃一下就過了畫面不好看，故至少執行一秒
			this.setState({
				ready: true
			})
		}, 1000);
		this.caller(this.state.accout_api, 'account_res')
		this.caller(this.state.match_sport, 'sportList')
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
			sport_id: window.sport,
			isRefrehingBalance: false,
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

	// sport change handler
	changeTab = (sport) => {
		this.setState({
			sport_id: sport
		})
	}


	render() {
		return (
			this.state.ready === true ?
				<div style={GameWrapperStyle} id="ResultOuterContainer">
					<CommonSliderUser ref={(ref) => (this.sliderRef = ref)} api_res={this.state.account_res} isRefrehingBalance={this.state.isRefrehingBalance} callBack={this.refreshWallet} />
					<PullToRefresh onRefresh={this.handleRefresh} pullingContent={''} style={{ width: '74%' }}>
						<ResultMenuNav api_res={this.state.sportList} callBack={this.changeTab} />
						<ResultContent sport_id={this.state.sport_id}/>
						<div onClick={this.handleCallBMethod} style={slideIconStyle}><GrMenu /></div>
					</PullToRefresh>
					<CommonFooter/>
				</div>
			:
			// 頁面loading樣式
			<CommonLoader />
		)
	}
};

export default Result;