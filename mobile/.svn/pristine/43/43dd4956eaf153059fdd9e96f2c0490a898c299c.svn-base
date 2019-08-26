{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain pagegrey" style="background-color: #fff">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}

            <!-- <section class="balance_total ycenter bg_danger">
                <div class="content" style="margin:0 auto;">
                    <div class="totaltext" style="font-size:3em">轻松推广,轻松赚钱</div>
                </div>
            </section> -->



            <section class="balance_class borderbottom withdrawal">
                <ul class="first">
                    <li >提现方式</li>
                    <li class="long">提现账户</li>
                    <li>提现姓名</li>
                </ul>
                {foreach $promote_withdrawal as $key=>$value}
                <ul class="click_to_update_withdrawal" data-id="{$value['pwithdrawal_id']}">
                    <li data-type="{$value['withdrawal_type']}">{$value['withdrawal_type_cn']}</li>
                    <li class="long">{$value['withdrawal_account']}</li>
                    <li>{$value['withdrawal_name']}</li>
                </ul>
                {foreachelse}
                <center>暂无数据</center>   
                {/foreach}

            </section>

            <div class="add_withdrawal">
                <button class="btn btn_add_withdrawal" id="btn_add_withdrawal">添加提现方式</button>
            </div>

        {/if}

<div class="shade">
    <img src="/statics/img/ani_arrow.gif" class="shade-ani">
    <img src="/statics/img/pointer_click.png" class="shade-pointer"/>
</div>    

<link rel="stylesheet" type="text/css" href="/statics/css/account.css?v={$version}">
<link rel="stylesheet" type="text/css" href="/statics/css/promote.css?v={$version}">

{include file="public/js.tpl" title=js}
<script src="/statics/js/jquery.animateNumber.min.js"></script>
<script src="/statics/js/promote.module.js?v={$version}"></script>
<script>
    $(function(){

        $('.earnings_monthly_list ul:last li').css('border-bottom','0');

 
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
<script src="/statics/js/account_order.js?v={$version}"></script>
{/if}


{include file="public/footer.tpl" title=footer}

