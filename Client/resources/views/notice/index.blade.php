@extends('layout.app')

@section('content')
<div id ="noticePage" class="h-100 notice-con">
        <div class="row notice-row">
            <div class="col-xl-2 col-lg-2 col-md-2 col-2 nopad notice-col-left">
                <nav>
                    <div class="nav nav-tabs flex-column" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-baseball" data-bs-toggle="tab" data-bs-target="#navBaseball" type="button" role="tab" aria-controls="#navBaseball" aria-selected="true">{{ trans('rule.ruleTitles.baseball') }}</button>      
                        <button class="nav-link" id="nav-basketball" data-bs-toggle="tab" data-bs-target="#navBasketball" type="button" role="tab" aria-controls="#navBasketball" aria-selected="false">{{ trans('rule.ruleTitles.basketball') }}</button>
                        <button class="nav-link" id="nav-soccor" data-bs-toggle="tab" data-bs-target="#navSoccor" type="button" role="tab" aria-controls="#navSoccor" aria-selected="false">{{ trans('rule.ruleTitles.soccor') }}</button>          
                    </div>
                </nav>
            </div>
            <div class="col-xl-10 col-lg-10 col-md-10 col-10 notice-col-right">
            <div class="notice-tab">
                <div class="notice-tab-con">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane active" id="navBaseball" role="tabpanel" aria-labelledby="nav-baseball">
								
                            </div>
							<div class="tab-pane" id="nav-basketball" role="tabpanel" aria-labelledby="nav-basketball">
								
                            </div>
							<div class="tab-pane" id="nav-soccor" role="tabpanel" aria-labelledby="nav-soccor">
								
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
		// loop noticeListD here to generate the search select then append into the page
		// loop noticeListD here to generate the search select then append into the page
	}

	$("button.nav-link").click(function() {
        $(".notice-tab-con").animate({ scrollTop: 0 }, "smooth");
        console.log("top");   
    });

</script>
@endpush