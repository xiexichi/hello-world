{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain pagegrey" style="background-color: #efefef">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
            <section class="balance_total ycenter" style="height: 140px">
                <div class="content">
                    <div class="totaltext"><span>{$integral_total}</span></div>
                </div>
            </section>
            {if $integral_total>0}
                <ul class="balance_list">
                </ul>
            {/if}
        {/if}
    </section>
    {if $integral_total>0}
        <div class="loaddiv" {if $session_uid==""||$session_uid==0}style="display: none"{/if}>
            <div class="loading-msg">
                <span>数据加载中请稍后</span>
                <div class="loading-box">
                    <div class="loading" index="0"></div>
                    <div class="loading" index="1"></div>
                    <div class="loading" index="2"></div>
                    <div class="loading" index="3"></div>
                    <div class="loading" index="4"></div>
                </div>
            </div>
        </div>
    {/if}
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
    <script src="/statics/js/integral.list.js?v={$version}"></script>
    <script src="/statics/js/jquery.animateNumber.min.js"></script>
    <script>
        $(function(){
            var integral_total = $('.totaltext span').html();
            $('.totaltext span').animateNumber({ number: integral_total });
        });
    </script>
{/literal}
{/if}

{include file="public/footer.tpl" title=footer}