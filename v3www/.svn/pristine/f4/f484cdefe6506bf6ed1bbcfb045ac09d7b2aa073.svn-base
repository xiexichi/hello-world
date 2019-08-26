<?php
namespace app\common\taglib;
use think\template\TagLib;

class Hea extends TagLib{
    /**
     * 定义标签列表
     */
    protected $tags   =  [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'css'     => ['attr' => 'src, rel', 'close' => 0],
        'js'      => ['attr' => 'src', 'close' => 0],
    ];

    // 引入css文件，默认当前控制器名称
    public function tagCss($tag)
    {
        // 获取当前请求方法名称
        $action = request()->action();
        // 如果控制器是orders         
        $controller = request()->controller();
        // 模型名称
        $module = request()->module();
        if(empty($tag['rel'])) {
            $rel = 'stylesheet';
            $type = 'css';
        }else{
            $rel = 'stylesheet/'.$tag['rel'];
            $type = $tag['rel'];
        }
        if(empty($tag['src'])) {
            $src = '/static/style/' . $module . '/' . strtolower(humpToLine($controller)) . '.' . $type;
        }else{
            $src = $tag['src'];
        }
        $parse = '<link rel="'.$rel.'" href="'.$src.'" />';
        return $parse;
    }

    // 引入js文件，默认当前控制器名称
    public function tagJs($tag)
    {
        // 获取当前请求方法名称
        $action = request()->action();
        // 如果控制器是orders         
        $controller = request()->controller();
        // 模型名称
        $module = request()->module();

        if(empty($tag['src'])) {
            $src = '/static/js/' . $module . '/' . strtolower(humpToLine($controller)) . '.js';
        }else{
            $src = $tag['src'];
        }
        $parse = '<script type="text/javascript" charset="utf-8" src="'.$src.'"></script>';
        return $parse;
    }
}