<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller
{

    //ajax返回函数
    protected function returns ($data,$message,$status){
        $result = array('data'=>$data,'message'=>$message,'status'=>$status);
        $this->ajaxReturn($result);
    }

    //图片列表
    protected function photoList($status,$numbers){ //
        $photosModel = M('photos');
        $where = array('status'=>$status);
        if(empty($where['status'])){
            $count = $photosModel->count();
        }else{
            $count = $photosModel->where($where)->count();
        }
        $count_page = $numbers ? $numbers : 1;
        $Page = new \Think\Page($count,$count_page);
        $pages = $Page->show();// 分页显示输出
        if(empty($where['status'])){
            $list = $photosModel->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
        }else{
            $list = $photosModel->where($where)->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
        }
        $data = array(
            'papges' => $pages,
            'list'   => $list,
        );
        return $data;
    }



}