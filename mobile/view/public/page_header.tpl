<div class="layoutmasker"></div>
<div class="page_header_out">
    <header class="header_in page_header">
        <div class="topbar_left">
            {if $model=='home'}
            <a href="#/?m=search" class="search site-search-btn"><i class="iconfont">&#xe657;</i></a>
            {elseif $module=='promote'}
            <a href="javascript:;" class="search toggle_nav_promote" onclick="toggle_nav_promote();"><i class="iconfont" style="color:#444;">&#xe679;</i></a>
            {else}
            <a href="{if isset($goback)}{$goback}{else}javascript:history.go(-1);{/if}" class="back"><i class="iconfont">&#xe616;</i></a>
            {/if}
        </div>

        <div class="pagetitle">
        {$page_sed_title}
        {if isset($page_sed_search) && $page_sed_search}
        <i class="iconfont" id="{$page_sed_search}" style="font-size:1.2em;line-height:45px;">&#xe657;</i>
        {/if}

        {if $model=='share'}
        <a href="/?m=share&a=plus" class="share_tit_btn"><i class="iconfont">&#xe62e;</i>上传晒图</a>
        {/if}
        </div>

        {if !$isapp}
        <div class="topbar_right">
            <a href="javascript:;" class="menu nav__trigger" role="button"><i class="iconfont">&#xe604;</i></a>
            <!-- <a href="/?m=search" class="search"><i class="iconfont">&#xe657;</i></a> -->
        </div>
        {/if}
    </header>
</div>
{if $module=='promote'}
<nav class="nav-promote">
    <a href="?m=account&a=promote" class="item btn3    {if $submodule == 'promote_index'}nav-promote-active{/if}"><i class="iconfont">&#xe682;</i>推广返佣</a>
    <a href="?m=account&a=promotion" class="item btn1  {if $submodule == 'promote_promotion'}nav-promote-active{/if}"><i class="iconfont">&#xe668;</i>推广效果</a>
    <a href="?m=account&a=earnings" class="item btn2   {if $submodule == 'promote_earnings'}nav-promote-active{/if}"><i class="iconfont">&#xe64b;</i>我的收益</a>
    <a href="?m=account&a=earnings_detail" class="item btn5  {if $submodule == 'promote_earnings_detail'}nav-promote-active{/if}"><i class="iconfont">&#xe686;</i>收益明细</a>
    <a href="?m=account&a=withdrawal" class="item btn4 {if $submodule == 'promote_withdrawal'}nav-promote-active{/if}"><i class="iconfont">&#xe67f;</i>提现方式</a>
</nav>
{/if}

{if $site_top_banner && !$hide_site_top_banner}
<div class="top_banner">
  {$site_top_banner}
</div>
{/if}
{if $addend_top_banner}
<div class="addend_top_banner">
  {$addend_top_banner}
</div>
{/if}