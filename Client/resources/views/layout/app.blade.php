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
    <link href="{{ asset('css/common.css?v=' . $system_config['version']) }}" rel="stylesheet">
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
		<img src="{{ asset('image/loading.png?v=' . $system_config['version']) }}" alt="Logo">
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
								<div onclick="navTo('order')" class="submenu-btn"><i class="fa-regular fa-circle-dot"></i> <p>{{ trans('common.left_menu.record') }}</p></div>
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

						<div class="submenu-main" id="lf_calculator">
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

		

		// player and sport_id
		const player = @json(session('player.id'));
		const token = 12345
		var sport = parseInt(searchData.sport)

		// loading page control
		var isReadyCommon = false
		var isReadyCommonInt = null

		// call api data
		const commonCallData = { token: token, player: player }

		// 帳號
		var accountD = {}
		const account_api = '/api/v2/common_account'

		// marquee
		var marqueeD = {}
		const marquee_api = '/api/v2/index_marquee'

		// sportList
		var sportListD = {}
		const sportList_api = '/api/v2/match_sport'

		function caller(url, data, obj, isUpdate = 0) {
			return new Promise((resolve, reject) => {
				$.ajax({
					url: url,
					method: 'POST',
					data: data,
					success: function (data) {
						const json = JSON.parse(data);
						if (json.gzip) {
							const str = json.data;
							const bytes = atob(str).split('').map(char => char.charCodeAt(0));
							const buffer = new Uint8Array(bytes).buffer;
							const uncompressed = JSON.parse(pako.inflate(buffer, { to: 'string' }));
							json.data = uncompressed;
						}
						Object.assign(obj, json);
						if (isUpdate === 0) {
							showSuccessToast(json.message);
						}
						resolve(); // 解决 Promise
					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.error('Ajax error:', textStatus, errorThrown);
						showErrorToast(jqXHR);
						reject(errorThrown); // 拒绝 Promise 并传递错误信息
					}
				});
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

			var modalContainer = $('<div>', {
				id: 'modalMarqueeCon',
				class: 'modalContainer',
			});

			marqueeD.data.forEach(function(item, index) { 
				var modalId = 'marquee_' + index;

				var link = $('<a>', { // 创建<a>元素
					href: '#' + modalId, // Link to the corresponding modal
					class: 'marqlink'
				});

				var span = $('<span>', { // 创建<span>元素
					class: 'marq_context',
					text: item
				});

				link.append(span); // 将<span>添加到<a>中
				marqueeContainer.append(link); // 将<a>添加到跑马灯容器中
				
				// Create a modal for each item
				var modal = $('<div>', {
					id: modalId, // Assign the unique ID to the modal
					class: 'modaldiv modal' + modalId,
				});

				var modalContent = $('<div>', {
					class: 'marquee-modal-content',
					text: item,
				});

				var modalHtml = `
					<div class="modal-header">
						<span class="close-modal">&times;</span>
					</div>
					<div class="modal-body">
						<h5>` + item + `</h5>
					</div>
				`;
				modalContent.html(modalHtml);

				modal.append(modalContent);
				// Append the modal to  the main container of modal items
				modalContainer.append(modal);
			});

			// 将跑马灯容器添加到页面中
			$('.rightNavTag').before(marqueeContainer);
			marqueeContainer.after(modalContainer);

			//marquee onclick ------
			function showModal(modalId) {
				$('#' + modalId).css({ 'display': 'block', 'opacity': 0 }).animate({ opacity: 1 }, 500);
				// to close the modal when clicking outside
				$('.modaldiv').click(function (e) {
					if ($(e.target).hasClass('modaldiv')) {
						closeModal(modalId);
					}
				});
				$('.close-modal').click(function () {
					closeModal(modalId);
				});
			}
			// Function to close the modal
			function closeModal(modalId) {
				$('#' + modalId).animate({ opacity: 0 }, 500, function() {
					$(this).css('display', 'none');
				});
				$('.modaldiv').off('click'); // Remove the click event handler
			}
			// if click marquee, show the modal
			$('.marqlink').click(function (e) {
				e.preventDefault(); // Prevent the default behavior of the link
				var modalId = $(this).attr('href').substring(1); // Get the modal ID from the href attribute
				showModal(modalId);
			});
			//marquee onclick ------

			// left menu - sportListD 
			const pathName = window.location.pathname;
			var currentPage = null
			var currentResult = null
			switch (pathName) {
				case '/':case '/index':
					currentPage = 'lf_sport'
					break;
				case '/m_order':
					currentPage = 'lf_mOrder'
					break;
				case '/order':
					currentPage = 'lf_order';
					break;
				case '/match':
					currentPage = 'lf_match'
					break;
				case '/rule':
					currentPage = 'lf_rule'
					break;
				case '/logs':
					currentPage = 'lf_logs'
					break;
				case '/calculator':
					currentPage = 'lf_calculator'
					break;
				case '/notice':
					currentPage = 'lf_notice'
					break;
			}

			if (sportListD && sportListD.data) {
				var sports = sportListD.data;
				sports.forEach(function (x, index) {
				var key = index + 1;
				x.key = key;

				function createSportSelect(container, url) {
					var sportSelect = document.createElement("a");
					sportSelect.setAttribute("key", x.sport_id);
					sportSelect.setAttribute("class", "sportSelect");
					sportSelect.setAttribute("href", url + x.sport_id);
					sportSelect.innerHTML = "<div class='sportname-con'><i class='fa-solid icon-" + key + "'></i><span><p>" + x.name + "</p></div><span class='menuStatistics_1'>" + ' ' + "</span>";
					container.appendChild(sportSelect);
				}

				createSportSelect(indexSportCon, "/?sport=");
				createSportSelect(mOrderSportCon, "/m_order?sport=");
				createSportSelect(matchSportCon, "/match?sport=");
				});

				$(`#${currentPage}`).addClass('active currentpage');
				$(`#${currentPage} .submenu-toggle-list`).animate({'max-height': '900px'}, 300);
				$(`#subMenuContainer .currentpage a[key="${sport}"]`).addClass('openToggle')
				$(`#${currentResult}`).addClass('openToggle')

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
					if( !sport ) sport = sportListD.data[0].sport_id // default sport
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
		var submenuClicked = false;
		var submenuToggleList = $(".submenu-toggle-list"); // Precalculate the scrollHeight once
		$(".submenu-btn").click(function () {
			$(this).closest('.submenu-main').toggleClass('active');

			var currentSubmenuToggleList = $(this).next(".submenu-toggle-list");
			if (currentSubmenuToggleList.length) {
				if (currentSubmenuToggleList[0].style.maxHeight === '0px' || currentSubmenuToggleList[0].style.maxHeight === '') {
					currentSubmenuToggleList.animate({ maxHeight: submenuToggleList[0].scrollHeight + 'px' }, 300);
					console.log("Click");
					submenuClicked = true;
				} else {
					currentSubmenuToggleList.animate({ maxHeight: '0' }, 300);
					console.log("unclick");
					submenuClicked = false;
				}
			}

			$('.submenu-main').not($(this).closest('.submenu-main')).removeClass("active");
			$('.submenu-toggle-list').not(currentSubmenuToggleList[0]).animate({ maxHeight: '0' }, 300);
		});
		// ----------------------------


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
