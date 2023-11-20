@extends('layout.app')

@section('content')
	<div id ="noticePage" class="h-100 notice-con">
        <div class="row notice-row">
            <div class="col-xl-2 col-lg-2 col-md-2 col-2 nopad notice-col-left">
                <nav>
                    <div class="nav nav-tabs flex-column" id="nav-tab" role="tablist">
						<h3>{{ trans('notice.main.notice') }}</h3>
						<button class="nav-link active" id="tabAll" data-bs-toggle="tab" data-bs-target="#tab_All" type="button" role="tab" aria-controls="#tab_All" aria-selected="true">{{ trans('notice.main.all') }}</button>
						<button class="nav-link" id="tabSyst" data-bs-toggle="tab" data-bs-target="#tab_Syst" type="button" role="tab" aria-controls="#tab_Syst" aria-selected="false">{{ trans('notice.main.system') }}</button>
                        <button class="nav-link" data-bs-toggle="tab" type="button" role="tab" aria-selected="false" template="NavTabTemplate" hidden></button>          
                    </div>
                </nav>
            </div>
            <div class="col-xl-10 col-lg-10 col-md-10 col-10 notice-col-right">
            <div class="notice-tab">
                <div class="notice-tab-con">
                        <div class="tab-content" id="nav-tabContent">
							<div class="tab-pane active" id="tab_All" role="tabpanel" aria-labelledby="tabAll">
								<div class="tab-card-container"></div>
							</div>
							<div class="tab-pane" id="tab_Syst" role="tabpanel" aria-labelledby="tabSyst">
								<div class="tab-card-container"></div>
							</div>
                            <div class="tab-pane" role="tabpanel" template="tabPanelTemplate" hidden>
								<div class="tab-card-container"></div>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<link href="{{ asset('css/notice.css?v=' . $current_time) }}" rel="stylesheet">
<!-- <link href="{{ asset('css/notice.css?v=' . $system_config['version']) }}" rel="stylesheet"> -->
<style>
	/* 寫入頁面限定CSS */
</style>
@endSection

@push('main_js')
<script src="{{ asset('js/bootstrap.min.js?v=' . $system_config['version']) }}"></script>
<script>
	// detect ini ajax
	var isReadyNoticeInt = null
	var isReadyNotice = false

	// notice list data
	var noticeListD = {}

	var noticeListD1 = {
		"status": 1,
		"data": [
			[
				{
					"sport_id": 6046,
					"title": "【賽事取消-足球/哥倫比亞足球甲級聯賽】",
					"context": "賽事已取消 1月1日 08:00 AT國民隊 vs. 圖利馬",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【賽事重複-籃球/美國職業籃球賽】",
					"context": "賽事與#11760616重複故取消 1月1日 08:00 波特蘭拓荒者 vs. 洛杉磯湖人",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【賽事重複-籃球/美國職業籃球賽】",
					"context": "賽事與#11620382重複故取消 1月1日 08:00 猶他爵士 vs. 鳳凰城太陽",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【賽事重複-籃球/美國職業籃球賽】",
					"context": "賽事與#11760631重複故取消 1月1日 08:00 洛杉磯快艇 vs. 侯斯頓火箭",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【賽事取消-足球/西班牙足球甲級聯賽】",
					"context": "賽事已取消 1月1日 08:00 馬略卡 vs. 卡迪斯",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【賽事取消-足球/西班牙足球甲級聯賽】",
					"context": "賽事已取消 1月1日 08:00 馬略卡 vs. 卡迪斯",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【賽事取消-籃球/美國職業籃球賽】",
					"context": "賽事已取消 1月1日 08:00 洛杉磯湖人 vs. 孟斐斯灰熊",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【賽事取消-籃球/美國職業籃球賽】",
					"context": "賽事已取消 1月1日 08:00 丹佛金塊 vs. 洛杉磯快艇",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【賽事取消-籃球/美國職業籃球賽】",
					"context": "賽事已取消 1月1日 08:00 金州勇士 vs. 明尼蘇達灰狼",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【賽事取消-足球/哥倫比亞足球甲級聯賽】",
					"context": "賽事已取消 1月1日 08:00 馬格達萊納聯盟 vs. 曼特寧獨立",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【賽事取消-足球/哥倫比亞足球甲級聯賽】",
					"context": "賽事已取消 1月1日 08:00 卡利阿美利加 vs. 布卡拉曼格",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【選手次序錯誤-棒球/日本職業棒球】",
					"context": "已賽事選手錯序故取消 1月1日 08:00 阪神虎 vs. 歐力士猛牛",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【選手次序錯誤-籃球/FIBA歐洲盃】",
					"context": "已賽事選手錯序故取消 1月1日 08:00 開姆尼茨 vs. 英雄登博斯",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【選手次序錯誤-棒球/日本職業棒球】",
					"context": "已賽事選手錯序故取消 1月1日 08:00 歐力士猛牛 vs. 阪神虎",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【賽事取消-足球/法國甲組聯賽】",
					"context": "賽事已取消 1月1日 08:00 馬賽 vs. 里昂",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【賽事取消-足球/法國甲組聯賽】",
					"context": "賽事已取消 1月1日 08:00 馬賽 vs. 里昂",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "【選手次序錯誤-棒球/日本職業棒球】",
					"context": "已賽事選手錯序故取消 1月1日 08:00 阪神虎 vs. 歐力士猛牛",
					"create_time": "2023-11-20 14:54:17"
				},
				{
					"sport_id": 0,
					"title": "title 222",
					"context": "活動公告 - 世界盃期間,我們將抽出幾位幸運兒",
					"create_time": "2023-04-04 09:10:00"
				},
				{
					"sport_id": 0,
					"title": "title 111",
					"context": "系統公告 - Fourtune 365 首家菲律賓線上體育上線啦",
					"create_time": "2023-04-04 09:09:53"
				}
			]
		],
		"message": "SUCCESS_API_INDEX_NOTICE_01",
		"gzip": true
	}
	const noticeList_api = '/api/v2/index_notice'

	$(document).ready(function() {

		// ini data from ajax
		caller(noticeList_api, commonCallData, noticeListD) // noticeListD

		// check if api are all loaded every 500 ms 
		isReadyNoticeInt = setInterval(() => {
			if (noticeListD.status === 1) {
				isReadyNotice = true;
			}
			if (isReadyNotice && isReadyCommon) {
				$('#dimmer').dimmer('hide'); // hide loading
				$('#wrap').css('opacity', 1); // show the main content
				renderView()
				clearInterval(isReadyNoticeInt); // stop checking
			}
		}, 500);
	});


	function renderView() {
		// sportlistD
		if (sportListD && sportListD.data) {
			sportListD.data.forEach((sportItem, sportIndex) => {
				createTabBtnAndContainer(sportItem, sportIndex);
			});
		}
    	// noticelistD
    	if (noticeListD1 && noticeListD1.data) {
        	noticeListD1.data.forEach((noticeItem, noticeIndex) => {
            	createTabContent(noticeItem, noticeIndex);
				checkEmptyTabPanes();			
			});
		}
	}

	function createTabBtnAndContainer(sportItem, sportIndex) {
		const NavTabBtn = $('button[template="NavTabTemplate"]').clone().removeAttr('hidden').removeAttr('template');
		NavTabBtn.attr('id', 'tab' + sportItem.sport_id);
		NavTabBtn.attr('data-bs-target', '#tab_' + sportItem.sport_id);
		NavTabBtn.attr('aria-controls', 'tab_' + sportItem.sport_id);
		NavTabBtn.html(sportItem.name);
		$('#nav-tab').append(NavTabBtn);

		const tabPanel = $('<div class="tab-pane" role="tabpanel"></div>');
		tabPanel.attr('id', 'tab_' + sportItem.sport_id);
		tabPanel.attr('aria-labelledby', 'tab' + sportItem.sport_id);

		$('#nav-tabContent').append(tabPanel);
	}

	function createTabContent(noticeItem, noticeIndex) {
		const sportId = noticeItem[0].sport_id;
		const tabContent = $('#tab_' + sportId + ' .tab-card-content');

		// Append to the specific sport_id tab
		if (sportId !== undefined) {
			noticeItem.forEach((item) => {
				const noticeHtml = createNoticeHtml(item);
				tabContent.append(noticeHtml);
			});
		}

		// If sport_id is 0, append to #tab_Syst tab
		if (sportId === 0) {
			const systTabContent = $('#tab_Syst .tab-card-container');
			noticeItem.forEach((item) => {
				const noticeHtml = createNoticeHtml(item);
				systTabContent.append(noticeHtml);
			});
		}

		// Append to #tab_All tab
		noticeItem.forEach((item) => {
			const noticeHtml = createNoticeHtml(item);
			$('#tab_All .tab-card-container').append(noticeHtml);
		});
	}

	function createNoticeHtml(noticeItem) {
		return `
			<div class="tab-card">
				<div class="tab-card-title"><p class="noticetitle">${noticeItem.title}</p><p class="noticetime">${noticeItem.create_time}</p></div>
				<div class="tab-card-content"><p>${noticeItem.context}</p></div>
			</div>
		`;
	}

	function checkEmptyTabPanes() {
		$('.tab-pane').each((_, tabPane) => {
			if (!$(tabPane).find('.tab-card').length) {
				$(tabPane).append('<div class="no-tab-card-text">{{ trans("match.main.nomoredata") }}</div>');
			}
		});
	}

	$("button.nav-link").click(function() {
        $(".notice-tab-con").animate({ scrollTop: 0 }, "smooth");
    });

</script>
@endpush