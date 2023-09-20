@extends('layout.app')

@section('content')
	<!-- 搜尋框 -->
	<div id='searchArea' style="height: 5.5rem;">
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
			
	</div>


	<div id="orderContainer">
		<div id="tableContainer">
			<table id="orderTable" class="cell-border w-100 text-center ">
				<thead>
				<tr class="no-border-top">
					<th style="width: 5%;" class="no-border-left">{{ trans('order.main.index') }}</th>
					<th style="width: 10%;">{{ trans('order.main.sport_type') }}</th>
					<th style="width: 10%;">{{ trans('order.main.order_type') }}</th>
					<th style="width: 10%;">{{ trans('order.main.bet_type') }}</th>
					<th style="width: 30%;">{{ trans('order.main.detail') }}</th>
					<th style="width: 12.5%;">{{ trans('order.main.bet_money') }}</th>
					<th style="width: 12.5%;">{{ trans('order.main.return_money') }}</th>
					<th style="width: 10%;" class="no-border-right">{{ trans('order.main.status') }}</th>
				</tr>
				</thead>
				<tbody>
					@isset($data['list'])
						@foreach($data['list'] as $key => $item)
							<tr>
								<td class="no-border-left">{{ $item['id'] }}</td>
								<td>
									@if($item['m_order'] === 1)
										{{ trans('order.main.m_bet') }}
									@else
										{{ trans('order.main.sport_bet') }}
									@endif
								</td>
								<td>{{ $sport_list[$search['sport']] }}</td>
								<td>
									@foreach($item['bet_data'] as $k => $v)
										<div key='{{ $key }}' class="text-center" style="@if($k !== 0) display: none; border-top: 2px solid rgb(196,211,211); @endif; padding: 0.5rem; height: 75px;line-height: 55px; ">
											{{ $v['type_name'] }}
										</div>
									@endforeach
								</td>
								<td>
									@foreach($item['bet_data'] as $k => $v)
										<div key='{{ $key }}' class="row m-0" style="@if($k !== 0) display: none; border-top: 2px solid rgb(196,211,211); @endif; padding: 0.5rem; height: 75px;line-height: 55px; ">
											<div class="col-2">
												@if($item['m_order'] === 1)
													<div class="orderInfoIndex">{{$k+1}}</div>
												@endif
											</div>
											<div class="col-8 text-left">
												<p class="mb-0 textOverFlow">{{ $v['series_name'] }}</p>
												<p class="mb-0 textOverFlow">
													{{ $v['home_team_name'] }} 
												@if(isset($v['home_team_score']))
													<span>{{$v['home_team_score']}}</span>
												@endif
												 VS {{ $v['away_team_name'] }} 
												@if(isset($v['away_team_score']))
													<span>{{$v['away_team_score']}}</span>
												@endif
												</p>
												<p class="mb-0 textOverFlow">
													{{ $v['type_item_name'] }} @ {{ $v['bet_rate'] }}
												</p>
											</div>
											<div class="col-2 text-left">
												@if($item['m_order'] === 1 && $k === 0 )
													<div isopen=false onclick="toggleInfo('{{ $key }}', this)" class="orderInfoIndex text-center" style="width: 4rem;">{{ trans('order.main.open') }}▸</div>
												@endif
											</div>
										</div>
									@endforeach
								</td>
								<td class="text-right">{{ $item['bet_amount'] }}
									<br>
									<span class="text-muted">{{ date("m-d H:i", strtotime($item['create_time'])) }}</td>
								<td class="text-right">{{ $item['result_amount'] }}
									<br>
									<span class="text-muted">{{ date("m-d H:i", strtotime($item['result_time'])) }}</span>
								</td>
								<td class="no-border-right">{{ $status_list[$item['status']] }}</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td id="noDataTd" colspan="9" class="no-border-left no-border-right">
								<i class="fa-solid fa-circle-exclamation"></i>
								<p>{{ trans('order.main.nodata') }}</p>
							</td>
						</tr>
					@endisset
						<tr id="countTr" class="no-border-bottom">
							<td colspan="4"></td>
							<td class="p-0">
								<div class="text-white bg-deepgreen" id="orderCountTotal">{{ trans('order.main.total') }}</div>
							</td>
							<td class="text-right">{{ $data['total']['bet_amount'] }}</td>
							<td class="text-right">{{ $data['total']['result_amount'] }}</td>
							<td colspan="3"></td>
						</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div id="pagination">
		<button onclick="navPage(0)" class="ui button" @if($pagination['current_page'] == 1) disabled @endif>{{ trans('order.main.first_page') }}</button>
		<button onclick="navPage(1)" class="ui button" @if($pagination['current_page'] == 1) disabled @endif>{{ trans('order.main.pre_page') }}</button>
		<p>{{ $pagination['current_page'] }} /  {{ $pagination['max_page'] }}</p>
		<button onclick="navPage(2)" class="ui button" @if($pagination['current_page'] == $pagination['max_page'] || $pagination['max_page'] == 0 ) disabled @endif>{{ trans('order.main.next_page') }}</button>
		<button onclick="navPage(3)" class="ui button"@if($pagination['current_page'] == $pagination['max_page'] || $pagination['max_page'] == 0 ) disabled @endif>{{ trans('order.main.last_page') }}</button>
	</div>
@endsection

@section('styles')
<link href="{{ asset('css/order.css?v=' . $system_config['version']) }}" rel="stylesheet">
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
    var callOrderListData = { token: token, player: player, result: 1, page: 1 }
    const orderList_api = 'https://sportc.asgame.net/api/v2/common_order'



  	// 寫入頁面限定JS
  	$(document).ready(function() {

		// ===== DATA LATER =====

        // ini data from ajax
        caller(orderList_api, callOrderListData, orderListD) // match_list

        // check if api are all loaded every 500 ms 
        isReadyIndexInt = setInterval(() => {
            if (orderListD.status === 1) { isReadyOrder = true; }
            if( isReadyOrder === true && isReadyCommon === true) {
                $('#dimmer').dimmer('hide'); // hide loading
                $('#wrap').css('opacity', 1); // show the main content
                clearInterval(isReadyOrderInt); // stop checking
            }
        }, 500);


		// search condition
		if( searchData.sport !== undefined ) {
			$('select[name="sport"]').val(searchData.sport)
			$('select[name="sport"]').trigger('change')
		}
        if( searchData.series_id !== undefined ) {
			$('select[name="series_id"]').val(searchData.series_id)
			$('select[name="series_id"]').trigger('change')
		}
		if( searchData.order_id !== undefined ) {
			$('input[name="order_id"]').val(searchData.order_id)
			$('input[name="order_id"]').trigger('change')
		}
		if( searchData.status !== undefined ) {
			$('select[name="status"]').val(searchData.status)
			$('select[name="status"]').trigger('change')
		}
		if( searchData.start_time !== undefined ) {
			$('input[name="start_time"]').val(searchData.start_time)
			$('input[name="start_time"]').trigger('change')
		}
		if( searchData.end_time !== undefined ) {
			$('input[name="end_time"]').val(searchData.end_time)
			$('input[name="end_time"]').trigger('change')
		}
	});


	// 搜尋框 聯盟名稱
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

	// 左邊菜單  當點擊體育或串關時 移除目前選中樣式
    $('.menuTypeBtn').click(function(){
        let key = $(this).attr('key')
        if( (key === 'index' || key === 'm_order' || key === 'match') && $(this).hasClass('on') ) {
            $('div[key="order"] .slideMenuTag').css('border-bottom-left-radius','0')
            $('div[key="order"] .slideMenuTag').css('border-top-left-radius','0')
            $('div[key="order"] .slideMenuTag').css('background-color','#415b5a')
            $('div[key="order"] .slideMenuTag').css('color','white')

            $('div[key="m_order"] .slideMenuTag').css('border-bottom-right-radius','0')
            $('div[key="match"] .slideMenuTag').css('border-top-right-radius','0')
        } else {
            $('div[key="order"] .slideMenuTag').css('border-bottom-left-radius','25px')
            $('div[key="order"] .slideMenuTag').css('border-top-left-radius','25px')
            $('div[key="order"] .slideMenuTag').css('background-color','rgb(196, 211, 211)')
            $('div[key="order"] .slideMenuTag').css('color','#415b5a')

            $('div[key="m_order"] .slideMenuTag').css('border-bottom-right-radius','15px')
            $('div[key="match"] .slideMenuTag').css('border-top-right-radius','15px')
        }
    })

	// 內容太長 跑馬燈
    function fixTextOverflow() {
        Array.from(document.getElementsByClassName("textOverFlow")).forEach(
			function(element, index, array) {
				if (!element.querySelector("marquee") && isElementOverflowing(element)) {
					wrapContentsInMarquee(element);
				}
			}
		);
    }
    function isElementOverflowing(element) {
		var overflowX = element.offsetWidth < element.scrollWidth,
			overflowY = element.offsetHeight < element.scrollHeight;

		return (overflowX || overflowY);
		}

    function wrapContentsInMarquee(element) {
    var marquee = document.createElement('marquee'),
        contents = element.innerText;
        marquee.innerText = contents;
        element.innerHTML = '';
        element.appendChild(marquee);
        marquee.scrollAmount = 3
    }

	function navPage(pagination) {
		let queryData = @json($search);
		let searchPage = @json($search)['page']
		delete queryData.page;
		switch (pagination) {
			case 0:
				searchPage = 1
				break;
			case 1:
				searchPage -= 1
				break;
			case 2:
				searchPage += 1
				break;
			case 3:
				searchPage = @json($pagination)['max_page']
				break;
		}
		queryData['page'] = searchPage
		var queryString = new URLSearchParams(queryData).toString();
		window.location.href = '/order?' + queryString;
	}
	
	// order search
	function searchOrder() {
		let queryData = {}
		queryData.page = 1
		let sSport = $('select[name="sport"]').val()
		let sOrderId = $('input[name="order_id"]').val()
		let sSeriesId = $('select[name="series_id"]').val()
		let sStatus = $('select[name="status"]').val()
		let sStartTime = $('input[name="start_time"]').val()
		let sEndTime = $('input[name="end_time"]').val()
		if(sSport) queryData.sport = sSport
		if(sOrderId) queryData.order_id = sOrderId
		if(sSeriesId) queryData.series_id = sSeriesId
		if(sStatus) queryData.status = status
		if(sStartTime) queryData.start_time = sStartTime
		if(sEndTime) queryData.end_time = sEndTime
		var queryString = new URLSearchParams(queryData).toString();
		window.location.href = '/order?' + queryString;
	}


	// 展開 收合
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
		fixTextOverflow()
	}


  // for test
    console.log("menu_count");
    console.log(@json($menu_count));

    console.log("sport_list");
    console.log(@json($sport_list));

    console.log("series_list");
    console.log(@json($series_list));
    
    console.log("type_list");
    console.log(@json($type_list));

    console.log("status_list");
    console.log(@json($status_list));

    console.log("order_list");
    console.log(@json($data));

    console.log("pagination");
    console.log(@json($pagination));

    console.log("search");
    console.log(@json($search));
    

</script>
@endpush