{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    {if $session_uid==""||$session_uid==0}
        {include file="public/remind_login.tpl" title=header}
    {else}
        <section class="main pagemain">
        	<div class="share_plus_box">
                <div class="plus_bg_add"><img src="/statics/img/plus_bg_add.png" alt="添加晒图" /></div>
        		<div class="plus_textarea share_hide">
        			<textarea name="content" id="share_content" placeholder="跟大家分享什么吧！"></textarea>
        		</div>
                <div class="plus_photos up-image-box" data-id="1" id="up-image-box_1">
                    <div class="share_hide">
                        <div class="loadinger"></div>
                        <a class="addimg" rel="button" title="添加图片"><i class="iconfont">&#xe628;</i></a>
                        <span class="uptip">0/5</span>
                        <ul class="imglist uped-image-list"></ul>
                        <input type="file" multiple="multiple" accept="image/png,image/gif,image/jpeg" class="upload_input">
                        <div class="photos-box"></div>
                        <div class="blank10"></div>
                    </div>
                    <div class="alert alert-warning" role="alert" style="display:none;"></div>
                </div>
        		<div class="plus_button">
        			<a rel="button" class="btn add_btn">提交晒图</a>
        			<a rel="button" class="btn btn_gray" href="/?m=share">返回</a>
        		</div>
                <span class="blank10"></span>
        	</div>
        </section>
    {/if}
</div>
{include file="public/js.tpl" title=js}
<script src="/statics/js/share_wx.js?v={$version}"></script>
{include file="public/footer.tpl" title=footer}