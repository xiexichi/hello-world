<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:82:"D:\project\v3\view\shop\shop-o2o\public/../application/index\view\index\index.html";i:1556007596;s:45:"../application/common/view/common/header.html";i:1550129646;s:45:"../application/common/view/common/footer.html";i:1550129646;}*/ ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>25BOY - O2O新零售系统</title>
<link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
<link rel="stylesheet" href="/static/style/common.css" media="all">
<link rel="stylesheet" href="/static/style/admin.css" media="all">
<link rel="stylesheet/less" href="/static/style/myui.less">
<script src="/static/js/jquery-3.1.1.min.js"></script>

<!-- 百度echarts -->
<script src="/static/js/echarts.min.js"></script>

<!-- 自定义js -->
<script src="/static/js/common.js"></script>
<script src="/static/js/request.js"></script>

<!-- layui组件js -->
<!-- <script src="/static/layui/layui.js"></script> -->
<script src="/static/layui/layui.all.js"></script>
<script src="/static/js/layui-common.js"></script>
</head>

<body>
<div class="el-loading-mask is-fullscreen" id="el-loading-mask-fullscreen" style="display:none">
	<div class="el-loading-spinner">
		<svg viewBox="25 25 50 50" class="circular">
			<circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
		</svg>
	</div>
</div>
<div id="LAY_app">
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <!-- 头部区域 -->
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item layadmin-flexible" lay-unselect>
                    <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
                        <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a id="return" href="javascript:;" layadmin-event="return" title="后退">
                        <i class="layui-icon layui-icon-return"></i>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" layadmin-event="refresh" title="刷新">
                        <i class="layui-icon layui-icon-refresh-3"></i>
                    </a>
                </li>
            </ul>
            <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">
                <li class="layui-nav-item" lay-unselect>
                    <a lay-href="app/message/index.html" layadmin-event="message" title="消息中心">
                        <i class="layui-icon layui-icon-notice"></i>
                        <!-- 如果有新消息，则显示小圆点 -->
                        <span class="layui-badge-dot"></span>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="note">
                        <i class="layui-icon layui-icon-note"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="fullscreen">
                        <i class="layui-icon layui-icon-screen-full"></i>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;">
                        <cite><?php echo $userInfo['name']; ?></cite>
                    </a>
                    <dl class="layui-nav-child">
                        <!-- <dd><a lay-href="set/user/info.html">基本资料</a></dd> -->
                        <!-- <dd><a lay-href="set/user/password.html">修改密码</a></dd> -->
                        <!-- <hr> -->
                        <dd id="logout" style="text-align: center;"><a>退出</a></dd>
                    </dl>
                </li>
                <!-- <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="about"><i class="layui-icon layui-icon-more-vertical"></i></a>
          </li> -->
<<<<<<< .mine
          <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
            <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
          </li>
        </ul>
      </div>
      
      <!-- 侧边菜单 -->
      <div class="layui-side layui-side-menu">
        <div class="layui-side-scroll">
          <div class="layui-logo" lay-href="base.html">
            <span>25BOY新零售</span>
          </div>
          
          <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
            <li data-name="checkout" class="layui-nav-item">
              <a lay-href="/checkout/index" lay-tips="收银" lay-direction="2">
                <i class="layui-icon layui-icon-rmb"></i>
                <cite>收银台</cite>
              </a>
            </li>

            <li data-name="orders" class="layui-nav-item">
              <a href="javascript:;" lay-tips="订单" lay-direction="2">
                <i class="layui-icon layui-icon-list"></i>
                <cite>订单管理</cite>
              </a>
              <dl class="layui-nav-child">
                <dd data-name="orders"><a lay-href="/order/index/list.html">订单列表</a></dd>
                <dd data-name="refund"><a lay-href="/order/refund/list.html">退换货单</a></dd>
                <dd data-name="recharge"><a lay-href="/order/recharge/list.html">会员充值</a></dd>
              </dl>
            </li>

            <li data-name="depot" class="layui-nav-item">
              <a href="javascript:;" lay-tips="库存" lay-direction="2">
                <i class="layui-icon layui-icon-search"></i>
                <cite>库存管理</cite>
              </a>
              <dl class="layui-nav-child">
                <dd data-name="stock"><a lay-href="/depot/shop_depot/list.html">店铺库存</a></dd>
                <dd data-name="stock"><a lay-href="/depot/stock/list.html">店铺库存2</a></dd>
                <dd data-name="purchase"><a lay-href="/depot/purchase/list.html">进货单</a></dd>
                <dd data-name="sell">
                  <a lay-href="/depot/depot_pre_select/list.html">预选管理</a>
                </dd>

                <dd data-name="sell">
                  <a lay-href="/depot/shop_differ/list.html">进货差异单</a>
                </dd>
                <dd data-name="sell">
                  <a lay-href="/depot/shop_return/list.html">退货单</a>
                </dd>
                <dd data-name="sell">
                  <a lay-href="/depot/shop_adjust/list.html">调整单</a>
                </dd>
  
                <dd data-name="sell">
                  <a lay-href="/depot/shop_transfer/list.html">调拨单</a>
                </dd>
                
                <dd data-name="sell">
                  <a lay-href="/depot/shop_depot_change/list.html">库存变动记录</a>
                </dd>

              </dl>

            <li data-name="system" class="layui-nav-item">
              <a href="javascript:;" lay-tips="权限管理" lay-direction="2">
                <i class="layui-icon layui-icon-auz"></i>
                <cite>权限管理</cite>
              </a>
              <dl class="layui-nav-child">
                <dd data-name="role"><a lay-href="/power/role/list.html">角色设置</a></dd>
                <dd data-name="staff"><a lay-href="/power/staff/list.html">员工账号</a></dd>
              </dl>
            </li>
            
            <li data-name="home" class="layui-nav-item">
              <a href="javascript:;" lay-tips="员工管理" lay-direction="2">
                <i class="layui-icon layui-icon-user"></i>
                <cite>员工管理</cite>
              </a>
              <dl class="layui-nav-child">
                <!-- <dd><a lay-href="/analysis/sell/summary.html">会员分析</a></dd> -->
                <dd><a lay-href="/staff/staff/list.html">员工列表</a></dd>
                <dd><a lay-href="/staff/shop_auth_role/list.html">权限角色</a></dd>
                <dd data-name="form">
                  <a href="javascript:;">会员消费</a>
                  <dl class="layui-nav-child">
                    <dd><a lay-href="/bi/user/o2o_consum_order">实体店消费</a></dd>
                  </dl>
                </dd>
              </dl>
            </li>

            <li data-name="home" class="layui-nav-item">
              <a href="javascript:;" lay-tips="BI分析" lay-direction="2">
                <i class="layui-icon layui-icon-fonts-strong"></i>
                <cite>BI分析</cite>
              </a>
              <dl class="layui-nav-child">
                <!-- <dd><a lay-href="/analysis/sell/summary.html">会员分析</a></dd> -->
                <dd><a lay-href="/bi/consum/o2o_month.html">销售业绩</a></dd>
                <dd><a lay-href="/bi/consum/category_sales.html">分类销售</a></dd>
                <dd data-name="form">
                  <a href="javascript:;">会员消费</a>
                  <dl class="layui-nav-child">
                    <dd><a lay-href="/bi/user/o2o_consum_order">实体店消费</a></dd>
                  </dl>
                </dd>
              </dl>
            </li>

          </ul>
||||||| .r373
          <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
            <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
          </li>
        </ul>
      </div>
      
      <!-- 侧边菜单 -->
      <div class="layui-side layui-side-menu">
        <div class="layui-side-scroll">
          <div class="layui-logo" lay-href="base.html">
            <span>25BOY新零售</span>
          </div>
          
          <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
            <li data-name="checkout" class="layui-nav-item">
              <a lay-href="/checkout/index" lay-tips="收银" lay-direction="2">
                <i class="layui-icon layui-icon-rmb"></i>
                <cite>收银台</cite>
              </a>
            </li>

            <li data-name="orders" class="layui-nav-item">
              <a href="javascript:;" lay-tips="订单" lay-direction="2">
                <i class="layui-icon layui-icon-list"></i>
                <cite>订单管理</cite>
              </a>
              <dl class="layui-nav-child">
                <dd data-name="orders"><a lay-href="/order/index/list.html">订单列表</a></dd>
                <dd data-name="refund"><a lay-href="/order/refund/list.html">退换货单</a></dd>
                <dd data-name="recharge"><a lay-href="/order/recharge/list.html">会员充值</a></dd>
              </dl>
            </li>

            <li data-name="depot" class="layui-nav-item">
              <a href="javascript:;" lay-tips="库存" lay-direction="2">
                <i class="layui-icon layui-icon-search"></i>
                <cite>库存管理</cite>
              </a>
              <dl class="layui-nav-child">
                <dd data-name="stock"><a lay-href="/depot/shop_depot/list.html">店铺库存</a></dd>
                <dd data-name="purchase"><a lay-href="/depot/purchase/list.html">进货单</a></dd>
                <dd data-name="sell">
                  <a lay-href="/depot/depot_pre_select/list.html">预选管理</a>
                </dd>

                <dd data-name="sell">
                  <a lay-href="/depot/shop_differ/list.html">进货差异单</a>
                </dd>
                <dd data-name="sell">
                  <a lay-href="/depot/shop_return/list.html">退货单</a>
                </dd>
                <dd data-name="sell">
                  <a lay-href="/depot/shop_adjust/list.html">调整单</a>
                </dd>
  
                <dd data-name="sell">
                  <a lay-href="/depot/shop_transfer/list.html">调拨单</a>
                </dd>
                
                <dd data-name="sell">
                  <a lay-href="/depot/shop_depot_change/list.html">库存变动记录</a>
                </dd>

              </dl>

            <li data-name="system" class="layui-nav-item">
              <a href="javascript:;" lay-tips="权限管理" lay-direction="2">
                <i class="layui-icon layui-icon-auz"></i>
                <cite>权限管理</cite>
              </a>
              <dl class="layui-nav-child">
                <dd data-name="role"><a lay-href="/power/role/list.html">角色设置</a></dd>
                <dd data-name="staff"><a lay-href="/power/staff/list.html">员工账号</a></dd>
              </dl>
            </li>
            
            <li data-name="home" class="layui-nav-item">
              <a href="javascript:;" lay-tips="员工管理" lay-direction="2">
                <i class="layui-icon layui-icon-user"></i>
                <cite>员工管理</cite>
              </a>
              <dl class="layui-nav-child">
                <!-- <dd><a lay-href="/analysis/sell/summary.html">会员分析</a></dd> -->
                <dd><a lay-href="/staff/staff/list.html">员工列表</a></dd>
                <dd><a lay-href="/staff/shop_auth_role/list.html">权限角色</a></dd>
                <dd data-name="form">
                  <a href="javascript:;">会员消费</a>
                  <dl class="layui-nav-child">
                    <dd><a lay-href="/bi/user/o2o_consum_order">实体店消费</a></dd>
                  </dl>
                </dd>
              </dl>
            </li>

            <li data-name="home" class="layui-nav-item">
              <a href="javascript:;" lay-tips="BI分析" lay-direction="2">
                <i class="layui-icon layui-icon-fonts-strong"></i>
                <cite>BI分析</cite>
              </a>
              <dl class="layui-nav-child">
                <!-- <dd><a lay-href="/analysis/sell/summary.html">会员分析</a></dd> -->
                <dd><a lay-href="/bi/consum/o2o_month.html">销售业绩</a></dd>
                <dd><a lay-href="/bi/consum/category_sales.html">分类销售</a></dd>
                <dd data-name="form">
                  <a href="javascript:;">会员消费</a>
                  <dl class="layui-nav-child">
                    <dd><a lay-href="/bi/user/o2o_consum_order">实体店消费</a></dd>
                  </dl>
                </dd>
              </dl>
            </li>

          </ul>
=======
                <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
                    <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
                </li>
            </ul>
>>>>>>> .r376
        </div>
        <!-- 侧边菜单 -->
        <div class="layui-side layui-side-menu">
            <div class="layui-side-scroll">
                <div class="layui-logo" lay-href="base.html">
                    <span>25BOY新零售</span>
                </div>
                <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
                    <!-- <?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): if( count($menu)==0 ) : echo "" ;else: foreach($menu as $k=>$v): ?>
                        <li data-name="<?php echo $k; ?>" class="layui-nav-item">
                            <a href="javascript:void(0);" lay-tips="<?php echo $v['title']; ?>" lay-direction="2">
                                <i class="layui-icon <?php echo $v['icon']; ?>"></i>
                                <cite><?php echo $v['title']; ?></cite>
                            </a>
                            <dl class="layui-nav-child">
                                <?php if(is_array($v['children']) || $v['children'] instanceof \think\Collection || $v['children'] instanceof \think\Paginator): if( count($v['children'])==0 ) : echo "" ;else: foreach($v['children'] as $kk=>$vv): ?>
                                    <dd><a lay-href="<?php echo $vv['link']; ?>"><?php echo $vv['title']; ?></a></dd>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </dl>
                        </li>
                    <?php endforeach; endif; else: echo "" ;endif; ?> -->
                                        <li data-name="checkout" class="layui-nav-item">
                        <a lay-href="/checkout/index" lay-tips="收银" lay-direction="2">
                            <i class="layui-icon layui-icon-rmb"></i>
                            <cite>收银台</cite>
                        </a>
                    </li>
                    <li data-name="orders" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="订单" lay-direction="2">
                            <i class="layui-icon layui-icon-list"></i>
                            <cite>订单管理</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="orders"><a lay-href="/order/index/list.html">订单列表</a></dd>
                            <dd data-name="refund"><a lay-href="/order/refund/list.html">退换货单</a></dd>
                            <dd data-name="recharge"><a lay-href="/order/recharge/list.html">会员充值</a></dd>
                        </dl>
                    </li>
                    <li data-name="depot" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="库存" lay-direction="2">
                            <i class="layui-icon layui-icon-search"></i>
                            <cite>库存管理</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="stock"><a lay-href="/depot/shop_depot/list.html">店铺库存</a></dd>
                            <dd data-name="purchase"><a lay-href="/depot/purchase/list.html">进货单</a></dd>
                            <dd data-name="sell">
                                <a lay-href="/depot/depot_pre_select/list.html">预选管理</a>
                            </dd>
                            <dd data-name="sell">
                                <a lay-href="/depot/shop_differ/list.html">进货差异单</a>
                            </dd>
                            <dd data-name="sell">
                                <a lay-href="/depot/shop_return/list.html">退货单</a>
                            </dd>
                            <dd data-name="sell">
                                <a lay-href="/depot/shop_adjust/list.html">调整单</a>
                            </dd>
                            <dd data-name="sell">
                                <a lay-href="/depot/shop_transfer/list.html">调拨单</a>
                            </dd>
                            <dd data-name="sell">
                                <a lay-href="/depot/shop_depot_change/list.html">库存变动记录</a>
                            </dd>
                        </dl>
                    <li data-name="system" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="权限管理" lay-direction="2">
                            <i class="layui-icon layui-icon-auz"></i>
                            <cite>权限管理</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="power_role"><a lay-href="/power/power_role/page_list.html">角色设置</a></dd>
                        </dl>
                    </li>
                    <li data-name="home" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="员工管理" lay-direction="2">
                            <i class="layui-icon layui-icon-user"></i>
                            <cite>员工管理</cite>
                        </a>
                        <dl class="layui-nav-child">
                             <dd><a lay-href="/analysis/sell/summary.html">会员分析</a></dd>
                    <dd><a lay-href="/staff/staff/list.html">员工列表</a></dd>
                    <dd data-name="form">
                        <a href="javascript:;">会员消费</a>
                        <dl class="layui-nav-child">
                            <dd><a lay-href="/bi/user/o2o_consum_order">实体店消费</a></dd>
                        </dl>
                    </dd>
                    </dl>
                    </li>
                    <li data-name="home" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="BI分析" lay-direction="2">
                            <i class="layui-icon layui-icon-fonts-strong"></i>
                            <cite>BI分析</cite>
                        </a>
                        <dl class="layui-nav-child">
                             <dd><a lay-href="/analysis/sell/summary.html">会员分析</a></dd> 
                            <dd><a lay-href="/bi/consum/o2o_month.html">销售业绩</a></dd>
                            <dd><a lay-href="/bi/consum/category_sales.html">分类销售</a></dd>
                            <dd data-name="form">
                                <a href="javascript:;">会员消费</a>
                                <dl class="layui-nav-child">
                                    <dd><a lay-href="/bi/user/o2o_consum_order">实体店消费</a></dd>
                                </dl>
                            </dd>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>
        <!-- 页面标签 -->
        <div class="layadmin-pagetabs" id="LAY_app_tabs">
            <div class="layui-icon layadmin-tabs-control layui-icon-prev" layadmin-event="leftPage"></div>
            <div class="layui-icon layadmin-tabs-control layui-icon-next" layadmin-event="rightPage"></div>
            <div class="layui-icon layadmin-tabs-control layui-icon-down">
                <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
                    <li class="layui-nav-item" lay-unselect>
                        <a href="javascript:;"></a>
                        <dl class="layui-nav-child layui-anim-fadein">
                            <dd layadmin-event="closeThisTabs"><a href="javascript:;">关闭当前标签页</a></dd>
                            <dd layadmin-event="closeOtherTabs"><a href="javascript:;">关闭其它标签页</a></dd>
                            <dd layadmin-event="closeAllTabs"><a href="javascript:;">关闭全部标签页</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
            <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs">
                <ul class="layui-tab-title" id="LAY_app_tabsheader">
                    <li lay-id="home/console.html" lay-attr="home/console.html" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>
                </ul>
            </div>
        </div>
        <!-- 主体内容 -->
        <div class="layui-body" id="LAY_app_body">
            <div class="layadmin-tabsbody-item layui-show p-1">
                <iframe src="" frameborder="0" class="layadmin-iframe"></iframe>
            </div>
        </div>
        <!-- 辅助元素，一般用于移动设备下遮罩 -->
        <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>
</div>
<script src="/static/layui/layui.js"></script>
<script>
layui.config({
    base: '/static/' //静态资源所在路径
}).extend({
    index: 'lib/index' //主入口模块
}).use('index');

// 返回上一页
$('#return').on('click', function() {
    history.go(-1);
})

$('#logout').on('click', function() {

    console.info('logout');

    $.get('/index/auth/logout', function(res) {
        if (res.code == 0) {
            if (res.data && (undefined != res.data['path'])) {
                location.href = '/index/index/' + res.data['path'];
            } else {
                location.href = '/index/index/login';
            }
        }
    })

})

// 转跳到登录页面
function goLogin() {
    let loginPage = localStorage.getItem('loginPage');
    console.info(loginPage);
}
</script>
<script type="text/javascript" src="/static/js/less.min.js"></script>
</body>
</html>