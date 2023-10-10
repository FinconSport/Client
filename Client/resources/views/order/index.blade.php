@extends('layout.app')

@section('content')
	<div class="search-statistic-container">
		<div class="search-bar-container">
			<div class="select-con">
				<select id="selectOption" name="selectOption">
					<option value="">{{ trans('order.main.select_sport') }}</option>
					<option value="baseball">Baseball</option>
					<option value="football">Football</option>
					<option value="basketball">Basketball</option>
				</select>
			</div>
			<div class="datecalendar-con">
				<div class="datepicker-con">
					<div class="input-group date">
						<input type="text" class="form-control" id="datepicker_from">
						<span class="input-group-text"><i class="fa-sharp fa-regular fa-calendar-days" style="color: #415a5b;"></i></span>
					</div>
					<span class="date-divider"> ~ </span>
					<div class="input-group date">
						<input type="text" class="form-control" id="datepicker_to">
						<span class="input-group-text"><i class="fa-sharp fa-regular fa-calendar-days" style="color: #415a5b;"></i></span>
					</div>
				</div>
				<div class="datebutton-cons">
					<button class="dateCalendarBtn">{{ trans('order.main.last_month') }}</button>
					<button class="dateCalendarBtn">{{ trans('order.main.last_week') }}</button>
					<button class="dateCalendarBtn">{{ trans('order.main.yesterday') }}</button>
					<button class="dateCalendarBtn">{{ trans('order.main.today') }}</button>
					<button class="dateCalendarBtn">{{ trans('order.main.this_week') }}</button>
					<button class="dateCalendarBtn">{{ trans('order.main.this_month') }}</button>
				</div>
			</div>
		</div>
		<div class="statistic-container" id="countTr" template="orderTotalTemplate" hidden>
			<div class="stats-container">
				<span><i class="fa-sharp fa-regular fa-rectangle-list" style="color: #415b5a;margin-right: 0.5rem;"></i>{{ trans('order.main.total_bet_count') }}</span>
				<p class="total-bet-count"></p>
			</div>
			<div class="stats-container">
				<span><i class="fa-solid fa-circle-dollar-to-slot" style="color: #415b5a;margin-right: 0.5rem;"></i>{{ trans('order.main.total_bet_amount') }}</span>
				<p class="orderData_totalBetAmount"></p>
			</div>
			<div class="stats-container">
				<span><i class="fa-sharp fa-solid fa-star" style="color: #415a5b;margin-right: 0.5rem;"></i>{{ trans('order.main.total_effective_amount') }}</span>
				<p class="total-effective-amount"></p>
			</div>
			<div class="stats-container">
				<span><i class="fa-sharp fa-solid fa-trophy" style="color: #415a5b;margin-right: 0.5rem;"></i>{{ trans('order.main.total_result_amount') }}</span>
				<p class="orderData_totalResultAmount"></p>
			</div>
			<div class="stats-container total-win-amount">
				<span><i class="fa-solid fa-dollar-sign" style="color: #415a5b;margin-right: 0.5rem;"></i>{{ trans('order.main.total_win_amount') }}</span>
				<p class="total-win-amount"></p>
			</div>
		</div>
	</div>
    <div id="orderContainer">
        <div id="tableContainer" style="overflow: auto;">
            <table id="orderTable" class="cell-border w-100 text-center">
                <thead>
                    <tr class="no-border-top">
                        <th style="width: 10%;" class="no-border-left">{{ trans('order.main.index') }}</th>
                        <th style="width: 10%;">{{ trans('order.main.bet_type') }}</th>
                        <th style="width: 17%;">{{ trans('order.main.event') }}</th>
                        <th style="width: 10%;">{{ trans('order.main.bet_way') }}</th>
                        <th style="width: 10%;">{{ trans('order.main.result') }}</th>
                        <th style="width: 10%;">{{ trans('order.main.bet_amount') }}</th>
                        <th style="width: 10%;">{{ trans('order.main.effective_amount') }}</th>
						<th style="width: 10%;">{{ trans('order.main.result_amount') }}</th>
						<th style="width: 10%;" class="no-border-right">{{ trans('order.main.win_amount') }}</th>
                    </tr>
                </thead>
                <tbody id="orderDataTemp">
                    <tr id="orderTr" template="orderTemplate" hidden>
                        <td style="width: 10%;" class="orderData_id"></td>
                        <td style="width: 10%;text-align:left;"><span class="orderData_sportType"></span><br><span class="orderData_mOrder"></span></td>
                        <td style="width: 17%;" class="orderData_betData_Event"></td>
                        <td style="width: 10%;" class="orderData_betData_BetWay"></td>
                        <td style="width: 10%;" class="orderData_betData_Result"></td>
                        <td style="width: 10%;" class="text-right"><span class="orderData_betAmount"></span></td>
                        <td style="width: 10%;"></td>
						<td style="width: 10%;"><span class="orderData_resultAmount"></span></td>
						<td style="width: 10%;"><span class="orderData_WinLoss"></span></td>
                    </tr>
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
<script src="{{ asset('js/bootstrap.min.js?v=' . $current_time) }}"></script>
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

	let totalResultAmount = 0;
	let totalBetAmount = 0;
	let WinLoss = 0;

	// infinite scroll control
	var fetchMoreLock = false
	var isLastPage = false

	function renderView() {
		if (orderListD && orderListD.data.list) {
			orderListD.data.list.forEach((orderItem, orderIndex) => {
				const resultAmount = parseFloat(orderItem.result_amount);
				const betAmount = parseFloat(orderItem.bet_amount);
				const winLoss = resultAmount - betAmount;

				createList(orderItem, orderIndex, winLoss);
				orderItem.bet_data.forEach((betItem, betIndex) => {
					createBetDataDetails(orderItem, betItem, betIndex);
				});

				// Validate and accumulate total
				totalResultAmount += resultAmount;
				totalBetAmount += betAmount;
				totalResultAmount += winLoss;

				// Accumulate the total WinLoss
				WinLoss += winLoss;

			});

			// After accumulating the totals, round them to two decimal places
			totalResultAmount = parseFloat(totalResultAmount.toFixed(2));
			totalBetAmount = parseFloat(totalBetAmount.toFixed(2));

			if( orderListD.data.list.length !== 20 || orderListD.data.list.length === 0 ) isLastPage = true
				isLastPage && $('#noMoreData').show()
			}
	}


	function createList(orderItem, orderIndex, winLoss) {
		const orderData = $('tr[template="orderTemplate"]').clone().removeAttr('hidden').removeAttr('template');
		const orderDataId = orderData.find('.orderData_id');
		const orderDataSportType = orderData.find('.orderData_sportType');
		const orderDataMOrder = orderData.find('.orderData_mOrder');
		const orderDataBetAmount = orderData.find('.orderData_betAmount');
		const orderDataBetEvent = orderData.find('.orderData_betData_Event');
		const orderDataBetBetWay = orderData.find('.orderData_betData_BetWay');
		const orderDataBetResult = orderData.find('.orderData_betData_Result');
		const orderDataResultAmount = orderData.find('.orderData_resultAmount');
		const orderDataResultTime = orderData.find('.orderData_resultTime');
		const orderDataWinLoss = orderData.find('.orderData_WinLoss');

		let sportName = '';

		for (const bet of orderItem.bet_data) {
			const matchingSport = sportListD.data.find(sport => sport.sport_id === bet.sport_id);
			sportName = matchingSport ? matchingSport.name : '';
			orderDataSportType.html(sportName);
		}

		orderDataId.html(orderItem.m_order === 1 ? orderItem.m_id : orderItem.id);
		orderDataMOrder.html(orderItem.m_order === 0 ? '{{ trans("order.main.sport") }}' : '{{ trans("order.main.morder") }}');
		orderDataBetEvent.attr('id', `betDataDetailsEvent_${orderItem.id}`);
		orderDataBetBetWay.attr('id', `betDataDetailsBetWay_${orderItem.id}`);
		orderDataBetResult.attr('id', `betDataDetailsResult_${orderItem.id}`);
		orderDataBetAmount.html(orderItem.bet_amount);
		// orderDataCreatedTime.html(orderItem.create_time);
		orderDataResultAmount.html(orderItem.result_amount === null ? '' : orderItem.result_amount);
		orderDataResultTime.html(orderItem.result_time === null ? '' : orderItem.result_time);
		orderDataWinLoss.html(winLoss);

		$('#orderDataTemp').append(orderData);
	}

	function createBetDataDetails(orderItem, betItem, betIndex) {
		const createHtmlElement = (className, content) => $('<div>').html(`${content}`).addClass(className);

		const betDataEventID = `betDataDetailsEvent_${orderItem.id}`; 
		const orderDataBetEvent = $(`#${betDataEventID}`);
		const betDataEventContainer = $('<div class="betaDetcon">');
		betDataEventContainer.append(
			createHtmlElement('mb-3', `${betItem.league_name} (${orderItem.create_time})`),
			createHtmlElement('', `${betItem.home_team_name} VS ${betItem.away_team_name} <span style="color:red;">(${betItem.home_team_score === null ? '' : ` ${betItem.home_team_score}`}-${betItem.away_team_score === null ? '' : ` ${betItem.away_team_score}`})</span>`)
		);

		const betDataBetWayID = `betDataDetailsBetWay_${orderItem.id}`; 
		const orderDataBetWay = $(`#${betDataBetWayID}`);
		const betDataBetWayContainer = $('<div class="betaDetcon">');
		betDataBetWayContainer.append(
			createHtmlElement('', `${betItem.market_name}<br> <span style="color:green;">(${betItem.market_bet_name})${betItem.market_bet_line}</span> @<span style="color:#c79e42;">${betItem.bet_rate}</span>`),
		);

		const betDataResultID = `betDataDetailsResult_${orderItem.id}`; 
		const orderDataResult = $(`#${betDataResultID}`);
		const betDataResultContainer = $('<div class="betaDetcon">');
		betDataResultContainer.append(
			createHtmlElement('text-right', `${betItem.status}<br> ${orderItem.result_time}`),
		);

		
		if (betIndex > 0) {
			betDataEventContainer.addClass('hide-betaDetcon');
			betDataBetWayContainer.addClass('hide-betaDetcon');
			betDataResultContainer.addClass('hide-betaDetcon');
			$(`#betDataDetailsEvent_${orderItem.id} .order-toggleButton`).addClass('showbutton');
			$(`betDataDetailsBetWay_${orderItem.id} .order-toggleButton`).addClass('showbutton');
			$(`#betDataDetailsResult_${orderItem.id} .order-toggleButton`).addClass('showbutton');
		}

		orderDataBetEvent.append(betDataEventContainer);
		orderDataBetWay.append(betDataBetWayContainer);
		orderDataResult.append(betDataResultContainer);

		const betDataLength = orderItem.bet_data.length;

		if (betIndex === 0) {
			const button = $(`<button class='order-toggleButton'>{{ trans('order.main.expand') }} (${betDataLength})</button>`);
			button.on('click', function () {
			orderDataBetEvent.find('.hide-betaDetcon').slideToggle();
			orderDataBetWay.find('.hide-betaDetcon').slideToggle();
			orderDataResult.find('.hide-betaDetcon').slideToggle();
			button.text(button.text() === '{{ trans('order.main.expand') }} (' + betDataLength + ')' ? '{{ trans('order.main.close') }}' : '{{ trans('order.main.expand') }} (' + betDataLength + ')');
			});
			button.appendTo(orderDataBetEvent);
			button.appendTo(orderDataBetWay);
			button.appendTo(orderDataResult);
		}
	}

	
	function createTotal() {
		const orderDataTotal = $('#countTr').clone().removeAttr('hidden').removeAttr('template');
		orderDataTotal.find('.orderData_totalBetAmount').text(totalBetAmount);
		orderDataTotal.find('.orderData_totalResultAmount').text(totalResultAmount);
		$('.search-bar-container').after(orderDataTotal);
	}

	//updateTotal when new data is loaded
	function updateTotal() {
		$('.orderData_totalBetAmount').text(totalBetAmount);
		$('.orderData_totalResultAmount').text(totalResultAmount);
	}

	// 下拉更多資料
	async function fetchMore() {
		console.log('fetchMore')
		$('#loader').show() // loading transition
		callOrderListData.page += 1
		await caller(orderList_api, callOrderListData, orderListD, 1) // resultListD
		renderView()
		updateTotal()
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
				renderView();
				createTotal(totalResultAmount, totalBetAmount);
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

	$(document).ready(function() {
        $('#datepicker_from').datepicker();
		$('#ui-datepicker-div').addClass('custom-datepicker-class');
		$('#datepicker_to').datepicker();
    });
</script>
@endpush