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
				<p class="orderData_totalEffectiveAmount"></p>
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
                        <td style="width: 10%;" class="text-right"><span class="orderData_betAmount"></span><br><span style="color:#b2b2b2;" class="orderData_createTime"></span></td>
                        <td style="width: 10%;" class="text-right orderData_effectiveAmount"></td>
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
@endsection

@section('styles')
<link href="{{ asset('css/order.css?v=' . $system_config['version']) }}" rel="stylesheet">
<style>	
/* 寫入頁面限定CSS */
</style>
@endSection

@push('main_js')
<script src="{{ asset('js/bootstrap.min.js?v=' . $system_config['version']) }}"></script>
<script>

	// 語系
    var langTrans = @json(trans('order'));

	// detect ini ajax
    var isReadyOrderInt = null
    var isReadyOrder = false

	// order list data
    var orderListD = {}

	var callOrderListData = { token: token, player: player, result: 0, page: 1 }
    const orderList_api = '/api/v2/common_order'

	let totalBetItemCount = 0;
	let totalBetAmount = 0;
	let totalResultAmount = 0;
	let totalEffectivetAmount = 0;
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
				const effectiveAmount = parseFloat(orderItem.active_bet);
				const winLoss = resultAmount - betAmount;

				createList(orderItem, orderIndex, winLoss);
				orderItem.bet_data.forEach((betItem, betIndex) => {
					createBetDataDetails(orderItem, betItem, betIndex);
				});

				// Validate and accumulate total
				totalBetItemCount += betItemCounter;
				totalBetAmount += betAmount;
				totalResultAmount += resultAmount || 0;
				totalEffectivetAmount += effectiveAmount;
				totalWinLoss += winLoss || 0;
			});

			// After accumulating the totals, round them to two decimal places
			totalResultAmount = parseFloat(totalResultAmount.toFixed(2));
			totalEffectivetAmount = parseFloat(totalEffectivetAmount.toFixed(2));
			totalBetAmount = parseFloat(totalBetAmount.toFixed(2));
			totalWinLoss = parseFloat(totalWinLoss.toFixed(2));
			console.log(totalResultAmount);

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
		const orderDataCreateTime = orderData.find('.orderData_createTime');
		const orderDataBetEvent = orderData.find('.orderData_betData_Event');
		const orderDataBetBetWay = orderData.find('.orderData_betData_BetWay');
		const orderDataBetResult = orderData.find('.orderData_betData_Result');
		const orderDataEffectiveAmount = orderData.find('.orderData_effectiveAmount');
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
		orderDataBetAmount.html(orderItem.bet_amount === null ? '-' : orderItem.bet_amount.toFixed(2));
		orderDataCreateTime.html( orderItem.create_time === null ? '' : formatDateTime(orderItem.create_time));
		orderDataEffectiveAmount.html(orderItem.active_bet === null ? '-' : orderItem.active_bet.toFixed(2));
		orderDataResultAmount.html(orderItem.result_amount === null ? '-' : orderItem.result_amount.toFixed(2));
		orderDataResultTime.html(orderItem.result_time === null ? '' : orderItem.result_time);
		orderDataWinLoss.html(winLoss = isNaN(winLoss) ? '-' : winLoss.toFixed(2));

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
			const startTime = betItem.start_time === null ? '' : formatDateTime(betItem.start_time);

			return `${betItem.league_name} <span style="color:#808080;">(${startTime})</span><br>
					${betItem.home_team_name}<span style="color:green">[{{ trans("order.main.home") }}]</span>&nbsp;VS&nbsp;${betItem.away_team_name}&nbsp;
					<span style="color:red;white-space:nowrap;">
						${betItem.home_team_score !== null && betItem.away_team_score !== null ? `(` : ''}
						${betItem.home_team_score !== null ? `${betItem.home_team_score}` : ''}
						${betItem.away_team_score !== null && betItem.home_team_score !== null ? '-' : ''}
						${betItem.away_team_score !== null ? `${betItem.away_team_score}` : ''}
						${betItem.home_team_score !== null && betItem.away_team_score !== null ? `)` : ''}
					</span>`;
		}
		// bet way column 
		function createBetWayContent(betItem) {
			const marketName = betItem.market_name;
			const marketBetName = betItem.market_bet_name;
			const marketBetLine = betItem.market_bet_line;
			const betRate = betItem.bet_rate;
			const content = `${marketName}<br><span style="color:green;">[${marketBetName}] ${marketBetLine}</span>`;
			if (betRate !== null) {
				return `${content} @ <span style="color:#c79e42;">${betRate}</span>`;
			} else {
				return content;
			}
		}
		//result column
		function createResultContent(betItem, orderItem) {
			let resultText = '';

			switch (orderItem.status) {
				case 0:
					resultText = `<span style="color: #000000;">{{ trans("order.main.cancel") }}</span>`;
					break;
				case 1:
					resultText = `<span style="color: #000000;">{{ trans("order.main.waitToCreate") }}</span>`;
					break;
				case 2:
					resultText = `<span style="color: #000000;">{{ trans("order.main.waitToOpen") }}</span>`;
					break;
				case 3:
					resultText = `<span style="color: #000000;">{{ trans("order.main.waitToPrize") }}</span>`;
					break;
				case 4:
					resultText = betItem.result_percent === 0 ? `<span style="color: green;">{{ trans("order.result_precent.0") }}</span>` :
					betItem.result_percent === 1 ? `<span style="color: red;">{{ trans("order.result_precent.1") }}</span>` :
					betItem.result_percent === 2 ? `<span style="color: red;">{{ trans("order.result_precent.2") }}</span>` :
					betItem.result_percent === 3 ? `<span style="color: green;">{{ trans("order.result_precent.3") }}</span>` :
					betItem.result_percent === 4 ? `<span style="color: #c79e42;">{{ trans("order.result_precent.4") }}</span>` :
					betItem.result_percent === 5 ? `<span style="color: #ff00ff;">{{ trans("order.result_precent.5") }}</span>` : // Add more conditions as needed
					`${betItem.result_percent}`;
					break;
			}

			const resultTime = orderItem.result_time === null ? '' : formatDateTime(orderItem.result_time);
			return createHtmlElement('text-right', `${resultText}<br><span style="color:#b2b2b2;">${resultTime}</span>`);
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
			const minNumber = 1;const maxNumber = 100;
			const randomNumber = Math.floor(Math.random() * (maxNumber - minNumber + 1)) + minNumber;
			const dynamicId = `additionalTr_${betItem.league_id}_${randomNumber}`; // <-- make the id generated random
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
			// $(`#${dynamicId}`).addClass('hide-betaDetcon');
			$(`#${dynamicId}`).css('display', 'none');
		}

		orderDataBetEvent.append(betDataEventContainer);
		orderDataBetWay.append(betDataBetWayContainer);
		orderDataResult.append(betDataResultContainer);

		if (betIndex === 0) {
			const toggleButton = $('<button class="order-toggleButton"><i class="fa-sharp fa-solid fa-play fa-2xs" style="color: #ff0000;"></i></button>');
			const dynamicClass = `additionalTr_${orderItem.m_id}`;

			function toggleContainers() {
				const elements = $(`.${dynamicClass}`);

				if (elements.css('display') === 'none') {
					elements.css('display', 'table-row');
					toggleButton.find('i').addClass('fa-rotate-90');
				} else {
					elements.css('display', 'none');
					toggleButton.find('i').removeClass('fa-rotate-90');
				}

				// update the row colors and height when toggle containers
				updateRowColors();
				adjustContainerHeight();
			}

			toggleButton.on('click', toggleContainers);
			parentElement.find('.orderData_mOrder').append(toggleButton);
		}

		$('.betaDetcon:not(:first-child)').remove();
	}

	function createTotal() {
		const orderDataTotal = $('#countTr').clone().removeAttr('hidden').removeAttr('template');
		

		totalResultAmount = isNaN(totalResultAmount) ? 0 : totalResultAmount;
		totalEffectivetAmount = isNaN(totalEffectivetAmount) ? 0 : totalEffectivetAmount;
    	totalWinLoss = isNaN(totalWinLoss) ? 0 : totalWinLoss;

		orderDataTotal.find('.orderData_totalBetCount').text(totalBetItemCount);
		orderDataTotal.find('.orderData_totalBetAmount').text(totalBetAmount);
		orderDataTotal.find('.orderData_totalResultAmount').text(totalResultAmount);
		orderDataTotal.find('.orderData_totalEffectiveAmount').text(totalEffectivetAmount);
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
		$('.orderData_totalEffectiveAmount').text(totalEffectivetAmount === null ? '0' : totalEffectivetAmount);

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

		updateRowColors();
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
		
		adjustContainerHeight(); // update height when fetching more data's
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
				updateRowColors();
				adjustContainerHeight();
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
		adjustContainerHeight(); // update height
	}

    
	// Function to update row colors
	function updateRowColors() {
		const allRows = document.querySelectorAll('#orderTable tbody tr:not([style*="display: none"])');
		let rowCount = 0;
		allRows.forEach((row) => {
			rowCount++;
			if (rowCount % 2 === 1) {
				row.style.backgroundColor = '#e2f0f0'; // Change '#odd-color' to your desired background color for odd rows
				row.style.backgroundColor =  '#ffffff';// Change '#even-color' to your desired background color for even rows
			} else {
				row.style.backgroundColor =  '#ffffff';// Change '#even-color' to your desired background color for even rows
				row.style.backgroundColor = '#e2f0f0'; // Change '#odd-color' to your desired background color for odd rows
			}
		});
	}

	function adjustContainerHeight() {
		// Compute the height of #orderTable
		var orderTableHeight = $('#orderTable').height();

		// Check if the height is less than 877px
		if (orderTableHeight < 500) {
			$('#orderContainer').css('height', 'auto');
		} else {
			// Set the height of #orderContainer to 'calc(100% - 9.5rem)'
			$('#orderContainer').css('height', 'calc(100% - 9.5rem)');
		}
	}

</script>
@endpush