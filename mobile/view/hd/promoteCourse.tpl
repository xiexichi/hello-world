{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">

    <section class="main promote_course">
        
        <div class="promoteCourseBox">
            <dl class="promoteCourse">
                <dt>分享到朋友圈</dt>
                <dd>
                    <ol class="promoteCourse-ol">
                        <li>
                            <p>进入商品详情，点击<i class="iconfont" style="color:#b0b0b0;margin:0 3px;font-size:1.3em;">&#xe65f;</i>获取推广素材。</p>
                            <p><img src="http://img.25miao.com/115/1500367406.gif"></p>
                        </li>
                        <li>
                            <p>根据图片提示长按选择复制文字和长按保存图片。</p>
                            <p><img src="http://img.25miao.com/115/1500363530.gif"></p>
                        </li>
                        <li>
                            <p>选择刚才保存的图片发送朋友圈，并长按输入框粘贴刚才复制的文字。</p>
                            <p><img src="http://img.25miao.com/115/1500363533.gif"></p>
                        </li>
                        <li>
                            <p>下面是一张发送到朋友圈后的示例图</p>
                            <p><img src="http://img.25miao.com/115/1501138255.jpg"></p>
                        </li>
                        <li>
                            小贴示：想要得到更好的推广效果，建议根据自身情况自行编辑文字描述。
                        </li>
                    </ol>
                </dd>
            </dl>

            <dl class="promoteCourse">
                <dt>分享给好友</dt>
                <dd>
                    <ol class="promoteCourse-ol">
                        <li>
                            <p>进入商品详情页，确保自己是已登录的状态下，点击微信右上角菜单发送给朋友。</p>
                            <p><img src="http://img.25miao.com/115/1500363532.gif"></p>
                        </li>
                        <li>
                            小贴示：你也可以使用上面分享朋友圈的方法发送图片给朋友。
                        </li>
                    </ol>
                </dd>
            </dl>

            <dl class="promoteCourse">
                <dt>其它分享方式</dt>
                <dd>
                    <p>你可以保存以下链接和图片，发送到任何一个地方;</p>
                    <p>任何人通过你分享的链接和二唯码进入25BOY，注册成功即成为你的下线。</p>
                    {if $user_id}
                    <p class="myUrl">{$promote_link}</p>
                    <p class="myCode" style="display:none;"></p>
                    {else}
                    <p class="share-logintip">你还没登录，未能获取到推广链接，<a href="javascript:layerLoginTap();">请点击这里</a>。</p>
                    {/if}
                </dd>
            </dl>

            <a class="btn btn_gray go-myPromote" href="/?m=account&a=promote">进入我的推广中心</a>

        </div>
   
    </section>
</div>


{include file="public/js.tpl" title=js}
<link rel="stylesheet" type="text/css" href="/statics/css/promote.css?v={$version}">
<script type="text/javascript">
$(function(){
    myPromoteQrcode('p_redpack',function(res){
        if(res.code == '0'){
            $('.share-logintip').hide();
            $('.myCode').html('<img src="http://api.25boy.cn'+res.rs+'">').show();
        }
    });
});
</script>

{include file="public/footer.tpl" title=footer}