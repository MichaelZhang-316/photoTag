<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController{

    //获取首页最新图片
    public function newPhotos(){
        $photoModel = M('photos');
        $newPotos = $photoModel->where('status = 2')->order('id desc')->limit(10)->select();
        if($newPotos){
            $this->returns($newPotos,'获取最新图片成功','1');
        }else{
            $this->returns('array()','获取最新图片失败','0');
        }
    }

    //查找图片  （按类型查找）
    public function tagPhotosFind(){
        $keyword = I('keyword');  //前段传递 关键字
        $categoryModel = M('category');
        $where = array(
            'name' => array('like','%'.$keyword.'%'),
        );
        $category = $categoryModel->where($where)->select();
        foreach($category as $v){
            $pId[] = $v['id'];
        }
        $photoModel = M('photos');
        $photoList = $photoModel->where($pId)->where('status = 2')->select();
        if($photoList){
            $this->returns($photoList,'查找图片成功','1');
        }else{
            $this->returns('array()','查找图片失败','0');
        }
    }

    //收藏
    public function collect(){
        $memberInfo = $this->is_log();
        $photosId = I('photoid'); //前段传递  图片ID
        $userId = $memberInfo['id'];
        $data = array(
            'member_id'     =>$userId,
            'photo_id'      =>$photosId,
            'time'          =>time(),
            'status'        =>1,
        );
        $collectModel = D('collect');
        $add = $collectModel->add_collect($data);
        if($add){
            $this->returns($add,'收藏成功','1');
        }else{
            $this->returns(array(),'收藏失败','0');
        }
    }

    //下载
    public function downloads(){
        $memberInfo = $this->is_log();
        $photoId = I('photoid');  //前段传递  图片ID
        $data = array(
            'member_id' =>$memberInfo['id'],
            'photo_id'  =>$photoId,
            'time'      =>time(),
        );
        $downModel = D('download');
        $down = $downModel->add_download($data);
        if($down){
            $photoModel = D('photos');
            $photoInfo = $photoModel->get_List('id = '.$photoId);
            $number = $photoInfo[0]['download_nu'] + 1;
            $pd = array('id'=>$photoId);
            $d = array('download_nu'=>$number);
            $un = $photoModel->change($pd,$d);
            if($un){
                $this->returns($photoInfo,'下载成功','1');
            }else{
                $this->returns(array(),'下载失败','0');
            }
        }else{
            $this->returns(array(),'下载失败','0');
        }
    }

    //判断用户是否登录
    private function is_log(){
        $userInfo = $_SESSION['mem'];
        if(empty($userInfo)){
            $result = array('data'=>array(),'message'=>'您还没有登录','status'=>'2');
            $this->ajaxReturn($result);
        }else{
            return $userInfo;
        }
    }

    //登录界面验证码
    public function log()
    {
        Entry();
    }

}