<?php
header('Content-Type:text/html; charset=utf-8');
basename($_SERVER['PHP_SELF'])=='mysql.php'&&header('Location:http://'.$_SERVER['HTTP_HOST']); //禁止直接访问本页
/**
※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※
【文件名】: mysql.php
【作  用】: mysql数据库操作类
【作  者】: Riyan
【版  本】: version 2.0
【修改日期】: 2010/02/11
※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※※
**/

class mysql{
    private $host;         // 数据库主机
    private $user;         // 数据库用户名
    private $pass;         // 数据库密码
    private $data;         // 数据库名
    private $conn;         // 数据库连接标识
    private $sql;          // sql语句
    private $code;         // 数据库编码，GBK,UTF8,GB2312
    private $result;       // 执行query命令的结果数据集

    private $errLog=true;  // 是否开启错误日志,默认开启
    private $showErr=true; // 显示所有错误,具有安全隐患,默认开启

    private $pageNo=1;     // 当前页
    private $pageAll=1;    // 总页数
    private $rsAll=0;      // 总记录
    private $pageSize=10;  // 每页显示记录条数

    private $pageBar = "";

    private $mysqli_conn;

    private $connect_type = "mysql";
    private $count = 0;

    // 是否开启事务
    private $transBegin = FALSE;

    /******************************************************************
    -- 函数名：__construct($host,$user,$pass,$data,$code,$conn)
    -- 作  用：构造函数
    -- 参  数：$host 数据库主机地址(必填)
              $user 数据库用户名(必填)
              $pass 数据库密码(必填)
              $data 数据库名(必填)
              $conn 数据库连接标识(必填)
              $code 数据库编码(必填)
    -- 返回值：无 
    -- 实  例：无
    *******************************************************************/
    public function __construct($code='utf8',$conn='conn'){
        global $SITECONFIGER; 
        $codekey = constant("INPW");
        // var_dump(Base::enccode($SITECONFIGER["dbtest"]["user"], 'DECODE', $codekey, 0));
        // $this->host=$SITECONFIGER["db"]["host"];
        // $this->user="root";
        // $this->pass="123456";
        // $this->data="miao";

        // 线上使用
        $this->host=@Base::enccode($SITECONFIGER["db"]["host"], 'DECODE', $codekey, 0);
        $this->user=@Base::enccode($SITECONFIGER["db"]["user"], 'DECODE', $codekey, 0);
        $this->pass=@Base::enccode($SITECONFIGER["db"]["password"], 'DECODE', $codekey, 0);
        $this->data=@Base::enccode($SITECONFIGER["db"]["name"], 'DECODE', $codekey, 0);

        $this->conn=$conn;
        $this->code=$code;
        $this->connect();
    }

    public function __get($name){return $this->$name;}

    public function __set($name,$value){$this->$name=$value;}

    // 数据库连接
    private function connect(){
        if($this->connect_type=="mysql"){
            if ($this->conn=='pconn') $this->conn=mysql_pconnect($this->host,$this->user,$this->pass); // 永久链接
            else $this->conn=mysql_connect($this->host,$this->user,$this->pass); // 临时链接
        }else{
            $this->conn=new mysqli($this->host,$this->user,$this->pass);
        }
        if (!$this->conn) $this->show_error('无法连接服务器');
        $this->select_db($this->data);
        $this->query('SET NAMES '.$this->code);
        $this->query("SET CHARACTER_SET_CLIENT='{$this->code}'"); 
        $this->query("SET CHARACTER_SET_RESULTS='{$this->code}'");
    }

    // 数据库选择
    public function select_db($data){
        if($this->connect_type=="mysql"){
            $result=mysql_select_db($data,$this->conn);
        }else{
            $result=$this->conn->select_db($data);
        }
        if (!$result) $this->show_error('无法连接数据库'.$data);
        return $result;
    }



    
    /******************************************************************
    -- 函数名：get_info($num)
    -- 作  用：取得 MySQL 服务器信息
    -- 参  数：$num 信息值(选填)
    -- 返回值：字符串
    -- 实  例：无
    *******************************************************************/
    public function get_info($num){
        switch ($num){
            case 1:
                return mysql_get_server_info(); // 取得 MySQL 服务器信息
                break;
            case 2:
                return mysql_get_host_info();   // 取得 MySQL 主机信息
                break;
            case 3:
                return mysql_get_proto_info();  // 取得 MySQL 协议信息
                break;
            default:
                return mysql_get_client_info(); // 取得 MySQL 客户端信息
        }
    }

    /******************************************************************
    -- 函数名：query($sql)
    -- 作  用：数据库执行语句，可执行查询添加修改删除等任何sql语句
    -- 参  数：$sql sql语句(必填)
    -- 返回值：布尔
    -- 实  例：无
    *******************************************************************/
    public function query($sql){
        if (empty($sql)) $this->show_error('SQL语句为空');
        $this->sql=preg_replace('/ {2,}/',' ',trim($sql));
        if($this->connect_type=="mysql"){
            $this->result=mysql_query($this->sql,$this->conn);
        }else{
            $this->result=mysqli_query($this->conn,$this->sql);
        }
        if (!$this->result) $this->show_error('SQL语句有误',true);
        return $this->result;
    }
    /******************************************************************
    -- 函数名：pd_query($sql)
    -- 作  用：数据库存储过程执行语句，可执行查询添加修改删除等任何sql语句
    -- 参  数：$pro_name 存储过程名称(必填)
    -- 参  数：$pro_params 存储过程参数
    -- 返回值：布尔
    -- 实  例：无
    *******************************************************************/
    public function pd_query($pro_name,$pro_params,$pagesize,$pagecurrent){
        global $pageSize;
        if (empty($pro_name)) $this->show_error('存储过程名为空');

        $this->mysqli_conn=new mysqli($this->host,$this->user,$this->pass,$this->data);
        if (mysqli_connect_errno()){  
            $this->show_error('无法连接数据库'.$data);  
        }  
        $this->mysqli_conn->query("SET NAMES UTF8");  


        $rows = array();
        $params = "";
        foreach ($pro_params as $key => $value) {
            $params .= is_string($value) ? "'".$value."'" : $value;
            $params .= ",";
        }
        $params = substr($params, 0,strlen($params)-1);
        
        if($this->mysqli_conn->real_query("call ".$pro_name."(".$params.")")){
            do{
                if($result = $this->mysqli_conn->store_result()){
                    while ($row = $result->fetch_assoc()){
                        array_push($rows, $row);
                    }
                    $result->close();
                }
            }while($this->mysqli_conn->next_result());
        }
        

        if(count($rows)>1){
            $this->count = $rows[count($rows)-1]["count"];
        }else{
            $this->count = 0;
        }
        array_pop($rows);

        $this->rsAll = $this->count;
        $pageSize = $pagesize;

        if (isset($_GET['page']) && intval($_GET['page'])){$this->pageNo=intval($_GET['page']);}

        if ($this->rsAll>0){
            $this->pageAll=ceil($this->rsAll/$pageSize);
            if ($this->pageNo<1){$this->pageNo=1;}
            if ($this->pageNo>$this->pageAll){$this->pageNo=$this->pageAll;}
        }
        $this->mysqli_conn->close();
        return $rows;
    }


    /******************************************************************
    -- 函数名：create_db($data)
    -- 作  用：创建添加新的数据库
    -- 参  数：$data 数据库名称(必填)
    -- 返回值：字符串
    -- 实  例：无
    *******************************************************************/
    public function create_database($data=''){$this->query("CREATE DATABASE {$data}");}

    // 查询服务器所有数据库
    public function show_database(){
        $this->query('SHOW DATABASES');
        $db=array();
        while ($row=$this->fetch_array()) $db[]=$row['Database'];
        return $db;
    }

    // 查询数据库下所有的表
    public function show_tables($data=''){
        if (!empty($data)) $db=' FROM '.$data;
        $this->query('SHOW TABLES'.$data);
        $tables=array();
        while ($row=$this->fetch_row()) $tables[]=$row[0];
        return $tables;
    }

    /******************************************************************
    -- 函数名：copy_tables($tb1,$tb2,$where)
    -- 作  用：复制表
    -- 参  数：$tb1 新表名(必填)
              $tb2 待复制表的表名(必填)
              $Condition 复制条件(选填)
    -- 返回值：布尔
    -- 实  例：无
    *******************************************************************/
    public function copy_tables($tb1,$tb2,$Condition=''){$this->query("SELECT * INTO `{$tb1}` FROM `{$tb2}` {$Condition}");}

    /******************************************************************
    -- 函数名：Get($Table,$Fileds,$Condition,$Rows)
    -- 作  用：查询数据
    -- 参  数：$Table 表名(必填)
              $Fileds 字段名，默认为所有(选填)
              $Condition 查询条件(选填)
              $Rows 待查询记录条数，为0表示不限制(选填)
    -- 返回值：布尔
    -- 实  例：$DB->Get('mydb','user,password','order by id desc',10)
    *******************************************************************/
    public function Get($Table,$Fileds='*',$Condition='',$Rows=0){
        if (!$Fileds) $Fileds='*';
        if ($Rows>0) $Condition.=" LIMIT 0,{$Rows}";
        $sql="SELECT {$Fileds} FROM `{$Table}` {$Condition}";
        return $this->query($sql);
        //$this->fetch_assoc();
    }

    // 只查询一条记录
    public function GetRs($Table,$Fileds='*',$Condition=''){
        if (!$Fileds) $Fileds='*';
        $this->query("SELECT {$Fileds} FROM `{$Table}` {$Condition} LIMIT 0,1");
        return $this->fetch_array();
    }

    /**
     * 2017-10-16 andy新增
     * [GetAll 查询所有数据]
     * @param [type]  $Table     [表名(必填)]
     * @param string  $Fileds    [字段名，默认为所有(选填)]
     * @param string  $Condition [查询条件(选填)]
     * @param integer $Rows      [待查询记录条数，为0表示不限制(选填)]
     */
    public function GetAll($Table,$Fileds='*',$Condition='',$Rows=0){
        $data = array();
        $Row = $this->Get($Table,$Fileds,$Condition,$Rows);
        $Row = $DB->result;

        $RowCount = $this->num_rows($Row);
        if($RowCount!=0){
            while($result = $this->fetch_assoc($Row)){
                $data[] = $result;
            }
        }
        return $data;
    }



    /******************************************************************
    -- 函数名：Add($Table,$Data)
    -- 作  用：添加数据
    -- 参  数：$Table 表名(必填)
              $Data 待添加数据,可以为数组(必填)
    -- 返回值：布尔
    -- 实  例：$DB->Add('mydb',array('user'=>'admin','password'=>'123456','age'=>'18') 数组类型
              $DB->Add('mydb','user=admin,password=123456,age=18') 字符串类型
    *******************************************************************/
    public function Add($Table,$Data){
        if (!is_array($Data)){
            $arr=explode(',',$Data);
            $Data=array();
            foreach ($arr as $val){
                list($key,$val)=explode('=',$val);
                if (!$val) $val='';
                
                $Data[$key]=$val;
                
            }
        }

        $Value = "";
        foreach ($Data as $key => $val) {
                $Value .= "'".$val."',";
        }
        $Value=substr($Value,0,strlen($Value)-1);
        //echo $Value;
        $Fileds='`'.implode('`,`',array_keys($Data)).'`';
        //$Value=implode(",",array_values($Data));

        return $this->query("INSERT INTO `{$Table}` ({$Fileds}) VALUES ({$Value})");


    } 

    /******************************************************************
    -- 函数名：Set($Table,$Data,$Condition,$unQuot)
    -- 作  用：更改数据
    -- 参  数：$Table 表名(必填)
              $Data 待更改数据,可以为数组(必填)
              $Condition 更改条件(选填)
              $unQuot 不需要加引号的字段，用于字段的加减运算等情况，多个字段用,分隔或者写入一个数组(选填)
    -- 返回值：布尔
    -- 实  例：$DB->Set('mydb',array('user'=>'admin','password'=>'123456','WHERE id=1') 数组类型
              $DB->Set('mydb',"user='admin',password='123456'",'WHERE id=1') 字符串类型
    *******************************************************************/
    public function Set($Table,$Data,$Condition='',$unQuot=''){
        if (is_array($Data)){
            if (!is_array($unQuot)) $unQuot=explode(',',$unQuot);
            foreach ($Data as $key=>$val){
                $arr[]=$key.'='.(in_array($key,$unQuot)?$val:"'$val'");
            }
            // $Value = "";
            // foreach ($Data as $key => $val) {
            //     if(is_numeric($val)){
            //         $Value .= $val.",";
            //     }else{
            //         $Value .= "'".$val."',";
            //     }
            // }
            // $Value=substr($Value,0,strlen($Value)-1);
            // //
            $Value=implode(',',$arr);
        }else $Value=$Data;
        return $this->query("UPDATE `{$Table}` SET {$Value} {$Condition}");
    }

    /******************************************************************
    -- 函数名：Del($Table,$Condition)
    -- 作  用：删除数据
    -- 参  数：$Table 表名(必填)
              $Condition 删除条件(选填)
    -- 返回值：布尔
    -- 实  例：$DB->Del('mydb','id=1')
    *******************************************************************/
    //public function Del($Table,$Condition=''){return $this->query("DELETE FROM `{$Table}`".($Condition?" WHERE {$Condition}":''));} 


    public function Del($Table,$Fileds='',$Join='',$Condition=''){
        return $this->query("DELETE".($Fileds?" {$Fileds}":'')." FROM `{$Table}`".($Join?" {$Join}":'').($Condition?" WHERE {$Condition}":''));
    } 

    // 取得结果数据
    public function result($result=''){
        if (empty($result)) $result=$this->result;
        if ($result==null) $this->show_error('未获取到查询结果',true);
        return mysql_result($result);
    }

    /**
     * andy 2018-01-03 新增事务处理
     */
    
    /**
     * [begin_trains 开启事务]
     * @return [type] [description]
     */
    public function trans_begin(){
        if (!$this->transBegin) {
            $this->query('START TRANSACTION');

            // 标记开启事务
            $this->transBegin = TRUE;
        }
    }

    /**
     * [trans_rollback 回滚]
     * @return [type] [description]
     */
    public function trans_rollback(){
        if ($this->transBegin) {
            $this->query('ROLLBACK');
        }
    }

    /**
     * [trans_commit 提交事务]
     * @return [type] [description]
     */
    public function trans_commit(){
        if ($this->transBegin) {
            $this->query('COMMIT');
        }
    }

    /**
     * andy 2018-01-03 新增事务处理
     */
    

    /******************************************************************
    -- 函数名：fetch_array($Table,$Condition)
    -- 作  用：根据从结果集取得的行生成关联数组
    -- 参  数：$result 结果集(选填)
              $type 数组类型，可以接受以下值：MYSQL_ASSOC，MYSQL_NUM 和 MYSQL_BOTH(选填)
    -- 返回值：布尔
    -- 实  例：$DB->Del('mydb','id=1')
    *******************************************************************/
    public function fetch_array($result='',$type=MYSQL_ASSOC){
        if (empty($result)) $result=$this->result;
        if (!$result) $this->show_error('未获取到查询结果',true);
        return mysql_fetch_array($result,$type);
    }

    // 获取关联数组,使用$row['字段名']
    public function fetch_assoc($result=''){
        if (empty($result)) $result=$this->result;
        if (!$result) $this->show_error('未获取到查询结果',true);
        return mysql_fetch_assoc($result);
    }    

    // 获取数字索引数组,使用$row[0],$row[1],$row[2]
    public function fetch_row($result=''){
        if (empty($result)) $result=$this->result;
        if (!$result) $this->show_error('未获取到查询结果',true);
        return mysql_fetch_row($result);
    } 

    // 获取对象数组,使用$row->content 
    public function fetch_obj($result=''){
        if (empty($result)) $result=$this->result;
        if (!$result) $this->show_error('未获取到查询结果',true);
        return mysql_fetch_object($result);
    }  

    // 取得上一步 INSERT 操作产生的 ID
    public function insert_id(){return mysql_insert_id();}

    // 指向确定的一条数据记录
    public function data_seek($id){
        if ($id>0) $id=$id-1;
        if (!mysql_data_seek($this->result,$id)) $this->show_error('指定的数据为空');
        return $this->result; 
    }

    /******************************************************************
    函数名：num_fields($result)
    作  用：查询字段数量
    参  数：$Table 数据库表名(必填)
    返回值：字符串
    实  例：$DB->num_fields("mydb")
    *******************************************************************/
    public function num_fields($result=''){
        if (empty($result)) $result=$this->result;
        if (!$result) $this->show_error('未获取到查询结果',true);
        return mysql_num_fields($result);
    }

    // 根据select查询结果计算结果集条数 
    public function num_rows($result=''){ 
        if (empty($result)) $result=$this->result;
        $rows=mysql_num_rows($result);
        if ($result==null){
            $rows=0;
            $this->show_error('未获取到查询结果',true);
        }
        return $rows>0?$rows:0;
    }

    // 根据insert,update,delete执行结果取得影响行数 
    public function affected_rows(){return mysql_affected_rows();}

    // 获取地址栏参数
    public function getQuery($unset=''){ //$unset表示不需要获取的参数，多个参数请用,分隔(例如:getQuery('page,sort'))
        $list = "";
        if (!empty($unset)){
            $arr=explode(',',$unset);
            foreach ($arr as $val) unset($_GET[$val]);
        }
        foreach ($_GET as $key=>$val) $list[]=$key.'='.urlencode($val);
        return is_array($list)?implode('&',$list):'';
    }

    /******************************************************************
    函数名：getPage($Table,$Fileds,$Condition,$pageSize)
    作  用：获取分页信息
    参  数：$Table 表名(必填)
           $Fileds 字段名，默认所有字段(选填)
           $Condition 查询条件(选填)
           $pageSize 每页显示记录条数，默认10条(选填)
    返回值：字符串
    实  例：无
    *******************************************************************/
    public function getPage($Table,$Fileds='*',$Condition='',$pageSize=10){
        if (intval($pageSize)>0){$this->pageSize=intval($pageSize);}
        if (isset($_GET['page']) && intval($_GET['page'])){$this->pageNo=intval($_GET['page']);}
        if (empty($Fileds)){$Fileds='*';}
        $sql="SELECT * FROM {$Table} {$Condition}";
        $this->query($sql);
        $this->rsAll=$this->num_rows();
        if ($this->rsAll>0){
            $this->pageAll=ceil($this->rsAll/$this->pageSize);
            if ($this->pageNo<1){$this->pageNo=1;}
            if ($this->pageNo>$this->pageAll){$this->pageNo=$this->pageAll;}
            $sql="SELECT {$Fileds} FROM {$Table} {$Condition}".$this->limit(true);
            $this->query($sql);

        }
        return $this->rsAll;
    }

    // 构造分页limit语句，和getPage()函数搭配使用
    public function limit($str=false){
        $n=($this->pageNo-1)*$this->pageSize;
        return $str?' LIMIT '.$n.','.$this->pageSize:$n;
    }

    // 显示分页，必须和getPage()函数搭配使用
    public function showPage($number=true){
        $pageBar='';
        global $pageSize;
        $pageBar.="<span class='textItem f11px'>共 ".$this->rsAll." 条记录。</span>";
        if ($this->pageAll>1){

            $pageBar.="<span class='textItem f11px'>共 ".$this->rsAll." 条记录，每页 ".$pageSize." 条，页次：{$this->pageNo}/{$this->pageAll}</span>";


            $pageBar.='<div class="pagenum-wrapper">'.chr(10);
            $pageBar.='<div class="">'.chr(10);
            $url=$this->getQuery('page');
            $url=empty($url)?'?page=':'?'.$url.'&page=';
            if ($this->pageNo>1){
                $pageBar.='<a href="'.$url.'1" class="first" title="首页">&laquo;</a>'.chr(10);
                $pageBar.='<a href="'.$url.($this->pageNo-1).'" class="prev" title="上一页">&lsaquo;</a>'.chr(10);
            }else{
                $pageBar.='<a class="first disabled">&laquo;</a>'.chr(10);
                $pageBar.='<a class="prev disabled">&lsaquo;</a>'.chr(10);
            }





            if ($number){
                $pageBar.='<span>';

                $arr=array();
                if ($this->pageAll<6){
                    for ($i=0;$i<$this->pageAll;$i++) $arr[]=$i+1;
                }else{
                    if ($this->pageNo<3)
                        $arr=array(1,2,3,4,5);
                    elseif ($this->pageNo<=$this->pageAll&&$this->pageNo>($this->pageAll-3))
                        for ($i=1;$i<6;$i++) $arr[]=$this->pageAll-5+$i;
                    else
                        for ($i=1;$i<6;$i++) $arr[]=$this->pageNo-3+$i;
                }
                foreach ($arr as $val){

                    if ($val==$this->pageNo) $pageBar.='<a class="disabled">'.$val.'</a>'.chr(10);
                    else $pageBar.='<a href="'.$url.$val.'">'.$val.'</a>'.chr(10);
                }
                $pageBar.='</span>';
            }
            if ($this->pageNo<$this->pageAll){
                $pageBar.='<a href="'.$url.($this->pageNo+1).'" class="next" title="下一页">&rsaquo;</a>'.chr(10);
                $pageBar.='<a href="'.$url.$this->pageAll.'" class="last" title="尾页">&raquo;</a>'.chr(10);
            }else{
                $pageBar.='<a class="next disabled">&rsaquo;</a>'.chr(10);
                $pageBar.='<a class="last disabled">&raquo;</a>'.chr(10);
            }



            $pageBar.= "</div></div>";
            //$pageBar.='<li class="stop"><span>';
            //$pageBar.="页次:{$this->pageNo}/{$this->pageAll} {$this->pageSize}条/页 总记录:{$this->rsAll} 转到:";
            //$pageBar.="<input id=\"page\" value=\"{$this->pageNo}\" type=\"text\" onblur=\"goPage('{$url}',{$this->pageAll});\" />";
            //$pageBar.='</span></li></ul>'.chr(10);
        }
        echo $pageBar;
    }
    // 显示分页，必须和getPage()函数搭配使用
    public function showPageforFront($config=array()){
        $pageBar='';
        global $pageSize;
        if ($this->pageAll>1){

            $pageBar.="<div class='pagebar'>";
            $pageBar.='<ul>';
            $url=$this->getQuery('page');
            $url=empty($url)?'?page=':'?'.$url.'&page=';
            if ($this->pageNo>1){
                if (in_array('first', $config)){
                    $pageBar.='<li class="first"><a href="'.$url.'1" title="首页">&laquo;</a></li>';
                }
                if (in_array('prev', $config)){
                    $pageBar.='<li class="previous"><a href="'.$url.($this->pageNo-1).'"><span aria-hidden="true">&larr;</span>上一页</a></li>';
                }
            }else{
                if (in_array('first', $config)){
                    $pageBar.='<li class="first disabled"><a>&laquo;</a></li>';
                }
                if (in_array('prev', $config)){
                    $pageBar.='<li class="previous disabled"><a><span aria-hidden="true">&larr;</span>上一页</a></li>';
                 }   
            }

            if (in_array('number', $config)){
                $arr=array();
                if ($this->pageAll<6){
                    for ($i=0;$i<$this->pageAll;$i++) $arr[]=$i+1;
                }else{
                    if ($this->pageNo<3)
                        $arr=array(1,2,3,4,5);
                    elseif ($this->pageNo<=$this->pageAll&&$this->pageNo>($this->pageAll-3))
                        for ($i=1;$i<6;$i++) $arr[]=$this->pageAll-5+$i;
                    else
                        for ($i=1;$i<6;$i++) $arr[]=$this->pageNo-3+$i;
                }
                foreach ($arr as $val){

                    if ($val==$this->pageNo) $pageBar.='<li class="disabled"><a>'.$val.'</a></li>';
                    else $pageBar.='<li><a href="'.$url.$val.'">'.$val.'</a></li>';
                }
            }
            if ($this->pageNo<$this->pageAll){
                if (in_array('next', $config)){
                    $pageBar.='<li class="next"><a href="'.$url.($this->pageNo+1).'">下一页<span aria-hidden="true">&rarr;</span></a></li>';
                }
                if (in_array('last', $config)){
                    $pageBar.='<li class="last"><a href="'.$url.$this->pageAll.'">&raquo;</a></li>';
                }
            }else{
                if (in_array('next', $config)){
                    $pageBar.='<li class="next disabled"><a>&rsaquo;</a></li>';
                }
                if (in_array('last', $config)){
                    $pageBar.='<li class="last disabled"><a>下一页<span aria-hidden="true">&rarr;</span></a></li>';
                }
            }

            $pageBar .= "</ul>";
            if (in_array('info', $config)){
                $pageBar .= "<div class='info'>{$this->pageNo}/{$this->pageAll}</div>";
            }
            $pageBar .= "</div>";
            //$pageBar.='<li class="stop"><span>';
            //$pageBar.="页次:{$this->pageNo}/{$this->pageAll} {$this->pageSize}条/页 总记录:{$this->rsAll} 转到:";
            //$pageBar.="<input id=\"page\" value=\"{$this->pageNo}\" type=\"text\" onblur=\"goPage('{$url}',{$this->pageAll});\" />";
            //$pageBar.='</span></li></ul>'.chr(10);
        }
        return $pageBar;
    }
    // 显示分页，必须和getPage()函数搭配使用
    public function showPageEn($number=true){
        $pageBar='';
        global $pageSize;
        //$pageBar.="<span class='textItem f11px'>Total ".$this->rsAll."</span>";
        if ($this->pageAll>1){

            //$pageBar.="<span class='textItem f11px'>Total ".$this->rsAll.",".$pageSize." / Perpage , Page: {$this->pageNo} /{$this->pageAll}</span>";
            $pageBar.="<div class=\"page-info\"><span class='textItem f11 '>Total ".$this->rsAll." Products , ".$pageSize." Products / Perpage</span></div>";

            $pageBar.='<div class="pagenum-wrapper">'.chr(10);
            $pageBar.='<div class="">'.chr(10);
            $url=$this->getQuery('page');
            $url=empty($url)?'?page=':'?'.$url.'&page=';
            if ($this->pageNo>1){
                $pageBar.='<a href="'.$url.'1" class="first" title="First Page">First</a>'.chr(10);
                $pageBar.='<a href="'.$url.($this->pageNo-1).'" class="prev" title="Prev Page">Prev</a>'.chr(10);
            }else{
                $pageBar.='<a class="first disabled hide">First</a>'.chr(10);
                $pageBar.='<a class="prev disabled hide">Prev</a>'.chr(10);
            }





            if ($number){
                $pageBar.='<span>';

                $arr=array();
                if ($this->pageAll<6){
                    for ($i=0;$i<$this->pageAll;$i++) $arr[]=$i+1;
                }else{
                    if ($this->pageNo<3)
                        $arr=array(1,2,3,4,5);
                    elseif ($this->pageNo<=$this->pageAll&&$this->pageNo>($this->pageAll-3))
                        for ($i=1;$i<6;$i++) $arr[]=$this->pageAll-5+$i;
                    else
                        for ($i=1;$i<6;$i++) $arr[]=$this->pageNo-3+$i;
                }
                foreach ($arr as $val){

                    if ($val==$this->pageNo) $pageBar.='<a class="disabled">'.$val.'</a>'.chr(10);
                    else $pageBar.='<a href="'.$url.$val.'">'.$val.'</a>'.chr(10);
                }
                $pageBar.='</span>';
            }
            if ($this->pageNo<$this->pageAll){
                $pageBar.='<a href="'.$url.($this->pageNo+1).'" class="next" title="Next Page">Next</a>'.chr(10);
                $pageBar.='<a href="'.$url.$this->pageAll.'" class="last" title="Last Page">Last</a>'.chr(10);
            }else{
                $pageBar.='<a class="next disabled hide">Next</a>'.chr(10);
                $pageBar.='<a class="last disabled hide">Last</a>'.chr(10);
            }



            $pageBar.= "</div></div>";
            //$pageBar.='<li class="stop"><span>';
            //$pageBar.="页次:{$this->pageNo}/{$this->pageAll} {$this->pageSize}条/页 总记录:{$this->rsAll} 转到:";
            //$pageBar.="<input id=\"page\" value=\"{$this->pageNo}\" type=\"text\" onblur=\"goPage('{$url}',{$this->pageAll});\" />";
            //$pageBar.='</span></li></ul>'.chr(10);
        }
        echo $pageBar;
    }

    // 获得客户端真实的IP地址
    public function getip(){
        // if ($_SERVER['HTTP_X_FORWARDED_FOR']) return $_SERVER['HTTP_X_FORWARDED_FOR'];
        // elseif ($_SERVER['HTTP_CLIENT_IP']) return $_SERVER['HTTP_CLIENT_IP'];
        // elseif ($_SERVER['REMOTE_ADDR']) return $_SERVER['REMOTE_ADDR'];
        // elseif (getenv('HTTP_X_FORWARDED_FOR')) return getenv('HTTP_X_FORWARDED_FOR');
        // elseif (getenv('HTTP_CLIENT_IP')) return getenv('HTTP_CLIENT_IP');
        // elseif (getenv('REMOTE_ADDR')) return getenv('REMOTE_ADDR');
        // else 
            return '';
    }

    /******************************************************************
    -- 函数名：show_error($message,$sql)
    -- 作  用：输出显示错误信息
    -- 参  数：$msg 错误信息(必填)
              $sql 显示错误的SQL语句，在SQL语句错误时使用(选填)
    -- 返回值：字符串
    -- 实  例：无
    *******************************************************************/
    public function show_error($msg='',$sql=false){
        $err='['.mysql_errno().']'.mysql_error();
        if ($sql) $sql='SQL语句：'.$this->sql;
        if ($this->errLog){
            $dirs=$_SERVER['DOCUMENT_ROOT'].'/error/'; //设置错误日志保存目录
            $fileName=date('Y-m-d').'.log';
            $filePath=$dirs.$fileName;
            if (!is_dir($dirs)){
                $dirs=explode('/',$dirs);
                $temp='';
                foreach($dirs as $dir){
                    $temp.=$dir.'/';
                    if (!is_dir($temp)){
                        mkdir($temp,0777) or die('__无法建立目录'.$temp.'，自动取消记录错误信息');
                    }
                }
                $filePath=$temp.$fileName;
            }
            $text="错误事件：".$msg."\r\n错误原因：".$err."\r\n".($sql?$sql."\r\n":'')."客户端IP：".$this->getip()."\r\n记录时间：".date('Y-m-d H:i:s')."\r\n\r\n";
            $log='错误日志：__'.(error_log($text,3,$filePath)?'此错误信息已被自动记录到日志'.$fileName:'写入错误信息到日志失败');
        }
        if ($this->showErr){
      echo '
      <fieldset class="errlog">
        <legend>错误信息提示</legend>
        <label class="tip">错误事件：'.$err.'</label>
        <label class="msg">错误原因：'.$msg.'</label>
        <label class="sql">'.$sql.'</label>
        <label class="log">'.$log.'</label>
      </fieldset>';
      exit();
        }
    }

    /******************************************************************
    -- 函数名：drop($table)
    -- 作  用：删除表(请慎用,无法恢复)
    -- 参  数：$table 要删除的表名，默认为所有(选填)
    -- 返回值：无
    -- 实  例：$DB->drop('mydb')
    *******************************************************************/
    public function drop($table){
        if ($table){
            $this->query("DROP TABLE IF EXISTS `{$table}`");
        }else{
            $rst=$this->query('SHOW TABLES'); 
            while ($row=$this->fetch_array()){
                $this->query("DROP TABLE IF EXISTS `{$row[0]}`");
            }
        }
    }

    /******************************************************************
    -- 函数名：makeSql($table)
    -- 作  用：从数据表读取信息并生成SQL语句
    -- 参  数：$table 待读取的表名(必填)
    -- 返回值：字符串
    -- 实  例：无
    *******************************************************************/
    public function makeSql($table){
        $result=$this->query("SHOW CREATE TABLE `{$table}`");
        $row=$this->fetch_row($result);
        $sqlStr='';
        if ($row){
            $sqlStr.="-- ---------------------------------------------------------------\r\n";
            $sqlStr.="-- Table structure for `{$table}`\r\n";
            $sqlStr.="-- ---------------------------------------------------------------\r\n";
            $sqlStr.="DROP TABLE IF EXISTS `{$table}`;\r\n{$row[1]};\r\n";
            $this->Get($table);
            $fields=$this->num_fields();
            if ($this->num_rows()>0){
                $sqlStr.="\r\n";
                $sqlStr.="-- ---------------------------------------------------------------\r\n";
                $sqlStr.="-- Records of `{$table}`\r\n";
                $sqlStr.="-- ---------------------------------------------------------------\r\n";
                while ($row=$this->fetch_row()){
                    $comma='';
                    $sqlStr.="INSERT INTO `{$table}` VALUES (";
                    for($i=0;$i<$fields;$i++){
                        $sqlStr.=$comma."'".mysql_escape_string($row[$i])."'";
                        $comma=',';
                    }
                    $sqlStr.=");\r\n";
                }
            }
            $sqlStr.="\r\n";
        }
        return $sqlStr;
    }

    /******************************************************************
    -- 函数名：readSql($filePath)
    -- 作  用：读取SQL文件并过滤注释
    -- 参  数：$filePath SQL文件路径(必填)
    -- 返回值：字符串/布尔/数组
    -- 实  例：无
    *******************************************************************/
    public function readSql($filePath){
        if (!file_exists($filePath)) return false;
        $sql=file_get_contents($filePath);
        if (empty($sql)) return '';
        $sql=preg_replace('/(\/\*(.*)\*\/)/s','',$sql); //过滤批量注释
        $sql=preg_replace('/(--.*)|[\f\n\r\t\v]*/','',$sql); //过滤单行注释与回车换行符
        $sql=preg_replace('/ {2,}/',' ',$sql); //将两个以上的连续空格替换为一个，可以省略这一步
        $arr=explode(';',$sql);
        $sql=array();
        foreach ($arr as $str){
            $str=trim($str);
            if (!empty($str)) $sql[]=$str;
        }
        return $sql;
    }

    /******************************************************************
    -- 函数名：saveSql($sqlPath,$table)
    -- 作  用：将当前数据库信息保存为SQL文件
    -- 参  数：$sqlPath SQL文件保存路径，如果为空则自动以当前日期为文件名并保存到当前目录(选填)
              $table 待保存的表名，为空着表示保存所有信息(选填)
    -- 返回值：字符串
    -- 实  例：$DB->saveSql('../mydb.sql');
    *******************************************************************/
    public function saveSql($sqlPath='',$table=''){
        if (empty($table)){
            $result=$this->query('SHOW TABLES');
            while ($arr=$this->fetch_row($result)){
                $str=$this->makeSql($arr[0]);
                if (!empty($str)) $sql.=$str;
            }
            $text="/***************************************************************\r\n";
            $text.="-- Database: $this->data\r\n";
            $text.="-- Date Created: ".date('Y-m-d H:i:s')."\r\n";
            $text.="***************************************************************/\r\n\r\n";
        }else{
            $text='';
            $sql=$this->makeSql($table);
        }
        if (empty($sql)) return false;
        $text.=$sql;
        $dir=dirname($sqlPath);
        $file=basename($sqlPath);
        if (empty($file)) $file=date('YmdHis').'.sql';
        $sqlPath=$dir.'/'.$file;
        if (!empty($dir)&&!is_dir($dir)){
            $path=explode('/',$dir);
            $temp='';
            foreach ($path as $dir){
                $temp.=$dir.'/';
                if (!is_dir($temp)){
                    if (!mkdir($temp,0777)) return false;
                }
            }
            $sqlPath=$temp.$file;
        }
        $link=fopen($sqlPath,'w+');
        if (!is_writable($sqlPath)) return false;
        return fwrite($link,$text);
        fclose($link);
    }

    /******************************************************************
    -- 函数名：loadSql($filePath)
    -- 作  用：从SQL文件导入信息到数据库
    -- 参  数：$filePath SQL文件路径(必填)
    -- 返回值：字符串
    -- 实  例：无
    *******************************************************************/
    public function loadSql($filePath){
        $val=$this->readSql($filePath);
        if ($val==false) $this->show_error($filePath.'不存在');
        elseif (empty($val)) $this->show_error($filePath.'中无有效数据');
        else{
            $errList='';
            foreach ($val as $sql){
                $result=mysql_query($sql);
                if (!$result) $errList.='执行语句'.$sql.'失败<br />';
            }
            return $errList;
        }
        return false;
    }

    // 释放结果集
    public function free(){

        mysql_free_result($this->result);
    }

    // 关闭数据库
    public function close(){mysql_close($this->conn);}

    // 析构函数，自动关闭数据库,垃圾回收机制
    public function __destruct(){
        //$this->free();
        //$this->close();
    }
}
?>