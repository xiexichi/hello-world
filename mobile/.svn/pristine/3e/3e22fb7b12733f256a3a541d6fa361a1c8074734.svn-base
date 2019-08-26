{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}


    <div id="bodybox">
        <section class="main pagemain pagegrey">
            <div id="map"></div>
            <div class="business_name">{$business.business_name}</div>
            <div class="bueinessInfo">
                <dl>
                    <dt>地址</dt>
                    <dd>{$business.business_address}</dd>
                </dl>
                <dl>
                    <dt>电话</dt>
                    <dd>{$business.business_tel}</dd>
                </dl>
            </div>
        </section>
    </div>

{literal}
<script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key=V4RBZ-JMDW4-R6TUY-DSPEQ-KOF62-EABMS"></script>
<script>
function init(latitude,longitude){
    var center=new qq.maps.LatLng(latitude,longitude);
    var map=new qq.maps.Map(document.getElementById("map"),{
        disableDefaultUI: true,
        center:center,
        zoom:16
    });
    setTimeout(function(){
        var marker=new qq.maps.Marker({
            position:center,
            animation:qq.maps.MarkerAnimation.DROP,
            map:map
        });
    },1000);
}
</script>
<style type="text/css">
body{background-color:#f2f2f2;}
#map{width:100%;height:200px;}
.business_name{font-size:16px;padding:10px;background-color:#fff;}
.bueinessInfo{padding:5px 10px;font-size:14px;margin-top:10px;background-color:#fff;}
.bueinessInfo dl{border-top:1px solid #eee;padding:10px 0;}
.bueinessInfo dl dt{color:#999;margin-bottom:5px;}
.bueinessInfo dl:nth-child(1){border-top:none;}
</style>
{/literal}
<script type="text/javascript">
$(function(){
    var latitude = {$business.latitude};
    var longitude = {$business.longitude};
    init(latitude,longitude);
})
</script>

{include file="public/js.tpl" title=js}
{include file="public/footer.tpl" title=footer}