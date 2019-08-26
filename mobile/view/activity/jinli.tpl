<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
</head>

<style type="text/css">
	
	.winning-info{
		position: absolute;
		top: 28%;
		text-align: center;
		width: 100%;
		color: red;
		/*font-size: 2.5rem;*/
	}

	.tip{
		position: absolute;
		top: 26%;
		width: 100%;
		text-align: right;
		font-size: 0.6rem;
		font-weight: bold;
	}

	.tip-font{
		text-align: center;
		position: absolute;
		right: 15px;
	}

	.num{
		font-size: 1rem;
		/*color: red;*/
		text-align: center;
		border-radius: 15px;
		padding: 5px;
		padding-top: 6px; 
		border: 1.8px solid red;
		font-weight: bold;
		color: #8e8a8a;
	}
</style>

<div style="width: 96%;position: absolute;">

	<div class="tip">
		<div class="tip-font">
			长按上面二维码<br/>
			点击识别小程序码<br/>
			查看属于您的抽奖码
		</div>
	</div>
	
	<div class="winning-info">
		<h3 style="margin-bottom:12px;">获得HEA锦鲤大奖的幸运码</h3>
		<!-- <p class="num">9758321935</p> -->

		<input type="" name="" value="{$code}" class="num" readonly="true">
	</div>

	<img style="width: 100%;" src="https://img.25miao.com/1808/a0eb50b6876f01ed1f7b80811eca5c58.jpg">
	
</div>