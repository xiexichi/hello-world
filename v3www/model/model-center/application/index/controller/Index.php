<?php
namespace app\index\controller;

use app\common\controller\Common;

class Index extends Common
{
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="ad_bd568ce7058a1091"></think>';
    }


    /**
     * [chanExcel 蝉需要的excel]
     * @return [type] [description]
     */
    public function chanExcel(){
    	$model = new \app\index\model\Index();

    	// $model->chanExcel($this->helper->readExcel('erp_add.xlsx'));
    }


    /**
     * [sales 查找销量]
     * @return [type] [description]
     */
    public function sales(){
    	// 线下
    	// $data = $this->helper->readExcel('offline.xlsx');

    	// 线上
    	$data = $this->helper->readExcel('online.xlsx');

    	// $model = new \app\index\model\Index();
    	
    	// pe($data);

    	$skuData = [];

    	// 商品
    	foreach ($data as $k => $v) {

    		// 分颜色
    		$first = substr($v[2], 0, 1);

    		// 码数
    		$size = substr($v[2], 1, strlen($v[2]) - 1);

    		if (empty($skuData[$v[1]])) {
				$skuData[$v[1]] = [];
    		}

    		// 添加字母颜色
    		if (empty($skuData[$v[1]][$first])) {
    			$skuData[$v[1]][$first] = [];
    		}

    		// 添加到商品的颜色中
    		foreach (array_keys($skuData[$v[1]]) as $kk => $vv) {
    			if ($first == $vv) {
    				$skuData[$v[1]][$vv][$size] = $v;
    				break;
    			}
    		}

    	}


    	// 码数排序
    	foreach ($skuData as $k => $v) {
    		foreach ($v as $kk => $vv) {
    			$skuData[$k][$kk] = $this->sizeKeySort($vv);
    		}
    	}

    	// 最终输出数据
    	$bodyData = [];

    	// 重新组合输出数据
    	foreach ($skuData as $k => $v) {

    		$j = 0;

    		// 第一个元素组合商品信息
    		foreach ($v as $kk => $vv) {

    			// 第一个元素
    			$first = $vv[0];

    			// 颜色和尺码
    			$cas = explode(' ', $first[4]);
    			if (count($cas) < 2) {
					// pe($colorAndSize);
					preg_match('/[a-zA-Z]/', $v3[4], $s);
					if ($s) {
						$sizeLetterPos = strpos($v3[4], $s[0]);
						// 颜色
						$color = substr($v3[4], 0, $sizeLetterPos+1);
						// 尺码
						$size = str_replace($color, '', $v3[4]);
					}
				} else {
					$color = $cas[0];
					$size = $cas[1];
				}

				if ($j == 0) {
					// 组合商品数据
					$bodyData[] = [
						'category' => $first[6],
						'sku' => $first[1],
						'price' => $first[7],
						'color' => $color,
						'sum' => "",
						'size' => "尺码",
						'produce' => "生产",
						'sales' => "销量",
						'ratio' => "产销比",
						'date' => "",
					];
				} else {
					// 组合商品数据
					$bodyData[] = [
						'category' => "",
						'sku' => "",
						'price' => "",
						'color' => $color,
						'sum' => "",
						'size' => "尺码",
						'produce' => "生产",
						'sales' => "销量",
						'ratio' => "产销比",
						'date' => "",
					];
				}

				// 累加 
				$j++;

				// 合计数据
				$summary = [
					'category' => "",
					'sku' => "",
					'price' => "",
					'color' => "",
					'sum' => "合计",
					'size' => 0,
					'produce' => 0,
					'sales' => 0,
					'ratio' => 0,
					'date' => "",
				];
				// 空行
				$empty = [
					'category' => "",
					'sku' => "",
					'price' => "",
					'color' => "",
					'sum' => "",
					'size' => "",
					'produce' => "",
					'sales' => "",
					'ratio' => "",
					'date' => "",
				];

				$i = 0;
    			foreach ($vv as $k3 => $v3) {
    				// 颜色和尺码
    				$colorAndSize = explode(' ', $v3[4]);

    				if (count($colorAndSize) < 2) {
    					// pe($colorAndSize);
    					preg_match('/[a-zA-Z]/', $v3[4], $s);
    					if ($s) {
    						$sizeLetterPos = strpos($v3[4], $s[0]);
    						// 颜色
    						$color = substr($v3[4], 0, $sizeLetterPos+1);
    						// 尺码
    						$size = str_replace($color, '', $v3[4]);
    					}
    				} else {
    					$color = $colorAndSize[0];
    					$size = $colorAndSize[1];
    				}

    				// 中间元素
    				$ratio = (round($v3[5] / $v3[3], 2) * 100);
					// 组合数据
					$bodyData[] = [
						'category' => "",
						'sku' => "",
						'price' => "",
						'color' => "",
						'sum' => "",
						'size' => $size,
						'produce' => $v3[3],
						'sales' => $v3[5],
						'ratio' => $ratio . '%' ,
						'date' => "",
					];

					// 添加合计数据
					$summary['size'] += 1;
					$summary['produce'] += $v3[3];
					$summary['sales'] += $v3[5];
					$summary['ratio'] += $ratio;

    				// 最后一个元素
    				$i++;

    				if ($i == count($vv)) {

    					$summary['size'] .= '个码';
    					// 计算总产销比
    					$summary['ratio'] = (round(($summary['sales'] / $summary['produce']), 2) * 100) . '%';

    					// 追加一个合计
    					$bodyData[] = $summary;
    					// 追加一行空行
    					$bodyData[] = $empty;
    				}

    			}
    		}
    	}


    	// pe($bodyData);

    	$filename = '线上新';

    	$excelTitle = [
    		['title' => '分类'],
    		['title' => '货号'],
    		['title' => '价格'],
    		['title' => '颜色'],
    		['title' => '各尺码累计做货情况'],					
    		['title' => '各尺码累计做货情况'],					
    		['title' => '各尺码累计做货情况'],					
    		['title' => '各尺码累计做货情况'],					
    		['title' => '各尺码累计做货情况'],					
    		['title' => '数据截止12.20'],
    	];

    	$this->helper->createExcel($filename, $excelTitle, $bodyData);
    	exit;


    }



    protected function sizeKeySort($arr){
        /**
         * 
         */
        
        $sort = [
            '7(36/37)' => '7',
            '7（36/37）' => '7',
            '8(38/39)' => '8',
            '8（38/39）' => '8',
            '9(40/41)' => '9',
            '9（40/41）' => '9',
            '10(42/43)' => '10',
            '10（42/43）' => '10',
            '11(44/45)' => '11',
            '11（44/45）' => '11',
            'HK#19' => '1',
            '110' => '1',
            '120' => '2',
            '130' => '3',
            '140' => '4',
            '150' => '5',
            '180' => '8',
            '28' => '28',
            '30' => '30',
            '31' => '31',
            '32' => '32',
            '34' => '34',
            '36' => '36',
            '38' => '38',
            '40' => '40',
            '42' => '42',
            '44' => '44',
            'F' => '1',
            'R' => '2',
            'S' => '1',
            'M' => '2',
            'L' => '3',
            'XL' => '4',
            'XS' => '5',
            'XXL' => '6',
            '3XL' => '7',
            '4XL' => '8',
            '5XL' => '9',
            '6XL' => '10',
            '7XL' => '11',
            '均码' => '2',
        ];

        $sortArr = array();

        $newArr = [];
        foreach ($arr as $k => $v) {

            if (!isset($sort[$k])) {
                if (is_numeric($k)) {
                    $newArr[$k] = $v;
                } else {
                    $newArr[count($newArr)] = $v;
                }
                continue;
            }

            $newArr[$sort[$k]] = $v;
        }

        ksort($newArr);
        return array_values($newArr);
    }


}
