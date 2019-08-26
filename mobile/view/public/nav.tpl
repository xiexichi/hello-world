<nav id="menu" class="nav_in">
    <section class="categorybox">
        <ul class="category category_root">
            {section name=item loop=$category}
                {if $category[item].status==1}
                <li><a href="javascript:;" index="{$smarty.section.item.index}" rel="{$category[item].category_id}" {if $smarty.section.item.index ==0}class="current"{/if}>{$category[item].category_name}</a></li>
                {/if}
            {/section}
        </ul>
        <ul class="category category_sub">
            {foreach from=$category[0].childrens item=item}
                {if $item.status==1}
                <li><a href="?m=category&cid={$item.category_id}">{$item.category_name}</a></li>
                {/if}
            {/foreach}
        </ul>
    </section>

    <a href="/?m=home" class="item btn3 {if $model=='home'}on{/if}"><i class="iconfont">&#xe672;</i>首页</a>
    <a href="/?m=category&a=singlePage" class="item btn1 {if $model=='category'}on{/if}"><i class="iconfont">&#xe66f;</i> 分类</a>
    <a href="javascript:;" class="item btn2 show_QRcode"><i class="iconfont">&#xe661;</i> 关注微信</a>
    <!-- <a href="/?m=category&new=1" class="item btn2"><i class="iconfont">&#xe671;</i> 新品上架</a> -->
    <a href="/?m=cart" class="item btn5 {if $model=='cart'}on{/if}" id="navcart"><i class="iconfont">&#xe66c;</i> 购物车</a>
    <a href="/?m=account" class="item btn4 {if $model=='account'}on{/if}"><i class="iconfont">&#xe66e;</i> 我的二五</a>
</nav>

{if $smarty.get.showQRcode}
<div class="guide_line">
    <img src="/statics/img/guide_line.png" />
</div>
<script type="text/javascript">
$(function(){
    var h = (($(window).height()-356)/2-50);
    $('.guide_line img').css('height',h+'px');
    layer.open({
        content: '<div class="show_QRcode_Box" style="width:230px;"><img src="http://img.25miao.com/115/1482826662.jpg!w390" alt="25BOY微信公众号" style="display:block"></div>',
        style  : 'background:#fff',
        shade : true,
        shadeClose :true,
        end:function(){
            $('.guide_line').hide();
        }
    });
});
</script>
{/if}
