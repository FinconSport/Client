@extends('layout.app')

@section('content')
	<div id="calculatorWrapper">
        <div class="calTitle mb-2">
            <div>{{ trans('calculator.calculator.calculator') }}</div>
        </div>
        <div class="calMain">
            <div class="subTitile row">
                <div class="col-2">{{ trans('calculator.calculator.index') }}</div>
                <div class="col-4">{{ trans('calculator.calculator.rate') }}</div>
                <div class="col-6">{{ trans('calculator.calculator.condition') }}</div>
            </div>
            <div class="calMainArea row m-0">
                <div class="col-2">
                    @for ($i = 1; $i <= 10; $i++)
                        <div class="rowEach">
                            <div class="indexDiv">{{ $i }}</div>
                        </div>
                    @endfor
                </div>
                <div class="col-4 rateInput">
                    @for ($i = 1; $i <= 10; $i++)
                        <div class="rowEach">
                            <input class="w-100" id="rate_{{ $i }}" type="text" inputmode="numeric" pattern="[0-9]*([.,][0-9]+)?" key='userInput' oninput="this.value = this.value.replace(/[^0-9.,]/g, '')" >
                        </div>
                    @endfor
                </div>
                <div class="col-6">
                    @for ($i = 1; $i <= 10; $i++)
                        <div class="rowEach">
                            <select id="select_{{ $i }}" key="{{ $i }}" name="rate">
                                <option value=1 selected>{{ trans('calculator.calculator.condition_1') }}</option>
                                <option value=2>{{ trans('calculator.calculator.condition_2') }}</option>
                                <option value=3>{{ trans('calculator.calculator.condition_3') }}</option>
                                <option value=4>{{ trans('calculator.calculator.condition_4') }}</option>
                                <option value=5>{{ trans('calculator.calculator.condition_5') }}</option>
                                <option value=6>{{ trans('calculator.calculator.condition_6') }}</option>
                            </select>
                        </div>
                    @endfor
                </div>
            </div>
            <div class="calFooter">
                <div id="calProcess">
                    <p>{{ trans('calculator.calculator.process') }}</p>
                    <p id="processStr"></p>
                </div>
                <div class="row m-0">
                    <div class="col-1"></div>
                    <div class="col-5 row m-0">
                        <div class="col-6 p-0 text-right">
                            <span class="moneyLabel">{{ trans('calculator.calculator.betmoney') }}</span>
                        </div>
                        <div class="col-6 p-0 text-left">
                            <input class="w-100" id="betMoney" autocomplete="off" inputmode="numeric" oninput="this.value = this.value.replace(/\D+/g, '')" >
                        </div>
                    </div>
                    <div class="col-5 row m-0 text-right">
                        <div class="col-6 p-0">
                            <span class="moneyLabel">{{ trans('calculator.calculator.winmoney') }}</span>
                        </div>
                        <div class="col-6 p-0 text-left">
                            <input class="w-100" readonly id="winningMoney">
                        </div>
                    </div>
                    <div class="col-1"></div>
                </div>
                <div class="row m-0">
                    <div class="col-2"></div>
                    <div class="col-4">
                        <button onclick="clearAll()">{{ trans('calculator.calculator.clear') }}</button>
                    </div>
                    <div class="col-4">
                        <button onclick="claculate()">{{ trans('calculator.calculator.calculate') }}</button>
                    </div>
                    <div class="col-2"></div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('styles')
<link href="{{ asset('css/calculator.css?v=' . $system_config['version']) }}" rel="stylesheet">
<style>	
/* 寫入頁面限定CSS */
</style>
@endSection
    

@push('main_js')
<script>
    // 語系
    var langTrans = @json(trans('calculator'));

    // detect ini ajax
    var isReadyCalInt = null
    
    $(document).ready(function() {
        // check if api are all loaded every 500 ms 
        isReadyCalInt = setInterval(() => {
            if( isReadyCommon ) {
                $('#dimmer').dimmer('hide'); // hide loading
                $('#wrap').css('opacity', 1); // show the main content
                clearInterval(isReadyCalInt); // stop checking
            }
        }, 500);
    });

    // 左邊菜單  當點擊體育或串關時 移除目前選中樣式
    $('.menuTypeBtn').click(function(){
        let key = $(this).attr('key')
        if( (key === 'index' || key === 'm_order' || key === 'match') && $(this).hasClass('on') ) {
            $('div[key="calculator"] .slideMenuTag').css('border-bottom-left-radius','0')
            $('div[key="calculator"] .slideMenuTag').css('border-top-left-radius','0')
            $('div[key="calculator"] .slideMenuTag').css('background-color','#415b5a')
            $('div[key="calculator"] .slideMenuTag').css('color','white')

            $('div[key="logs"] .slideMenuTag').css('border-bottom-right-radius','0')
            $('div[key="menuBottomFill"] .slideMenuTag').css('border-top-right-radius','0')
        } else {
            $('div[key="calculator"] .slideMenuTag').css('border-bottom-left-radius','25px')
            $('div[key="calculator"] .slideMenuTag').css('border-top-left-radius','25px')
            $('div[key="calculator"] .slideMenuTag').css('background-color','rgb(196, 211, 211)')
            $('div[key="calculator"] .slideMenuTag').css('color','#415b5a')

            $('div[key="logs"] .slideMenuTag').css('border-bottom-right-radius','15px')
            $('div[key="menuBottomFill"] .slideMenuTag').css('border-top-right-radius','15px')
        }
    })

    function clearAll() {
        $('input').val('')
        $('#processStr').html('')
        $('select').val('1')
        $('select').trigger('change')
    }

    function claculate() {
        if($('#betMoney').val() === '') {
            alert(langTrans.calculator.noinputmoney)
            return;
        }

        let betMoney = parseInt($('#betMoney').val())
        let process = betMoney
        let winMoney = betMoney
        
        $('input[key="userInput"]').each(function () {
            var rate = parseFloat($(this).val())
            if(!isNaN(rate)) {
                let id = $(this).attr('id')
                let select = id.replace('rate', 'select')
                let selectVal = $('#' + select).val()

                switch (selectVal) {
                    case '1':
                        process += ' * ' + rate.toFixed(2) + ' '
                        winMoney *= rate
                        break;
                    case '2':
                        rate = 0
                        process += ' * ' + rate.toFixed(2) + ' '
                        winMoney *= rate
                        break;
                    case '3':
                        rate = (rate - 1) / 2 + 1
                        process += ' * ' + rate.toFixed(2) + ' '
                        winMoney *= rate
                        break;
                    case '4':
                        process += ' * ' + 0.5 + ' '
                        winMoney *= 0.5
                        break;
                    case '5':case '6':
                        break;
                }
            }
        });
        process += ' = ' + winMoney.toFixed(2)
        $('#processStr').html(process)
        $('#winningMoney').val(winMoney.toFixed(2))
    }
	

</script>
@endpush