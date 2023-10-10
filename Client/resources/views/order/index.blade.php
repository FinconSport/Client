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
						<span class="input-group-text"></span>
					</div>
					<span class="date-divider"> ~ </span>
					<div class="input-group date">
						<input type="text" class="form-control" id="datepicker_to">
						<span class="input-group-text"></span>
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
		<div class="statistic-container">
			<div class="stats-total-bet-count">
				<span><i class="fa-sharp fa-regular fa-rectangle-list" style="color: #415b5a;"></i> {{ trans('order.main.total_bet_count') }}</span>
			</div>
			<div class="stats-total-bet-amount">
				<span><i class="fa-solid fa-circle-dollar-to-slot" style="color: #415b5a;"></i> {{ trans('order.main.total_bet_amount') }}</span>
			</div>
			<div class="stats-total-effective-amount">
				<span><i class="fa-sharp fa-solid fa-star" style="color: #415a5b;"></i> {{ trans('order.main.total_effective_amount') }}</span>
			</div>
			<div class="stats-total-result-amount">
				<span><i class="fa-sharp fa-solid fa-trophy" style="color: #415a5b;"></i> {{ trans('order.main.total_result_amount') }}</span>
			</div>
			<div class="stats-total-win-amount">
				<span><i class="fa-solid fa-dollar-sign" style="color: #415a5b;"></i> {{ trans('order.main.total_win_amount') }}</span>
			</div>
		</div>
	</div>
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

	// infinite scroll control
	var fetchMoreLock = false
	var isLastPage = false

	function renderView() {
		if (orderListD && orderListD.data.list) {
			orderListD.data.list.forEach((orderItem, orderIndex) => {
				createList(orderItem, orderIndex);
				orderItem.bet_data.forEach((betItem, betIndex) => {
					createBetDataDetails(orderItem, betItem, betIndex);
				});

				// Validate and accumulate total
				totalResultAmount += parseFloat(orderItem.result_amount) || 0;
				totalBetAmount += parseFloat(orderItem.bet_amount) || 0;
			});

			if( orderListD.data.list.length !== 20 || orderListD.data.list.length === 0 ) isLastPage = true
			isLastPage && $('#noMoreData').show()
		}
	}

	function createList(orderItem, orderIndex) {
		const orderData = $('tr[template="orderTemplate"]').clone().removeAttr('hidden').removeAttr('template');
		const orderDataId = orderData.find('.orderData_id');
		const orderDataSportType = orderData.find('.orderData_sportType');
		const orderDataMOrder = orderData.find('.orderData_mOrder');
		const orderDataBetAmount = orderData.find('.orderData_betAmount');
		const orderDataBetDataDetails = orderData.find('.orderData_betDataDetails');
		const orderDataCreatedTime = orderData.find('.orderData_createdTime');
		const orderDataResultAmount = orderData.find('.orderData_resultAmount');
		const orderDataResultTime = orderData.find('.orderData_resultTime');
		const orderDataStatus = orderData.find('.orderData_status');

		let sportName = '';

		for (const bet of orderItem.bet_data) {
			const matchingSport = sportListD.data.find(sport => sport.sport_id === bet.sport_id);
			sportName = matchingSport ? matchingSport.name : '';
			orderDataSportType.html(sportName);
		}

		orderDataId.html(orderItem.m_order === 1 ? orderItem.m_id : orderItem.id);
		orderDataMOrder.html(orderItem.m_order === 0 ? '{{ trans("order.main.sport") }}' : '{{ trans("order.main.morder") }}');
		orderDataBetDataDetails.attr('id', `betDataDetails_${orderItem.id}`);
		orderDataBetAmount.html(orderItem.bet_amount);
		orderDataCreatedTime.html(orderItem.create_time);
		orderDataResultAmount.html(orderItem.result_amount === null ? '' : orderItem.result_amount);
		orderDataResultTime.html(orderItem.result_time === null ? '' : orderItem.result_time);
		orderDataStatus.html(orderItem.status);

		$('#countTr').before(orderData);
	}

	function createBetDataDetails(orderItem, betItem, betIndex) {
		const betDataDetailsId = `betDataDetails_${orderItem.id}`;
		const orderDataBetDataDetails = $(`#${betDataDetailsId}`);
		const betDataDetailsContainer = $('<div class="betaDetcon">');
		
		const createHtmlElement = (className, content) => $('<div>').html(`<span>${content}</span>`).addClass(className);
		
		betDataDetailsContainer.append(
			createHtmlElement('mb-3', betItem.league_name),
			createHtmlElement('', `${betItem.home_team_name} VS ${betItem.away_team_name}`),
			createHtmlElement('', `${betItem.market_name} (${betItem.market_bet_name}${betItem.market_bet_line})<span> @${betItem.bet_rate}</span>`),
			createHtmlElement('', `${betItem.home_team_name}${betItem.home_team_score === null ? '' : ` ${betItem.home_team_score}`}`),
			createHtmlElement('', `${betItem.away_team_name}${betItem.away_team_score === null ? '' : ` ${betItem.away_team_score}`}`),
		);

		if (betIndex > 0) {
			betDataDetailsContainer.addClass('hide-betaDetcon');
			$(`#betDataDetails_${orderItem.id} .order-toggleButton`).addClass('showbutton');
		}

		orderDataBetDataDetails.append(betDataDetailsContainer);

		const betDataLength = orderItem.bet_data.length;

		if (betIndex === 0) {
			const button = $(`<button class='order-toggleButton'>{{ trans('order.main.expand') }} (${betDataLength})</button>`);
			button.on('click', function () {
			orderDataBetDataDetails.find('.hide-betaDetcon').slideToggle();
			button.text(button.text() === '{{ trans('order.main.expand') }} (' + betDataLength + ')' ? '{{ trans('order.main.close') }}' : '{{ trans('order.main.expand') }} (' + betDataLength + ')');
			});
			button.appendTo(orderDataBetDataDetails);
		}
	}

	
	function createTotal() {
		const orderDataTotal = $('#countTr').clone().removeAttr('hidden').removeAttr('template');
		orderDataTotal.find('.orderData_totalBetAmount').text(totalBetAmount);
		orderDataTotal.find('.orderData_totalResultAmount').text(totalResultAmount);
		$('#orderDataTemp').append(orderDataTotal);
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