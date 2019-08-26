{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain pagegrey">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
            {if $favorite|count>0}
                <ul class="historylist">
                    {section name=item loop=$favorite}

                        <li>
                            <div class="innerbox">
                                <div class="imgbox"><a href="?m=category&a=product&id={$favorite[item].product_id}"><img src="{$favorite[item].product_image}!w200" /></a></div>
                                <div class="itemsummary">
                                    <p class="title">{$favorite[item].product_name}</p>
                                    <p class="price">￥{$favorite[item].product_price}{if $favorite[item].product_price!=$favorite[item].market_price} <span class="old">{$favorite[item].market_price}</span>{/if}</p>
                                    <p class="click">{if stock==1}库存 {$favorite[item].total_quantity} 件{else}<span class="f-c-999">已下架</span>{/if}</p>
                                    <p class="time"><i></i>{$favorite[item].create_time}</p>
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
    <script>
        $("header .pagesearchbox").hide();
        $(".homebtn").show();

    </script>
{/literal}
{/if}

{include file="public/footer.tpl" title=footer}