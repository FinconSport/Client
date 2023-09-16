import React from "react";
import $ from 'jquery';
import pako from 'pako'
import SlideToggle from "react-slide-toggle";
import GetIni from './AjaxFunction'
import MatchContentCard from './MatchContentCard_m';
import { langText } from "../pages/LanguageContext";
import { TbArrowBigUpFilled } from 'react-icons/tb';
import { IoIosArrowForward, IoIosArrowDown } from 'react-icons/io';
import 'swiper/css';


const ToTopStyle = {
	right: '0.5rem',
    bottom: '7rem',
	zIndex: 1,
	position: 'absolute',
	background: '#c79e42 ',
	color: 'white',
	borderRadius: '50%',
	fontSize: '2.5rem',
	padding: '0.3rem'
}

const MatchMainContainer = {
    width: '100%',
    background: 'rgb(65, 91, 90)',
    overflow: 'hidden scroll',
    padding: '0.5rem',
    height: 'calc(100% - 14rem)',
	fontWeight: '600'
}

const MatchCardTitle = {
	background: 'rgb(196, 211, 211)',
	borderRadius: '15px',
	padding: '0.5rem 1rem',
    marginBottom: '0.5rem',
	position: 'relative'
}

const MatchCardTitleArrow = {
	position: 'absolute',
	right: '0.5rem',
	top: '0.5rem',
	fontSize: '1.5rem',
}

const menuArr = ['early', 'living']


class MatchContent extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			menu_id: this.props.menu_id,
			sport_id: this.props.sport_id,
			swiperIndex: 0,
			betData: null,
			isOpenCal: false,
			toggleStates: {}, // 用一个对象来存储每个SlideToggle的开关状态
		};
		
		// this.updateData = this.updateData.bind(this); // 綁定 this
	}

	// 頁面初始化 0 / 往下滑加載下一頁 1 / 按上面分類加載資料 2
	async caller(apiUrl, callerType = 0) {
		if( callerType === 2 ) {
			// 先滑到最上面再撈資料
			this.scrollToTop()
		}
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

		var registerId = []
		Object.values(json.data).forEach((item) => {
			item.forEach(ele => {
				registerId.push(ele.series.id)
			});
		});

		this.setState({
			data: json.data,
		},() => {
			// 註冊訂閱ID
			const wsMsg = {
				"action":"register",
				"channel":'match',
				"player": window.player,
				"game_id": parseInt(window.sport),
				"series": registerId // 要註冊的賽事
			}

			// 當頁面重新Loading的時候有可能ws還沒連線好
			var detectWsConnect = null
			detectWsConnect = setInterval(() => {
				console.log('connect m_order')
				if( window.socket_status === true && window?.ws?.readyState === 1 ) {
					console.log('ws m_order send -> ')
					console.log(wsMsg)
					window.ws.send(JSON.stringify(wsMsg));
					clearInterval(detectWsConnect)

					window.ws.onmessage = null
					window.ws.onmessage = (message) => {
						var msg = JSON.parse(message.data);
						var match = msg.match_id
						if( msg.channel === 'risk' ) match = msg.data.match_id
						if ( !this.state.data ) return;
			
						// 定位舊資料
						var originalData = null
						var findData = null
						if( msg.channel === 'match' || msg.channel === 'match-group' || msg.channel === 'risk' ) {
							this.state.data[menuArr[window.menu]].forEach(ele => {
								let temp1 = ele.list.find(item => item.match_id === match )
								if( temp1 ) {
									originalData = temp1
									findData = temp1
								}
							});
						}
			
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
									$('div[cardid="' + msg.match_id + '"] .teamScore[index="1"]').addClass('raiseScore')
								}
								if( updateAwayScore > oldAwayScore.total_score ) {
									$('div[cardid="' + msg.match_id + '"] .teamScore[index="2"]').addClass('raiseScore')
								}
			
								// 三秒後移除上升樣式
								setTimeout(() => {
									$('div[cardid="' + msg.match_id + '"] .teamScore').removeClass('raiseScore')
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
					};
						}
					}, 500);
				})
		
		
	}

	// 初始化資料 + ws handler
	componentDidMount() {
		console.log('componentDidMount')
		this.caller(this.props.apiUrl + '&sport_id=' + this.state.sport_id)
	}
	
	// 偵測menu改變
	componentDidUpdate(prevProps) {
		if (prevProps.sport_id !== this.props.sport_id) {
			this.caller(this.props.apiUrl + '&sport_id=' + this.props.sport_id, 2);
	  }
	}

	// 滑到最上面
	scrollToTop = () => {
		document.getElementById('MatchMainContainer').scrollTo({top: 0, behavior: 'smooth'});
	}

	// 右邊滑動玩法區塊
	swiperTabHandler = (swiperIndex) => {
		this.setState({
			swiperIndex: swiperIndex
		})
	}

	// 投注資料
	getBetData = (betData) => {
		this.props.callBack(betData)
	}

	toggle(key) {
		this.setState(prevState => ({
		  toggleStates: {
			...prevState.toggleStates,
			[key]: !prevState.toggleStates[key] // 使用键来标识每个SlideToggle的状态
		  }
		}));
	  }

	render() {
		const { data } = this.state
		
		return (
			<div style={MatchMainContainer} id='MatchMainContainer'>
			{
				data && data[menuArr[window.menu]] &&
				Object.entries(data[menuArr[window.menu]]).map(([k, v]) => {
				return (
				<SlideToggle key={k} duration={500}>
					{({ toggle, setCollapsibleElement }) => (
					<>
						<div style={MatchCardTitle} onClick={() => { toggle();this.toggle(k) }}>
						{ v.series.name }({ v.list.length })
						{this.state.toggleStates[k] ? <IoIosArrowForward style={MatchCardTitleArrow} /> : <IoIosArrowDown style={MatchCardTitleArrow} />}
						</div>
						<div className='row m-0' ref={setCollapsibleElement}>
						{v.list.map(ele => {
							const selectedM = this.props.sendOrderData.bet_data.find(item => item.bet_match === ele.match_id);
							return(
								((window.menu === 0 && ele.status === 1) || (window.menu === 1 && ele.status === 2)) && (
									<MatchContentCard
										key={ele.match_id}
										swiperIndex={this.state.swiperIndex}
										swiperTabCallBack={this.swiperTabHandler}
										getBetDataCallBack={this.getBetData}
										data={ele}
										selectedM={selectedM}
									/>
									)	
							)
						})}
						</div>
					</>
					)}
				</SlideToggle>
				);
			})}
			<TbArrowBigUpFilled onClick={this.scrollToTop} style={ ToTopStyle }/>
			</div>
		);
	}
}

export default MatchContent;