{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
            {$balance_ads}
            <span class="blank10"></span>
            <div class="recharge_box">
                <form action="/ajax/do.account.payment.php" method="post" onsubmit="return check_payment();" id="payment_form">
                    <div class="payment_business_info">
                      <p class="img_box"><img src="/statics/img/logo_128x128.png" /></p>
                      <p class="store_name">{$store_name}</p>
                      <p class="txt">向25BOY付款</p>
                    </div>
                    <span class="blank20"></span>
                    <div class="inputbox"><input type="text" class="text" id="money_value" style="ime-mode:disabled;" onpaste="return false;" name="recharge" placeholder="输入金额" required="required" /></div>
                    <span class="blank10"></span>
                    <span class="blank20"></span>
                    <div class="inputbox">
                        <button id="btn_charge" class="btn" type="submit">确认金额并支付</button>
                    </div>
                </form>
            </div>

        {/if}
    </section>
</div>

<div class="loaddiv full" style="display:none;">
    <div class="loading-msg">
        <span>正在处理，请稍候</span>
        <div class="loading-box">
            <div class="loading" index="0"></div>
            <div class="loading" index="1"></div>
            <div class="loading" index="2"></div>
            <div class="loading" index="3"></div>
            <div class="loading" index="4"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
function check_payment(){
  if($("#money_value").val()=="" || isNaN($("#money_value").val()) || $("#money_value").val()<=0){
      alert('请输入金额，必须大于0元');
      $("#money_value").focus();
      return false;
  }
  user.show("reenterpassword",function(){
      post_form();
  });
  return false;
}
function post_form(){
  $('.loaddiv').show();
  var postdata = {
    'money' : $("#money_value").val()
  };
  var options = [{ "url": "/ajax/do.account.payment.php", "data":postdata, "type":"POST", "dataType":"json"}];
  Load(options, function(json){
    $('.loaddiv').hide();
    layer.open({ 
      content : json.msg,
      shadeClose: false,
      btn : ['确定'],
      yes: function(){
        if(json.status=='success'){
          window.location.href='/?m=account&a=balance';
        }
      }
    });
  });
}
</script>
<link rel="stylesheet" type="text/css" href="/statics/css/account.css?v={$version}">
{include file="public/js.tpl" title=js}
{include file="public/footer.tpl" title=footer}