<?php

namespace Home\Model;

use Think\Model;
class MembersModel extends Model {
    //统计用户
    public function get_members($where) {
    	$members = $this->where($where)->field(array("count(id)"=>"id","status"))->group('status')->select();
    	return $members;
    }
    //用户列表
    public function get_memberList($where){
        $memberList = $this->where($where)->select();
        return $memberList;
    }
    //修改用户信息
    public function ch_member($where,$data){
        $ch = $this->where($where)->save($data);
        return $ch;
    }
    //查找用户信息
    public function find_member($where){
        $memberInfo = $this->where($where)->find();
        return $memberInfo;
    }
    //增加用户
    public function add_member($data){
        $add = $this->data($data)->add();
        return $add;
    }
}