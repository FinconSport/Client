@extends('layout.app')

@section('content')
<div class="container notice-container">
	<div class="row notice-row">
		<!--- Left Navigation -->
		<div class="notice-nav nav flex-column nav-pills col-2 text-center" id="v-pills-tab" role="tablist" aria-orientation="vertical">
			<p>{{ trans('notice.main.notice') }}</p>
			<!--- Button for All Notification-->
			<button class="nav-link active" id="v-pills-all-tab" data-bs-toggle="pill" data-bs-target="#v-pills-all" type="button" role="tab" aria-controls="v-pills-all" aria-selected="true">{{ trans('notice.main.all') }}</button>
			<!--- Button for System Notification -->
			<button class="nav-link" id="v-pills-system-tab" data-bs-toggle="pill" data-bs-target="#v-pills-system" type="button" role="tab" aria-controls="v-pills-system" aria-selected="false">{{ trans('notice.main.system') }}</button>
			<!--- Sport Type Navigation Loop -->
		</div>


		<div class="card" hidden>
			<div class="card-header d-flex">
				<div class="p-2 bd-highlight notice-title">
					<p>  </p>
				</div>
				<div class="ms-auto p-2 bd-highlight">
					<p> </p>
				</div>
			</div>
			<div class="card-body">
				<p> </p>
			</div>
		</div>

		<div class="tab-pane fade" id="v-pills-" role="tabpanel" aria-labelledby="v-pills--tab" hidden>
					
		</div>


		<div class="notice-container-pad col-10">
			<!--- Tab Container -->
			<div class="notice-tab-content tab-content" id="v-pills-tabContent">
				<!---All Announcement Tab Container -->
				<div class="tab-pane fade show active" id="v-pills-all" role="tabpanel" aria-labelledby="v-pills-all-tab">
					<!--- all notice_list loop query -->
				</div>
				<!--- System Announcement Tab Container -->
				<div class="tab-pane fade" id="v-pills-system" role="tabpanel" aria-labelledby="v-pills-all-tab">
					<!--- system notice_list loop query -->
				</div>

				<!---Sport Announcement Tab Container Loop -->
				
			</div>
		</div>
	</div>
</div>

@endsection

@section('styles')
<link href="{{ asset('css/notice.css?v=' . $system_config['version']) }}" rel="stylesheet">
<style>
	/* 寫入頁面限定CSS */
</style>
@endSection

@push('main_js')
<script>
	// detect ini ajax
	var isReadyNoticeInt = null
	var isReadyNotice = false

	// notice list data
	var noticeListD = {}
	const noticeList_api = 'https://sportc.asgame.net/api/v2/index_notice'

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
		sportListD.data.forEach(ele => {
			let str = '<button class="nav-link" id="v-pills-' + ele.sport_id + '-tab" data-bs-toggle="pill" data-bs-target="#v-pills-' + ele.sport_id + '" type="button" role="tab" aria-controls="v-pills-' + ele.sport_id + '" aria-selected="false">' + ele.name + '</button>'
			$('#v-pills-tab').append(str)
		});
		



		// loop noticeListD here to generate the search select then append into the page
	}

	$('.nav-link').on('click', function(e) {
		e.preventDefault();
		$('.nav-link').removeClass('active');
		$(this).addClass('active');

		var target = $(this).data('bs-target');
		$('.tab-pane').removeClass('show active');
		$(target).addClass('show active');
		$('.notice-tab-content').animate({
			scrollTop: 0
		}, 'fast');
	});

	// 左邊菜單  當點擊體育或串關時 移除目前選中樣式
	$('.menuTypeBtn').click(function() {
		let key = $(this).attr('key')
		if ((key === 'index' || key === 'm_order' || key === 'match') && $(this).hasClass('on')) {
			$('div[key="notice"] .slideMenuTag').css('border-bottom-left-radius', '0')
			$('div[key="notice"] .slideMenuTag').css('border-top-left-radius', '0')
			$('div[key="notice"] .slideMenuTag').css('background-color', '#415b5a')
			$('div[key="notice"] .slideMenuTag').css('color', 'white')

			$('div[key="calculator"] .slideMenuTag').css('border-bottom-right-radius', '0')
			$('div[key="menuBottomFill"] .slideMenuTag').css('border-top-right-radius', '0')
			$('div[key="notice"] .slideMenuTag').css('border-top-right-radius', '0')
		} else {
			$('div[key="notice"] .slideMenuTag').css('border-bottom-left-radius', '25px')
			$('div[key="notice"] .slideMenuTag').css('border-top-left-radius', '25px')
			$('div[key="notice"]').css('background-color', '#415b5a')
			$('div[key="notice"] .slideMenuTag').css('background-color', 'rgb(196, 211, 211)')
			$('div[key="notice"] .slideMenuTag').css('color', '#415b5a')

			$('div[key="calculator"] .slideMenuTag').css('border-bottom-right-radius', '15px')
			$('div[key="menuBottomFill"] .slideMenuTag').css('border-top-right-radius', '15px')
			$('div[key="notice"] .slideMenuTag').css('border-top-right-radius', '0')
		}
	})
</script>
@endpush