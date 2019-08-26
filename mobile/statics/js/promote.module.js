//全局变量
var promoteVersion = '2016082807';


$(function() {

    /* **********************************************************************************
     * 不可提现提示
     * **********************************************************************************/
     $('#no_withdrawal_notice').on('click',function() {
        var withdrawal_notice = $(this).data('notice');
        var iswithdrawal = $(this).data('iswithdrawal');
        var withdrawal_type_cn = '';
        // console.log(this_withdrawal);
        if(iswithdrawal==1) {
            if(this_withdrawal['withdrawal_type'] == 'alipay') {
                withdrawal_type_cn = '支付宝';
            }
            layer.open({
                content:withdrawal_notice+"，详情如下：<br />提现方式："+withdrawal_type_cn+"<br />提现账户："+this_withdrawal.withdrawal_account+"<br />账户名称："+this_withdrawal.withdrawal_name+"<br />提现时间："+this_withdrawal.apply_time,
            });
        }else {
            layer.open({
                content:withdrawal_notice,
            });
        }
     });


    /* **********************************************************************************
     * 提示功能开关
     * **********************************************************************************/
      if(localStorage.promoteVersion != promoteVersion) {
        //显示指示
        $('.shade').show();
        //阻止屏幕手动默认行为
        $(document).on('touchmove', function(e) {
            e.stopPropagation();
            e.preventDefault();
        });
      }


});

//功能开关
function toggle_nav_promote() {
    //当用户点击了功能开关后，就不再出现指示了
    localStorage.promoteVersion = promoteVersion;
    // 弹出层位置
    if($('.top-downloadbar').length > 0){
        $('.nav-promote').css('top','120px');
    }else{
        $('.nav-promote').css('top','50px');
    }
    //开关
    $('.nav-promote').toggle();
    //隐藏指示
    $('.shade').hide();
    //取消绑定的行为
    $(document).off('touchmove');
}