<?php

namespace Home\Model;

use Think\Model;
class CollectModel extends Model {
    //添加收藏
    public function add_collect($data){
        $add = $this->data($data)->add();
        return $add;
    }
    //修改状态
    public function ch_collect($where,$data){
        $ch = $this->where($where)->save($data);
        return $ch;
    }
}