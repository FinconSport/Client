@extends('layout.app')

@section('content')
	<!-- 搜尋框 -->
	<!-- <div id='searchArea' style="height: 5.5rem;">
		<div class="w-100" style='display: inline-flex'>
			<div id="series_id" style="width: 35%;margin-left: 1%" data-filter="off" data-filterpage="1">
				<p class="mb-0 fw-600 fs-09">{{ trans('match.main.series') }}</p>
				<select name="series_id" class="ui dropdown searchSelect seriesOption" onchange="filterSeiries()">
					<option value="">{{ trans('common.search_area.series') }}</option>
					<option value="0">{{ trans('common.search_area.all') }}</option>
					@foreach ($series_list as $key => $list)
						@if ($key == intval($search['sport']))
							@foreach ($list as $key2 => $v)
								<option series="{{ $v }}" value="{{ $key2 }}">{{ $v }}</option>
							@endforeach
						@endif
					@endforeach
				</select>
			</div>
			<div class="ui form" style="width:45%;margin-left: 1%">
				<div class="two fields">
					<div class="field">
						<p class="mb-0 fw-600 fs-09">{{ trans('common.search_area.start_time') }}</p>
						<div class="ui calendar" id="rangestart">
							<div class="ui input left icon">
								<i class="fa-solid fa-calendar-days"></i>
								<input autocomplete="off" name="start_time" type="text" placeholder="{{ trans('common.search_area.start_time') }}">
							</div>
						</div>
					</div>
					<div class="field">
						<p class="mb-0 fw-600 fs-09">{{ trans('common.search_area.end_time') }}</p>
						<div class="ui calendar" id="rangeend">
							<div class="ui input left icon">
								<i class="fa-solid fa-calendar-days"></i>
								<input autocomplete="off" name="end_time" type="text" placeholder="{{ trans('common.search_area.end_time') }}">
							</div>
						</div>
					</div>
				</div>
			</div>
			<button style="width: 20%;" id='searchBtn' class="ui button active" onclick="searchResult()">{{ trans('common.search_area.search') }}
				<i class="fa-solid fa-magnifying-glass ml-1"></i>
			</button>
		</div>
	</div> -->
	
	<!-- Table -->
	<div id="tblMatchResult">
		<div id="tblbodyMatch">
			<table class="table table-striped table-bordered">
				<thead>
					<tr id="tableTitle">
					</tr>
				</thead>
				<tbody id="tableContent">
				</tbody>
			</table>
		</div>
	</div>
	<div id="noMoreData" style="display: none">
		<td colspan="16"><p class="mb-0">{{ trans('match.main.nomoredata') }}</p></td>
	</div>
	<div id="loader" style="display: none">
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
@endsection
@section('styles')
<link href="{{ asset('css/match.css?v=' . $system_config['version']) }}" rel="stylesheet">
<style>	
/* 寫入頁面限定CSS */
</style>
@endSection
@push('main_js')
<script>
    var isLastPage = false; // infinite scroll -> detect if it's last page
	var fetchMoreLock = false; // infinite scroll lock -> to prevent infinite loop
	var langTrans = @json(trans('match')); // lang file
	var matchTitle = null
	var matchCommonTitle = langTrans.matchTitle.commonTitle

	// detect ini ajax
    var isReadyResultInt = null
    var isReadyResult = false
	var isReadySportInt = null
	
	// result list data
    var resultListD = {}
    var callResultListData = { token: token, player: player, sport: sport, page: 1 }
    const resultList_api = 'https://sportc.asgame.net/api/v2/result_index'

	function renderView( isIni = 0 ) {
		
		matchTitle.forEach(ele => {
			let str = ''
			str += '<th>' + ele + '</th>'
			$('#tableTitle').append(str)
		});
		Object.entries(resultListD.data).map(([k, v]) => { 
			let str = '<tr>'
			str += '<td rowspan=2>' + formatDateTime(v.start_time) + '</td>'
			str += '<td rowspan=2>' + v.league_name + '</td>'

			str += '<td>' + v.home_team_name + '</td>'
			matchTitle.forEach((v2, k2) => {
				if(v.scoreboard[k2]) {
					str += '<td>' + v.scoreboard[k2][0] + '</td>'
				} else {
					str += '<td>-</td>'
				}
			});
			str += '</tr>'

			str += '<tr>'
			str += '<td>' + v.away_team_name + '</td>'
			matchTitle.forEach((v2, k2) => {
				if(v.scoreboard[k2]) {
					str += '<td>' + v.scoreboard[k2][1] + '</td>'
				} else {
					str += '<td>-</td>'
				}
			});
			str += '</tr>'
			$('#tableContent').append(str)

		})

		// detect if it's last page
		if( resultListD.data.length !== 20 || resultListD.data.length === 0 ) isLastPage = true
		isLastPage && $('#noMoreData').show()
	}

	$(document).ready(function() {

		// detest is sport List is ready
        isReadySportInt = setInterval(() => {
            if( isReadyCommon ) {
                callResultListData.sport_id = sport // default sport
				matchTitle = langTrans.matchTitle[sport].concat(matchCommonTitle)
				caller(resultList_api, callResultListData, resultListD) // resultListD
                clearInterval(isReadySportInt)
            }
        }, 100);

		// check if api are all loaded every 500 ms 
        isReadyResultInt = setInterval(() => {
            if (resultListD.status === 1) { isReadyResult = true; }
            // if( isReadyResult && isReadyCommon ) {
            if( isReadyCommon && isReadyResult ) {
                $('#dimmer').dimmer('hide'); // hide loading
                $('#wrap').css('opacity', 1); // show the main content
				renderView()
                clearInterval(isReadyResultInt); // stop checking
            }
        }, 500);
	});

	formatDateTime = (dateTimeString) => {
        const dateTime = new Date(dateTimeString);
        const month = (dateTime.getMonth() + 1).toString().padStart(2, '0'); // Get month (0-based index), add 1, and pad with '0' if needed
        const day = dateTime.getDate().toString().padStart(2, '0'); // Get day and pad with '0' if needed
        const hour = dateTime.getHours().toString().padStart(2, '0'); // Get hours and pad with '0' if needed
        const minute = dateTime.getMinutes().toString().padStart(2, '0'); // Get minutes and pad with '0' if needed
        return `${month}-${day} ${hour}:${minute}`;
    }


	// 下拉更多資料
	function fetchMore() {
		console.log('fetchMore')
		$('#loader').show() // loading transition

		callResultListData.page = parseInt(searchData.page) + 1
		$.ajax({
			url: resultList_api,
			type: 'POST',
			headers: {
				'X-CSRF-TOKEN': csrfToken
			},
			data: callResultListData,
			success: function(response) {
				var data = JSON.parse(response).data
				data = resultListD
				renderView() // render the new data
				$('#loader').hide()
                fetchMoreLock = false // reset the infinite scroll lock
			},
			error: function(xhr, status, error) {
				console.error('error');
				console.error(xhr,status,error);
			}
		});
	}

	// scroll to bottom
	var matchContainer = document.getElementById('tblbodyMatch');
	matchContainer.addEventListener('scroll', function() {
		var noDataDiv = document.getElementById("noDataF");
		var noDataDivL = noDataDiv ? noDataDiv.length : 0;
		var seriesElement = document.getElementById("series_id");
		var filterStatData = seriesElement.getAttribute("data-filter");
		var scrollHeight = matchContainer.scrollHeight;
		var scrollTop = matchContainer.scrollTop;
		var clientHeight = matchContainer.clientHeight;
		if (scrollTop + clientHeight + 1 >= scrollHeight && isLastPage === false && fetchMoreLock === false && noDataDivL < 1) {
			fetchMoreLock = true // lock
			fetchMore()
		}
	});
</script>
@endpush

