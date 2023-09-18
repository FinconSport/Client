<!DOCTYPE html>
<html lang="zn-tw">
  <head>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200|Open+Sans+Condensed:700" rel="stylesheet">
    <!-- Jquery -->
    <link href="{{ asset('css/jquery-ui.css?v=' . $system_config['version']) }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-ui.min.css?v=' . $system_config['version']) }}" rel="stylesheet">
    <!-- COMM CSS Files -->
    <link href="{{ asset('css/bootstrap.min.css?v=' . $system_config['version']) }}" rel="stylesheet">
    <link href="{{ asset('css/common.css?v=' . $current_time) }}" rel="stylesheet">
    <link href="{{ asset('css/icon/all.min.css?v=' . $system_config['version']) }}" rel="stylesheet">
    <link href="{{ asset('css/semantic.css?v=' . $system_config['version']) }}" rel="stylesheet">

    @yield('styles')
  </head>
  <body>
	@if($player['status'] !== 1)

	@endif
	<!-- toast -->
	<div id="toast"></div>
	<!-- loader -->
    <div class="ui dimmer" id="dimmer">
        <div class="ui massive text loader">
            <h3>Loading</h3>
        </div>
    </div>
	<div id='wrap' class="h-100 pb-3 w-100" hidden>
		<div class="leftArea">
			<div id='logoArea'>
				<img src="{{ asset('image/logo.png?v=' . $system_config['version']) }}" alt="Logo">
			</div>
			<div id="sidenav">
				<div id="userArea" class="row m-0">
					<div class="col-6">
						<p class="mb-0 text-center text-white"><span class="player">{{ $player['account'] }}</span></p>
					</div>
					<div class="col-6">
						<p class="mb-0 text-center text-white"><span class="balance">{{ $player['balance'] }}</span></p>
					</div>
				</div>
				<div id="gameCategory" style="position: relative;">
					<div id="subMenuContainer">
						<div class="bg-lightgreen ml-1 menuTypeBtn" key='menuTopFill'>
							<div class="p-0 bg-deepgreen slideMenuTag" style="height: 1rem;"></div>
						</div>
						<div class="bg-lightgreen ml-1 menuTypeBtn" key='index' onclick="toggleMenu('indexSportMenu')">
							<div  class="bg-deepgreen slideMenuTag">
								<i class="fa-solid fa-house"></i>
								<span>{{ trans('common.left_menu.sport_bet') }}</span>
								<div class='sportMenu' key='indexSportMenu'>
									@foreach ($menu_count[0] as $key => $item)
										@if(isset($sport_list[$key]))
											<a class="sportSelect {{ isset($search['sport']) && $search['sport'] == $key ? 'on' : ($loop->index === 0 && !isset($search['sport']) ? 'on' : '') }}" onclick='sportTo(event, "{{ $key }}", "")'>
												<i class="fa-solid icon-{{ $key }}"></i>
												<span>{{ $sport_list[$key] }}</span>
												<span class="menuStatistics_{{ $key }}">{{ $item }}</span>
											</a>
										@endif
									@endforeach
								</div>
							</div>
						</div>
						<div class="bg-lightgreen ml-1 menuTypeBtn" key='m_order' onclick="toggleMenu('mOrderSportMenu')">
							<div  class="bg-deepgreen slideMenuTag">
								<i class="fa-regular fa-circle-dot"></i>
								<span>{{ trans('common.left_menu.m_bet') }}</span>
								<div class='sportMenu' key='mOrderSportMenu'>
									@foreach ($menu_count[1] as $key => $item)
									@if(isset($sport_list[$key]))
									<a class="sportSelect {{ isset($search['sport']) && $search['sport'] == $key ? 'on' : ($loop->index === 0 && !isset($search['sport']) ? 'on' : '') }}" onclick='sportTo( event, "{{ $key }}", "m_order")'>
										<i class="fa-solid icon-{{ $key }}"></i>
										<span>{{ $sport_list[$key] }}</span>
										<span class="menuStatistics_{{ $key }}">{{ $item }}</span>
									</a>
									@endif
									@endforeach
								</div>
							</div>
						</div>
						<div class="bg-lightgreen ml-1 menuTypeBtn" key='order' onclick="navTo('order')">
							<div  class="bg-deepgreen slideMenuTag">
								<i class="fa-solid fa-file"></i>
								<span>{{ trans('common.left_menu.record') }}</span>
							</div>
						</div>
						<div class="bg-lightgreen ml-1 menuTypeBtn" key='match' onclick="toggleMenu('matchSportMenu')">
							<div class="bg-deepgreen slideMenuTag">
								<i class="fa-solid fa-table"></i>
								<span>{{ trans('common.left_menu.match') }}</span>
								<div class='sportMenu' key='matchSportMenu'>
									@foreach ($sport_list as $key => $item)
										<a class="sportSelect {{ isset($search['sport']) && $search['sport'] == $key ? 'on' : ($loop->index === 0 && !isset($search['sport']) ? 'on' : '') }}" onclick='sportTo( event, "{{ $key }}", "match")'>
											<i class="fa-solid icon-{{ $key }}"></i>
											<span>{{ $item }}</span>
											<span></span>
										</a>
									@endforeach
								</div>
							</div>
						</div>
						<div class="bg-lightgreen ml-1 menuTypeBtn" key='rule' onclick="navTo('rule')">
							<div class="bg-deepgreen slideMenuTag">
								<i class="fa-solid fa-chess-rook"></i>
								<span>{{ trans('common.left_menu.rule') }}</span>
							</div>
						</div>
						<div class="bg-lightgreen ml-1 menuTypeBtn" key='logs' onclick="navTo('logs')">
							<div class="bg-deepgreen slideMenuTag">
								<i class="fa-solid fa-credit-card"></i>
								<span>{{ trans('common.left_menu.logs') }}</span>
							</div>
						</div>
						<div class="bg-lightgreen ml-1 menuTypeBtn" key='calculator' onclick="navTo('calculator')">
							<div class="bg-deepgreen slideMenuTag">
								<i class="fa-solid fa-calculator"></i>
								<span>{{ trans('common.left_menu.calculator') }}</span>
							</div>
						</div>
						<div class="bg-lightgreen ml-1 menuTypeBtn" key='notice' onclick="navTo('notice')">
							<div class="bg-deepgreen slideMenuTag">
								<i class="fa-solid fa-scroll"></i>
								<span>{{ trans('common.left_menu.notice') }}</span>
							</div>
						</div>
						<div class="bg-lightgreen ml-1 menuTypeBtn" key='menuBottomFill'>
							<div class="p-0 bg-deepgreen slideMenuTag" style="height: 1rem;"></div>
						</div>
					</div>

					<!-- for test -->

					<!-- <p class="text-white">status:  <span id="testStatus">1</span></p>
					<button onclick="addStatus()">Add status Value</button>
					<button onclick="delStatus()">Del status Value</button>
					<p class="text-white">rate:  <span id="testRate">10</span></p>
					<button onclick="addRate()">Add Rate Value</button>
					<button onclick="delRate()">Del Rate Value</button> -->

					<!-- for test -->

					<button id="logoutBtn">{{ trans('common.left_menu.logout') }}</button>
				</div>
			</div>
		</div>
		<div class="rightArea">
			<div id="navMarqueeBar">
				<marquee id="marquee" class='bg-deepgreen' behavior="scroll" direction="left">
					<!--- system notice_list loop query -->
                    @foreach ( $notice_list as $key => $list)
                            @if ($key == 0)
                              @foreach ( collect($list)->sortByDesc('create_time')->all() as $li)<!--- sort by date descending -->
                                @if (!empty($li))
								<a href="#" data-createdate="{{ $li['create_time'] }}" class="marqlink"><span class="marq_title">{{ $li['title'] }}</span> : <span class="marq_context">{{ $li['context'] }}</span></a>
                                  @else
										<span> {{ trans('notice.main.no_result') }} </span>
                                @endif
                              @endforeach
                            @endif
                        @endforeach   
				</marquee>
				<div class='rightNavTag'>
					<span id="timer">{{ \Carbon\Carbon::createFromTimestamp($current_time)->format('H:i:s') }}</span>&ensp;
					<span>
						{{ \Carbon\Carbon::createFromTimestamp($current_time)->setTimezone(\Illuminate\Support\Facades\Auth::user()->timezone ?? config('app.timezone'))->format('\(\G\M\TP\)') }}
					</span>
				</div>
			</div>
			<!-- Modal -->
				<div class="modal fade" id="marqModal" tabindex="-1" aria-labelledby="marqModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header d-flex">
								<img src="{{ asset('image/logo.png?v=' . $system_config['version']) }}" alt="Logo">
								<div class="ms-auto p-2 bd-highlight"><span class="cdate">Date</span><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
						</div>
						<div class="modal-body">
							<div class="card">
								<div class="card-header">
									<h5 class="modal-title" id="marqModalLabel">Modal title</h5>
								</div>
								<div class="card-body">
								  <p class="modal-context">Context</p>
								</div>
								</div>
						</div>
					</div>
					</div>
				</div>
			<div id="mainArea">
				@yield('content')
			</div>
		</div>
	</div>

    <!--  COMM JS Files   -->
	<script src="{{ asset('js/jquery.min.js?v=' . $system_config['version']) }}"></script>
	<script src="{{ asset('js/common.js?v=' . $system_config['version']) }}"></script>
    <script src="{{ asset('js/jquery-ui.min.js?v=' . $system_config['version']) }}"></script>
    <script src="{{ asset('js/semantic.min.js?v=' . $system_config['version']) }}"></script>
	<script>
		// 獲取當前search條件
		var searchData = @json($search);
		// current_time
		var current_time = '{{ $current_time }}';
		var version = '{{ $system_config['version'] }}';
		// csrf
		const csrfToken = '{{ csrf_token() }}'
		var errormsg = null
		var successmsg = null
		// 語系
		var commonLang = @json(trans('common'));
		var isReady = false

		console.log('player')
		console.log(@json($player));
		var player_id = @json($player['id']);

		// for test
		@if(session()->has('player'))
			errormsg = @json(session('error'));
			successmsg = @json(session('success'));
		@else
			showErrorToast(commonLang.js.loginFirst);                                            
		@endif

		$(document).ready(function() {

			// loading page
			$('#dimmer').dimmer('show');

			// 預設左邊選中樣式
			$('.sportMenu').filter(':visible').closest('.menuTypeBtn').addClass('on')
			$('.sportMenu').filter(':visible').closest('.menuTypeBtn').prev().addClass('preBtn')
			$('.sportMenu').filter(':visible').closest('.menuTypeBtn').next().addClass('nextBtn')

			// 後臺訊息
			if( errormsg ) showErrorToast(errormsg)
			if( successmsg ) showSuccessToast(successmsg)
			// 搜尋框ui
			$('.searchSelect').dropdown('hide others');
			$('.searchSelect.clearSearch').dropdown({clearable: true});
			$('select[name="sport"]').val(searchData.sport)
			$('select[name="sport"]').trigger('change')
			$('.ui.calendar').calendar();
			$('#rangestart').calendar({
				type: 'date',
				endCalendar: $('#rangeend'),
				today: true,
				text: {
					days: [commonLang.js.sun, commonLang.js.mon, commonLang.js.tue, commonLang.js.wed, commonLang.js.thu, commonLang.js.fri, commonLang.js.sat],
					months: [commonLang.js.jan, commonLang.js.feb, commonLang.js.mar, commonLang.js.apr, commonLang.js.may, commonLang.js.jun, commonLang.js.jul, commonLang.js.aug, commonLang.js.sep, commonLang.js.oct, commonLang.js.nov, commonLang.js.sec],
					today: commonLang.js.today,
				},
				formatter: {
					date: 'YYYY-MM-DD',
				}
			});
			$('#rangeend').calendar({
				type: 'date',
				endCalendar: $('#rangeend'),
				today: true,
				text: {
					days: [commonLang.js.sun, commonLang.js.mon, commonLang.js.tue, commonLang.js.wed, commonLang.js.thu, commonLang.js.fri, commonLang.js.sat],
					months: [commonLang.js.jan, commonLang.js.feb, commonLang.js.mar, commonLang.js.apr, commonLang.js.may, commonLang.js.jun, commonLang.js.jul, commonLang.js.aug, commonLang.js.sep, commonLang.js.oct, commonLang.js.nov, commonLang.js.sec],
					today: commonLang.js.today,
				},
				formatter: {
					date: 'YYYY-MM-DD',
				},
				startCalendar: $('#rangestart')
			});

			// 更新時間
			var timestamp = parseInt('{{ $current_time }}');
			setInterval(function() {
				// 計算目前時間
				var date = new Date(timestamp * 1000);
				var hours = date.getHours();
				var minutes = date.getMinutes();
				var seconds = date.getSeconds();
				// 格式化時間，補零以達到固定長度
				hours = ("0" + hours).slice(-2);
				minutes = ("0" + minutes).slice(-2);
				seconds = ("0" + seconds).slice(-2);
				// 更新顯示的時間
				$('#timer').html(hours + ':' + minutes + ':' + seconds)
				// 增加一秒
				timestamp++;
			}, 1000);

			isReady = true


			$('.userField').removeClass('userField')
			$('.centered.inline.loader').remove() // 移除loader

			//marquee onclick
			$('.marqlink').click(function (event) {
				//event.preventDefault(); // Prevents the default anchor behavior
				event.stopPropagation(); //stopping propagation here
				var title = $(this).find('.marq_title').text();
        		var context = $(this).find('.marq_context').text();
				var create_d = $(this).data("createdate");
				modal = $('#marqModal');
				
				$('body').addClass("modal-open");
				if(!$('.modal-backdrop').length) //to not conflict with page with bootstrap js
				{
					$(document.body).append("<div class='modal-backdrop fade'></div>");
				}
				else{
					$('.modal-backdrop').removeClass("show");
				}
	
				modal.addClass("show");
				modal.css("display", "block");
				setTimeout(function() {
					$('.modal-backdrop').css("opacity", 0.5);
					modal.css("opacity", 1);
					modal.css("top",0);
				}, 200);
				
				modal.find('.modal-title').text(title);
				modal.find('.cdate').text(create_d);
				modal.find('.modal-context').text(context);
				// Add an event listener to close the modal when clicking outside
				$(document).on('click', function (event) {
					if ($('#marqModal').hasClass('show')) {
						closeModal();
					}
			});
			});
			
			

			function closeModal() {
				var modal = $('#marqModal');
				modal.css("top","-100%");
				modal.css("opacity", 0);
				modal.removeClass("show");
				$('body').removeClass("modal-open");
				$('.modal-backdrop').css("opacity", 0);
				$(document).off('click'); // Remove the click outside event listener
				setTimeout(function() {
					modal.css("display", "none");
					$(".modal-backdrop").remove();
				}, 200);
			}

			$('#marqModal .btn-close').click(function (event) {
				event.preventDefault(); // Prevents the default anchor behavior
				closeModal();
			});
		});
		

		// 搜尋框 聯盟名稱
		function filterSeiries(type = 0) {
			// console.log('filterSeiries->' + type)
			if( isReady === true && type === 0 ) {
				$('.clearSearch').dropdown('clear')
				console.log('dropdown clear')
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


	</script>
  @stack('main_js')
  </body>
</html>
