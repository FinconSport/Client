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
                        <th style="width: 30%;">{{ trans('order.main.detail') }}</th>
                        <th style="width: 12%;">{{ trans('order.main.bet_money') }}</th>
                        <th style="width: 12.5%;">{{ trans('order.main.return_money') }}</th>
                        <th style="width: 10%;" class="no-border-right">{{ trans('order.main.status') }}</th>
                    </tr>
                </thead>
                <tbody id="orderDataTemp">
                    <tr id="orderTr" template="orderTemplate" hidden>
                        <td class="no-border-left orderData_id"></td>
                        <td>
                            <span class="orderData_sportType"></span>
                        </td>
                        <td class="orderData_mOrder"></td>
                        <td class="orderData_betDataDetails">
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
                        <td style="width: 30%;" class="p-0"><div class="text-white bg-deepgreen" id="orderCountTotal">{{ trans('order.main.total') }}</div></td>
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

	// order list data
    var orderListD = {}

    var callOrderListData = { token: token, player: player, result: 0, page: 1 }
    const orderList_api = 'https://sportc.asgame.net/api/v2/common_order'


	function renderView() {
		let totalResultAmount = 0;
		let totalBetAmount = 0;

		orderListD.data.list.forEach((orderItem, orderIndex) => {
			createList(orderItem, orderIndex);
			orderItem.bet_data.forEach((betItem, betIndex) => {
			createBetDataDetails(orderItem, betItem, betIndex);
			});

			// Validate and accumulate total
			totalResultAmount += parseFloat(orderItem.result_amount) || 0;
			totalBetAmount += parseFloat(orderItem.bet_amount) || 0;
		});

		createTotal(totalResultAmount, totalBetAmount);

		
	}

	function createList(orderItem, orderIndex) {
		let orderData = $('tr[template="orderTemplate"]').clone();
		orderData.removeAttr('hidden');
		orderData.removeAttr('template');

		// Find elements within the cloned template
		let orderData_id = orderData.find('.orderData_id');
		let orderData_sportType = orderData.find('.orderData_sportType');
		let orderData_mOrder = orderData.find('.orderData_mOrder');
		let orderData_betAmount = orderData.find('.orderData_betAmount');
		let orderData_betDataDetails = orderData.find('.orderData_betDataDetails');
		let orderData_createdTime = orderData.find('.orderData_createdTime');
		let orderData_resultAmount = orderData.find('.orderData_resultAmount');
		let orderData_resultTime = orderData.find('.orderData_resultTime');
		let orderData_status = orderData.find('.orderData_status');

		let sportName = "";

		// Iterate through orderListD.data.list
		for (const item of orderListD.data.list) {
			for (const bet of item.bet_data) {
				const sportId = bet.sport_id;
				const matchingSport = sportListD.data.find(sport => sport.sport_id === sportId);
				sportName = matchingSport ? matchingSport.name : "";
			}
		}
		// Set content for the found elements
		orderData_id.html(orderItem.id);
		orderData_sportType.html(sportName); 
		orderData_mOrder.html(orderItem.m_order === 0 ? '{{ trans("order.main.sport") }}' : '{{ trans("order.main.morder") }}');
		orderData_betDataDetails.attr('id', 'betDataDetails_' + orderItem.id)
		orderData_betAmount.html(orderItem.bet_amount);
		orderData_createdTime.html(orderItem.create_time);
		orderData_resultAmount.html(orderItem.result_amount === null ? '' : orderItem.result_amount);
		orderData_resultTime.html(orderItem.result_time === null ? '' : orderItem.result_time);
		orderData_status.html(orderItem.status);

		$('#countTr').before(orderData);
	}

	function createBetDataDetails(orderItem, betItem, betIndex) {
		let betDataDetailsId = 'betDataDetails_' + orderItem.id;
		let orderDataBetDataDetails = $('#' + betDataDetailsId);

		// Create a container for each bet_data
		let betDataDetailsContainer = $('<div class="betaDetcon">');

		// Set content
		let betDataDetails_leagueName = $('<div class="mb-3">').html('<span>' + betItem.league_name + '</span>');
		let betDataDetails_HomeName = $('<div>').html('<span>' + betItem.home_team_name + ' VS ' + betItem.away_team_name + '</span>');
		let betDataDetails_MarketNameLineRate = $('<div>').html('<span>' + betItem.market_name + ' (' +betItem.market_bet_name + betItem.market_bet_line + ')</span><span> @' + betItem.bet_rate + '</span>');
		let betDataDetails_HomeTeam = $('<div>').html('<span>' + betItem.home_team_name + '</span>' + (betItem.home_team_score === null ? '' : '<span> ' + betItem.home_team_score + '</span>'));
		let betDataDetails_AwayTeam = $('<div>').html('<span>' + betItem.away_team_name + '</span>' + (betItem.away_team_score === null ? '' : '<span> ' + betItem.away_team_score + '</span>'));
		let betDataDetails_Status = $('<div>').html('<span>' + betItem.status + '</span>');

		// Append the elements to the container
		betDataDetailsContainer.append(
			betDataDetails_leagueName,
			betDataDetails_HomeName,
			betDataDetails_MarketNameLineRate,
			betDataDetails_HomeTeam,
			betDataDetails_AwayTeam,
			betDataDetails_Status
		);

		if (betIndex > 0) { // Check if it's not the first item
			betDataDetailsContainer.addClass('hide-betaDetcon');
			$('#betDataDetails_' + orderItem.id + ' .order-toggleButton').addClass('showbutton');
		}

		// Append the container to the orderDataBetDataDetails
		orderDataBetDataDetails.append(betDataDetailsContainer);
		console.log('count:' + betIndex);

		if (betIndex === 0) { // Check if it's the first item
			var button = $("<button class='order-toggleButton'>{{ trans('order.main.expand') }}</button>");
			button.on('click', function () {
				orderDataBetDataDetails.find('.hide-betaDetcon').slideToggle();
				if (button.text() === '{{ trans('order.main.expand') }}') {
					button.text('{{ trans('order.main.close') }}');
				} else {
					button.text('{{ trans('order.main.expand') }}');
				}
			});
			button.appendTo(orderDataBetDataDetails);
		}
	}

	function createTotal(totalResultAmount, totalBetAmount) {
		let orderDataTotal = $('#countTr').clone();

		orderDataTotal.removeAttr('hidden');
		orderDataTotal.removeAttr('template');

		orderDataTotal.find('.orderData_totalBetAmount').text(totalBetAmount);
		orderDataTotal.find('.orderData_totalResultAmount').text(totalResultAmount);

		$('#orderDataTemp').append(orderDataTotal);
	}

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