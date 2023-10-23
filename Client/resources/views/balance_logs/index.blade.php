@extends('layout.app')

@section('content')
	<!-- search -->
	<div class="search-bar-container">
		<div class="select-con">
			<select class="ui selection dropdown" id="selectOption" name="balance_type" onchange="redirectToPage()">	
				<option value='' selected>{{ trans('logs.main.all') }}</option>
				<option value='game_bet' >{{ trans('logs.main.game_bet') }}</option>
				<option value='game_result'>{{ trans('logs.main.game_result') }}</option>
				<option value='recharge'>{{ trans('logs.main.recharge') }}</option>
				<option value='withdraw'>{{ trans('logs.main.withdraw') }}</option>
				<option value='delay_bet_refund'>{{ trans('logs.main.delay_bet_refund') }}</option>
			</select>
		</div>
		<div class="datecalendar-con">
			<div class="ui form">
				<div class="two fields">
					<div class="field">
					<div class="ui calendar" id="rangestart">
						<div class="ui input left icon">
						<i class="calendar icon"></i>
						<input type="text" placeholder="{{ trans('common.search_area.start_time') }}" onchange="redirectToPage()">
						</div>
					</div>
					</div>
					<div class="field">
					<div class="ui calendar" id="rangeend">
						<div class="ui input left icon">
						<i class="calendar icon"></i>
						<input type="text" placeholder="{{ trans('common.search_area.end_time') }}" onchange="redirectToPage()">
						</div>
					</div>
					</div>
				</div>
			</div>
			<div class="datebutton-cons">
				<button class="dateCalendarBtn" data-range="lastMonth">{{ trans('common.search_area.last_month') }}</button>
				<button class="dateCalendarBtn" data-range="lastWeek">{{ trans('common.search_area.last_week') }}</button>
				<button class="dateCalendarBtn" data-range="yesterday">{{ trans('common.search_area.yesterday') }}</button>
				<button class="dateCalendarBtn" data-range="today">{{ trans('common.search_area.today') }}</button>
				<button class="dateCalendarBtn" data-range="thisWeek">{{ trans('common.search_area.this_week') }}</button>
				<button class="dateCalendarBtn" data-range="thisMonth">{{ trans('common.search_area.this_month') }}</button>
			</div>
		</div>
	</div>
	<!-- Table -->
	<div id="matchContainer">
        <div id="tableContainer" style="overflow: auto;">
            <table id="matchTable" class="cell-border w-100 text-center">
				<thead>
					<tr id="tableTitle">
						<th>{{ trans('logs.main.id') }}</th>
						<th>{{ trans('logs.main.logs_type') }}</th>
						<th>{{ trans('logs.main.logs_amount') }}</th>
						<th>{{ trans('logs.main.logs_before_amount') }}</th>
						<th>{{ trans('logs.main.logs_after_amount') }}</th>
						<th>{{ trans('logs.main.logs_time') }}</th>
					</tr>
				</thead>
				<tbody id="tableContent">
				</tbody>
            </table>
			<div id="loader" style="display: none; margin-top: 2rem;">
				<div colspan="29" class="loading loading04">
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
			</div>  
			<div id="noMoreData" style="display: none; margin-top: 2rem;">
				<td colspan="6"><p class="mb-0">{{ trans('logs.main.nomoredata') }}</p></td>
			</div>
        </div>
    </div>
@endsection
@section('styles')
<link href="{{ asset('css/match.css?v=' . $system_config['version']) }}" rel="stylesheet">
<style>	
/* 寫入頁面限定CSS */
</style>
@endSection
@push('main_js')
<script>

	// 語系
    var langTrans = @json(trans('logs'));
	var isLastPage = false; // infinite scroll -> detect if it's last page
	var fetchMoreLock = false; // infinite scroll lock -> to prevent infinite loop

	// detect ini ajax
	var isReadyLogsInt = null
	var isReadyLogs = false

	var logsListD = {}
    var callLogsListData = { token: token, player: player, page: 1 }
    const logsList_api = '/api/v2/balance_logs'

	$(document).ready(function() {

		caller(logsList_api, callLogsListData, logsListD) // logsListD


		// check if api are all loaded every 500 ms 
		isReadyLogsInt = setInterval(() => {
            if (logsListD.status === 1) { isReadyLogs = true; }
			if( isReadyCommon && isReadyLogs ) {
				$('#dimmer').dimmer('hide'); // hide loading
				$('#wrap').css('opacity', 1); // show the main content
				renderView(1)
				clearInterval(isReadyLogsInt); // stop checking
			}
		}, 500);
	});

	function redirectToPage() {
		let balance_type = $('#selectOption').val()
		let start_time = $('#rangestart input').val()
		let end_time = $('#rangeend input').val()

		const queryParams = {};
		if( balance_type ) queryParams.balance_type = balance_type;
		if( start_time ) queryParams.start_time = start_time;
		if( end_time ) queryParams.end_time = end_time;
		
		const queryString = new URLSearchParams(queryParams).toString();
		const urlWithQuery = `?${queryString}`;
		window.location.href = urlWithQuery
	}

	function renderView( isIni = 0 ) {
		// search
		if( isIni ) {
			// place holder of date
			let tt = new Date();
			let yy = new Date();
			yy.setDate(yy.getDate() - 1);
			setRange(searchDate(yy), searchDate(tt))

			// search condition
			$('#selectOption').val(searchData.balance_type || '' )
			setRange(searchData.start_time || '', searchData.end_time || '')
		}


		Object.entries(logsListD.data.list).map(([k, v]) => { 
			let str = '<tr class="odd">'
			if( k % 2 === 0) str = '<tr class="even">'

			str += '<td>' + v.id + '</td>'
			str += '<td>' + v.type + '</td>'
			str += '<td>' + v.change_balance + '</td>'
			str += '<td>' + v.before_balance + '</td>'
			str += '<td>' + v.after_balance + '</td>'
			str += '<td>' + v.create_time + '</td>'
			str += '</tr>'
			$('#tableContent').append(str)
		})

		// detect if it's last page
		if( logsListD.data.list.length !== 20 || logsListD.data.list.length === 0 ) isLastPage = true
		isLastPage && $('#noMoreData').show()

		if( isIni === 1 && window.innerHeight > 750 ) fetchMore()
	}

	// 下拉更多資料
	async function fetchMore() {
		$('#loader').show() // loading transition
		callLogsListData.page += 1
		await caller(logsList_api, callLogsListData, logsListD, 1) // logsListD
		renderView()
		$('#loader').hide() // loading transition
		fetchMoreLock = false

	}

	// scroll to bottom
	var matchContainer = document.getElementById('tableContainer');
	matchContainer.addEventListener('scroll', function() {
		var scrollHeight = matchContainer.scrollHeight;
		var scrollTop = matchContainer.scrollTop;
		var clientHeight = matchContainer.clientHeight;
		if (scrollTop + clientHeight + 1 >= scrollHeight && isLastPage === false && fetchMoreLock === false) {
			fetchMoreLock = true // lock
			fetchMore()
		}
	});

	
	
</script>
@endpush