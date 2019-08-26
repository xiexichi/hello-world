<?php

/**
 * 公共助手类
 */
namespace app\common\controller;

class Helper{

	protected $request; // 请求对象

	protected $error; // 错误信息

	public function __construct($request){
		$this->request = $request;
	}

	/**
	 * [checkInputParams 验证输入参数]
	 * @return [type] [description]
	 */
	public function checkInputParams(array $params,$type = 'param'){
		$inputParam = $this->request->$type();
		
		$resultParams = [];	
		foreach ($params as $k => $v) {
			if (!isset($inputParam[$v])) {
				$this->error = '缺失'.$v.'参数';
				return false;
			}

			$resultParams[$v] = $inputParam[$v];
		}
		return $resultParams;
	}


    /**
     * [uploadFile 单文件上传]
     * @param  [type] $filename  [文件名称]
     * @param  [type] $save_path [保存路径]
     * @return [type]            [true/false]
     */
    public function uploadFile($filename , $save_path, $size = 3145728,$ext = 'jpg,jpeg,png,gif'){
    	$file = $this->request->file($filename);
    	
        // 创建文件储存目录
        if (!file_exists($save_path)) {
            // 创建目录
            mkdir($save_path, 777, true);
        }

    	if ($file) {

    		$info = $file->validate(['size'=>$size,'ext'=>$ext])->move($save_path);

    		if ($info) {
    			return str_replace('\\','/', $save_path.'/'.$info->getSaveName());
    		} 

    		// 设置错误信息
    		$this->error = $file->getError();
    	} else {
    		$this->error = '文件不存在';
    	}

    	return false;
    }


	/**
	 * [getError 获取错误信息]
	 * @return [type] [description]
	 */
	public function getError(){
		return $this->error;
	}

    /**
     * [readExcel 读取Excel]
     * @param  [type] $filePath [文件路径]
     * @return [type]           [description]
     */
    public function readExcel($filePath){
        // 引入excel插件
        vendor('phpExcel.PHPExcel');
        // Create new PHPExcel object
        $PHPExcel = new \PHPExcel();

        /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if(!$PHPReader->canRead($filePath)){
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filePath)){
                echo 'no Excel';
                return ;
            }
        }
         
        $PHPExcel = $PHPReader->load($filePath);
        /**读取excel文件中的第一个工作表*/
        $currentSheet = $PHPExcel->getSheet(0);
        /**取得最大的列号*/
        $allColumn = $currentSheet->getHighestColumn();
        /**取得一共有多少行*/
        $allRow = $currentSheet->getHighestRow();

        // 返回数组
        $result = [];

        /**从第二行开始输出，因为excel表中第一行为列名*/
        for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
            $item = [];
            /**从第A列开始输出*/
            for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
                $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/

                $item[] = $val;
            }

            $result[] = $item;
        }

        return $result;
    }


	/**
	 * [createExcel 创建Excel]
	 * @param  [type] $filename   [Excel文件名称]
	 * @param  [type] $excelTitle [表格标题]
	 * @param  [type] $data       [表格数据]
	 * @param  [type] $mergeKey   [合并字段]
 	 * @param  [type] $headers    [头部行]
	 */
	public function createExcel($filename, $excelTitle, $data, $mergeKey = NULL,$headers = array()){
		
		// 设置永不超时
		set_time_limit(0);

        // 引入excel插件
        // require_once(dirname(BASEPATH).'/plugin/PHPExcel.php');
        
        vendor('phpExcel.PHPExcel');

        // Create new PHPExcel object
        $PHPExcel = new \PHPExcel();

        // 设置当前的sheet
        $PHPExcel->setActiveSheetIndex(0);

		// 创建列字母
        $alphabet = range('A','Z');


        // 设置头部行
        foreach ($headers as $k => $v) {
        	// 按照数组$v的长度，获取列字母，组合行与列的位置
    		$col = $alphabet[0] . ($k+1);
    		// 左对齐
            $PHPExcel->getActiveSheet()->setCellValue($col, $v)->getStyle($col)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            // 设置字体
            $PHPExcel->getActiveSheet()->getStyle($col)->getFont()->setName('宋体');
        }

        
        // 1.设置标题
        foreach ($excelTitle as $k => $v) {

        	if (empty($headers)) {
        		$col = $alphabet[$k] . '1';
        	} else {
        		$col = $alphabet[$k] . (2 + count($headers));
        	}

    	    // 设置单元格数据
    		$PHPExcel->getActiveSheet()->setCellValue($col, $v['title'])->getStyle($col)->getFont()->setBold(true);

    		// 如果有宽度
    		if (isset($v['width'])) {
    			$PHPExcel->getActiveSheet()->getColumnDimension($alphabet[$k])->setWidth($v['width']);
    		}	

        }


        if (empty($headers)) {
	        // 行号
    		$rowNum = 2;
    	} else {
	        $rowNum = 3 + count($headers);
    	}

        // 2.设置数据
        foreach ($data as $k => $v) {

        	// 列字母下标
        	$i = 0;

        	foreach ($v as $kk => $vv) {
        		// 按照数组$v的长度，获取列字母，组合行与列的位置
        		$col = $alphabet[$i] . $rowNum;

                // 判断是否合并单元格
	        	if ( $mergeKey && $k > 0 && ($v[$mergeKey] == $data[$k-1][$mergeKey]) && !empty($excelTitle[$i]['merge'])) {
	        		// 上一个单元格
	        		$prevCol = $alphabet[$i] . ($rowNum - 1);

	        		// pe("{$prevCol}:{$col}");

	        		// 合并单元格
	        		$PHPExcel->getActiveSheet()->mergeCells("{$prevCol}:{$col}");
	        		// 垂直居中
                    $PHPExcel->getActiveSheet()->getStyle("{$prevCol}:{$col}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
	        		// 左对齐
                    $PHPExcel->getActiveSheet()->getStyle("{$prevCol}:{$col}")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
                    // 设置字体
                    $PHPExcel->getActiveSheet()->getStyle("{$prevCol}:{$col}")->getFont()->setName('宋体');
	        	} else {

		        	// 左对齐
	                $PHPExcel->getActiveSheet()->setCellValue($col, $vv)->getStyle($col)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	                // 设置字体
	                $PHPExcel->getActiveSheet()->getStyle($col)->getFont()->setName('宋体');	        		
	        	}



        		// 下移
        		$i++;
        	}

        	// 行号下移
        	$rowNum++;

        }

        // pe($data);

        // 3.输出Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    	header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
    	header('Cache-Control: max-age=0');
    	// If you're serving to IE 9, then the following may be needed
    	header('Cache-Control: max-age=1');

    	// If you're serving to IE over SSL, then the following may be needed
    	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    	header ('Pragma: public'); // HTTP/1.0

    	$objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
    	$objWriter->save('php://output');
	}



    /**
     * [createFieldExcel 创建Excel]
     * @param  [type] $filename   [Excel文件名称]
     * @param  [type] $excelTitle [表格标题]
     * @param  [type] $data       [表格数据]
     * @param  [type] $mergeKey   [合并字段]
     * @param  [type] $headers    [头部行]
     */
    public function createFieldExcel($filename, $excelTitle, $data, $mergeKey = NULL,$headers = array()){
        
        // 设置永不超时
        set_time_limit(0);

        // 引入excel插件
        // require_once(dirname(BASEPATH).'/plugin/PHPExcel.php');
        
        vendor('phpExcel.PHPExcel');

        // Create new PHPExcel object
        $PHPExcel = new \PHPExcel();

        // 设置当前的sheet
        $PHPExcel->setActiveSheetIndex(0);

        // 创建列字母
        $alphabet = range('A','Z');


        // 设置头部行
        foreach ($headers as $k => $v) {
            // 按照数组$v的长度，获取列字母，组合行与列的位置
            $col = $alphabet[0] . ($k+1);
            // 左对齐
            $PHPExcel->getActiveSheet()->setCellValue($col, $v)->getStyle($col)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            // 设置字体
            $PHPExcel->getActiveSheet()->getStyle($col)->getFont()->setName('宋体');
        }

        
        // 1.设置标题
        foreach ($excelTitle as $k => $v) {

            if (empty($headers)) {
                $col = $alphabet[$k] . '1';
            } else {
                $col = $alphabet[$k] . (2 + count($headers));
            }

            // 设置单元格数据
            $PHPExcel->getActiveSheet()->setCellValue($col, $v['title'])->getStyle($col)->getFont()->setBold(true);

            // 如果有宽度
            if (isset($v['width'])) {
                $PHPExcel->getActiveSheet()->getColumnDimension($alphabet[$k])->setWidth($v['width']);
            }   

        }


        if (empty($headers)) {
            // 行号
            $rowNum = 2;
        } else {
            $rowNum = 3 + count($headers);
        }

        // 2.设置数据
        foreach ($data as $k => $v) {

            // 列字母下标
            $i = 0;

            foreach ($excelTitle as $kk => $vv) {
                // 按照数组$v的长度，获取列字母，组合行与列的位置
                $col = $alphabet[$i] . $rowNum;

                // 判断是否合并单元格
                if ( $mergeKey && $k > 0 && ($v[$mergeKey] == $data[$k-1][$mergeKey]) && !empty($excelTitle[$i]['merge'])) {
                    // 上一个单元格
                    $prevCol = $alphabet[$i] . ($rowNum - 1);

                    // pe("{$prevCol}:{$col}");

                    // 合并单元格
                    $PHPExcel->getActiveSheet()->mergeCells("{$prevCol}:{$col}");
                    // 垂直居中
                    $PHPExcel->getActiveSheet()->getStyle("{$prevCol}:{$col}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    // 左对齐
                    $PHPExcel->getActiveSheet()->getStyle("{$prevCol}:{$col}")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
                    // 设置字体
                    $PHPExcel->getActiveSheet()->getStyle("{$prevCol}:{$col}")->getFont()->setName('宋体');
                } else {

                    // 单元格数据
                    $d = isset($v[$vv['field']]) ? $v[$vv['field']] : '';

                    // 左对齐
                    $PHPExcel->getActiveSheet()->setCellValue($col, $d)->getStyle($col)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    // 设置字体
                    $PHPExcel->getActiveSheet()->getStyle($col)->getFont()->setName('宋体');                  
                }
                // 下移
                $i++;
            }

            // 行号下移
            $rowNum++;

        }
        // pe($data);
        // 3.输出Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }



    /**
     * [echoExecl 输出Execl数据]
     * @param  [array] $title [输出的标题]
     * @param  [array] $data  [输出的数据:关联数组]
     * @param  [String] $execl_name  [文件名]
     */
    public function echoExecl($title , $data ,$execl_name){

        // 在这里将数据已json格式保存到文件中，让GO生成excel文件，并输出下载
        // 创建文件夹
        $dirName = 'excels/'.date('Y-m-d').'/'.time();
        mkdir($dirName, 0755, true);

        file_put_contents($dirName.'/title.txt', json_encode($title));
        file_put_contents($dirName.'/data.txt', json_encode($data));

        // key
        $keys = array_keys($data[0]);

        $alps = range('A', 'Z');
        $keyValues = [];
        foreach ($keys as $k => $v) {
            $keyValues[$v] = $alps[$k];
        }

        // 字段名称与excel的列位置
        file_put_contents($dirName.'/keyValues.txt', json_encode($keyValues));

        pe('create success');
    }

    /**
     * [assocToNumber 将关联数组转换为数值数组]
     * @param  [array] $assoc [关联数组]
     * @return [array]        [索引数组]
     */
    public function assocToNumber($assoc){
        $int_array = array();
        for($i=0;$i<count($assoc);$i++){
            $int_array[$i] = array();
            $j = 0;
            foreach ($assoc[$i] as $k => $v) {
                $int_array[$i][$j] = $v;
                $j++;
            }
        }
        return $int_array;
    }




}