@extends('layout.app')

@section('content')
<div id ="noticePage" class="h-100 notice-con">
        <div class="row notice-row">
            <div class="col-xl-2 col-lg-2 col-md-2 col-2 nopad notice-col-left">
                <nav>
                    <div class="nav nav-tabs flex-column" id="nav-tab" role="tablist">
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
							<div class="tab-pane active" id="tab_All" role="tabpanel" aria-labelledby="tabAll"></div>
							<div class="tab-pane" id="tab_Syst" role="tabpanel" aria-labelledby="tabSyst"></div>
                            <div class="tab-pane" role="tabpanel" template="tabPanelTemplate" hidden></div>
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
		//sportlistD
		if (sportListD && sportListD.data) {
			sportListD.data.forEach((sportItem, sportIndex) => {
				console.log(sportItem, sportIndex);
				createNavTabButton(sportItem, sportIndex);
				createTabPanel(sportItem, sportIndex);
			});
		}

	}
	
	function createNavTabButton(sportItem, sportIndex) {
		const NavTabBtn = $('button[template="NavTabTemplate"]').clone().removeAttr('hidden').removeAttr('template');

		NavTabBtn.attr('id', 'tab' + sportItem.sport_id);
		NavTabBtn.attr('data-bs-target', '#tab_' + sportItem.sport_id);
		NavTabBtn.attr('aria-controls', '#tab_' + sportItem.sport_id);
		NavTabBtn.html(sportItem.name);

		$('#navSyst.nav-link').after(NavTabBtn);
	}

	function createTabPanel(sportItem, sportIndex) {
		const tabPanel = $('div[template="tabPanelTemplate"]').clone().removeAttr('hidden').removeAttr('template');
		tabPanel.attr('id', 'tab_' + sportItem.sport_id);
		tabPanel.attr('aria-labelledby', 'tab' + sportItem.sport_id);
		tabPanel.html(sportItem.name);

		$('#navSyst.tab-pane').after(tabPanel);
	}

	$("button.nav-link").click(function() {
        $(".notice-tab-con").animate({ scrollTop: 0 }, "smooth");
        console.log("top");   
    });

</script>
@endpush