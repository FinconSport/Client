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
import { langText } from "../pages/LanguageContext";

class Game extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
            game_api: 'https://sportc.asgame.net/api/v1/game_index?token=' + window.token+ '&player=' + window.player+ '&sport_id=' + Cookies.get('sport', { path: '/' }) + '&match_id='+Cookies.get('GameMatchId', { path: '/' }),
			accout_api: 'https://sportc.asgame.net/api/v1/common_account?token=' + window.token+ '&player=' + window.player+ '',
			betData: null,
			isOpenCal: false,
			isGameRefreshing: false,
			isRefrehingBalance: false,
			sport: parseInt(Cookies.get('sport', { path: '/' }))
        };

		// this.updateData = this.updateData.bind(this); // 綁定 this
	}

    async caller(apiUrl, api_res, type = 0) {

		// console.log(apiUrl)

		const start = Date.now(); // 记录开始时间
		const elapsedTime = Date.now() - start; // 计算经过的时间

		const json = await GetIni(apiUrl); 
		// 先判定要不要解壓縮
		if(json.gzip === 1) {
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


		if( api_res === 'game_res' ){
			this.setState({
				[api_res]: json.data[0].list[0]
			},() => {
				if( !window.ws ) window.WebSocketDemo( this.state.sport );
				// 註冊訂閱ID
				const registerId = [this.state?.game_res?.series?.id]
				const wsMsg = {
					"action":"register",
					"channel":'match',
					"player": window.player,
					"game_id": parseInt(this.state.sport),
					"series": registerId // 要註冊的賽事
				}
	
				// 當頁面重新Loading的時候有可能ws還沒連線好
				var detectWsConnect = null
				detectWsConnect = setInterval(() => {
					if( window.socket_status === true && window?.ws?.readyState === 1 ) {
						console.log('ws game send -> ')
						console.log(wsMsg)
						window.ws.send(JSON.stringify(wsMsg));
	
						window.ws.onmessage = null
						window.ws.onmessage = (message) => {
							var msg = JSON.parse(message.data);
							var originalData = this?.state?.game_res
							var findData = originalData
				
							if ( !originalData ) return;
				
							// 控盤
							if( msg.channel === 'risk') {
								// pos
								let pos = JSON.parse(msg.data.pos)
								Object.entries(pos).forEach(([k, v]) => {
									// 改變risk 
									const targetRateList = originalData.rate[msg.data.game_priority];
										for (let i = 0; i < targetRateList.length; i++) {
											const rateData = targetRateList[i];
											const rateKeys = Object.keys(rateData.rate);
											if (rateKeys.length > k) {
												// 找到第 k 比資料，設置 risk 
												this.setState(() => {
													rateData.rate[rateKeys[k]].risk = v
													return { rateData }
												})
											}
										}
								});
							}
							
							// 比分更新
							if(msg.action === 'update' && msg.channel === 'match' ) {
								var updateHome = msg.data.teams.find(item => item.index === 1)
								var updateAway = msg.data.teams.find(item => item.index === 2)
								var updateHomeScore = updateHome.total_score
								var updateAwayScore = updateAway.total_score
				
								if( originalData ) {
									var oldHomeScore = originalData.teams.find( item => item.index === 1 )
									var oldAwayScore = originalData.teams.find( item => item.index === 2 )
				
									// 分數上升樣式
									if( updateHomeScore > oldHomeScore.total_score ) {
										$('span[scoretag="home_score"]').addClass('raiseScore')
									}
									if( updateAwayScore > oldAwayScore.total_score ) {
										$('span[scoretag="away_score"]').addClass('raiseScore')
									}
				
									// 三秒後移除上升樣式
									setTimeout(() => {
										$('span[scoretag]').removeClass('raiseScore')
									}, 3000);
				
									this.setState(() => {
										oldHomeScore.total_score = updateHomeScore // 客隊分數
										oldHomeScore.scores = updateHome.scores // 主隊局數更新
										oldAwayScore.total_score = updateAwayScore // 客隊分數
										oldAwayScore.scores = updateAway.scores // 客隊局數更新
										originalData.status = msg.data.status // 狀態更新
										return { originalData }
									})
								}
							}
				
							if ( ( msg.action === 'update' || msg.action === 'update-B' ) && msg.channel === 'match-group') {
								var isAppendNewBet = false
								var isAppendNewRate = false
								if( originalData?.rate[msg.game_priority] !== undefined ) { 
									// 原本就有的game_priority
									originalData = originalData.rate[msg.game_priority].find(item => item.rate_id === msg.rate_id)
									if ( originalData ) { 
										// 有rate_id
										msg.data.forEach(e => {
				
											if ( originalData.updated_at > e.updated_at ) return;
				
											let itemData = originalData.rate[e.id]
											let originalRate = itemData.rate
											let originalStatus = itemData.status
											let updateRate = e.rate
											let updateStatus = e.status
											let updateRisk = e.risk
				
											// 更新state data
											this.setState(() => {
												itemData.risk = updateRisk 
												itemData.rate = updateRate 
												itemData.status = updateStatus
												originalData.status = msg.status
												originalData.updated_at = msg.data[0].updated_at
												return { itemData, originalData }
											})
				
											// 賠率上升
											if( updateRate > originalRate ) {
												// 加上賠率變化樣式並更改賠率
												$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + '] .odd').html(updateRate)
												$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + ']').addClass('raiseOdd')
								
												// 三秒後移除
												setTimeout(() => {
													$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + ']').removeClass('raiseOdd')
												}, 3000);
											}
				
											// 賠率下降
											if( updateRate < originalRate ) {
												// 加上賠率變化樣式並更改賠率
												$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + '] .odd').html(updateRate)
												$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + ']').addClass('lowerOdd')
								
												// 三秒後移除
												setTimeout(() => {
													$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + ']').removeClass('lowerOdd')
												}, 3000);
											}
										})
										
									} else {
										isAppendNewBet = true
										isAppendNewRate = true
									}
								} else {
									isAppendNewBet = true
								}
				
								if ( isAppendNewBet ) {
									let bet_name = langText.MatchContent.game_priority[msg.game_priority]
									let insertRateData = msg.data.reduce((acc, item) => {
										acc[item.id] = item;
										item['name'] = item.name_cn // ws通知沒有語系
										item['value'] = item.name_cn // ws通知沒有語系
										return acc;
									}, {});
				
									let insertData = {
										rate_id: msg.rate_id,
										game_priority: msg.game_priority,
										name: bet_name,
										rate: insertRateData,
										status: msg.status,
										updated_at: msg.data[0].updated_at
									}
				
									if( !isAppendNewRate ) {
										this.setState(() => {
											findData.rate[msg.game_priority] = []
											return { findData }
										});
									}
									
									this.setState(() => {
										findData.rate[msg.game_priority] = findData.rate[msg.game_priority].concat([insertData])
										return { findData }
									});
								}
							}
						};
						clearInterval(detectWsConnect)
					}
				}, 500);
			})
		} else {
			this.setState({
				[api_res]: json,
			})
		}

		if(json.status === 0) {
			this.setState(prevState => ({
				toastMsg: [...prevState.toastMsg, json.message],
			}))
		}
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
	
	// 頁面初始
	componentDidMount() {
		window.sport = Cookies.get('sport', { path: '/' })
		this.caller(this.state.game_api, 'game_res')
		this.caller(this.state.accout_api, 'account_res')

		// 若是刷新遊戲頁面 則一秒後移除旋轉動畫
		setTimeout(() => {
			this.setState({
				isGameRefreshing: false
			})
		}, 1000);
	}


	// ws通知
	updateData = ( msg ) => {
		// console.log(msg)
		msg.data.forEach(e => {
			let originalRate = $('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + '] .odd').html()
			let updateRate = e.rate
			if(updateRate > originalRate) {
				// 定位state資料
				let match = this.state.game_res.data[0]
				let type = match.rate[msg.game_priority].find(item => item.rate_id === msg.rate_id)
				let item = null

				// 改變state
				if(msg.game_priority !== 7 && msg.game_priority !== 8 ){
					item = type.rate[e.id]
				} else {
					// 遍歷 rate 物件的子陣列
					for (const key in type.rate) {
						if (type.rate.hasOwnProperty(key)) {
							// 在子陣列中尋找符合條件的資料
							item = type.rate[key].find(item => item.id === e.id);
							if (item) {
								break; // 如果找到資料，停止迴圈
							}
						}
					}
				}

				this.setState(() => {
					item.rate = updateRate
					item.status = e.status
					return { item }
				})
				
				// 先移除現有樣式
				$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + ']').removeClass('raiseOdd')
				$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + ']').removeClass('lowerOdd')

				// 加上賠率變化樣式並更改賠率
				$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + '] .odd').html(updateRate)
				$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + ']').addClass('raiseOdd')
				
				// 三秒後移除
				setTimeout(() => {
					$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + ']').removeClass('raiseOdd')
				}, 3000);
			}

			if(updateRate < originalRate) {
				// 定位state資料
				let match = this.state.game_res.data[0]
				let type = match.rate[msg.game_priority].find(item => item.rate_id === msg.rate_id)
				let item = null

				// 改變state
				if(msg.game_priority !== 7 && msg.game_priority !== 8 ){
					item = type.rate[e.id]
				} else {
					// 遍歷 rate 物件的子陣列
					for (const key in type.rate) {
						if (type.rate.hasOwnProperty(key)) {
							// 在子陣列中尋找符合條件的資料
							item = type.rate[key].find(item => item.id === e.id);
							if (item) {
								break; // 如果找到資料，停止迴圈
							}
						}
					}
				}

				this.setState(() => {
					item.rate = updateRate
					item.status = e.status
					return { item }
				})
				
				// 先移除現有樣式
				$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + ']').removeClass('raiseOdd')
				$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + ']').removeClass('lowerOdd')

				// 加上賠率變化樣式並更改賠率
				$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + '] .odd').html(updateRate)
				$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + ']').addClass('lowerOdd')
				
				// 三秒後移除
				setTimeout(() => {
					$('div[bet_match=' + msg.match_id + '][bet_type=' + msg.rate_id + '][bet_type_item=' + e.id + ']').removeClass('lowerOdd')
				}, 3000);
			}
		});
	}


	// 下拉更新
	handleRefresh =() => {
		console.log('handleRefresh')
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
						<GameMain data={data} getBetDataCallBack={this.getBetData} />
					</PullToRefresh>
					<CommonCalculator isOpenCal={this.state.isOpenCal} data={betData} CloseCal={this.CloseCal} api_res={this.state.account_res} isRefrehingBalance={this.state.isRefrehingBalance} callBack={this.refreshWallet} />
				</>
			:
			<CommonLoader/>
		)
	}
};

export default Game;