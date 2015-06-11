<?php

namespace Home\Model;

use Think\Model;
class TagsModel extends Model {
    //标签列表
    public function get_tagList($where){
        $tagList = $this->where($where)->select();
        return $tagList;
    }
    //修改标签信息
    public function change($where,$data){
        $ch = $this->where($where)->save($data);
        return $ch;
    }

}