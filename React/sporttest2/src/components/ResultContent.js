import React from "react";
import pako from 'pako'
import GetIni from './AjaxFunction'
import ResultContentCard from './ResultContentCard';
import { TbArrowBigUpFilled } from 'react-icons/tb';
import InfiniteScroll from 'react-infinite-scroll-component';
import { langText } from "../pages/LanguageContext";

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
	padding: '0.3rem',
	opacity: 0.7
}

const PageContainer = {
  overflowY: 'auto',
  overflowX: 'hidden',
  width: '100%',
  height: 'calc(100% - 1px)',
  background: 'rgb(65, 91, 90)',
}

const ResultMainContainer = {
  width: '100%',
  background: 'rgb(65, 91, 90)',
  overflow: 'hidden scroll',
  padding: '0 0.5rem',
  height: 'calc(100% - 14rem)',
  fontWeight: '600'
}


class ResultContent extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
      		apiUrl: 'https://sportc.asgame.net/api/v2/result_index?token=',
			sport_id: this.props.sport_id,
      		page: 1,
			swiperIndex: 0,
      		fetchMoreLock: 0, // default close
			hasMore: true
		};
	}

	// 頁面初始化 0 / 往下滑加載下一頁 1 / 按上面分類加載資料 2
	async caller(apiUrl, callerType = 0, page=1) {
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

		const newData = json.data
		var data = []
		switch (callerType) {
			case 0:case 2:
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
			data: data,
			page: page,
			fetchMoreLock: 0
		});

		if(Object.keys(newData).length === 0 || Object.keys(newData).length !== 20 || Object.keys(newData).length > 20){
			this.setState({
				hasMore: false
			})
		} else {
			this.setState({
				hasMore: true
			})
		}
	}
	
	// ini
	componentDidMount() {
		this.caller(this.state.apiUrl + window.token +'&player=' + window.player + '&page=' + this.state.page + '&sport=' + window.sport)
	}

	// sport_id onchange
	componentDidUpdate(prevProps) {
		if (prevProps.sport_id !== this.props.sport_id) {
			this.caller(this.state.apiUrl + window.token +'&player=' + window.player + '&page=' + this.state.page + '&sport=' + window.sport, 2)
			this.setState({
				toggleStates: {},
				sport_id: window.sport,
				data: null,
				hasMore: true
			});
		}
	}

	// 滑到最上面
	scrollToTop = () => {
		document.getElementById('ResultMain').scrollTo({top: 0, behavior: 'smooth'});
	}

	// 右邊滑動玩法區塊
	swiperTabHandler = (swiperIndex) => {
		this.setState({
			swiperIndex: swiperIndex
		})
	}

  	// call api載入下一頁資料
	getNewPage = () => {
		if(this.state.fetchMoreLock === 0) {
			this.setState({
				fetchMoreLock: 1
			},() => {
				let nowPage = parseInt(this.state.page)
				let nextPage = nowPage + 1
				this.caller(this.state.apiUrl + window.token +'&player=' + window.player + '&page=' + nextPage + '&sport=' + this.state.sport_id, 1, nextPage)
			})
		}
	}
	render() {
		const { data } = this.state
		return (
			<div style={ResultMainContainer} id='ResultMainContainer'>
				<div id="ResultMain" style={ PageContainer }>
				{
					data ?
						data && data.length > 0 && 
						<InfiniteScroll
							dataLength={ data }
							next={this.getNewPage}
							hasMore={this.state.hasMore}
							loader={
								<div className="loading loading04">
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
						scrollableTarget="ResultMain">
						{
							data &&
							data.map((v) => (
								<ResultContentCard
									key={v.fixture_id}
									swiperIndex={this.state.swiperIndex}
									swiperTabCallBack={this.swiperTabHandler}
									data={v}
								/>
							))
						}
						{
							!this.state.hasMore &&
							<h5 className='mt-2 text-center fw-600' style={{ color: 'rgb(196, 211, 211)' }}>{langText.CommonLogs.nomoredata}</h5>
						}
						</InfiniteScroll>
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
			</div>
		);
	}
}

export default ResultContent;