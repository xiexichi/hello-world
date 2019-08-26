{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain">

        <div class="categoryPage">
            {foreach $category as $key=>$item}
                {if $key < 3}
                <dt class="categoryPage-dl">
                    <dt class="categoryPage-dt">
                        <a href="/?m=category&cid={$item['category_id']}"><img src="https://api.25boy.cn/Public/img/categorys-tit-{$item.category_id}.png" alt="{$item.category_name}" /></a>
                    </dt>
                    <dd class="flex categoryPage-dd">
                        {foreach $item['childrens'] as $v}
                            {if $v['img_url']!="" && $v['status']==1}
                            <a href="/?m=category&cid={$v.category_id}" class="categoryPage-item">
                                <img src="{$v.img_url}!w200" />
                            </a>
                            {/if}
                        {/foreach}
                    </dd>
                </dt>
                {/if}
            {/foreach}
        </div>

    </section>
</div>

{include file="public/js.tpl" title=js}
<script src="/statics/js/root.js?v={$version}"></script>

{include file="public/footer.tpl" title=footer}
