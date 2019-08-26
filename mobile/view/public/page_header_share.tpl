<div class="layoutmasker"></div>
<div class="page_header_out">
    <header class="header_in page_header header-share">
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
            <span class="share-filter dropdown-toggle" id="shareFilter" data-toggle="dropdown" role="button" aria-haspopup="true">{$filterName}</span>
            <ul class="dropdown-menu" aria-labelledby="shareFilter">
                <li><a href="/?m=share&sort=time">按时间</a></li>
                <li><a href="/?m=share&sort=zan">按票数</a></li>
            </ul>
        </div>

        {if !$isapp}
        <div class="topbar_right" style="width:auto">
            <a href="/?m=share&a=plus" class="share_tit_btn"><i class="iconfont">&#xe62e;</i>晒图</a>
        </div>
        {/if}
    </header>
</div>

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