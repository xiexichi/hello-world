{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox" style="background-color:transparent">
    <section class="main artlcles">
    	<div class="info-list">
    		<ul class="listBox">
    			{section name=item loop=$trendList}
    			<li>
                    <a href="/?m=trends&a=view&id={$trendList[item].article_id}" title="{$trendList[item].title}">
        				<p class="img_box"><img src="{$trendList[item].img_url}" alt="{$trendList[item].title}" /></p>
        				<p class="txt">
        					<span class="title">{$trendList[item].title}</span>
        					<span class="intro">{$trendList[item].desc}</span>
        					<span class="info"><span class="fl"><i class="iconfont">&#xe600;</i>{$trendList[item].date_added}</span> <span class="fr"><i class="iconfont">&#xe603;</i>{$trendList[item].click}</span></span>
        				</p>
        				<p class="blank10"></p>
                        <p class="blank5"></p>
                    </a>
    			</li>
    			{/section}
    		</ul>
    	</div>
    </section>

    <div class="loaddiv" style="bottom:60px; display: none">
        <div class="blank10"></div>
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

</div>
{include file="public/js.tpl" title=js}
<script type="text/javascript">var catid="{$catid}",tag="{$tag}";</script>
<script src="/statics/js/root.js?v={$version}"></script>
<script src="/statics/js/trends.list.js?v={$version}"></script>

{include file="public/footer.tpl" title=footer}
