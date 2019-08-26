{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain pagegrey">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
            {if $history|count>0}
                <ul class="historylist">
                {section name=item loop=$history}

                    <li>
                        <div class="innerbox">
                            <div class="imgbox"><a href="?m=category&a=product&id={$history[item].product_id}"><img src="{$history[item].product_image}!w200" /></a></div>
                            <div class="itemsummary">
                                <p class="title">{$history[item].product_name}</p>
                                <p class="price">￥{$history[item].product_price}</p>
                                <p class="click">看了 {$history[item].click} 次</p>
                                <p class="time"><i></i>{$history[item].create_time}</p>
                            </div>
                        </div>
                    </li>
                {/section}
                </ul>
            {else}

            {/if}
        {/if}
    </section>
</div>
<link rel="stylesheet" type="text/css" href="/statics/css/account.css?v={$version}">
{include file="public/js.tpl" title=js}
{include file="public/footer.tpl" title=footer}