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
	<div id='wrap' class="h-100 pb-3 w-100" style="opacity: 0">
		<div class="leftArea">
			<div id='logoArea'>
				<img src="{{ asset('image/logo.png?v=' . $system_config['version']) }}" alt="Logo">
			</div>
			<div id="sidenav">
				<div id="userArea" class="user-con">
					<p><span class="player"></span></p>
					<p><span class="balance"></span></p>
				</div>
				<div id="gameCategory" class="game-con">
					<div id="subMenuContainer">
						<div class="submenu-main" id="lf_sport">
							<div class="submenu-inner">
								<div class="submenu-btn"><i class="fa-solid fa-house"></i> <p>{{ trans('common.left_menu.sport_bet') }}</p></div>
								<div id="indexSportCon" class="submenu-toggle-list">
								</div>
							</div>
						</div>

						<div class="submenu-main" id="lf_mOrder">
							<div class="submenu-inner">
								<div class="submenu-btn"><i class="fa-regular fa-circle-dot"></i> <p>{{ trans('common.left_menu.m_bet') }}</p></div>
								<div id="mOrderSportCon" class="submenu-toggle-list">
								</div>
							</div>
						</div>
						
						<div class="submenu-main" id="lf_order">
							<div class="submenu-inner">
								<div onclick="navTo('order')" class="submenu-btn"><i class="fa-solid fa-file"></i> <p>{{ trans('common.left_menu.record') }}</p></div>
							</div>
						</div>

						<div class="submenu-main" id="lf_match">
							<div class="submenu-inner">
								<div class="submenu-btn"><i class="fa-solid fa-table"></i> <p>{{ trans('common.left_menu.match') }}</p></div>
								<div id="matchSportCon" class="submenu-toggle-list">
								</div>
							</div>
						</div>

						<div class="submenu-main" id="lf_rule">
							<div class="submenu-inner">
								<div onclick="navTo('rule')" class="submenu-btn"><i class="fa-solid fa-chess-rook"></i> <p>{{ trans('common.left_menu.rule') }}</p></div>
							</div>
						</div>

						<div class="submenu-main" id="lf_logs">
							<div class="submenu-inner">
								<div onclick="navTo('logs')" class="submenu-btn"><i class="fa-solid fa-credit-card"></i> <p>{{ trans('common.left_menu.logs') }}</p></div>
							</div>
						</div>

						<div class="submenu-main" id="lf_calcu">
							<div class="submenu-inner">
								<div onclick="navTo('calculator')" class="submenu-btn"><i class="fa-solid fa-calculator"></i> <p>{{ trans('common.left_menu.calculator') }}</p></div>
							</div>
						</div>

						<div class="submenu-main" id="lf_notice">
							<div class="submenu-inner">
								<div onclick="navTo('notice')" class="submenu-btn"><i class="fa-solid fa-scroll"></i> <p>{{ trans('common.left_menu.notice') }}</p></div>
							</div>
						</div>
					</div>
				</div>
				<div class="subMenuLogoutCon">
					<button id="logoutBtn">{{ trans('common.left_menu.logout') }}</button>
				</div>
			</div>
		</div>
		<div class="rightArea">
			<div id="navMarqueeBar">
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
	<script src="{{ asset('js/pako.min.js?v=' . $system_config['version']) }}"></script><!-- 解壓縮 -->
	<script>
		const current_time = '{{ $current_time }}';
		const version = '{{ $system_config["version"] }}';
		const csrfToken = '{{ csrf_token() }}'
		const commonLang = @json(trans('common')); // lang file
		
		// search conditions
		const params = new URL(document.location).searchParams;
		const entries = params.entries(); 
		const searchData = paramsToObject(entries); 

		// for sys msg
		var errormsg = null
		var successmsg = null
		@if(session()->has('player'))
			errormsg = @json(session('error'));
			successmsg = @json(session('success'));
		@else
			showErrorToast(commonLang.js.loginFirst);                                            
		@endif


		// ===== DATA LAYER ======

		console.log('player')
		console.log(@json(session('player')));

		// player and sport_id
		const player = @json(session('player.id'));
		const token = 12345
		const sport = parseInt(searchData.sport)

		// loading page control
		var isReadyCommon = false
		var isReadyCommonInt = null

		// call api data
		const commonCallData = { token: token, player: player }

		// 帳號
		var accountD = {}
		const account_api = 'https://sportc.asgame.net/api/v2/common_account'

		// marquee
		var marqueeD = {}
		const marquee_api = 'https://sportc.asgame.net/api/v2/index_marquee'

		// sportList
		var sportListD = {}
		const sportList_api = 'https://sportc.asgame.net/api/v2/match_sport'

		function caller( url, data, obj, isUpdate = 0 ) {
			$.ajax({
				url: url,
				method: 'POST',
				data: data,
				success: function(data) {
					const json = JSON.parse(data); 
					console.log(json)
					if(json.gzip) { // 解壓縮
						const str = json.data;
						const bytes = atob(str).split('').map(char => char.charCodeAt(0));
						const buffer = new Uint8Array(bytes).buffer;
						const uncompressed = JSON.parse(pako.inflate(buffer, { to: 'string' }));
						json.data = uncompressed
					}
					if( isUpdate === 0 ) {
						Object.assign(obj, json); // 将 json 中的属性复制到 obj 中
					} else {
						// ajax更新 不可以整包覆蓋
						// loop json.data-> 比較時間戳 不一樣再更新該筆就好
					}
					showSuccessToast(json.message)
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.error('Ajax error:', textStatus, errorThrown);
					showErrorToast(jqXHR)
				}
			});
		}
		// ===== DATA LAYER ======

		// ===== VIEW LAYER ======
		function viewCommonIni() {
			// 帳號 餘額
			$('.player').html(accountD.data.account)
			$('.balance').html(accountD.data.balance)

			// 创建一个空的跑马灯容器
			var marqueeContainer = $('<marquee>', {
				id: 'marquee',
				class: 'bg-deepgreen',
				behavior: 'scroll',
				direction: 'left'
			});

			marqueeD.data.forEach(function(item) { 
				var link = $('<a>', { // 创建<a>元素
					href: '#',
					class: 'marqlink'
				});

				var span = $('<span>', { // 创建<span>元素
					class: 'marq_context',
					text: item
				});

				link.append(span); // 将<span>添加到<a>中
				marqueeContainer.append(link); // 将<a>添加到跑马灯容器中
			});

			// 将跑马灯容器添加到页面中
			$('.rightNavTag').before(marqueeContainer);

			// left menu - sportListD 
			var sportType = sport;
			console.log(sportType);

			const currentUrl = window.location.href;
			const noPath = window.location.pathname;

			const urlMappings = {
				'/?sport': 'lf_sport',
				'm_order?': 'lf_mOrder',
				'order': 'lf_order',
				'match?': 'lf_match',
				'rule': 'lf_rule',
				'logs': 'lf_logs',
				'calculator': 'lf_calcu',
				'notice': 'lf_notice'
			};

			if (currentUrl.includes('index') || noPath == '/') {
				sportType = 1; // Update sportType to 1 based on conditions
				$("#lf_sport").addClass('active');
				$("#lf_sport .submenu-toggle-list").css('max-height', '900px');
			}

			for (const urlFragment in urlMappings) {
				if (currentUrl.includes(urlFragment)) {
					$(`#${urlMappings[urlFragment]}`).addClass('active currentpage');
					$(`#${urlMappings[urlFragment]} .submenu-toggle-list`).css('max-height', '900px');
					break;
				}
			}

			if (sportListD && sportListD.data) {
				var sports = sportListD.data;

				sports.forEach(function (x, index) {
				var key = index + 1;
				x.key = key;

				function createSportSelect(container, url) {
					var sportSelect = document.createElement("a");
					sportSelect.setAttribute("id", x.sport_id);
					sportSelect.setAttribute("class", "sportSelect " + (currentUrl.includes(x.sport_id) ? "openToggle" : ""));
					sportSelect.setAttribute("href", url + x.sport_id);
					sportSelect.innerHTML = "<div class='sportname-con'><i class='fa-solid icon-" + key + "'></i><span><p>" + x.name + "</p></div><span class='menuStatistics_1'>" + ' ' + "</span>";
					container.appendChild(sportSelect);
				}

				createSportSelect(indexSportCon, "/?sport=");
				createSportSelect(mOrderSportCon, "/m_order?sport=");
				createSportSelect(matchSportCon, "/match?sport=");
				});
			}

			// msg
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
		}
		// ===== VIEW LAYER ======


		$(document).ready(function() {

			// loading page
			$('#dimmer').dimmer('show');

			// data layer
			caller(account_api, commonCallData, accountD) // account
			caller(marquee_api, commonCallData, marqueeD) // marquee
			caller(sportList_api, commonCallData, sportListD) // sportList
			// data layer


			// view layer
			// check if api are all loaded every 500 ms 
			isReadyCommonInt = setInterval(() => {
				if(accountD.status === 1 && marqueeD.status === 1 && sportListD.status === 1) {
					isReadyCommon = true
					viewCommonIni() // excute all common view layer ini function
					clearInterval(isReadyCommonInt); // stop checking
				}
			}, 500);
			// view layer


			// time update
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
		});

		
		// left side menu click function
		$(document).ready(function () {
			var submenuClicked = false;
			$(".submenu-btn").click(function () {
				$(this).closest('.submenu-main').toggleClass('active');

				var submenuToggleList = $(this).next(".submenu-toggle-list");
				if (submenuToggleList.length) {
				if (submenuToggleList[0].style.maxHeight === '0px' || submenuToggleList[0].style.maxHeight === '') {
					submenuToggleList.animate({ maxHeight: submenuToggleList[0].scrollHeight + 'px' }, 300);
					console.log("Click");
					submenuClicked = true;
				} else {
					submenuToggleList.animate({ maxHeight: '0' }, 300);
					console.log("unclick");
					submenuClicked = false;
				}
				}

				$('.submenu-main').not($(this).closest('.submenu-main')).removeClass("active");
				$('.submenu-toggle-list').not(submenuToggleList[0]).animate({ maxHeight: '0' }, 300);

				// Delayed action using setTimeout
				setTimeout(function () {
				if (!submenuClicked) {
					console.log("!submenuClicked");
					if ($(".submenu-main").hasClass("currentpage")) {
					$(".submenu-main.currentpage").addClass('active');
					$(".submenu-main.currentpage .submenu-toggle-list").animate({ maxHeight: '900px' }, 300);
					}
				}
				}, 3000);

			});
		});
		// ----------------------------

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
		
		// modal close
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

		// marquee close
		$('#marqModal .btn-close').click(function (event) {
			event.preventDefault(); // Prevents the default anchor behavior
			closeModal();
		});

		// convert search data to obj
		function paramsToObject(entries) {
			const result = {}
			for(const [key, value] of entries) { // each 'entry' is a [key, value] tupple
				result[key] = value;
			}
			return result;
		}
	</script>
  @stack('main_js')
  </body>
</html>
