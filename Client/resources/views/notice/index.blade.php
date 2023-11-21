@extends('layout.app')

@section('content')
	<div id ="noticePage" class="h-100 notice-con">
        <div class="row notice-row">
            <div class="col-xl-2 col-lg-2 col-md-2 col-2 nopad notice-col-left">
                <nav>
                    <div class="nav nav-tabs flex-column" id="nav-tab" role="tablist">
						<h3>{{ trans('notice.main.notice') }}</h3>
						<button class="nav-link active" id="tabAll" data-bs-toggle="tab" data-bs-target="#tab_All" type="button" role="tab" aria-controls="#tab_All" aria-selected="true">{{ trans('notice.main.all') }}</button>
						<button class="nav-link" id="tabSystemNotice" data-bs-toggle="tab" data-bs-target="#tab_SystemNotice" type="button" role="tab" aria-controls="#tab_SystemNotice" aria-selected="false">{{ trans('notice.main.system') }}</button>
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
							<div class="tab-pane" id="tab_SystemNotice" role="tabpanel" aria-labelledby="tabSystemNotice">
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

		//noticelistD
		if (noticeListD && noticeListD.data) {
        	noticeListD.data.forEach((noticeItem, noticeIndex) => {
            	createTabContent(noticeItem, noticeIndex);
				checkEmptyTabPanes();			
			});
		}
		
	}

	function createTabBtnAndContainer(sportItem, sportIndex) {
		//tab btn
		const NavTabBtn = $('button[template="NavTabTemplate"]').clone().removeAttr('hidden').removeAttr('template');
		NavTabBtn.attr('id', 'tab' + sportItem.sport_id);
		NavTabBtn.attr('data-bs-target', '#tab_' + sportItem.sport_id);
		NavTabBtn.attr('aria-controls', 'tab_' + sportItem.sport_id);
		NavTabBtn.html(sportItem.name);
		$('#nav-tab').append(NavTabBtn);

		//tab container
		const tabPanel = $('<div class="tab-pane" role="tabpanel"></div>');
		tabPanel.attr('id', 'tab_' + sportItem.sport_id);
		tabPanel.attr('aria-labelledby', 'tab' + sportItem.sport_id);
		tabPanel.append('<div class="tab-card-container"></div>');
		$('#nav-tabContent').append(tabPanel);
	}

	function createTabContent(noticeItem, noticeIndex) {
		noticeItem.forEach((item) => {
			const sportId = item.sport_id;

			// Filter the sportListD data for the current sport_id
			const matchingSport = sportListD.data.find((sport) => sport.sport_id === sportId);

			if (matchingSport) {
				const tabContent = $(`#tab_${sportId} .tab-card-container`);
				const noticeHtml = createNoticeHtml(item);
				tabContent.append(noticeHtml);
			}

			// If sport_id is 0, append to #tab_SystemNotice tab
			if (sportId === 0) {
				const systTabContent = $('#tab_SystemNotice .tab-card-container');
				const noticeHtml = createNoticeHtml(item);
				systTabContent.append(noticeHtml);
			}

			// Append to #tab_All tab
			const allTabContent = $('#tab_All .tab-card-container');
			const noticeHtml = createNoticeHtml(item);
			allTabContent.append(noticeHtml);
		});
	}

	// notice content
	function createNoticeHtml(noticeItem) {
		return `
			<div class="tab-card">
				<div class="tab-card-title"><p class="noticetitle">${noticeItem.title}</p><p class="noticetime">${noticeItem.create_time}</p></div>
				<div class="tab-card-content"><p>${noticeItem.context}</p></div>
			</div>
		`;
	}

	// no more data function
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