@extends('layout.app')

@section('content')
	<!-- 搜尋框 -->
	<div id='searchArea' style="height: 5.5rem;">
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
	</div>
	
	<!-- Table -->
	<div id="tblMatchResult">
		<div id="tblbodyMatch">
		@switch(intval($search['sport']))
        @case('1')
            <!-- Content for sport 1 -->
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
						<th>{{ trans('match.main.date') }}</th>
						<th>{{ trans('match.main.series') }}</th>
						<th>{{ trans('match.main.homeaway') }}</th>
						<th>{{ trans('match.football.fulltimescore') }}</th>
						<th>{{ trans('match.football.firsthalfscore') }}</th>
						<th>{{ trans('match.football.secondhalfscore') }}</th>
						<th>{{ trans('match.football.cornerscore') }}</th>
						<th>{{ trans('match.football.freekickscore') }}</th>
						<th>{{ trans('match.football.overtimescore') }}</th>
						<th>{{ trans('match.football.scoreofdangerousoffenses') }}</th>
						<th>{{ trans('match.football.penaltyscore') }}</th>
						<th>{{ trans('match.football.redcardscore') }}</th>
						<th>{{ trans('match.football.yellowcardscore') }}</th>
						<th>{{ trans('match.football.scoreofmissedshots') }}</th>
						<th>{{ trans('match.football.scoreofshotsontarget') }}</th>
						<th>{{ trans('match.football.numberofattacks') }}</th>
						<th>{{ trans('match.football.cornerscore') }}</th>
						</tr>
					</thead>
					<tbody>
						@if(!empty($data['list']))
								@foreach($data['list'] as $key => $item)
								<tr>
									<td class="nowrap" rowspan="2">{{date('m-d H:i', strtotime($item['start_time']))}}</td>
									<td class="nowrap" rowspan="2">
										@if(isset($item['series_logo']))
										<img src="{{ $item['series_logo'] }}" class='serieslogo' onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'" >
										@endif
										@if(isset($item['series_name']))
											{{ $item['series_name'] }}
										@endif
									</td>
									<!--Home Logo-->
									<td class="nowrap">
										@if(isset($item['home_team_logo']))
										<img src="{{ $item['home_team_logo'] }}" alt='homelogo' class='teamlogo' onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'">
										@endif
										{{ $item['home_team_name'] }}
									</td>
									<!--Home Stat-->
									@if(isset($item['stat']['home_stat']) && is_array($item['stat']['home_stat']))
									@foreach($item['stat']['home_stat'] as $val2 => $e2)
											@if ($e2!="")
												<td>{{$e2}}</td>
											@else
												<td>-</td>
											@endif
									@endforeach
									@else
									@for ($i = 0; $i < 14; $i++)
										<td>-</td>
									@endfor
									@endif
								</tr>
								<tr>
									<!--Away Logo-->
									<td class="nowrap">
										@if(isset($item['away_team_logo']))
										<img src="{{ $item['away_team_logo'] }}" alt='awaylogo' class='teamlogo' onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'" >
										@endif {{$item['away_team_name']}}
									</td>
									<!--Away Stat-->
									@if(isset($item['stat']['away_stat']) && is_array($item['stat']['away_stat']))
									@foreach($item['stat']['away_stat'] as $val2 => $e2)
											@if ($e2!="")
												<td>{{$e2}}</td>
											@else
												<td>-</td>
											@endif
									@endforeach
									@else
									@for ($i = 0; $i < 14; $i++)
										<td>-</td>
									@endfor
									@endif
								</tr>
						@endforeach         
					@endif
					<tr id="loader" style="display: none">
						<td class="loading loading04">
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
						</td>
					</tr>
					</tbody>
				</table>
            @break

        @case('2')
            <!-- Content for sport 2 -->
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
						<th>{{ trans('match.main.date') }}</th>
						<th>{{ trans('match.main.series') }}</th>
						<th>{{ trans('match.main.homeaway') }}</th>
						<th>{{ trans('match.basketball.fulltimescore') }}</th>
						<th>{{ trans('match.basketball.firsthalfscore') }}</th>
						<th>{{ trans('match.basketball.secondhalfscore') }}</th>
						<th>{{ trans('match.basketball.firstquarter') }}</th>
						<th>{{ trans('match.basketball.secondquarter') }}</th>
						<th>{{ trans('match.basketball.thirdquarter') }}</th>
						<th>{{ trans('match.basketball.fourthquarter') }}</th>
						<th>{{ trans('match.basketball.twopoints') }}</th>
						<th>{{ trans('match.basketball.threepoints') }}</th>
						<th>{{ trans('match.basketball.penalty') }}</th>
						<th>{{ trans('match.basketball.freethrowpercentage') }}</th>
						<th>{{ trans('match.basketball.numberoffreethrows') }}</th>
						<th>{{ trans('match.basketball.totalnumberoffouls') }}</th>
						<th>{{ trans('match.basketball.foulsfirstquarter') }}</th>
						<th>{{ trans('match.basketball.foulssecondquarter') }}</th>
						<th>{{ trans('match.basketball.foulsthirdquarter') }}</th>
						<th>{{ trans('match.basketball.foulsfourthquarter') }}</th>
						<th>{{ trans('match.basketball.overtimefouls') }}</th>
						<th>{{ trans('match.basketball.foulsfirsthalf') }}</th>
						<th>{{ trans('match.basketball.foulssecondhalf') }}</th>
						<th>{{ trans('match.basketball.totalnumberofpauses') }}</th>
						<th>{{ trans('match.basketball.timeoutsfirstquarter') }}</th>
						<th>{{ trans('match.basketball.timeoutssecondquarter') }}</th>
						<th>{{ trans('match.basketball.timeoutsthirdquarter') }}</th>
						<th>{{ trans('match.basketball.timeoutsfourthquarter') }}</th>
						<th>{{ trans('match.basketball.overtimetimeouts') }}</th>
						</tr>
					</thead>
					<tbody>
						@if(!empty($data['list']))
								@foreach($data['list'] as $key => $item)
								<tr>
									<td class="nowrap" rowspan="2">{{date('m-d H:i', strtotime($item['start_time']))}}</td>
									<td class="nowrap" rowspan="2">
										@if(isset($item['series_logo']))
										<img src="{{ $item['series_logo'] }}" class='serieslogo' onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'" >
										@endif
										@if(isset($item['series_name']))
											{{ $item['series_name'] }}
										@endif
									</td>
									<!--Home Logo-->
									<td class="nowrap">
										@if(isset($item['home_team_logo']))
										<img src="{{ $item['home_team_logo'] }}" alt='homelogo' class='teamlogo' onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'">
										@endif
										{{ $item['home_team_name'] }}
									</td>
									<!--Home Stat-->
									@if(isset($item['stat']['home_stat']) && is_array($item['stat']['home_stat']))
									@foreach($item['stat']['home_stat'] as $val2 => $e2)
											@if ($e2!="")
												<td>{{$e2}}</td>
											@else
												<td>-</td>
											@endif
									@endforeach
									@else
									@for ($i = 0; $i < 26; $i++)
										<td>-</td>
									@endfor
									@endif
								</tr>
								<tr>
									<!--Away Logo-->
									<td class="nowrap">
										@if(isset($item['away_team_logo']))
										<img src="{{ $item['away_team_logo'] }}" class='serieslogo' onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'" >
										@endif {{$item['away_team_name']}}
									</td>
									<!--Away Stat-->
									@if(isset($item['stat']['away_stat']) && is_array($item['stat']['away_stat']))
									@foreach($item['stat']['away_stat'] as $val2 => $e2)
											@if ($e2!="")
												<td>{{$e2}}</td>
											@else
												<td>-</td>
											@endif
									@endforeach
									@else
									@for ($i = 0; $i < 26; $i++)
										<td>-</td>
									@endfor
									@endif
								</tr>
						@endforeach
						      
					@endif
					</tbody>
				</table>
            @break

        @case('3')
		<!-- Content for sport 3 -->
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
					<th>{{ trans('match.main.date') }}</th>
					<th>{{ trans('match.main.series') }}</th>
					<th>{{ trans('match.main.homeaway') }}</th>
					<th>{{ trans('match.baseball.fulltimescore') }}</th>
					<th>{{ trans('match.baseball.firstround') }}</th>
					<th>{{ trans('match.baseball.secondgame') }}</th>
					<th>{{ trans('match.baseball.thirdinning') }}</th>
					<th>{{ trans('match.baseball.fourthinning') }}</th>
					<th>{{ trans('match.baseball.fifthinning') }}</th>
					<th>{{ trans('match.baseball.sixthinning') }}</th>
					<th>{{ trans('match.baseball.seventhinning') }}</th>
					<th>{{ trans('match.baseball.eighthinning') }}</th>
					<th>{{ trans('match.baseball.ninthinning') }}</th>
					<th>{{ trans('match.baseball.tenthinning') }}</th>
					<th>{{ trans('match.baseball.eleventhinning') }}</th>
					<th>{{ trans('match.baseball.twelfthinning') }}</th>
					<th>{{ trans('match.baseball.overtime') }}</th>
					<th>{{ trans('match.baseball.hitscore') }}</th>
					</tr>
				</thead>
				<tbody>
					@if(!empty($data['list']))
							@foreach($data['list'] as $key => $item)
							<tr>
								<td class="nowrap" rowspan="2">{{date('m-d H:i', strtotime($item['start_time']))}}</td>
								<td class="nowrap" rowspan="2">
									@if(isset($item['series_logo']))
									<img src="{{ $item['series_logo'] }}" class='serieslogo' onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'" >
									@endif
									@if(isset($item['series_name']))
										{{ $item['series_name'] }}
									@endif
								</td>
								<!--Home Logo-->
								<td class="nowrap">
									@if(isset($item['home_team_logo']))
									<img src="{{ $item['home_team_logo'] }}" alt='homelogo' class='teamlogo' onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'">
									@endif
									{{ $item['home_team_name'] }}
								</td>
								<!--Home Stat-->
								@if(isset($item['stat']['home_stat']) && is_array($item['stat']['home_stat']))
								@foreach($item['stat']['home_stat'] as $val2 => $e2)
										@if ($e2!="")
											<td>{{$e2}}</td>
										@else
											<td>-</td>
										@endif
								@endforeach
								@else
								@for ($i = 0; $i < 15; $i++)
									<td>-</td>
								@endfor
								@endif
							</tr>
							<tr>
								<!--Away Logo-->
								<td class="nowrap">
									@if(isset($item['away_team_logo']))
									<img src="{{ $item['away_team_logo'] }}" class='serieslogo' onerror="this.src='https://sporta.asgame.net/uploads/default.png?v={{$system_config['version']}}'" >
									@endif {{$item['away_team_name']}}
								</td>
								<!--Away Stat-->
								@if(isset($item['stat']['away_stat']) && is_array($item['stat']['away_stat']))
								@foreach($item['stat']['away_stat'] as $val2 => $e2)
										@if ($e2!="")
											<td>{{$e2}}</td>
										@else
											<td>-</td>
										@endif
								@endforeach
								@else
								@for ($i = 0; $i < 15; $i++)
									<td>-</td>
								@endfor
								@endif
							</tr>
					@endforeach     
					@endif
				</tbody>
			</table>
			
		
		@break
            <!-- Content for sport 3 -->
        @default
    @endswitch
	@if(empty($data['list']))
		<div id="noDataF">
			<i class="fa-solid fa-circle-exclamation"></i>
			<p class="mb-0">{{ trans('match.main.nogame') }}</p>
		</div>   
	@endif
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

	/*
		===== PECO =====
		1. search api and infiniti api?
		2. 
		===== PECO =====
	*/ 

	//Identify sport id
	var sportn = parseInt(searchData["sport"], 10);

	

    
    var isLastPage = false; // infinite scroll -> detect if it's last page
	var fetchMoreLock = false; // infinite scroll lock -> to prevent infinite loop
	var langTrans = @json(trans('match')); // lang file

	// detect ini ajax
    var isReadyResultInt = null
    var isReadyResult = false
	
	// result list data
    var resultListD = {}
    var callResultListData = { token: token, player: player, sport: sport, page: 1 }
    const resultList_api = 'https://sportc.asgame.net/api/v1/result_index'

	// seriesList
	var seriesListD = {}
    var callSeriesListData = commonCallData
	const seriesList_api = '' // tbd

	function renderView( isIni = 0 ) {
		if( isIni === 1 ) { // initial
			// search condition
				if( searchData.series_id !== undefined ) {
				$('select[name="series_id"]').val(searchData.series_id)
				$('select[name="series_id"]').trigger('change')
			}
			if( searchData.start_time !== undefined ) {
				$('input[name="start_time"]').val(searchData.start_time)
				$('input[name="start_time"]').trigger('change')
			}
			if( searchData.end_time !== undefined ) {
				$('input[name="end_time"]').val(searchData.end_time)
				$('input[name="end_time"]').trigger('change')
			}
		}
		

		/* render resultListD here

		loop resultListD to generate the html element
		then use insertRow() to insert
		note that insertRow() may need to be edited




		render resultListD here */

		// detect if it's last page
		if( resultListD.length !== 20 || resultListD.length === 0 ) isLastPage = true
		isLastPage && $('#noMoreData').show()
	}

	$(document).ready(function() {

		// ini data from ajax
        caller(resultList_api, callSeriesListData, resultListD) // resultListD
        // caller(seriesList_api, callSeriesListData, seriesListD) // seriesListD


		// check if api are all loaded every 500 ms 
        isReadyResultInt = setInterval(() => {
            if (resultListD.status === 1) { isReadyResult = true; }
            if( isReadyResult === true && isReadyCommon === true) {
                $('#dimmer').dimmer('hide'); // hide loading
                $('#wrap').css('opacity', 1); // show the main content
				renderView()
                clearInterval(isReadyResultInt); // stop checking
            }
        }, 500);
	});


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

	function insertRow(e, n) {
		//start time
		let insertStr = '<tr><td class="nowrap" rowspan="2">'+formatDateTime(e.start_time)+'</td>'
		//series logo
		insertStr += '<td class="nowrap" rowspan="2"><img src="' + e.series_logo + '" class="serieslogo" onerror="this.src=\'https://sporta.asgame.net/uploads/default.png?v=' + version + '\'" >'+e.series_name+'</td>'
		//home logo and name
		insertStr += '<td class="nowrap"><img src="'+e.home_team_logo+'" alt="homelogo" class="teamlogo" onerror="this.src=\'https://sporta.asgame.net/uploads/default.png?v=' + version + '\'" >'+e.home_team_name+'</td>'
		//need to loop e.stat to get home_stat
		if (e.stat.home_stat && Array.isArray(e.stat.home_stat)) {
			let homeStat = e.stat.home_stat;
			for (let i = 0; i < n; i++) {
				insertStr += '<td>' + (homeStat[i] ? homeStat[i] : '-') + '</td>';
			}
		} else {
			insertStr += '<td>-</td>'.repeat(n); // Repeat '<td>-</td>' 
		}
		insertStr += '</tr><tr>'
		insertStr += '<td class="nowrap"><img src="'+e.away_team_logo+'" alt="awaylogo" class="teamlogo" onerror="this.src=\'https://sporta.asgame.net/uploads/default.png?v=' + version + '\'" >'+e.away_team_name+'</td>'
		//need to loop e.stat to get away_stat
		if (e.stat.away_stat && Array.isArray(e.stat.away_stat)) {
			let awayStat = e.stat.away_stat;
			for (let i = 0; i < n; i++) {
				insertStr += '<td>' + (awayStat[i] ? awayStat[i] : '-') + '</td>';
			}
		} else {
			insertStr += '<td>-</td>'.repeat(n); // Repeat '<td>-</td>' 
		}
		insertStr += '</tr>'
		$('#tblbodyMatch tbody').append(insertStr)
	}

	// search area series filter
	function filterSeiries(type = 0) {
		if( type === 0 ) {
			$('.clearSearch').dropdown('clear')
		}
		let val = $('select[name="sport"]').val()
		setTimeout(() => {
			$('#series_id select option').each(function() {
				let id = $(this).val()
				if ($(this).attr('sport') === val) {
					$('#series_id div[data-value="'+id+'"]').show()
				} else {
					$('#series_id div[data-value="'+id+'"]').hide()
				}
			});
		}, 100);
	}
	
	// reesult search
	function searchResult() {
		let queryData = {}
		queryData.page = 1
		let sSeriesId = $('select[name="series_id"]').val()
		let sStartTime = $('input[name="start_time"]').val()
		let sEndTime = $('input[name="end_time"]').val()
		if(sSeriesId) queryData.series_id = sSeriesId
		if(sStartTime) queryData.start_time = sStartTime
		if(sEndTime) queryData.end_time = sEndTime
		var queryString = new URLSearchParams(queryData).toString();
		window.location.href = '/order?' + queryString;
	}


  	// for test
    console.log("menu_count");
    console.log(@json($menu_count));

    console.log("sport_list");
    console.log(@json($sport_list));

    console.log("series_list");
    console.log(@json($series_list));

    console.log("status_list");
    console.log(@json($status_list));
    
    console.log("match_list");
    console.log(@json($data));
	
    console.log("search");
    console.log(@json($search));
    
</script>
@endpush

