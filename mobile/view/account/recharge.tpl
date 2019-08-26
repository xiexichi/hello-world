{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain">
        {if $firstRecharge.tips}
        <div class="firstRecharge">{$firstRecharge.tips}</div>
        {/if}
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
            {$balance_ads}
            <span class="blank10"></span>
            <div class="recharge_box">
                <form action="/alipay_charge.php" method="get" onsubmit="return check_recharge();" id="recharge_form">
                    <!-- <input type="text" name="test" value="测试"> -->
                    {if empty($recharge) || $recharge['not_top']}
                    <div class="remind f-s-22 f-c-999">请输入充值金额(元)</div>
                    <span class="blank20"></span>
                    <div class="inputbox"><input type="text" class="text" id="recharge_value" style="ime-mode:disabled;" onpaste="return false;" name="recharge" /></div>
                    <span class="blank10"></span>
                    <p class="plus_price">赠送200元</p>

                    {else}
                    <div class="remind f-s-22 f-c-999">请选择充值金额(元)</div>
                    <span class="blank10"></span>
                    <div class="inputbox"><input type="hidden" class="text" id="recharge_value" style="ime-mode:disabled;" onpaste="return false;" name="recharge" /></div>
                    
                    <div class="static_recharge_area">
                        {for $i=0 to count($recharge['recharge_value'])-1}
                        <div class="static_recharge">
                          <dl data-value="{$recharge['recharge_value'][$i]}">
                            <dd class="static_recharge_full">充值{$recharge['recharge_value'][$i]}元</dd>
                            <dd class="static_recharge_plus">送{$recharge['recharge_price'][$i]}元</dd>
                          </dl>
                        </div>
                       {/for}

                    </div>

                    <span class="blank10"></span>

                    {/if}
                    <div class="inputbox f-c-999"><label><input type="checkbox" checked="checked" id="recharge_provision" /> 我已经阅读活动规则，知晓充值金额不可提现、退款</label></div>
                    <span class="blank20"></span>
                    <input type="hidden" name="salt" value="{$salt}" />
                    <div class="inputbox">
                        <button id="btn_charge" class="btn" type="submit">确认金额并支付</button>
                    </div>
                </form>
            </div>
            <span class="blank30"></span>
            <div class="recharge_rules">
              <h3>活动规则：</h3>
              <ol class="info">
                  <li>充值成功后，您的充值金额及赠送金额将会实时进入您的二五账户。</li>
                  <li>在活动期间，不限充值次数，可累计充值，多充多送。</li>
                  <li>充值金额仅可通过您的个人账户购买二五商品，不可提现、退款及转赠。</li>
                  <li>使用充值金额购物的订单，按二五普通商品退换货规则办理退换货，退换货订单产生的退款将返回账户余额。</li>
                  <li>余额有效日期：永久有效。</li>
                  <li>分销商不参与充值赠送活动。</li>
              </ol>
            </div>

        {/if}
    </section>

</div>
<link rel="stylesheet" type="text/css" href="/statics/css/account.css?v={$version}">
<link rel="stylesheet" type="text/css" href="/statics/css/promote.css?v={$version}">
{include file="public/js.tpl" title=js}
{if $session_uid==""||$session_uid==0}
{literal}
<script>
    $(function(){
        user.by_btn("#cart_user_by","by",true);
    });
</script>
{/literal}
{else}
{literal}
<style type="text/css">
.firstRecharge{background-color:#fff9e6;color:red;border-bottom:1px solid #8ce6b0;font-size:16px;padding:8px 15px;text-align:center;}
</style>
<script>
if(iswx){
    $('#recharge_form').attr('action','/wxpay_charge.php');
}
$('#recharge_value').blur(function(){
	var price = $(this).val();
	var options = [{ "url": '/ajax/get.prepaid.price.php', "data":{money:price}, "type":"get", "dataType":"json"}];
    Load(options, function(res){
    	$('.plus_price').html('赠送'+res.p+'元');
		if(res.p > 0){
			$('.plus_price').show();
		}
    });
});
function check_recharge(){
    if($("#recharge_value").val()=="" || isNaN($("#recharge_value").val()) || $("#recharge_value").val()<=0){
        alert('请输入充值金额，必须大于0元');
        return false;
    }
    if($('#recharge_provision').prop('checked')==false){
        alert('请阅读并同意关于支付的条款');
        return false;
    }
    return true;
}


$(function() {
      /* 固定金额选择充值  */
      $('.static_recharge dl').click(function() {
        $('.static_recharge dl').removeClass('static_recharge_selected');
        $(this).addClass('static_recharge_selected');
        $('#recharge_value').val($(this).data('value'));
      });
});
</script>
{/literal}
{/if}

{include file="public/footer.tpl" title=footer}