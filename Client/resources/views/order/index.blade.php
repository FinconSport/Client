@extends('layout.app')

@section('content')
	<div class="search-statistic-container">
		<div class="search-bar-container">
			<div class="select-con">
			<select id="selectOption" name="selectOption"  onchange="redirectToPage(this)">
				<option value="{{ trans('common.left_menu.unsettled') }}" data-link="?result=0">{{ trans('common.left_menu.unsettled') }}</option>
				<option value="{{ trans('common.left_menu.settled') }}" data-link="?result=1">{{ trans('common.left_menu.settled') }}</option>
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
				<p class="orderData_totalBetCount"></p>
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
				<p class="orderData_totalWinAmount"></p>
			</div>
		</div>
	</div>
    <div id="orderContainer">
        <div id="tableContainer" style="overflow: auto;">
            <table id="orderTable" class="cell-border w-100 text-center">
                <thead>
                    <tr class="no-border-top">
                        <th style="width: 8%;" class="no-border-left">{{ trans('order.main.index') }}</th>
                        <th style="width: 9%;">{{ trans('order.main.bet_type') }}</th>
                        <th style="width: 21%;">{{ trans('order.main.event') }}</th>
                        <th style="width: 10%;">{{ trans('order.main.bet_way') }}</th>
                        <th style="width: 10%;">{{ trans('order.main.result') }}</th>
                        <th style="width: 10%;">{{ trans('order.main.bet_amount') }}</th>
                        <th style="width: 10%;">{{ trans('order.main.effective_amount') }}</th>
						<th style="width: 10%;">{{ trans('order.main.result_amount') }}</th>
						<th style="width: 10%;" class="no-border-right">{{ trans('order.main.win_amount') }}</th>
                    </tr>
                </thead>
                <tbody id="orderDataTemp">
                    <tr class="orderData_main" template="orderTemplate" hidden>
                        <td style="width: 8%;" class="orderData_id"></td>
                        <td style="width: 9%;text-align:left;"><span class="orderData_sportType"></span><br><span class="orderData_mOrder"></span></td>
                        <td style="width: 21%;" class="orderData_betData_Event"></td>
                        <td style="width: 10%;" class="orderData_betData_BetWay"></td>
                        <td style="width: 10%;" class="orderData_betData_Result"></td>
                        <td style="width: 10%;" class="text-right orderData_betAmount"></td>
                        <td style="width: 10%;"></td>
						<td style="width: 10%;" class="text-right orderData_resultAmount"></td>
						<td style="width: 10%;" class="text-right orderData_WinLoss"></td>
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
			<div id="noMoreData" style="display: none;">
				<td colspan="16"><p class="mb-0 p-3">{{ trans('match.main.nomoredata') }}</p></td>
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

	let totalBetItemCount = 0;
	let totalBetAmount = 0;
	let totalResultAmount = 0;
	let totalWinLoss = 0;
	

	// infinite scroll control
	var fetchMoreLock = false
	var isLastPage = false

	function renderView() {
		if (orderListD && orderListD.data.list) {
			orderListD.data.list.forEach((orderItem, orderIndex) => {
				const betItemCounter = orderItem.bet_data.length; 
				const betAmount = parseFloat(orderItem.bet_amount);
				const resultAmount = parseFloat(orderItem.result_amount);
				const winLoss = resultAmount - betAmount;

				createList(orderItem, orderIndex, winLoss);
				orderItem.bet_data.forEach((betItem, betIndex) => {
					createBetDataDetails(orderItem, betItem, betIndex);
				});

				// Validate and accumulate total
				totalBetItemCount += betItemCounter;
				totalBetAmount += betAmount;
				totalResultAmount += resultAmount;
				totalWinLoss += winLoss;
			});

			// After accumulating the totals, round them to two decimal places
			totalResultAmount = parseFloat(totalResultAmount.toFixed(2));
			totalBetAmount = parseFloat(totalBetAmount.toFixed(2));
			totalWinLoss = parseFloat(totalWinLoss.toFixed(2));

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
		const winLossValue = parseFloat(winLoss);
		if (winLossValue >= 0) {
			orderDataWinLoss.css('color', 'red'); // Set text color to red
		} else {
			orderDataWinLoss.css('color', 'green'); // Set text color to green
		}

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
		orderDataBetAmount.html(orderItem.bet_amount === null ? '-' : orderItem.bet_amount);
		orderDataResultAmount.html(orderItem.result_amount === null ? '-' : orderItem.result_amount);
		orderDataResultTime.html(orderItem.result_time === null ? '' : orderItem.result_time);
		orderDataWinLoss.html(winLoss = isNaN(winLoss) ? '-' : winLoss);

		$('#orderDataTemp').append(orderData);
	}

	function createBetDataDetails(orderItem, betItem, betIndex) {
		const createHtmlElement = (className, content) => $('<div>').html(content).addClass(className);
		const orderDataBetEvent = $(`#betDataDetailsEvent_${orderItem.id}`);
		const orderDataBetWay = $(`#betDataDetailsBetWay_${orderItem.id}`);
		const orderDataResult = $(`#betDataDetailsResult_${orderItem.id}`);
		const parentElement = orderDataBetEvent.parent();
		// event column 
		function createBetDataEventContent(betItem, orderItem) {
			return `${betItem.league_name} (${formatDateTime(orderItem.create_time)})<br>
					${betItem.home_team_name} VS ${betItem.away_team_name} 
					<span style="color:red;white-space:nowrap;">
						${betItem.home_team_score !== null || betItem.away_team_score !== null ? `(` : ''}
						${betItem.home_team_score !== null ? `${betItem.home_team_score}` : ''}
						${betItem.away_team_score !== null && betItem.home_team_score !== null ? '-' : ''}
						${betItem.away_team_score !== null ? `${betItem.away_team_score}` : ''}
						${betItem.home_team_score !== null || betItem.away_team_score !== null ? `)` : ''}
					</span>`;
		}
		// bet way column 
		function createBetWayContent(betItem) {
			const marketName = betItem.market_name;
			const marketBetName = betItem.market_bet_name;
			const marketBetLine = betItem.market_bet_line;
			const betRate = betItem.bet_rate;
			const content = `${marketName}<br><span style="color:green;">(${marketBetName})${marketBetLine}</span>`;
			if (betRate !== null) {
				return `${content} @<span style="color:#c79e42;">${betRate}</span>`;
			} else {
				return content;
			}
		}
		//result column
		function createResultContent(betItem, orderItem) {
			const resultText =
				betItem.status === 0 ? `<span style="color: green;">{{ trans("order.result_precent.0") }}</span>` :
				betItem.status === 1 ? `<span style="color: red;">{{ trans("order.result_precent.1") }}</span>` :
				betItem.status === 2 ? `<span style="color: red;">{{ trans("order.result_precent.2") }}</span>` :
				betItem.status === 3 ? `<span style="color: green;">{{ trans("order.result_precent.3") }}</span>` :
				betItem.status === 4 ? `<span style="color: #c79e42;">{{ trans("order.result_precent.4") }}</span>` :
				`${betItem.status}`;
			const resultTime = formatDateTime(orderItem.result_time);
			return createHtmlElement('text-right', `${resultText}<br>${resultTime}`);
		}
		
		const BetDataEventContent = createBetDataEventContent(betItem, orderItem);
		const betWayContent = createBetWayContent(betItem);
		const resultContent = createResultContent(betItem, orderItem);
		//content event ,bet way , result
		const betDataEventContainer = $('<div class="betaDetcon">').append(createHtmlElement('', BetDataEventContent));
		const betDataBetWayContainer = $('<div class="betaDetcon">').append(createHtmlElement('text-leftt', betWayContent));
		const betDataResultContainer = $('<div class="betaDetcon">').append(resultContent);

		if (betIndex > 0) {
			//append in another td if have another bet_item
			const dynamicId = `additionalTr_${betItem.league_id}${betItem.league_name}_${betIndex}`;
			const dynamicClass = `additionalTr_${orderItem.m_id}`;
			const additionalTr = $('<tr></tr>').attr('id', dynamicId).addClass(dynamicClass).addClass('orderData_expand').append(
				'<td style="width: 8%;"></td>'.repeat(2) +
				'<td style="width: 21%; text-align:left;" class="orderData_betData_Event"></td>' +
				'<td style="width: 10%; text-align:left;" class="orderData_betData_BetWay"></td>' +
				'<td style="width: 10%; text-align:right;" class="orderData_betData_Result"></td>' +
				'<td style="width: 10%;"></td>'.repeat(4)
			);
			$('#orderDataTemp').append(additionalTr);

			const betDataEventContainer = $(`#${dynamicId}`);
			//content event ,bet way , result
			betDataEventContainer.find('.orderData_betData_Event').html(BetDataEventContent);
			betDataEventContainer.find('.orderData_betData_BetWay').html(betWayContent);
			betDataEventContainer.find('.orderData_betData_Result').html(resultContent);

			parentElement.find('.order-toggleButton').addClass('showbutton');
			$(`#${dynamicId}`).addClass('hide-betaDetcon');
		}

		orderDataBetEvent.append(betDataEventContainer);
		orderDataBetWay.append(betDataBetWayContainer);
		orderDataResult.append(betDataResultContainer);

		if (betIndex === 0) {
			const toggleButton = $('<button class="order-toggleButton"><i class="fa-sharp fa-solid fa-play fa-rotate-90 fa-2xs" style="color: #ff0000;"></i></button>');
			const dynamicClass = `additionalTr_${orderItem.m_id}`;

			function toggleContainers() {
				$(`.${dynamicClass}`).toggleClass("show-betaDetcon");
				toggleButton.find('i').toggleClass('fa-rotate-90');
			}
			toggleButton.on('click', toggleContainers);
			parentElement.find('.orderData_mOrder').append(toggleButton);
		}

		$('.betaDetcon:not(:first-child)').remove();
	}

	function createTotal() {
		const orderDataTotal = $('#countTr').clone().removeAttr('hidden').removeAttr('template');

		totalResultAmount = isNaN(totalResultAmount) ? 0 : totalResultAmount;
    	totalWinLoss = isNaN(totalWinLoss) ? 0 : totalWinLoss;

		orderDataTotal.find('.orderData_totalBetCount').text(totalBetItemCount);
		orderDataTotal.find('.orderData_totalBetAmount').text(totalBetAmount);
		orderDataTotal.find('.orderData_totalResultAmount').text(totalResultAmount);
		orderDataTotal.find('.orderData_totalWinAmount').text(totalWinLoss);
		if (totalWinLoss >= 0) {
			orderDataTotal.find('.orderData_totalWinAmount').css('color', 'red');
		} else {
			orderDataTotal.find('.orderData_totalWinAmount').css('color', 'green');
		}
		$('.search-bar-container').after(orderDataTotal);
	}


	//updateTotal when new data is loaded
	function updateTotal() {
		totalResultAmount = isNaN(totalResultAmount) ? 0 : totalResultAmount;
    	totalWinLoss = isNaN(totalWinLoss) ? 0 : totalWinLoss;

		$('.orderData_totalBetCount').text(totalBetItemCount);
		$('.orderData_totalBetAmount').text(totalBetAmount);
		$('.orderData_totalResultAmount').text(totalResultAmount === null ? '0' : totalResultAmount);

		const totalWinAmountElement = $('.orderData_totalWinAmount');
		const currentColor = totalWinAmountElement.css('color'); // Get the current text color
		totalWinAmountElement.text(totalWinLoss === null ? '0' : totalWinLoss);
		// Check if the color needs to be updated
		if ((totalWinLoss >= 0 && currentColor !== 'red') || (totalWinLoss < 0 && currentColor !== 'green')) {
			if (totalWinLoss >= 0) {
				totalWinAmountElement.css('color', 'red');
			} else {
				totalWinAmountElement.css('color', 'green');
			}
		}
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

	formatDateTime = (dateTimeString) => {
        const dateTime = new Date(dateTimeString);
        const month = (dateTime.getMonth() + 1).toString().padStart(2, '0'); // Get month (0-based index), add 1, and pad with '0' if needed
        const day = dateTime.getDate().toString().padStart(2, '0'); // Get day and pad with '0' if needed
        const hour = dateTime.getHours().toString().padStart(2, '0'); // Get hours and pad with '0' if needed
        const minute = dateTime.getMinutes().toString().padStart(2, '0'); // Get minutes and pad with '0' if needed
        return `${month}-${day} ${hour}:${minute}`;
    }

	// function of selected unsettled and settled
	const urlParams = new URLSearchParams(window.location.search);
	const select = document.getElementById("selectOption");
	const unsettledOption = select.querySelector("option[value='{{ trans('common.left_menu.unsettled') }}']");
	const settledOption = select.querySelector("option[value='{{ trans('common.left_menu.settled') }}']");
	// Check for the 'result' query parameter in the URL
	const resultParam = urlParams.get("result");

	if (resultParam === "0") {
		unsettledOption.setAttribute("selected", "selected");
	} else if (resultParam === "1") {
		settledOption.setAttribute("selected", "selected");
	}

	function redirectToPage(select) {
		var selectedOption = select.options[select.selectedIndex];
		var link = selectedOption.getAttribute('data-link');
		if (link) {
			window.location.search = link;
		}
	}

	// Function to update row colors based on position and display property
	function updateRowColors() {
		var allRows = document.querySelectorAll('tr');
		var displayedRows = Array.from(allRows).filter(row => {
			return row.classList.contains('show-betaDetcon');
		});
		
		for (var i = 0; i < displayedRows.length; i++) {
			if (displayedRows[i].classList.contains('show-betaDetcon') || i % 2 === 0) {
				displayedRows[i].classList.add('green-bg'); // Even rows or rows with class show-betaDetcon are green
				displayedRows[i].classList.remove('white-bg');
				console.log("even");
			} else {
				displayedRows[i].classList.remove('green-bg');
				displayedRows[i].classList.add('white-bg'); // Odd rows are blue
				console.log("odd");
			}
		}
	}

	updateRowColors();

</script>
@endpush