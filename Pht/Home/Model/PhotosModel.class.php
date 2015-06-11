<?php

namespace Home\Model;

use Think\Model;
class PhotosModel extends Model {
    //统计
    public function get_photos($where) {
    	$photos = $this->where($where)->field(array("count(id)"=>"id","status"))->group('status')->select();
    	return $photos;
    }
    //图片列表
    public function get_List($where){
        $memberList = $this->where($where)->select();
        return $memberList;
    }
    //修改图片信息
    public function change($where,$data){
        $ch = $this->where($where)->save($data);
        return $ch;
    }

}