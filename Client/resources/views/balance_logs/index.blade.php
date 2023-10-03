@extends('layout.app')

@section('content')


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
				<td colspan="6"><p class="mb-0">>{{ trans('logs.main.nodata') }}</p></td>
			</div>
        </div>
    </div>
@endsection
@section('styles')
<link href="{{ asset('css/match.css?v=' . $current_time) }}" rel="stylesheet">
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
    const logsList_api = 'https://sportc.asgame.net/api/v2/balance_logs'

	$(document).ready(function() {

		caller(logsList_api, callLogsListData, logsListD) // logsListD


		// check if api are all loaded every 500 ms 
		isReadyLogsInt = setInterval(() => {
            if (logsListD.status === 1) { isReadyLogs = true; }
			if( isReadyCommon && isReadyLogs ) {
				$('#dimmer').dimmer('hide'); // hide loading
				$('#wrap').css('opacity', 1); // show the main content
				renderView()
				clearInterval(isReadyLogsInt); // stop checking
			}
		}, 500);
	});

	function renderView() {
		Object.entries(logsListD.data.list).map(([k, v]) => { 
			console.log(v)
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
	}

	// 下拉更多資料
	async function fetchMore() {
		console.log('fetchMore')
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