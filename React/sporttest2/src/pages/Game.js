import React from "react";
import $ from 'jquery';
import GameTopSlider from "../components/GameTopSlider";
import GameMain from '../components/GameMain'
import GetIni from '../components/AjaxFunction'
import pako from 'pako'
import Cookies from 'js-cookie';
import PullToRefresh from 'react-simple-pull-to-refresh';
import CommonLoader from '../components/CommonLoader';
import CommonCalculator from '../components/CommonCalculator';

var u = null
var o = null
class Game extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
            game_api: 'https://sportc.asgame.net/api/v2/game_index?token=' + window.token+ '&player=' + window.player+ '&sport_id=' + window.sport + '&fixture_id='+Cookies.get('GameMatchId', { path: '/' }),
			accout_api: 'https://sportc.asgame.net/api/v2/common_account?token=' + window.token+ '&player=' + window.player+ '',
			betData: null,
			isOpenCal: false,
			isGameRefreshing: false,
			isRefrehingBalance: false,
			sport: window.sport
        };
	}

    async caller(apiUrl, api_res, isAcc = 0, isUpdate = 0) {
		const start = Date.now(); // 记录开始时间
		const elapsedTime = Date.now() - start; // 计算经过的时间
		const json = await GetIni(apiUrl); 

		// 先判定要不要解壓縮
		if(json.gzip) {
			const str = json.data;
			const bytes = atob(str).split('').map(char => char.charCodeAt(0));
			const buffer = new Uint8Array(bytes).buffer;
			const uncompressed = JSON.parse(pako.inflate(buffer, { to: 'string' }));
			json.data = uncompressed
		}

		// 是否是更新
		if( isUpdate === 1 ) {
			var oldData = this.state.game_res?.data?.list
			var updateData = json.data?.list
			if( updateData ) this.findDifferences(oldData, updateData)
		}

		// 餘額刷新功能 -> 有時ajax回傳太快導致icon旋轉會閃一下就結束 故至少執行一秒
		if( isAcc === 1 ) {
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
		}, () => {
			if( isUpdate === 0) {
				clearInterval(window.ajaxInt)
				window.ajaxInt = setInterval(() => {
					this.caller(this.state.game_api, 'game_res', 0, 1)
				}, 5000);
			}
		})
	}

	findDifferences = (originalData, updateData, path = []) => {
		for (const key in updateData) {
			const currentPath = [...path, key];
			if (originalData.hasOwnProperty(key)) {
				u = updateData // 最後一筆物件資料
				o = originalData // 最後一筆物件資料
				if (typeof originalData[key] === 'object' && typeof updateData[key] === 'object') {
					this.findDifferences(originalData[key], updateData[key], currentPath);
				} else if (key === 'price' && originalData[key] !== updateData[key]) {
					// console.log(`============== ${u.market_bet_id} ==============`);
					// console.log(`原始值: ${originalData[key]}`);
					// console.log(`更新值: ${updateData[key]}`);
					let market_bet_id = u.market_bet_id
					let status = u.status
					let uRate = u.price
					let oRate = o.price
					if( status === 1 ) {
						if(uRate > oRate) {
							this.removeRateStyle(market_bet_id)
							// 賠率上升
							$('div[market_bet_id=' + market_bet_id + ']').addClass('raiseOdd')
						}
						if(uRate < oRate) {
							this.removeRateStyle(market_bet_id)
							// 賠率下降
							$('div[market_bet_id=' + market_bet_id + ']').addClass('lowerOdd')
						}
						setTimeout(() => {
							this.removeRateStyle(market_bet_id)
						}, 3000);
					}					
				}
			}
		}
	}

	removeRateStyle = (market_bet_id) => {
		$('div[market_bet_id=' + market_bet_id + ']').removeClass('raiseOdd')
		$('div[market_bet_id=' + market_bet_id + ']').removeClass('lowerOdd')
	}

	componentWillUnmount() {
		clearInterval(window.ajaxInt)
	}

	// 頁面初始
	componentDidMount() {
		clearInterval(window.ajaxInt)

		// 若是刷新遊戲頁面 則一秒後移除旋轉動畫
		setTimeout(() => {
			this.setState({
				isGameRefreshing: false
			})
		}, 1000);

		this.caller(this.state.game_api, 'game_res')
		this.caller(this.state.accout_api, 'account_res', 1)
	}

	// 刷新錢包餘額
	refreshWallet = () => {
		this.setState({
            isRefrehingBalance: true
        })
		this.caller(this.state.accout_api, 'account_res', 1)
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

	// 下拉更新
	handleRefresh =() => {
		this.setState({
			betData: null,
			isOpenCal: false,
			isGameRefreshing: false,
			isRefrehingBalance: false,
		},() => {
			// 下拉刷新時，重新初始化頁面
			this.componentDidMount()
		})
		// 返回一個Promise對象，用於通知組件刷新操作已經完成
		return new Promise(resolve => setTimeout(resolve, 1000));
	}
	refreshGame = () => {
		this.componentDidMount()
		this.setState({
			isGameRefreshing: true
		})
	}

	render() {
		const data = this.state?.game_res
		const betData = this.state.betData


		return (
			data !== undefined ?
				<>
					<PullToRefresh onRefresh={this.handleRefresh} pullingContent={''} className="h-100" >
						<GameTopSlider data={data} refreshGame={this.refreshGame} isGameRefreshing={this.state.isGameRefreshing} />
						<GameMain data={data.data} getBetDataCallBack={this.getBetData} />
					</PullToRefresh>
					<CommonCalculator isOpenCal={this.state.isOpenCal} data={betData} CloseCal={this.CloseCal} accountD={this.state.account_res} isRefrehingBalance={this.state.isRefrehingBalance} callBack={this.refreshWallet} />
				</>
			:
			<CommonLoader/>
		)
	}
};

export default Game;