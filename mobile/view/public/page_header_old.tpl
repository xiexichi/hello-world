<div class="layoutmasker"></div>
<div class="page_header_out">
    <header class="header_in page_header">
        <div class="log_status_box">
            <a href="{if isset($goback)}{$goback}{else}javascript:history.go(-1);{/if}" class="back"><i class="iconfont">&#xe616;</i></a>
        </div>
        <div class="pagetitle">
        {$page_sed_title}
        {if isset($page_sed_search) && $page_sed_search}
        <i class="iconfont" id="search_orders" style="font-size:1.2em;line-height:45px;float: right;">&#xe657;</i>
        {/if}
        </div>
        
        <div class="homebtn">
            <a href="javascript:;" class="menu dropdown-toggle" id="quickMenu" data-toggle="dropdown" role="button" aria-haspopup="true"><i class="iconfont">&#xe604;</i></a>
        </div>
        <ul class="dropdown-menu" aria-labelledby="quickMenu">
            <li><a href="/">首页</a></li>
            <li><a href="/?m=cart">购物车</a></li>
            <li><a href="/?m=category">全部商品</a></li>
            <li><a href="/?m=account">我的二五</a></li>
        </ul>
        <div class="dropdown-backdrop" style="display:none"></div>
    </header>
</div>

{if $site_top_banner}
<div class="top_banner">
  {$site_top_banner}
</div>
{/if}