<?php
class kuaidi {

	 private  $_table_kuaidi = 'kuaidi';

    public function __construct(){}

    // 快递公司代码
    public function company_code($com){
        switch (strtolower($com)) {
            case 'sf':
                $company  = 'shunfeng';
                break;
            case 'yt':
            case 'yto':
                $company  = 'yuantong';
                break;
            case 'zt':
            case 'zto':
                $company  = 'zhongtong';
                break;
            default:
                $company = $com;
                break;
        }

        return $company;
    }


    // 快递公司名称
    public function company_name($com){
      $company = self::company_code($com);
      switch ($company) {
        case 'shunfeng':
          $company_name = '顺丰速运';
          break;
        case 'yuantong':
          $company_name = '圆通快递';
          break;
        case 'zhongtong':
          $company_name = '中通快递';
          break;
        default:
          $company_name = strtoupper($company);
          break;
      }

      return $company_name;
    }


    /**
     * 快递100接口 订阅
     * @param  string  $com    快递公司代码
     * @param  string  $nu     快递单号
     * @param  string  $type   返回数据类型  json/xml
     * @return bool
    */
    public function post_kuaidi($com, $nu, $to, $type='json'){

        $company = self::company_code($com);
        if( $company == 'yuantong' ){
          $from = '广东广州';
        }else{
          $from = '广东佛山';
        }
        $param = array(
            'company' => strtolower($company),
            'number'  => trim($nu),
            'from'    => $from,
            'to'      => $to,
            'key'  => $SITECONFIGER['kuaidi']['kuaidi100_appkey'],
            'parameters' => array(
              'callbackurl'    => 'http://www.25boy.cn/kuaidi/callback.html',
              'salt'    => 'MiaO25BoY',
            ),
        );

        $post_data = array();
        $post_data["schema"] = $type;
        //callbackurl请参考callback.php实现，key经常会变，请与快递100联系获取最新key
        $post_data["param"] = json_encode($param);

        $url='http://www.kuaidi100.com/poll';
        $o=""; 
        foreach ($post_data as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";   //默认UTF-8编码格式
        }
        $post_data=substr($o,0,-1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $result = curl_exec($ch);   //返回提交结果，格式与指定的格式一致（result=true代表成功）

        return $result;

    }


    /**
     * 查询快递信息
     * @param  string  $ship_sn     快递单号
     * @return string
    */
    public function get_kuaidi($ship_sn, $com=null){
      if(empty($ship_sn)) return false;
        global $DB;
        $Fileds = "*";
        $Condition = "where `nu`='".trim($ship_sn)."'";
        if(!empty($com)){
            $com = trim($com);
            $company = self::company_code($com);
            $Condition .= " AND `com`='".$company."' ";
        }
        $rs = $DB->GetRs($this->_table_kuaidi,$Fileds,$Condition);
        return $rs;
    }


    /**
     * 更新物流信息
     * @param  array  $data     物流信息
     * @return string
    */
    public function update_kuaidi($data=array()){
        global $DB;
        if(!empty($data['nu'])){
          $data['nu'] = trim($data['nu']);
        }
        if(!empty($data['com'])){
          $data['com'] = trim($data['com']);
        }
        $row = self::get_kuaidi($data['nu'],$data['com']);
        if(isset($row['id'])){
            $data['edit_time'] = time();
            $Condition = " `nu`='".$data['nu']."' AND `com`='".$data['com']."'";
            $result = $DB->Set($this->_table_kuaidi, $data, $Condition);
        }else{
            $data['add_time'] = time();
            $result = $DB->Add($this->_table_kuaidi,$data);
        }

        return $result;
    }


}