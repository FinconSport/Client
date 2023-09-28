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
var u = null
var o = null

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

	// 頁面初始化 0 / update 1 / 按上面分類加載資料 2
	async caller(apiUrl, callerType = 0) {
		if( callerType === 2 ) {
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

		if( callerType === 1 ) {
			var oldData = this.state.data[menuArr[window.menu]][window.sport]?.list
			var updateData = json.data[menuArr[window.menu]][window.sport]?.list
			if( updateData ) this.findDifferences(oldData, updateData)
		}

		// var registerId = []
		// Object.values(json.data).forEach((item) => {
		// 	item.forEach(ele => {
		// 		registerId.push(ele.series.id)
		// 	});
		// });

		this.setState({
			data: json.data,
		}, () => {
			if( callerType !== 1) {
				clearInterval(window.ajaxInt)
				window.ajaxInt = setInterval(() => {
					this.caller(this.props.apiUrl + '&sport_id=' + window.sport, 1)
				}, 5000);
			}
		})
	}

	// ws function 
	// detect if there's still package need to be processed
	async processMessageQueueAsync() {
		while (true) {
			if (window.messageQueue.length > 0) {
				this.processMessageQueue(); // package process function
			} else {
				await this.sleep(2); // check after 2 ms
			}
		}
	}

	// sleep function to pause
	sleep = (ms) => {
		return new Promise(resolve => setTimeout(resolve, ms));
	}

	// package process function
	processMessageQueue = () => {
		const message = window.messageQueue.shift(); // to get the head pkg
		const msg = JSON.parse(message.data); // convert to json
		// setState to rerender




		// setState to rerender
	}
	// ws function
	
	// 初始化資料 + ws handler
	componentDidMount() {
		this.caller(this.props.apiUrl + '&sport_id=' + window.sport)

		// ws
		
	}
	
	// 偵測menu改變
	componentDidUpdate(prevProps) {
		if (prevProps.sport_id !== this.props.sport_id || prevProps.menu_id !== this.props.menu_id) {
			this.caller(this.props.apiUrl + '&sport_id=' + window.sport, 2);
			this.setState({
				toggleStates: {},
				sport_id: window.sport
			});
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
		const bet_data = this.props.sendOrderData
		return (
			<div style={MatchMainContainer} id='MatchMainContainer'>
				{
					data && data[menuArr[window.menu]][window.sport]?.list ?
					Object.entries(data[menuArr[window.menu]][window.sport].list).length > 0 ?
					Object.entries(data[menuArr[window.menu]][window.sport].list).map(([k, v]) => (
						<SlideToggle key={k} duration={500}>
						  {({ toggle, setCollapsibleElement }) => (
							<>
								<div style={MatchCardTitle} onClick={() => { this.toggle(k) }}>
									{ v.league_name }({ Object.keys(v.list).length })
									{this.state.toggleStates[k] ? <IoIosArrowForward style={MatchCardTitleArrow} /> : <IoIosArrowDown style={MatchCardTitleArrow} />}
								</div>
								<div className='row m-0' ref={setCollapsibleElement}>
									{Object.entries(v.list).map(([k2, v2]) => {
										const selectedM = bet_data.find(item => item.fixture_id === v2.fixture_id)
										return (
											<MatchContentCard
												series_name={v.league_name}
												key={v2.fixture_id}
												swiperIndex={this.state.swiperIndex}
												swiperTabCallBack={this.swiperTabHandler}
												getBetDataCallBack={this.getBetData}
												data={v2}
												isOpen={this.state.toggleStates[k] ? false : true}
												selectedM={ selectedM }
											/>
										);
									})}
								</div>

							</>
						  )}
						</SlideToggle>
					)) :
					<h5 className='mt-2 text-center fw-600' style={{ color: 'rgb(196, 211, 211)' }}>{langText.MatchContent.nomorematch}</h5>
					:
					<div className="loading loading04 text-white mt-5">
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
					</div>
				}
				<TbArrowBigUpFilled onClick={this.scrollToTop} style={ ToTopStyle }/>
			</div>
		);
	}
}

export default MatchContent;