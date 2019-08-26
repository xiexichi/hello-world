<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * [objHasPropertie 判断对象是否有属性]
 * @param  [object] $obj           [对象]
 * @param  [string] $propertieName [属性名称]
 * @return [bool]                  [TRUE/FALSE]
 */
if( !function_exists('objHasPropertie') )
{
    function objHasPropertie($obj, $propertieName){
        $reflect = new ReflectionObject($obj);

        foreach ($reflect->getProperties() as $k => $v) {
            // 判断是否有自定视图文件名
            if ($v->name == $propertieName) {
                return TRUE;
            }
        }

        return FALSE;
    }
}

/**
 * [objHasMethod 判断对象是否有此方法]
 * @param  [object] $obj           [对象]
 * @param  [string] $method [方法名称]
 * @return [bool]                  [TRUE/FALSE]
 */
if( !function_exists('objHasMethod') )
{
    function objHasMethod($obj, $method){
        $reflect = new ReflectionObject($obj);

        foreach ($reflect->getMethods() as $k => $v) {
            // 判断是否有自定视图文件名
            if ($v->name == $method) {
                return TRUE;
            }
        }

        return FALSE;
    }
}


/**
 * [p 打印函数]
 * @param  [mixed] $data [数据]
 */
if( !function_exists('p') )
{
    function p($data){
        echo '<pre>';
        print_r($data);
    }
}


/**
 * [pe 打印并终止执行函数]
 * @param  [mixed] $data [数据]
 */
if( !function_exists('pe') )
{
    function pe($data){
        echo '<pre>';
        print_r($data);
        exit;
    }
}

/**
 * 返回json数据
 * @param  integer $code  [description]
 * @param  string  $msg   [description]
 * @param  string  $state [description]
 * @param  array   $data  [description]
 * @return [type]         [description]
 */
if( !function_exists('returnJson') )
{
    function returnJson( $data = [] ,$code = 0 , $msg = '',$state = 'SUCCESS', $count = NULL){
        $result = [
            'code' => $code,
            'state'=> $state,
            'msg'  => $msg,
        ];

        if($code != 0){
            $result['state'] = 'ERROR';
        }

        // if($data){
            $result['data'] = $data;
        // }

        if ($count !== NULL) {
            $result['count'] = $count;
        }

        // 返回json数据
        return json($result);
    }
}

/**
 * [successJson 返回成功状态的json数据]
 * @param  array  $data [description]
 * @param  string $msg  [description]
 * @return [type]       [description]
 */
if( !function_exists('successJson') )
{
    function successJson($data = [],$msg = '', $count = NULL){

        // 四舍五入小数点后两位
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if (is_array($v)) {

                    foreach ($v as $kk => $vv) {
                        // 如果是小数
                        if (is_float($vv)) {
                            $data[$k][$kk] = round($vv, 2);
                        }
                    }

                } if (is_object($v)) {

                    $odata = $v->getData();

                    foreach ($odata as $kk => $vv) {
                        // 如果是小数
                        if (is_numeric($vv)) {
                            $odata[$kk] = round($vv, 2);
                        }
                    }
                    $v->data($odata);

                    $data[$k] = $v;
                } else {
                    // 如果是小数
                    if (is_float($v)) {
                        $data[$k] = round($v, 2);
                    }
                }
            }
        }


        return returnJson( $data ,$code = 0 , $msg ,$state = 'SUCCESS', $count);
    }   
}


/**
 * [errorJson 返回失败状态的json数据]
 * @param  array  $code [状态码]
 * @param  string $msg  [错误提示信息]
 * @return [type]       [description]
 */
if( !function_exists('errorJson') )
{
    function errorJson($code ,$msg = ''){
        return returnJson( [] ,$code , $msg ,$state = 'ERROR');
    }
}

/**
 * 输出json数据
 * @param  array   $data  [description]
 */
if( !function_exists('echoJson') )
{
    function echoJson( $json ){

        // 输出json对象数据
        header('Content-type:application/json; charset=utf-8');

        if (is_object($json)) {

            exit(json_encode($json->getData()));

        } else {

            exit(json_encode($json));
        }
    }
}


/**
 * 驼峰转下划线
 * @param  array   $data  [description]
 */
if( !function_exists('humpToLine') )
{
    function humpToLine($str){
        $str = preg_replace_callback("/([A-Z]{1})/",function($matches){
            return '_'.strtolower($matches[0]);
        },$str);

        return trim($str,'_');
    }
}


/**
 * 两个日期的差
 * @param  array   $start_date  [开始日期]
 * @param  array   $end_date    [结束日期]
 */
if( !function_exists('dateDiff') )
{
    function dateDiff($start_date, $end_date){
        $startTime = strtotime(date('Y-m-d', strtotime($start_date)));
        
        $endTime = strtotime(date('Y-m-d', strtotime($end_date)));

        return ($endTime - $startTime) / (3600 * 24);
    }
}

/**
 * 连通图片空间
 * 生成JWT token
 * @param $username [可选]
 */
if( !function_exists('createJWTtoken') )
{
    function createJWTtoken($username = '')
    {
        vendor('jwt.Jwt_helper');
        // 验证token
        $token = session('photojwttoken');
        if(!empty($token)){
            $check = Jwt_helper::validate($token);
            if($check) return $token;
        }

        // 匹配成功生成JWT token
        $jwtData = array(
            'username' => $username,
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            'host' => $_SERVER['HTTP_HOST'],
            'ip' => $_SERVER['SERVER_ADDR'],
        );
        $token = Jwt_helper::create($jwtData);
        // 保存session下次访问
        session('photojwttoken', $token);

        return $token;
    }
}