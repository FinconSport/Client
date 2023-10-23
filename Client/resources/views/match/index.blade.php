@extends('layout.app')

@section('content')
	<!-- search -->
	<div class="search-bar-container">
		<div class="select-con">
			<select class="ui selection dropdown" id="selectOption" name="selectOption" onchange="redirectToPage()">
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
	<div id="matchContainer" class="match">
        <div id="tableContainer" style="overflow: auto;">
            <table id="matchTable" class="cell-border w-100 text-center">
				<thead>
					<tr id="tableTitle">
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
				<td colspan="16"><p class="mb-0">{{ trans('match.main.nomoredata') }}</p></td>
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
    var isLastPage = false; // infinite scroll -> detect if it's last page
	var fetchMoreLock = false; // infinite scroll lock -> to prevent infinite loop
	var langTrans = @json(trans('match')); // lang file
	var matchTitle = null
	var matchCommonTitle = langTrans.matchTitle.commonTitle
	var matchTitleAll = null

	// detect ini ajax
    var isReadyResultInt = null
    var isReadyResult = false
	var isReadySportInt = null
	
	// result list data
    var resultListD = {}
    var callResultListData = { token: token, player: player, sport: sport, page: 1 }
    const resultList_api = '/api/v2/result_index'


	function redirectToPage() {
		let league_id = $('#selectOption').val()
		let start_time = $('#rangestart input').val()
		let end_time = $('#rangeend input').val()

		const queryParams = {};
		queryParams.sport_id = sport
		if( league_id ) queryParams.league_id = league_id;
		if( start_time ) queryParams.start_time = start_time;
		if( end_time ) queryParams.end_time = end_time;
		
		const queryString = new URLSearchParams(queryParams).toString();
		const urlWithQuery = `${queryString}`;
		window.location.href = urlWithQuery
	}

	function renderView(isIni = 0) {
		// initial
		if( isIni === 1 ) {
			// table title
			matchTitleAll.forEach(ele => {
				let str = ''
				str += '<th>' + ele + '</th>'
				$('#tableTitle').append(str)
			});

			// select option
			let leagueArr = sportListD.data.find(item => item.sport_id === sport).league
			leagueArr.forEach( e =>{
				$('#selectOption').append(`<option value=${e.league_id}>${e.name}</option>`)
			})
			
		}

		const scoreResults = document.querySelectorAll('.scoreResult');
		if (scoreResults.length > 0) {
			scoreResults.forEach((element, index) => {
				if (index % 2 === 0) {
				element.style.color = 'red';
				}
			});
		}
		
		Object.entries(resultListD.data).map(([k, v]) => { 
			let str = '<tr class="odd">'
			if( k % 2 === 0) str = '<tr class="even">'
			str += '<td rowspan=2>'
			str += '<p class="mb-0">' + formatDateTime(v.start_time) + '</p>'
			str += '<p class="mb-0 text-red">'
			if( v.status > 3 && v.status < 9 ) str += v.status_name
			str += '</p>'
			str += '</td>'
			
			
			str += '<td rowspan=2>' + v.league_name + '</td>'

			str += '<td>' + v.home_team_name + '</td>'
			matchTitle.forEach((v2, k2) => {
				if(v.scoreboard[k2]) {
					str += '<td class="scoreResult">' + v.scoreboard[k2][0] + '</td>'
				} else {
					str += '<td>-</td>'
				}
			});
			str += '</tr>'

			if( k % 2 === 0) {
				str += '<tr class="even">'
			} else {
				str += '<tr class="odd">'
			}
			str += '<td>' + v.away_team_name + '</td>'
			matchTitle.forEach((v2, k2) => {
				if(v.scoreboard[k2]) {
					str += '<td class="scoreResult">' + v.scoreboard[k2][1] + '</td>'
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

		if( searchData.league_id ) callResultListData.league_id = parseInt(searchData.league_id) // get result params
		if( searchData.start_time ) callResultListData.start_time = searchData.start_time // get start_time params
		if( searchData.end_time ) callResultListData.end_time = searchData.end_time // get end_time params

		// detest is sport List is ready
        isReadySportInt = setInterval(() => {
            if( isReadyCommon ) {
                callResultListData.sport_id = sport // default sport
				matchTitle = langTrans.matchTitle[sport]
				matchTitleAll = matchCommonTitle.concat(langTrans.matchTitle[sport])
				console.log(callResultListData)
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
				renderView(1)
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
	async function fetchMore() {
		console.log('fetchMore')
		$('#loader').show() // loading transition
		callResultListData.page += 1
		await caller(resultList_api, callResultListData, resultListD, 1) // resultListD
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

