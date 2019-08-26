{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main artlcles">
    	<div class="info-object">
    		<div class="infoBox">
                <div class="img_box">
                    <img src="{$object.img_url}" alt="{$object.title}" />
                </div>
                <h1 class="title">{$object.title}</h1>
                <p class="info">{$object.time}
                &nbsp;&nbsp;&nbsp;<i class="iconfont">&#xe603;</i>&nbsp;{$object.click}
                &nbsp;&nbsp;&nbsp;<a href="http://www.25boy.cn">25BOY</a>
                </p>
                <div class="info-body">{$object.content}</div>
                <div class="info-tags">
                    文章标签：
                    {foreach $object['tags'] as $tag}
                    <a href="/?m=trends&tag={$tag}" title="25BOY {$tag}">{$tag}</a> 
                    {/foreach}
                    <a href="/?m=trends&tag=25BOY" title="25BOY国潮男装" target="_blank">25BOY</a>
                </div>
            </div>
            <div class="related-list">
                <h3>更多阅读</h3>
                <ul class="waterfall">
                    {section name=item loop=$related}
                    <li class="pin">
                        <a href="/?m=trends&a=view&id={$related[item].article_id}" title="{$related[item].title}">
                            <img src="{$related[item].img_url}" alt="{$related[item].title}" />
                            <span>{$related[item].title}</span>
                        </a>
                    </li>
                    {/section}
                </ul>
            </div>
    	</div>
    </section>
</div>
{include file="public/js.tpl" title=js}
<script src="/statics/js/root.js?v={$version}"></script>

{include file="public/footer.tpl" title=footer}
