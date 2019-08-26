<?php

namespace app\goods\model;

class Category extends Base
{
    protected $name = 'goods_categorys';

    public function getCateAll($data){
        $where['is_deleted'] = 0;
        $whereOr = [];
        if(isset($data['pid'])){//获取某父级下的子级
            $where['pid'] = $data['pid'];
            $whereOr['id'] = $data['pid'];
        }
        if(isset($data['field']) && !empty($data['field']) ){
            if(is_array($data['field'])){
                $field = '';
                foreach( $data['field'] as $keyName ){
                    $field .= $keyName.',';
                }
                $field = trim($field,',');
                $this->field($field);
            }else if(is_string($data['field'])){
                $this->field($data['field']);
            }
        }
        $cateAll = $this->where($where)->whereOr($whereOr)
            ->order('sort','desc')
            ->select();
        if( !empty($cateAll) ){
            foreach( $cateAll as $k => $v ){
                $v['name'] = $v['cate_name'];
                $v['value'] = $v['id'];
                $cateAll[$k] = $v;
            }
        }
        $geType = empty($data['showType']) ? 'list' : $data['showType'];
        switch( $geType ){
            case 'list' ://一维数组
                $list = $cateAll;
                break;
            case 'tree' ://普通多维树状数组
                $list = setTree($cateAll);
                break;
            case 'tree_list' ://一维数组分层列表 (注意占用进程资源问题)
                $list = setTreeList(setTree($cateAll));
                break;
            default :
                $list = $cateAll;
                break;
        }
        return $list;
    }




    public function addBefore(){
        $this->data['create_time'] = date('Y-m-d H:i:s');
        $this->data['update_time'] = $this->data['create_time'];
        return true;
    }

    public function editBefore(){
        $this->data['update_time'] = date('Y-m-d H:i:s');
        return true;
    }

    /**
     * 检查参数组名唯一
     * @param array $where  额外条件
     * @return bool
     */
    public function checkRepeatCateName($where=[]){
        if(!empty($this->data['cate_name'])){//前置方法使用
            $this->where('cate_name',$this->data['cate_name']);
        }
        if( !empty($where) ){
            foreach( $where as $key => $val ) {
                $this->where($key,$val[0],$val[1]);
            }
        }
        $info = $this->find();
        if($info){
            $this->code = 200402;
            $this->isExit = true;
            $this->error = "分类名已存在！";
            return false;
        }
        return true;
    }

}
