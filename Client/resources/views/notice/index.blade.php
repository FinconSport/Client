@extends('layout.app')

@section('content')
<div id ="noticePage" class="h-100 notice-con">
        <div class="row notice-row">
            <div class="col-xl-2 col-lg-2 col-md-2 col-2 nopad notice-col-left">
                <nav>
                    <div class="nav nav-tabs flex-column" id="nav-tab" role="tablist">
						<button class="nav-link active" id="tabAll" data-bs-toggle="tab" data-bs-target="#tab_All" type="button" role="tab" aria-controls="#tab_All" aria-selected="true">{{ trans('notice.main.all') }}</button>
						<button class="nav-link" id="tabSyst" data-bs-toggle="tab" data-bs-target="#tab_Syst" type="button" role="tab" aria-controls="#tab_Syst" aria-selected="false">{{ trans('notice.main.system') }}</button>
                        <button class="nav-link" data-bs-toggle="tab" type="button" role="tab" aria-selected="false" template="NavTabTemplate" hidden></button>          
                    </div>
                </nav>
            </div>
            <div class="col-xl-10 col-lg-10 col-md-10 col-10 notice-col-right">
            <div class="notice-tab">
                <div class="notice-tab-con">
                        <div class="tab-content" id="nav-tabContent">
							<div class="tab-pane active" id="tab_All" role="tabpanel" aria-labelledby="tabAll">
								<div class="tab-card">
									<div class="tab-card-title"></div>
									<div class="tab-card-content"></div>
								</div>
							</div>
							<div class="tab-pane" id="tab_Syst" role="tabpanel" aria-labelledby="tabSyst">
								<div class="tab-card">
									<div class="tab-card-title"></div>
									<div class="tab-card-content"></div>
								</div>
							</div>
                            <div class="tab-pane" role="tabpanel" template="tabPanelTemplate" hidden>
								<div class="tab-card">
									<div class="tab-card-title"></div>
									<div class="tab-card-content"></div>
								</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<link href="{{ asset('css/notice.css?v=' . $current_time) }}" rel="stylesheet">
<!-- <link href="{{ asset('css/notice.css?v=' . $system_config['version']) }}" rel="stylesheet"> -->
<style>
	/* 寫入頁面限定CSS */
</style>
@endSection

@push('main_js')
<script src="{{ asset('js/bootstrap.min.js?v=' . $system_config['version']) }}"></script>
<script>
	// detect ini ajax
	var isReadyNoticeInt = null
	var isReadyNotice = false

	// notice list data
	var noticeListD = {}
	const noticeList_api = '/api/v2/index_notice'

	$(document).ready(function() {

		// ini data from ajax
		caller(noticeList_api, commonCallData, noticeListD) // noticeListD

		// check if api are all loaded every 500 ms 
		isReadyNoticeInt = setInterval(() => {
			if (noticeListD.status === 1) {
				isReadyNotice = true;
			}
			if (isReadyNotice && isReadyCommon) {
				$('#dimmer').dimmer('hide'); // hide loading
				$('#wrap').css('opacity', 1); // show the main content
				renderView()
				clearInterval(isReadyNoticeInt); // stop checking
			}
		}, 500);
	});


	function renderView() {
		// sportlistD
		if (sportListD && sportListD.data) {
			sportListD.data.forEach((sportItem, sportIndex) => {
				createTabBtnAndContainer(sportItem, sportIndex);
			});

			// noticelistD
			if (noticeListD && noticeListD.data) {
				// Track whether any notices are inserted
				let noticesInserted = false;

				noticeListD.data.forEach((noticeItem, noticeIndex) => {
					if (createTabContent(noticeItem)) {
						noticesInserted = true;
					}
				});

				// If no notices were inserted, display "no data" text
				if (!noticesInserted) {
					displayNoDataText();
				}
			} else {
				// If no notice data is available, display "no data" text
				displayNoDataText();
			}
		}
	}
	
	function createTabBtnAndContainer(sportItem, sportIndex) {
		const NavTabBtn = $('button[template="NavTabTemplate"]').clone().removeAttr('hidden').removeAttr('template');
		NavTabBtn.attr('id', 'tab' + sportItem.sport_id);
		NavTabBtn.attr('data-bs-target', '#tab_' + sportItem.sport_id);
		NavTabBtn.attr('aria-controls', '#tab_' + sportItem.sport_id);
		NavTabBtn.html(sportItem.name);
		$('#nav-tab').append(NavTabBtn);

		const tabPanel = $('div[template="tabPanelTemplate"]').clone().removeAttr('hidden').removeAttr('template');
		tabPanel.attr('id', 'tab_' + sportItem.sport_id);
		tabPanel.attr('aria-labelledby', 'tab' + sportItem.sport_id);
		$('#nav-tabContent').append(tabPanel);
	}

	function createTabContent(noticeItem) {
		const sportId = noticeItem.sport_id;

		// Check if there is a corresponding sport_id in sportListD data
		const isSportIdValid = sportListD.data.some(sportItem => sportItem.sport_id === sportId);

		if (isSportIdValid) {
			// Insert into "tab_Syst" tab if sport_id is 0
			if (sportId === 0) {
				const tabContentSyst = $('#tab_Syst .tab-card-content');
				createTabCardContent(tabContentSyst, noticeItem);
			}

			// Insert into "tab_All" tab
			const tabContentAll = $('#tab_All .tab-card-content');
			createTabCardContent(tabContentAll, noticeItem);

			// Find the corresponding tab based on sport_id
			const tabContent = $('#tab_' + sportId + ' .tab-card-content');

			// Create content only if the tab exists and sport_id is not 0
			if (tabContent.length > 0 && sportId !== 0) {
				createTabCardContent(tabContent, noticeItem);
			}

			return true; // Return true if notice is inserted
		}

		return false; // Return false if no notice is inserted
	}

	function displayNoDataText() {
		// Display "no data" text in a specific element or console.log
		console.log("No data available.");
	}

	function createTabCardContent(tabContent, noticeItem) {
		const tabCard = $('<div class="tab-card"></div>');
		const tabCardTitle = $('<div class="tab-card-title"></div>').text(noticeItem.title);
		const tabCardContent = $('<div class="tab-card-content"></div>').text(noticeItem.context);

		tabCard.append(tabCardTitle, tabCardContent);
		tabContent.append(tabCard);
	}
		

	$("button.nav-link").click(function() {
        $(".notice-tab-con").animate({ scrollTop: 0 }, "smooth");
        console.log("top");   
    });

</script>
@endpush