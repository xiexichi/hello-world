{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain pagegrey" style="background-color: #efefef">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
            <section class="coupon_box">
                <div class="inputbox">
                    <input id="coupon_pw" type="text" placeholder="请输入领取密码" />
                </div>
                <button id="btn_get_coupon">领取</button>
                <div class="remindbox">
                    * 25BOY保留最终解释权
                </div>
            </section>

            {if $ad_coupon}
            <p style="padding:1em 2em;">{$ad_coupon}</p>
            {/if}

            <section class="nav-tags nav-tags-col2 borderbottom">
                <span class="item {if $type=='coupon'}current{/if}"><a href="/?m=account&amp;a=coupon">代金券</a></span>
                <span class="item {if $type=='lottery'}current{/if}"><a href="/?m=account&amp;a=coupon&amp;c=lottery">抽奖码</a></span>
            </section>

            {if $type=='lottery'}

                {if $lottery_list|count>0}
                <ul class="coupon_list lottery_code">
                    {section name=item loop=$lottery_list}
                    {if $lottery_list[item].result==1}
                    <li class="victory">
                    {else if $lottery_list[item].timeout<0}
                    <li class="fail">
                    {else}
                    <li class="wait">
                    {/if}
                        <div class="title">{$lottery_list[item].title}</div>
                        <div class="time">抽奖日期：{$lottery_list[item].end_date}</div>
                    </li>
                    {/section}
                </ul>
                {/if}

            {else}

                {if $coupon_data|count>0}
                <ul class="coupon_list">
                    {section name=item loop=$coupon_data}
                        {if $coupon_data[item].coupon_active==1}
                            <li class="useed">
                        {else if $coupon_data[item].timeout<0}
                            <li class="disenable">
                        {else}
                            {if $coupon_data[item].coupon_type=="A"}
                                <li class="mulit">
                            {else}
                                <li class="single">
                            {/if}
                        {/if}

                        <div class="title">{$coupon_data[item].title}</div>
                        <div class="time">{$coupon_data[item].start_date} 至 {$coupon_data[item].exp_date}<!-- {if $coupon_data[item].is_pws==0},不限次数{/if} --></div>
                    {/section}
                </ul>
                {/if}
            {/if}

        {/if}
    </section>

</div>
<link rel="stylesheet" type="text/css" href="/statics/css/account.css?v={$version}">
{include file="public/js.tpl" title=js}

{if $session_uid==""||$session_uid==0}
{literal}
    <script>
        $(function(){
            user.by_btn("#cart_user_by","by",true);
        })
    </script>
{/literal}
{else}
{literal}
    <script src="/statics/js/coupon.js?v={$version}"></script>
{/literal}
{/if}

{include file="public/footer.tpl" title=footer}