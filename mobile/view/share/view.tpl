{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}
<link href="/statics/js/layer/need/layer.css?v={$version}"></link>

<div id="bodybox">
    <section class="main artlcles">
    	<div class="info-object">
    		<div class="infoBox">
                <div class="author clearfix">
                    <img class="avatar" src="{$object.avatar}">
                    <span class="name">{$object.username}</span>
                    <span class="intro fr mr10">{$object.time}
                        &nbsp;<i class="iconfont">&#xe603;</i>&nbsp;{$object.click}
                        &nbsp;<span onclick="voteset('share',{$object.share_id})" class="like_{$object.share_id}"><i class="iconfont">&#xe602;</i>&nbsp;<b class="vote likenum">{$object.vote}</b></span></span>
                </div>
                <div class="share-imaglist">
                    {foreach $object['photos'] as $p}
                    <img src="{$p}!w640" alt="达人晒图 潮流时尚" />
                    {/foreach}
                </div>

                {if $object['content']}
                <div class="share-content">
                    <i class="iconfont yinghao-t">&#xe627;</i>
                    <i class="iconfont yinghao-b">&#xe626;</i>
                    {$object.content}
                </div>
                {/if}
                <div class="share-btn-group">
                    <button class="share_like_btn like_{$object.share_id}" onclick="voteset('share',{$object.share_id})"><i class="iconfont">&#xe602;</i>投票 (<b class="likenum">{$object.vote}</b>)</button>
                </div>
                
            </div>
<!--             <div class="related-list share-list">
                <h3><a href="/?m=share">更多达人晒图</a></h3>
                <ul class="waterfall">
                    {section name=item loop=$moreshare}
                    <li class="pin">
                        <a href="/?m=share&a=view&id={$moreshare[item].share_id}">
                            <img src="{$moreshare[item].img_url}!w640" alt="{$moreshare[item].content}" />
                        </a>
                        <p class="txt">
                        <span class="view"><i class="iconfont">&#xe603;</i>{$moreshare[item].click} <span class="like pr like_{$moreshare[item].share_id}" onclick="voteset('share',{$moreshare[item].share_id})"><i class="iconfont">&#xe602;</i><b class="likenum">{$moreshare[item].vote|intval}</b></span></span>
                        <span class="name"><img class="avatar" src="{$moreshare[item].userimg}" alt="{$moreshare[item].username}" />{$moreshare[item].username}</span>
                        <span class="title">{$moreshare[item].content}</span>
                        </p>
                    </li>
                    {/section}
                    <li class="pin">
                        <a class="back_li" href="/index.php?m=share">
                            <i class="iconfont">&#xe650;</i>
                            返回列表
                        </a>
                    </li>
                </ul>
            </div> -->
            
            <div class="flex share-comment-count">
                <span>全部评论（<span class="scc">{$comment_count}</span>）</span>
                <span id="comment-order" order='{$order}'>
                {if $order eq 2}
                    <i class="iconfont">&#xe6a1;</i>&nbsp;按时间
                {else}
                    <i class="iconfont">&#xe6a2;</i>&nbsp;按热度
                {/if}
                </span>
            </div>


            <div class="share-comment">
                
                <div class="comment-list">

                    {foreach from=$comments item=v}
                    <div class="comment-item">
                            <div class="flex author">
                                <div class="flex info">
                                    <img class="avatar" src="{$v.image_url}">
                                    <span class="name">{$v.nickname}</span>
                                    <!-- <span class="time">06-28 16:46</span> -->
                                </div>
                                <div class="info-right">
<!--                                 <a href="javascript:;" class="zan"><i class="iconfont">&#xe69e;</i></a> -->
                                    <i class="iconfont zan" share_comment_id="{$v.id}">&#xe69e;</i>
                                    <span class="zan-count">{$v.zan}</span>
                                </div>
                            </div>
                            

                        <div class="comment" share_comment_id="{$v.id}">
                            <div share_comment_id="{$v.id}" uname="{$v.nickname}" class="reply">
                                <p>
                                    {$v.comment}
                                </p>
                                <div class="time-div">   
                                    <span class="time">{$v.create_time}</span>·
                                    <span>回复</span>
                                </div>                                
                            </div>

                            {if $v.replys}
                            <!-- 评论回复 -->
                            <div class="replys">
                                <!-- 评论回复 -->
                                {foreach from=$v.replys item=vv}
                                    <div class="reply-author" share_comment_id="{$vv.id}">
                                        <div>
                                            <!-- <img class="avatar" src="{$vv.image_url}"> -->
                                            <span class="name">{$vv.nickname}</span>：
                                            <span class="reply-comment">{$vv.comment}</span>
                                        </div>
                                        <p class="reply-time">{$vv.create_time}</p>
                                    </div>
                                {/foreach}
                            </div>
                            {/if}
                        </div>

                    </div>
                    {/foreach}


                </div>
            </div>
    
            <!-- 加载更多 -->
            <div id="loading-more" style="text-align: center;display: none;">
                <img src="/statics/img/loader3.gif" alt="">
            </div>


            
            <div class="comment-bottom">
                <div class="input-row">
                    <input type="text" class="comment-input" id="comment-input" placeholder="评论一下...">
                    <div class="comment-icon">
                        <i class="iconfont">&#xe73a;</i>&nbsp;<span class="scc">{$comment_count}</span>
                    </div>
                </div>     
                <div class="textarea-row">
                    <textarea name="comment" id="comment-area" cols="30" rows="5"></textarea>
                    <div class="flex flex-right">
                        <button id="sure">发表</button>
                        <button id="cancle">取消</button>
                    </div>
                </div>        
            </div>


    	</div>
    </section>
</div>


<!-- 发表loading -->
<div id="comment-loading">
    <img src="/statics/img/loader3.gif" alt="">
</div>


<div id="max-id" style="display: none;">{$maxId}</div>


<!-- 克隆div -->
<div id="clone-div" style="display:none">
    <!-- 评论项 -->
    <div class="comment-item">
            <div class="author">
                <div class="info">
                    <img class="avatar" src="">
                    <span class="name"></span>
                    <!-- <span class="time">06-28 16:46</span> -->
                </div>
                <div class="info-right">
                    <i class="iconfont zan" share_comment_id="">&#xe69e;</i>
                    <span class="zan-count">0</span>
                </div>
            </div>
            
        <div class="comment">
            <div share_comment_id="" uname="" class="reply">
                <p>
                    
                </p>
                <div class="time-div">   
                    <span class="time"> </span>·
                    <span>回复</span>
                </div>                
            </div>
        </div>
    </div>

    <!-- 评论回复 -->
    <div class="reply-author">
        <div>
            <span class="name"></span>：
            <span class="reply-comment"></span>
        </div>
        <p class="reply-time"></p>
    </div>
</div>



<script>var share_id="{$object.share_id}";</script>
{include file="public/js.tpl" title=js}
<script src="/statics/js/root.js?v={$version}"></script>
{if $is_weixin && $show_wx_share_div==true}
<script type="text/javascript">
$(function(){
    show_wx_share_div('/statics/img/share_to_wx.png');
});
</script>
{/if}
<script src="/statics/js/share.comment-new.js?v={$version}"></script>

{include file="public/footer.tpl" title=footer}