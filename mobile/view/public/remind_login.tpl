{if $is_weixin && $openid}
<script>
$(function(){
var b = new Base64();
var gourl = b.encode(window.location.href);
window.location.href ='/?m=login&a=weixin.bind&gourl='+gourl;
});
</script>
{else}
<input type="hidden" id="action_zindex" value="1">
<script>
$(function(){
	user.action('by',true);
	user.createpannel();
	$('#user_pannel').removeClass("right_to_left").addClass('login_pannel');
	$('#user_by').css('transform', 'translateX(0px)');
});
</script>
{/if}