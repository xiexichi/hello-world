{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}
<style type="text/css">
.alert-events{
    background-color:#fff3cd;border-bottom:1px solid #ffeeba;color:#856404;font-size:14px;padding:5px 10px;display:none;
}
</style>

    <div id="bodybox">
        <section class="main pagemain pagebgwhite">
            <div class="alert-events"></div>

            {if $session_uid==""||$session_uid==0}
                {include file="public/remind_login.tpl" title=header}
            {else}
                {if $total_cart<1}
                    <section class="nologinbox">
                        <div class="imgbox rotateZ360"><i class="iconfont">&#xe622;</i></div>
                        <div class="titlebox">购物车空了！</div>
                        <div class="remarkbox">马上去选择商品吧</div>
                        <div class="btnbox"><a href="/?m=category" id="" class="btn">浏览商品</a></div>
                    </section>
                {else}
                    <div class="cartlistbox">
                        <ul></ul>
                    </div>

                    <span class="blank10"></span>
                    <span class="blank10"></span>
                    <span class="blank10"></span>
                    <span class="blank10"></span>
                    <span class="blank10"></span>
                    <span class="blank10"></span>
                    <span class="blank10"></span>
                    <span class="blank10"></span>
                    <span class="blank10"></span>
                    <span class="blank10"></span>
                    <span class="blank10"></span>
                {/if}
            {/if}
        </section>
        {if $total_cart>0}
        <div class="loaddiv" style="bottom:60px; {if $session_uid==""||$session_uid==0}display: none{/if}">
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

        {include file="public/cart.bottom.tpl" title=cartbottom}
        {/if}
    </div>
    {include file="public/js.tpl" title=js}
    <script>
        var totalcart = {$total_cart};
    </script>

    {if $session_uid==""||$session_uid==0}
        {literal}
            <script>
            $(function(){
                user.by_btn("#cart_user_by","by",true);
            });
            </script>
        {/literal}
    {else}
        {if $total_cart>0}
        <script defer src="/statics/js/cart.list.js?v={$version}"></script>
        {/if}
    {/if}

{include file="public/footer.tpl" title=footer}