@extends('layout.app')

@section('content')
	<!-- 搜尋框 -->
	<!-- <div id='searchArea' style="height: 5.5rem;">
		<div class="w-100" style='display: inline-flex'>
			<div style="width: 10%; margin-left: 1%">
				<p class="mb-0 fw-600 fs-09">{{ trans('common.search_area.sport') }}</p>
				<select name="sport" class="ui dropdown searchSelect" onchange="filterSeiries()">
					<option value="">{{ trans('common.search_area.sport') }}</option>
					@foreach($sport_list as $key => $item)
						@if(isset($series_list[$key]))
						    <option sport="{{ $key }}" value="{{ $key }}">{{ $item }}</option>
                        @endif
					@endforeach
				</select>
			</div>

			<div id="series_id" style="width: 35%;margin-left: 1%">
				<p class="mb-0 fw-600 fs-09">{{ trans('common.search_area.series') }}</p>
				<select name="series_id" class="ui dropdown clearSearch searchSelect" onchange="filterSeiries(1)">
					<option value="">{{ trans('common.search_area.series') }}</option>
					@foreach($series_list as $key => $item)
						@foreach($item as $key2 => $value)
							<option sport="{{ $key }}" value="{{ $key2 }}">{{ $value }}</option>
						@endforeach
					@endforeach
				</select>
			</div>
			<div style="width: 10%; margin-left: 1%">
				<p class="mb-0 fw-600 fs-09">{{ trans('common.search_area.order_id') }}</p>
				<div class="ui input focus">
					<input autocomplete="off" class="w-100" name="order_id" type="text" placeholder="{{ trans('common.search_area.order_id') }}">
				</div>
			</div>
			<div style="width: 12%;margin-left: 1%">
				<p class="mb-0 fw-600 fs-09">{{ trans('common.search_area.status') }}</p>
				<select name="status" class="ui dropdown clearSearch searchSelect">
					<option value="">{{ trans('common.search_area.status') }}</option>
					@foreach($status_list as $key => $item)
						<option value="{{ $key }}">{{ $item }}</option>
					@endforeach
				</select>
			</div>
			<div class="ui form" style="width: 23%;margin-left: 1%">
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
			<button style="width: 10%;" id='searchBtn' class="ui button active" onclick="searchOrder()">{{ trans('common.search_area.search') }}
				<i class="fa-solid fa-magnifying-glass ml-1"></i>
			</button>
		</div>
	</div>
	<div id='searchCondition' class="p-0" style="background-color: transparent;">
		<div>{{ trans('common.search_area.total') }}{{ $pagination['max_count'] }}{{ trans('common.search_area.game') }}</div>
		@if(isset($search['_token']))
			@foreach($search as $key => $item)
				@switch($key)
					@case('order_id')
					@case('start_time')
					@case('end_time')
						<div>{{ $item }}</div>
						@break
					@case('series_id')
						<div>{{ $series_list[$search['sport']][$item] }}</div>
						@break
					@case('sport')
						<div>{{ $sport_list[$search['sport']] }}</div>
						@break
					@case('status')
						<div>{{ $status_list[$item] }}</div>
						@break
				@endswitch
			@endforeach
		@else
			<div>{{ $sport_list[$search['sport']] }}</div>
		@endif
			
	</div> -->


    <div id="orderContainer">
        <div id="tableContainer" style="overflow: auto;">
            <table id="orderTable" class="cell-border w-100 text-center">
                <thead>
                    <tr class="no-border-top">
                        <th style="width: 5%;" class="no-border-left">{{ trans('order.main.index') }}</th>
                        <th style="width: 10%;">{{ trans('order.main.sport_type') }}</th>
                        <th style="width: 10%;">{{ trans('order.main.order_type') }}</th>
                        <th style="width: 40%;">{{ trans('order.main.detail') }}</th>
                        <th style="width: 12%;">{{ trans('order.main.bet_money') }}</th>
                        <th style="width: 12.5%;">{{ trans('order.main.return_money') }}</th>
                        <th style="width: 10%;" class="no-border-right">{{ trans('order.main.status') }}</th>
                    </tr>
                </thead>
                <tbody id="orderDataTemp">
                    <tr id="orderTr" template="orderTemplate" hidden>
                        <td class="no-border-left orderData_id"></td>
                        <td template="sportType">
                            <span class="orderData_sportType"></span>
                        </td>
                        <td class="orderData_mOrder"></td>
                        <td class="orderData_betDataDetails">
							<span template="betDataDetailsTemp" hidden>
								<span class="betDataDetails_leagueName"></span>
								<div>
									<span class="betDataDetails_HomeName"></span>
									<span>&ensp;VS&ensp;</span>
									<span class="betDataDetails_AwayName"></span>
								</div>
								<div>
									<span class="betDataDetails_BetNameLine"></span>
								</div>
							</span>
						</td>
                        <td class="text-right">
                            <span class="orderData_betAmount"></span>
                            <br>
                            <span class="text-muted orderData_createdTime"></span>
                        </td>
                        <td class="text-right">
                            <span class="orderData_resultAmount"></span>
                            <br>
                            <span class="text-muted orderData_resultTime"></span>
                        </td>
                        <td class="no-border-right orderData_status"></td>
                    </tr>
                    <tr id="countTr" class="no-border-bottom" template="orderTotalTemplate" hidden>
						<td style="width: 5%;"></td>
                        <td style="width: 10%;"></td>
                        <td style="width: 10%;"></td>
                        <td style="width: 40%;" class="p-0"><div class="text-white bg-deepgreen" id="orderCountTotal">{{ trans('order.main.total') }}</div></td>
                        <td style="width: 12%;" class="text-right orderData_totalBetAmount"></td>
                        <td style="width: 12.5%;" class="text-right orderData_totalResultAmount"></td>
                        <td style="width: 10%;"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

	<!-- <div id="pagination">
		<button onclick="navPage(0)" class="ui button" @if($pagination['current_page'] == 1) disabled @endif>{{ trans('order.main.first_page') }}</button>
		<button onclick="navPage(1)" class="ui button" @if($pagination['current_page'] == 1) disabled @endif>{{ trans('order.main.pre_page') }}</button>
		<p>{{ $pagination['current_page'] }} /  {{ $pagination['max_page'] }}</p>
		<button onclick="navPage(2)" class="ui button" @if($pagination['current_page'] == $pagination['max_page'] || $pagination['max_page'] == 0 ) disabled @endif>{{ trans('order.main.next_page') }}</button>
		<button onclick="navPage(3)" class="ui button"@if($pagination['current_page'] == $pagination['max_page'] || $pagination['max_page'] == 0 ) disabled @endif>{{ trans('order.main.last_page') }}</button>
	</div> -->
@endsection

@section('styles')
<!-- <link href="{{ asset('css/order.css?v=' . $system_config['version']) }}" rel="stylesheet"> -->
<link href="{{ asset('css/order.css?v=' . $current_time) }}" rel="stylesheet">
<style>	
/* 寫入頁面限定CSS */
</style>
@endSection

@push('main_js')
<script>

	// 語系
    var langTrans = @json(trans('order'));

	// detect ini ajax
    var isReadyOrderInt = null
    var isReadyOrder = false

	// title
	var sportPriority = langTrans.sportPriority

	// order list data
    var orderListD = {}
	// var orderListD = {
	// 	"status": 1,
	// 	"data": {
	// 		"list": [
	// 		{
	// 			"id": 165,
	// 			"m_id": 165,
	// 			"bet_amount": "123.000",
	// 			"result_amount": "159.900",
	// 			"create_time": "2023-09-27 16:32:05",
	// 			"result_time": "2023-09-27 16:47:00",
	// 			"status": "已開獎",
	// 			"m_order": 0,
	// 			"bet_data": [
	// 			{
	// 				"market_bet_name": "主",
	// 				"market_bet_line": "-4.5",
	// 				"market_priority": 3,
	// 				"league_id": 7807,
	// 				"league_name": "韓國職棒聯賽",
	// 				"home_team_id": 299490,
	// 				"away_team_id": 299495,
	// 				"home_team_name": "NC恐龍",
	// 				"away_team_name": "起亞老虎",
	// 				"home_team_score": "4",
	// 				"away_team_score": "0",
	// 				"bet_rate": "1.300",
	// 				"status": "已開獎"
	// 			}
	// 			]
	// 		},
	// 		{
	// 			"id": 164,
	// 			"m_id": 164,
	// 			"bet_amount": "100.000",
	// 			"result_amount": "0.000",
	// 			"create_time": "2023-09-27 16:31:53",
	// 			"result_time": "2023-09-27 16:53:00",
	// 			"status": "已開獎",
	// 			"m_order": 0,
	// 			"bet_data": [
	// 			{
	// 				"market_bet_name": "小",
	// 				"market_bet_line": "5.5",
	// 				"market_priority": 5,
	// 				"league_id": 7807,
	// 				"league_name": "韓國職棒聯賽",
	// 				"home_team_id": 52555190,
	// 				"away_team_id": 52325610,
	// 				"home_team_name": "SSG登陸者",
	// 				"away_team_name": "鬥山熊",
	// 				"home_team_score": "3",
	// 				"away_team_score": "0",
	// 				"bet_rate": "2.250",
	// 				"status": "已開獎"
	// 			}
	// 			]
	// 		},
	// 		{
	// 			"id": 163,
	// 			"m_id": 163,
	// 			"bet_amount": "100.000",
	// 			"result_amount": "157.000",
	// 			"create_time": "2023-09-27 16:31:50",
	// 			"result_time": "2023-09-27 16:53:00",
	// 			"status": "已開獎",
	// 			"m_order": 0,
	// 			"bet_data": [
	// 			{
	// 				"market_bet_name": "大",
	// 				"market_bet_line": "5.5",
	// 				"market_priority": 5,
	// 				"league_id": 7807,
	// 				"league_name": "韓國職棒聯賽",
	// 				"home_team_id": 52555190,
	// 				"away_team_id": 52325610,
	// 				"home_team_name": "SSG登陸者",
	// 				"away_team_name": "鬥山熊",
	// 				"home_team_score": "3",
	// 				"away_team_score": "0",
	// 				"bet_rate": "1.570",
	// 				"status": "已開獎"
	// 			}
	// 			]
	// 		},
	// 		{
	// 			"id": 162,
	// 			"m_id": 162,
	// 			"bet_amount": "100.000",
	// 			"result_amount": "255.000",
	// 			"create_time": "2023-09-27 16:31:45",
	// 			"result_time": "2023-09-27 18:13:00",
	// 			"status": "已開獎",
	// 			"m_order": 0,
	// 			"bet_data": [
	// 			{
	// 				"market_bet_name": "客",
	// 				"market_bet_line": "-1.5",
	// 				"market_priority": 3,
	// 				"league_id": 7807,
	// 				"league_name": "韓國職棒聯賽",
	// 				"home_team_id": 52555190,
	// 				"away_team_id": 52325610,
	// 				"home_team_name": "SSG登陸者",
	// 				"away_team_name": "鬥山熊",
	// 				"home_team_score": "3",
	// 				"away_team_score": "0",
	// 				"bet_rate": "2.550",
	// 				"status": "已開獎"
	// 			}
	// 			]
	// 		},
	// 		{
	// 			"id": 161,
	// 			"m_id": 161,
	// 			"bet_amount": "100.000",
	// 			"result_amount": "0.000",
	// 			"create_time": "2023-09-27 16:31:43",
	// 			"result_time": "2023-09-27 18:13:00",
	// 			"status": "已開獎",
	// 			"m_order": 0,
	// 			"bet_data": [
	// 			{
	// 				"market_bet_name": "主",
	// 				"market_bet_line": "-1.5",
	// 				"market_priority": 3,
	// 				"league_id": 7807,
	// 				"league_name": "韓國職棒聯賽",
	// 				"home_team_id": 52555190,
	// 				"away_team_id": 52325610,
	// 				"home_team_name": "SSG登陸者",
	// 				"away_team_name": "鬥山熊",
	// 				"home_team_score": "3",
	// 				"away_team_score": "0",
	// 				"bet_rate": "1.480",
	// 				"status": "已開獎"
	// 			}
	// 			]
	// 		},
	// 		{
	// 			"id": 160,
	// 			"m_id": 160,
	// 			"bet_amount": "100.000",
	// 			"result_amount": "100.000",
	// 			"create_time": "2023-09-27 16:31:34",
	// 			"result_time": "2023-09-27 18:13:00",
	// 			"status": "已開獎",
	// 			"m_order": 0,
	// 			"bet_data": [
	// 			{
	// 				"market_bet_name": "客",
	// 				"market_bet_line": "",
	// 				"market_priority": 1,
	// 				"league_id": 7807,
	// 				"league_name": "韓國職棒聯賽",
	// 				"home_team_id": 52555190,
	// 				"away_team_id": 52325610,
	// 				"home_team_name": "SSG登陸者",
	// 				"away_team_name": "鬥山熊",
	// 				"home_team_score": "3",
	// 				"away_team_score": "0",
	// 				"bet_rate": "6.750",
	// 				"status": "已開獎"
	// 			}
	// 			]
	// 		},
	// 		{
	// 			"id": 159,
	// 			"m_id": 159,
	// 			"bet_amount": "100.000",
	// 			"result_amount": "100.000",
	// 			"create_time": "2023-09-27 16:31:31",
	// 			"result_time": "2023-09-27 18:13:00",
	// 			"status": "已開獎",
	// 			"m_order": 0,
	// 			"bet_data": [
	// 			{
	// 				"market_bet_name": "主",
	// 				"market_bet_line": "",
	// 				"market_priority": 1,
	// 				"league_id": 7807,
	// 				"league_name": "韓國職棒聯賽",
	// 				"home_team_id": 52555190,
	// 				"away_team_id": 52325610,
	// 				"home_team_name": "SSG登陸者",
	// 				"away_team_name": "鬥山熊",
	// 				"home_team_score": "3",
	// 				"away_team_score": "0",
	// 				"bet_rate": "1.090",
	// 				"status": "已開獎"
	// 			}
	// 			]
	// 		},
	// 		{
	// 			"id": 148,
	// 			"m_id": 148,
	// 			"bet_amount": "100.000",
	// 			"result_amount": "100.000",
	// 			"create_time": "2023-09-27 15:03:08",
	// 			"result_time": "2023-09-27 18:13:00",
	// 			"status": "已開獎",
	// 			"m_order": 0,
	// 			"bet_data": [
	// 			{
	// 				"market_bet_name": "客",
	// 				"market_bet_line": "",
	// 				"market_priority": 1,
	// 				"league_id": 7807,
	// 				"league_name": "韓國職棒聯賽",
	// 				"home_team_id": 52555190,
	// 				"away_team_id": 52325610,
	// 				"home_team_name": "SSG登陸者",
	// 				"away_team_name": "鬥山熊",
	// 				"home_team_score": "3",
	// 				"away_team_score": "0",
	// 				"bet_rate": "2.650",
	// 				"status": "已開獎"
	// 			}
	// 			]
	// 		},
	// 		{
	// 			"id": 144,
	// 			"m_id": 144,
	// 			"bet_amount": "100.000",
	// 			"result_amount": "0.000",
	// 			"create_time": "2023-09-27 14:58:58",
	// 			"result_time": "2023-09-27 16:53:00",
	// 			"status": "已開獎",
	// 			"m_order": 0,
	// 			"bet_data": [
	// 			{
	// 				"market_bet_name": "小",
	// 				"market_bet_line": "5.5",
	// 				"market_priority": 5,
	// 				"league_id": 7807,
	// 				"league_name": "韓國職棒聯賽",
	// 				"home_team_id": 52555190,
	// 				"away_team_id": 52325610,
	// 				"home_team_name": "SSG登陸者",
	// 				"away_team_name": "鬥山熊",
	// 				"home_team_score": "3",
	// 				"away_team_score": "0",
	// 				"bet_rate": "1.830",
	// 				"status": "已開獎"
	// 			}
	// 			]
	// 		},
	// 		{
	// 			"id": 143,
	// 			"m_id": 143,
	// 			"bet_amount": "123.000",
	// 			"result_amount": "0.000",
	// 			"create_time": "2023-09-27 14:58:46",
	// 			"result_time": "2023-09-27 16:53:00",
	// 			"status": "已開獎",
	// 			"m_order": 0,
	// 			"bet_data": [
	// 			{
	// 				"market_bet_name": "小",
	// 				"market_bet_line": "5.5",
	// 				"market_priority": 5,
	// 				"league_id": 7807,
	// 				"league_name": "韓國職棒聯賽",
	// 				"home_team_id": 52555190,
	// 				"away_team_id": 52325610,
	// 				"home_team_name": "SSG登陸者",
	// 				"away_team_name": "鬥山熊",
	// 				"home_team_score": "3",
	// 				"away_team_score": "0",
	// 				"bet_rate": "1.830",
	// 				"status": "已開獎"
	// 			}
	// 			]
	// 		}
	// 		]
	// 	},
	// 	"message": "SUCCESS_API_COMMON_ORDER_01",
	// 	"gzip": true
	// }
    var callOrderListData = { token: token, player: player, result: 0, page: 1 }
    const orderList_api = 'https://sportc.asgame.net/api/v2/common_order'

	function renderView() {
		let totalResultAmount = 0;

		orderListD.data.list.forEach((orderItem, orderIndex) => {
			createList(orderItem, orderIndex);
			orderItem.bet_data.forEach((betItem, betIndex) => {
				createBetDataDetails(orderItem, betItem, betIndex);
			});

			totalResultAmount += orderItem.result_amount;
		});

		console.log('Total Result Amount:', totalResultAmount);

    	return totalResultAmount;
	}

	console.log(sportListD);

	function createList(orderItem, orderIndex) {
		let orderData = $('tr[template="orderTemplate"]').clone();
		orderData.removeAttr('hidden');
		orderData.removeAttr('template');

		// Find elements within the cloned template
		let orderData_id = orderData.find('.orderData_id');
		let orderData_mOrder = orderData.find('.orderData_mOrder');
		let orderData_betAmount = orderData.find('.orderData_betAmount');
		let orderData_betDataDetails = orderData.find('.orderData_betDataDetails');
		let orderData_createdTime = orderData.find('.orderData_createdTime');
		let orderData_resultAmount = orderData.find('.orderData_resultAmount');
		let orderData_resultTime = orderData.find('.orderData_resultTime');
		let orderData_status = orderData.find('.orderData_status');

		// Set content for the found elements
		orderData_id.html(orderItem.id);
		orderData_mOrder.html(orderItem.m_order);
		orderData_betDataDetails.attr('id', 'betDataDetails_' + orderItem.id)
		orderData_betAmount.html(orderItem.bet_amount);
		orderData_createdTime.html(orderItem.create_time);
		orderData_resultAmount.html(orderItem.result_amount);
		orderData_resultTime.html(orderItem.result_time);
		orderData_status.html(orderItem.status);

		$('#countTr').before(orderData);
	}

	function createBetDataDetails(orderItem, betItem, betIndex) {
		let betDataDetailsId = 'betDataDetails_' + orderItem.id;
		let orderDataBetDataDetails = $('#' + betDataDetailsId);

		// Remove any existing bet data details before appending a new one
		orderDataBetDataDetails.empty();

		let betDataDetails = $('span[template="betDataDetailsTemp"]').clone();
		betDataDetails.removeAttr('hidden');
		betDataDetails.removeAttr('template');

		// Find elements within the cloned template
		let betDataDetails_leagueName = betDataDetails.find('.betDataDetails_leagueName');
		let betDataDetails_HomeName = betDataDetails.find('.betDataDetails_HomeName');
		let betDataDetails_AwayName = betDataDetails.find('.betDataDetails_AwayName');
		let betDataDetails_BetName_Line = betDataDetails.find('.betDataDetails_BetNameLine');
		let betDataDetails_BetLine = betDataDetails.find('.betDataDetails_BetLine');

		// Set content for the found elements
		betDataDetails_leagueName.html(betItem.league_name);
		betDataDetails_HomeName.html(betItem.home_team_name);
		betDataDetails_AwayName.html(betItem.away_team_name);
		betDataDetails_BetName.html(betItem.market_bet_name);
		betDataDetails_BetLine.html(betItem.market_bet_line + sportPriority[betItem.market_priority]);
		// Append the new betDataDetails to the orderDataBetDataDetails
		orderDataBetDataDetails.append(betDataDetails);
	}

	function createTotal() {
		let orderDataTotal = $('tr[template="orderTotalTemplate"]').clone();
		orderDataTotal.removeAttr('hidden');
		orderDataTotal.removeAttr('template');

		// Find elements within the cloned template
		let orderData_totalBetAmount = orderDataTotal.find('.orderData_totalBetAmount');
		let orderData_totalResultAmount = orderDataTotal.find('.orderData_totalResultAmount');

		// Set content for the found elements
		orderData_totalBetAmount.html('0');
		orderData_totalResultAmount.html('0');

		$('#orderTr').after(orderDataTotal);
	}

	createTotal();


  	// 寫入頁面限定JS
  	$(document).ready(function() {
		// ===== DATA LATER =====
		if( searchData.result ) callOrderListData.result = parseInt(searchData.result) // get result params
        caller(orderList_api, callOrderListData, orderListD) // orderListD
		
		// check if api are all loaded every 500 ms 
        isReadyOrderInt = setInterval(() => {
            if (orderListD.status === 1) { isReadyOrder = true; }
            if( isReadyOrder && isReadyCommon) {
                $('#dimmer').dimmer('hide'); // hide loading
                $('#wrap').css('opacity', 1); // show the main content
				renderView()
                clearInterval(isReadyOrderInt); // stop checking
            }
        }, 500);
	});

	// toggle the m_order details content
	function toggleInfo(key, e) {
		$('div[key="' + key + '"]:not(:first-child)').slideToggle();
		let isopen = $(e).attr('isopen')
		let switchStr = ''
		if(isopen === 'false') {
			switchStr = langTrans.main.close + '▾'
			isopen = true
		} else {
			switchStr =  langTrans.main.open + '▸'
			isopen = false
		}
		$(e).html(switchStr)
		$(e).attr('isopen', isopen)
	}
</script>
@endpush