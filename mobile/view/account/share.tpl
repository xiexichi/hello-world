{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain pagegrey">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
            {if $share|count>0}
                <ul class="historylist">
                {section name=item loop=$share}
                    <li class="share_li_{$share[item].share_id}">
                        <div class="innerbox">
                            <div class="imgbox"><a href="?m=share&a=view&id={$share[item].share_id}"><img src="{$share[item].img_url}!w200" /></a></div>
                            <div class="itemsummary">
                                <p class="title"><a href="?m=share&a=view&id={$share[item].share_id}">{$share[item].content}</a></p>
                                <p class="price"><i class="iconfont">&#xe602;</i>{$share[item].vote}票 <i class="iconfont">&#xe603;</i>{$share[item].click}浏览</p>
                                <p class="time"><i class="iconfont">&#xe600;</i> {$share[item].date_added}</p>
                                <p><button onclick="del_share('{$share[item].share_id}')" class="btn btn_mini btn_gray">删除晒图</button></p>
                            </div>
                        </div>
                        {if $share[item].status==0}
                        <i class="iconfont v_check">&#xe630;</i>
                        {/if}
                    </li>
                {/section}
                </ul>
            {else}
                <div class="empty-content"><i class="iconfont"></i></div>
                <div class="empty-tips">
                    <p>您暂无晒图！</p>
                    <p><a href="/?m=share">看看大家的晒图吧！</a></p>
                </div>
            {/if}
        {/if}
    </section>
</div>
<link rel="stylesheet" type="text/css" href="/statics/css/account.css?v={$version}">
{include file="public/js.tpl" title=js}
{literal}
<script type="text/javascript">
function del_share(share_id){
    layer.open({
        title: '删除提示',
        content: '删除后不能恢复，真的要删除吗？',
        btn: ['删除', '取消'],
        yes: function(index){
            var postdata = {'share_id':share_id };
            var options = [{ "url": "/ajax/share.delete.php", "data":postdata, "type":"POST", "dataType":"json"}];
            var loadindex = layer.open({type: 2});
            Load(options, function(json){
                console.log(JSON.stringify(json));
                if(json.ms_code==1){
                    $('.share_li_'+share_id).slideUp('slow');
                }
                layer.close(loadindex);
                layer.open({content:json.ms_msg, time:2});
            });
        }
    });
}
</script>
{/literal}

{include file="public/footer.tpl" title=footer}