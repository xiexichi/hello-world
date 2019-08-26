{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain pagegrey" style="background-color: #efefef">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}

            <section class="balance_total ycenter bg_danger">
                <div class="content" style="width:100%;margin:0 auto;">
                    <div class="totaltext" data-val="{$promote['cash_total']}"><span><sup>￥</sup><span>{$promote['cash_total']}</span></span></div>
                    <div class="btnbox">
                        
                        <button style="width:50%;margin:0 auto;" {if $withdrawal['is_withdrawal']}id="btn_withdraw"{else}id="no_withdrawal_notice" data-notice="{$withdrawal['withdrawal_notice']}" data-iswithdrawal="{if $is_already_withdrawal}1{else}0{/if}"{/if} >
                            提现
                        </button>
                    </div>
                </div>
            </section>

            <section class="balance_class borderbottom earnings_recently">
                <span class="item b_d_d_ccc">
                    <p class="c_555">今日预估收入</p>
                    <p class="earnings_recently_val"><sup>¥</sup><span class="bold fs1_3">{$promote_earnings['today']}</span></p>
                </span>
                <span class="item b_d_d_ccc">
                    <p class="c_555">昨日预估收入</p>
                    <p class="earnings_recently_val"><sup>¥</sup><span class="bold fs1_3">{$promote_earnings['yesterday']}</span></p>
                </span>
                <span class="item">
                    <p class="c_555">本月预估收入</p>
                    <p class="earnings_recently_val"><sup>¥</sup><span class="bold fs1_3">{$promote_earnings['this_month']}</span></p>

                </span>
                <span class="item">
                    <p class="c_555">上月预估收入</p>
                    <p class="earnings_recently_val"><sup>¥</sup><span class="bold fs1_3">{$promote_earnings['last_month']}</span></p>
                </span>
            </section>

            <div class="earnings_monthly_title">按月份显示往期收入情况</div>
            <section class="balance_class borderbottom earnings_monthly">
                <div class="earnings_monthly_header">
                    <i class="iconfont">&#xe663;</i>
                    {for $i=2016 to $this_year}
                    <span {if $i==$this_year}class="current_year"{/if}>{$i}</span>
                    {/for}
                </div>
            </section>

            <section class="balance_class borderbottom earnings_monthly_list">
                <ul class="first">
                    <li>月份</li>
                    <li>付款笔数</li>
                    <li>充值笔数</li>
                    <li>实际收入</li>
                </ul>
                {foreach $promote_monthly as $key => $value}
                <ul>
                    <li>{$key}月</li>
                    <li>{$value['paid_num']}</li>
                    <li>{$value['recharge_num']}</li>
                    <li>¥{$value['earnings']}</li>
                </ul>
                {/foreach}
            </section>

        {/if}      

<div class="shade">
    <img src="/statics/img/ani_arrow.gif" class="shade-ani">
    <img src="/statics/img/pointer_click.png" class="shade-pointer"/>
</div>

<link rel="stylesheet" type="text/css" href="/statics/css/account.css?v={$version}">
<link rel="stylesheet" type="text/css" href="/statics/css/promote.css?v={$version}">
<script type="text/javascript">
    var all_withdrawal = {$all_withdrawal};
    var this_withdrawal = {$this_withdrawal};
</script>
{include file="public/js.tpl" title=js}
<script src="/statics/js/jquery.animateNumber.min.js"></script>
<script src="/statics/js/promote.module.js?v={$version}"></script>

<script>
    var c = "{$c}";
    var decimal_places = 2;
    var decimal_factor = decimal_places === 0 ? 1 : decimal_places * 10;


    $(function(){
        if($('.earnings_monthly_list ul').size() > 1) {
            $('.earnings_monthly_list ul:last li').css('border-bottom','0');
        }


    
        /* **********************************************************************************
         * 数字动态显示
         * **********************************************************************************/
        var integral_total = $('.totaltext span span').html();
        $('.totaltext span span').animateNumber({
            number: integral_total * decimal_factor,
            numberStep: function(now, tween) {
                var floored_number = Math.floor(now) / decimal_factor,
                        target = $(tween.elem);
                if (decimal_places > 0) {
                    floored_number = floored_number.toFixed(decimal_places);
                    /*floored_number = floored_number.toString().replace('.', ',');*/
                }

                target.text(floored_number);
            }
        },1000);


        /* **********************************************************************************
         * 切换年份
         * **********************************************************************************/
        $('.earnings_monthly_header span').click(function() {
        
            if(!$(this).hasClass('current_year')) {
                $('.earnings_monthly_header span').removeClass('current_year');
                $(this).addClass('current_year');

                /*取得指定年份的数据*/
                var year  = Number($(this).text());
                /*加载条*/
                layer.open({
                    type : 2
                });
                $.getJSON('/ajax/get.earnings.info.monthly.php',
                    {
                        year : year
                    },
                    function(json) {
                      layer.closeAll();
                      $('.earnings_monthly_list ul').not('.first').remove();
                      var data  = json.info;
                      var month = json.month;
                      for(var i=1;i<=month;i++) {
                        var _html = '';
                        _html += '<ul>';
                        _html += '<li>'+i+'月</li>';
                        _html += '<li>'+data.promote_paid_order_monthly[i]+'</li>';
                        _html += '<li>'+data.promote_recharge_num_monthly[i]+'</li>';
                        _html += '<li>¥'+data.promote_earnings_monthly[i]+'</li>';
                        _html += '</ul>';
                        $('.earnings_monthly_list').append(_html);
                      }
                        if($('.earnings_monthly_list ul').size() > 1) {
                          $('.earnings_monthly_list ul:last li').css('border-bottom','0');
                        }

                    });
            }

        });

    });




</script>

{if $session_uid==""||$session_uid==0}
{literal}
    <script>
        $(function(){
            user.by_btn("#cart_user_by","by",true);
        })
    </script>
{/literal}
{else}
<script src="/statics/js/account_order.js"></script>
{/if}


{include file="public/footer.tpl" title=footer}