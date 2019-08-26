{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">

    <section class="main promote_course">
        <ul>
            {foreach from=$datalist item=item}
            <li class="course_list" data-id="{$item.article_id}">
                <a href="/?m=about&id={$item.article_id}">
                <p class="title">{$item.title}</p>
                <p class="intro">{$item.desc}...</p>
                <i class="iconfont iconfont_arrow">&#xe636;</i>
                </a>
            </li>
            {/foreach}
        </ul>
   
    </section>
</div>


{include file="public/js.tpl" title=js}
<script src="/statics/js/account_order.js?v={$version}"></script>
<link rel="stylesheet" type="text/css" href="/statics/css/promote.css?v={$version}">
{include file="public/footer.tpl" title=footer}