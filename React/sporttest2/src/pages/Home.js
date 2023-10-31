import React from "react";
import pako from 'pako'
import PullToRefresh from 'react-simple-pull-to-refresh';
import IndexMarquee from "../components/IndexMarquee"
import IndexMatchList from "../components/IndexMatchList"
import IndexNavbar from "../components/IndexNavbar"
import IndexCarousel from "../components/IndexCarousel"
import CommonSliderUser from '../components/CommonSliderUser'
import GetIni from '../components/AjaxFunction'
import CommonLoader from '../components/CommonLoader'
import CommonFooter from "../components/CommonFooter"
import { GrMenu } from "react-icons/gr";


const slideIconStyle = {
    position: 'absolute',
    top: '0.3rem',
    right: '0.5rem',
    fontSize: '1.5rem',
}


class Home extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			accout_api: 'https://sportc.asgame.net/api/v2/common_account?token=' + window.token+ '&player=' + window.player+ '',
			carousel_api: 'https://sportc.asgame.net/api/v2/index_carousel?token=' + window.token+ '&player=' + window.player+ '',
			marquee_api: 'https://sportc.asgame.net/api/v2/index_marquee?token=' + window.token+ '&player=' + window.player+ '',
			indexMatchList_api: 'https://sportc.asgame.net/api/v2/index_match_list?token=' + window.token+ '&player=' + window.player+ '',
			toastMsg: [],
			isRefrehingBalance: false
		};
	}

	async caller(apiUrl, api_res, type = 0) {
		const start = Date.now(); // 记录开始时间
		const json = await GetIni(apiUrl);
		const elapsedTime = Date.now() - start; // 计算经过的时间

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

		this.setState({
			[api_res]: json,
		})


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

		if(json.status === 1) {
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
		setTimeout(() => { // 有時候loading太快，閃一下就過了畫面不好看，故至少執行一秒
			this.setState({
				ready: true
			})
		}, 1000);

		this.loadData(); // 初始加载数据

		this.renderInterval = setInterval(() => {
			this.caller(this.state.indexMatchList_api, 'indexMatchList_res');
		}, 5000);
	}

	loadData() {
		// 呼叫所需api
		this.caller(this.state.accout_api, 'account_res');
		this.caller(this.state.carousel_api, 'carousel_res');
		this.caller(this.state.marquee_api, 'marquee_res');
		this.caller(this.state.indexMatchList_api, 'indexMatchList_res');
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
		// 下拉刷新時，重新初始化頁面
		this.componentDidMount()
		// 返回一個Promise對象，用於通知組件刷新操作已經完成
		return new Promise(resolve => setTimeout(resolve, 1000));
	}

	handleCallBMethod = () => {
		// 調用slider中的方法
		this.sliderRef.handleMenuChange();
	}

	render() {
		return (
			this.state.ready === true ?
				<>
					<div className="h-100">
						<PullToRefresh onRefresh={this.handleRefresh} pullingContent={''}>
							<IndexNavbar />
							<IndexCarousel api_res={this.state.carousel_res} />
							<IndexMarquee api_res={this.state.marquee_res} />
							<IndexMatchList api_res={this.state.indexMatchList_res} />
							<div onClick={this.handleCallBMethod} style={slideIconStyle}><GrMenu /></div>
						</PullToRefresh>
						<CommonSliderUser ref={(ref) => (this.sliderRef = ref)} api_res={this.state.account_res} isRefrehingBalance={this.state.isRefrehingBalance} callBack={this.refreshWallet} />
					</div>
					<CommonFooter index={3} />
				</>
			:
			// 頁面loading樣式
			<CommonLoader />
		);
	}
};

export default Home;