{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<span id="btn_opensorttools">筛选</span>
<span id="btn_opencategorytools" style="text-align: center"><img src="/statics/img/icon.search.png" width="50%" style="margin-top: 25%" /></span>
<section id="sorttools" class="shadow">
    <div class="row">
        <div class="title">您想要如何排序</div>
        <div class="content">
            <div class="sortbtn sort_selected" id="default">默认</div>
            <div class="sortbtn" id="click">人气</div>
            <div class="sortbtn" id="sale">销量</div>
            <div class="sortbtn" id="price">价格<i class="iconfont">&#xe60a;</i></div>
            <div class="sortbtn" id="brand">品牌</div>
        </div>
    </div>
    <div class="row brandSet">
        <div class="brandsList">
        {foreach from=$brands item=item }  
        <span><a class="brandBtn" data-id="{$item.brand_id}" re_href="/?m=category&brand_id={$item.brand_id}"><img src="/statics/img/brand/brand-{$item.brand_id}.png" alt="{$item.brand_name}" /></a></span>
        {/foreach}
        </div>
        <div class="title priceSet">您要购买的价格区间</div>
    </div>
    <div class="row">
        <div class="content">
            <div class="price"><input type="text" value="" onkeyup="this.value=this.value.replace(/\D/g,'')" placeholder="￥" id="start_price" /></div>
            <div class="price-to">至</div>
            <div class="price"><input type="text" value="" onkeyup="this.value=this.value.replace(/\D/g,'')" placeholder="￥" id="end_price" /></div>
        </div>
    </div>
    <div class="row">
        <center>
            <button type="button" id="beginsort" class="btn">确定</button>
            <button type="button" id="cancelsort" class="btn btn_secondary">取消</button>
        </center>
    </div>
</section>
<section id="categorytools" class="shadow">
    <div class="row">
        <div class="title">商品搜索</div>
        <div class="content">
            <input type="text" value="" placeholder="{$k}" id="keyword" />
        </div>

    </div>
    <span class="blank10"></span>
    <div class="row">
        <center>
            <button type="button" id="beginsearch" class="btn">搜索</button>
            <button type="button" id="cancelsearch" class="btn btn_secondary">取消</button>
        </center>
    </div>
    <span class="blank10"></span>
    <span class="blank10"></span>
</section>
<section class="main pagemain pagebgwhite">
    <span class="blank5"></span>
    <div class="gird_box">
        <div class="gird_items" id="productlist">
        </div>
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
</section>
<div class="loaddiv">
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
{include file="public/js.tpl" title=js}
<script>
    var k = "{$k}";
    $(function() {
        $(".selectproductbtnbox").click(function(){
            if($(".selectproductbtnbox i.arrowup").css("background-position")=="-40px 0px"){
                $(".selectproductbtnbox i.arrowup").css("background-position","0px 0px");
            }else{
                $(".selectproductbtnbox i.arrowup").css("background-position","-40px 0px");
            }
            $(".submenu").slideToggle();
        });
    });
</script>
<script defer src="/statics/js/product.search.list.js?v={$version}"></script>
{literal}
<style>
    #categorytools .row {
        overflow: hidden;
        height:auto;
    }
    #categorytools .row .title {
        border-top:1px solid #eee;padding:1em;text-align: center;
    }
    #categorytools .row .content {

        border-top:1px solid #eee;
        background: #f9f9f9;
        padding:1em;
        display: -webkit-box;
        -webkit-box-orient: horizontal;
        -webkit-box-pack: center;
        -webkit-box-align: center;
        display: box;
        box-orient: horizontal;
        box-pack: center;
        box-align: center;
    }
    #categorytools .row .content input {border:1px solid #ccc;padding: 5px 15px;}
    #categorytools .row .content .sort_selected {border:1px solid #da3335; color:#fff; background: #da3335}
    #categorytools .row .btn{line-height: 2.3em}
</style>
{/literal}

{include file="public/footer.tpl" title=footer}