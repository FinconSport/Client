// 左側選單選擇
function selectSlideMenu( e ) {
    $('.slideMenuTag').removeClass('on bd-t-l-r-25 bd-b-l-r-25');
    $('.slideMenuTagConstainer').removeClass('bd-t-l-r-25 bd-b-l-r-25');
    $('.preIndex').removeClass('preIndex');
    $('.nextIndex').removeClass('nextIndex');
    $(e).addClass('on');
    $(e).addClass('bd-t-l-r-25')
    $(e).addClass('bd-b-l-r-25')
    $(e).parent().addClass('bd-t-l-r-25')
    $(e).parent().addClass('bd-b-l-r-25')
    let index = $('.slideMenuTag').index(e);
    let preIndex = index - 1;
    let nextIndex = index + 1;
    if(index === 0) preIndex = null
    if(index === $('.slideMenuTag').length-1) nextIndex = null
    preIndex !== null ? $('.slideMenuTag').eq(preIndex).addClass('preIndex') : $('#gameCategoryTopFill').eq(preIndex).addClass('preIndex')
    nextIndex !== null ? $('.slideMenuTag').eq(nextIndex).addClass('nextIndex') : $('#gameCategoryBottomFill').eq(preIndex).addClass('preIndex')
}

// 菜單球類收合
function toggleMenu( menu ) {
    $('.menuTypeBtn').removeClass('on')
    $('.menuTypeBtn').removeClass('preBtn')
    $('.menuTypeBtn').removeClass('nextBtn')
    
    if( $('div[key=' + menu + ']').is(':visible') ) {
        $('div[key=' + menu + ']').hide(500)
    } else {
        $('div[key=' + menu + ']').show(500);
        $('div[key=' + menu + ']').closest('.menuTypeBtn').addClass('on')
        $('div[key=' + menu + ']').closest('.menuTypeBtn').prev().addClass('preBtn')
        $('div[key=' + menu + ']').closest('.menuTypeBtn').next().addClass('nextBtn')
    }

    $('.sportMenu').filter(':visible').each(function(){
        if($(this).attr('key') !== menu) $(this).hide(500)
    })

}

// 分頁
function navTo(page) {
    window.location.href = '/' + page;
}

// menu
function menuTo(evt, key) {
    evt.stopPropagation()
    console.log(key)
    let str = ''
    switch (key) {
        case 'today':
            // 获取今天的日期并格式化
            var today = new Date();
            var todayDate = today.toISOString().slice(0, 10);

            // 获取明天的日期并格式化
            var tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            var tomorrowDate = tomorrow.toISOString().slice(0, 10);
            str += '?start_time=' + todayDate;
            str += '&end_time=' + tomorrowDate;
            str += '&mm=1';
            break;
        case 'living':
            str += '?status=2&mm=2';
            break;
        case 'early':
            str += '?status=1&mm=3';
            break;
    }
    console.log(str)
    window.location.href = str
}

function sportTo(evt, key, menu) {
    evt.stopPropagation()
    // var status = searchData.status
    // var start_time = searchData.start_time
    // var end_time = searchData.end_time
    const queryParams = {};
    queryParams.sport = key;
    // if (status) {
    //     queryParams.status = status;
    // }
    // if (start_time) {
    //     queryParams.start_time = start_time;
    // }
    // if (end_time) {
    //     queryParams.end_time = end_time;
    // }
    const queryString = new URLSearchParams(queryParams).toString();
    const urlWithQuery = `/${menu}?${queryString}`;
    window.location.href = urlWithQuery

}

var today = new Date();
var tomorrow = new Date(today);
tomorrow.setDate(today.getDate() + 1);

var formattedToday = formatDate(today);
var formattedTomorrow = formatDate(tomorrow);

// console.log(formattedToday);    // 输出今天的日期，例如：2023-07-04
// console.log(formattedTomorrow); // 输出明天的日期，例如：2023-07-05

function formatDate(date) {
  var year = date.getFullYear();
  var month = ("0" + (date.getMonth() + 1)).slice(-2);
  var day = ("0" + date.getDate()).slice(-2);
  
  return year + "-" + month + "-" + day;
}


// 選擇球種
function sportSelect(event, key) {
    // reset form
    $('#indexSearch')[0].reset();
    console.log('sportSelect')
    event.stopPropagation();
    console.log(key)
    switch (key) {
        case 'today':
            $('input[name="start_time"]').val(formattedToday)
            $('input[name="start_time"]').trigger('change')
            $('input[name="end_time"]').val(formattedTomorrow)
            $('input[name="end_time"]').trigger('change')
            break;
        case 'living':
            $('input[name="status"]').val(2)
            $('input[name="status"]').trigger('change')
            break;
        case 'early':
            $('input[name="status"]').val(1)
            $('input[name="status"]').trigger('change')
            break;
        default:
            $('input[name="sport"]').val(key)
            $('input[name="sport"]').trigger('change')
            break;
    }

    $('#indexSearch').submit()
}

// Toast function
function showSuccessToast(title) {
    toast({
        title: title,
        type: "success",
    });
}

function showErrorToast(title) {
    toast({
        title: title,
        type: "error",
    });
}

function toast({
    title = "",
    type = "",
    duration = 2000
}) {
    const main = document.getElementById("toast");
    if (main) {
        const toast = document.createElement("div");
        // Auto remove toast
        const autoRemoveId = setTimeout(function() {
            main.removeChild(toast);
        }, duration + 1000);
        // Remove toast when clicked
        toast.onclick = function(e) {
            if (e.target.closest(".toast__close")) {
                main.removeChild(toast);
                clearTimeout(autoRemoveId);
            }
        };

        const icons = {
            success: "fas fa-check-circle",
            info: "fas fa-info-circle",
            warning: "fas fa-exclamation-circle",
            error: "fas fa-exclamation-circle"
        };
        const icon = icons[type];
        const delay = (duration / 1000).toFixed(2);

        toast.classList.add("toast", "text-center", "mt-3");
        toast.style.animation = `slideInLeft ease .3s, fadeOut linear 1s ${delay}s forwards`;
        toast.style.width = `20rem;`;
        toast.innerHTML = `<h3 class="toastType ${type}">${title}</h3>`;
        main.appendChild(toast);
    }
}