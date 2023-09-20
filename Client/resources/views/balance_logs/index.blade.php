@extends('layout.app')

@section('content')
	<!-- 搜尋框 -->
	<div id='searchArea' style="height: 5.5rem;">
		<div class="ui active centered inline loader" style="margin-top: 3rem!important;"></div>
		<div class="w-100" style='display: inline-flex'>
            <!-- 交易編號 -->
			<div style="width: 15%; margin-left: 1%">
				<p class="mb-0 fw-600 userField fs-09">{{ trans('common.search_area.logs_id') }}</p>
				<div class="ui input focus">
					<input autocomplete="off" class="userField w-100" name="id" type="text" placeholder="{{ trans('common.search_area.logs_id') }}">
				</div>
			</div>
            <!-- 開始 結束 時間 -->
			<div class="ui form" style="width: 45%;margin-left: 1%;">
				<div class="two fields">
					<div class="field">
						<p class="mb-0 fw-600 userField fs-09">{{ trans('common.search_area.start_time') }}</p>
						<div class="ui calendar userField" id="rangestart">
							<div class="ui input left icon">
								<i class="fa-solid fa-calendar-days"></i>
								<input autocomplete="off" name="start_time" type="text" placeholder="{{ trans('common.search_area.start_time') }}">
							</div>
						</div>
					</div>
					<div class="field">
						<p class="mb-0 fw-600 userField fs-09">{{ trans('common.search_area.end_time') }}</p>
						<div class="ui calendar userField" id="rangeend">
							<div class="ui input left icon">
								<i class="fa-solid fa-calendar-days"></i>
								<input autocomplete="off" name="end_time" type="text" placeholder="{{ trans('common.search_area.end_time') }}">
							</div>
						</div>
					</div>
				</div>
			</div>
            <!-- 類型 -->
            <div style="width: 15%;margin-left: 1%;">
				<p class="mb-0 fw-600 userField fs-09">{{ trans('common.search_area.logsType') }}</p>
				<select name="type" class="ui dropdown clearSearch searchSelect userField">
					<option value="">{{ trans('common.search_area.logsType') }}</option>
					@foreach($type_list as $key => $item)
						<option value="{{ $key }}">{{ $item }}</option>
					@endforeach
				</select>
			</div>
            <!-- 搜尋按鈕 -->
			<button style="width: 15%;" id='searchBtn' class="ui button active userField" onclick="searchLogs()">{{ trans('common.search_area.search') }}
				<i class="fa-solid fa-magnifying-glass ml-1"></i>
			</button>
		</div>
	</div>
	<div id="logsContainer">
		<div id="tableContainer">
			<table id="logsTable" class="cell-border w-100 text-center ">
				<thead>
				<tr class="no-border-top">
					<th style="width: 10%;" class="no-border-left">{{ trans('logs.main.id') }}</th>
					<th style="width: 16%;">{{ trans('logs.main.logs_type') }}</th>
					<th style="width: 16%;">{{ trans('logs.main.logs_amount') }}</th>
					<th style="width: 16%;">{{ trans('logs.main.logs_before_amount') }}</th>
					<th style="width: 16%;">{{ trans('logs.main.logs_after_amount') }}</th>
					<th style="width: 20%;" class="no-border-right">{{ trans('logs.main.logs_time') }}</th>
				</tr>
				</thead>
				<tbody>
				@isset($list)
					@if(count($list) > 0)
						@foreach($list as $key => $item)
							<tr>
								<td class="no-border-left text-left">{{ $item['id'] }}</td>
								<td class="text-left">{{ $item['type'] }}</td>
								<td class="text-right">{{ $item['change_balance'] }}</td>
								<td class="text-right">{{ $item['before_balance'] }}</td>
								<td class="text-right">{{ $item['after_balance'] }}</td>
								<td class="no-border-right text-left">{{ $item['create_time'] }}</td>
							</tr>
						@endforeach
					@else
						<tr class="no-border-bottom">
							<td id="noDataTd" colspan="9" class="no-border-left no-border-right">
								<i class="fa-solid fa-circle-exclamation"></i>
								<p>{{ trans('logs.main.nodata') }}</p>
							</td>
						</tr>
					@endif
				@endisset
				</tbody>
			</table>
		</div>
	</div>
	<div id="pagination">
		<button onclick="navPage(0)" class="ui button" @if($pagination['current_page'] == 1) disabled @endif>{{ trans('logs.main.first_page') }}</button>
		<button onclick="navPage(1)" class="ui button" @if($pagination['current_page'] == 1) disabled @endif>{{ trans('logs.main.pre_page') }}</button>
		<p>{{ $pagination['current_page'] }} /  {{ $pagination['max_page'] }}</p>
		<button onclick="navPage(2)" class="ui button" @if($pagination['current_page'] == $pagination['max_page'] || $pagination['max_page'] == 0 ) disabled @endif>{{ trans('logs.main.next_page') }}</button>
		<button onclick="navPage(3)" class="ui button"@if($pagination['current_page'] == $pagination['max_page'] || $pagination['max_page'] == 0 ) disabled @endif>{{ trans('logs.main.last_page') }}</button>
	</div>
@endsection

@section('styles')
<link href="{{ asset('css/logs.css?v=' . $system_config['version']) }}" rel="stylesheet">
<style>	
/* 寫入頁面限定CSS */
</style>
@endSection

@push('main_js')
<script>

	// 帳變類型選單
	console.log("type_list");
    console.log(@json($type_list));
	
	// 列表
	console.log("list");
    console.log(@json($list));

	// 分頁
	console.log("pagination");
    console.log(@json($pagination));

	// 語系
    var langTrans = @json(trans('logs'));


	// detect ini ajax
	var isReadyLogsInt = null

	$(document).ready(function() {
		// check if api are all loaded every 500 ms 
		isReadyLogsInt = setInterval(() => {
			if( isReadyCommon ) {
				$('#dimmer').dimmer('hide'); // hide loading
				$('#wrap').css('opacity', 1); // show the main content
				renderView()
				clearInterval(isReadyLogsInt); // stop checking
			}
		}, 500);
	});

	function renderView() {
		// search condition
		if( searchData.type !== undefined ) {
			$('select[name="type"]').val(searchData.type)
			$('select[name="type"]').trigger('change')
		}
		if( searchData.id !== undefined ) {
			$('input[name="id"]').val(searchData.id)
			$('input[name="id"]').trigger('change')
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

	// 左邊菜單  當點擊體育或串關時 移除目前選中樣式
    $('.menuTypeBtn').click(function(){
        let key = $(this).attr('key')
        if( (key === 'index' || key === 'm_order' || key === 'match') && $(this).hasClass('on') ) {
            $('div[key="logs"] .slideMenuTag').css('border-bottom-left-radius','0')
            $('div[key="logs"] .slideMenuTag').css('border-top-left-radius','0')
            $('div[key="logs"] .slideMenuTag').css('background-color','#415b5a')
            $('div[key="logs"] .slideMenuTag').css('color','white')

            $('div[key="rule"] .slideMenuTag').css('border-bottom-right-radius','0')
            $('div[key="calculator"] .slideMenuTag').css('border-top-right-radius','0')
        } else {
            $('div[key="logs"] .slideMenuTag').css('border-bottom-left-radius','25px')
            $('div[key="logs"] .slideMenuTag').css('border-top-left-radius','25px')
            $('div[key="logs"] .slideMenuTag').css('background-color','rgb(196, 211, 211)')
            $('div[key="logs"] .slideMenuTag').css('color','#415b5a')

            $('div[key="rule"] .slideMenuTag').css('border-bottom-right-radius','15px')
            $('div[key="calculator"] .slideMenuTag').css('border-top-right-radius','15px')
        }
    })

	function navPage(pagination) {
		let queryData = @json($search);
		let searchPage = parseInt(@json($search)['page']);
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
		window.location.href = '/logs?' + queryString;
	}
	
    // 搜尋
    function searchLogs() {
        var type = $('select[name="type"]').val()
        var id = $('input[name="id"]').val()
        var start_time = $('input[name="start_time"]').val()
        var end_time = $('input[name="end_time"]').val()
        const queryParams = {};
        if (type) {
            queryParams.type = type;
        }
        if (id) {
            queryParams.id = id;
        }
        if (start_time) {
            queryParams.start_time = start_time;
        }
        if (end_time) {
            queryParams.end_time = end_time;
        }
        const queryString = new URLSearchParams(queryParams).toString();
        const urlWithQuery = `?${queryString}`;
        window.location.href = urlWithQuery
    }
</script>
@endpush