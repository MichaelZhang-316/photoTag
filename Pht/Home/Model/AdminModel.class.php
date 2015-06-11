<?php

namespace Home\Model;

use Think\Model;
class AdminModel extends Model {
    //查找管理员
    public function get_admin($where) {
    	$admin = $this->where($where)->find();
    	return $admin;
    }
    //修改信息
    public function push_admin($where,$data){
        $save = $this->where($where)->save($data);
        return $save;
    }
}