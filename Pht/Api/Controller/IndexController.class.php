<?php
namespace Api\Controller;
use Think\Controller;
class IndexController extends Controller {

    private static $userInfo = null;    // 初始化用户基本信息

    //初始化验证用户       --michael
    public function _initialize(){
        if($token = I('token')){
            $this->userInfo = M('members')->where("token='{$token}'")->find();
        }else{
            $this->userInfo = null;
//            $this->userInfo = $this->userInfo = M('members')->where("id=2")->find();
        }
    }

    //验证用户登录        --michael
    public function islogin(){
        $userInfo = $this->userInfo;
        if(!$userInfo){
            $result = array('data'=>array(),'message'=>'请先登录账户','status'=>'0');
            $this->ajaxReturn($result);
        }
        return $userInfo;
    }

    //用户登录--不需要验证      --michael
    public function login(){
        $username = I('username');
        $password = md5(I('password'));
        $token    = md5(time());
        $model = M('members');
        $where = array(
            'name'    => $username,
            'password' => $password,
        );
        $user = $model->field("password", true)->where($where)->find();
        if($user){
            // 记录用户登录信息
            $data = array(
                'logtime' => time(),
                'lognumber'	  => $user['lognumber']+1,
                'token' 		  => $token,
            );
            $model->data($data)->where(array('id'=>$user['id']))->save();
            $users = $model->where(array('id'=>$user['id']))->find();
            $result = array('data'=>$users,'message'=>'登录成功','status'=>'1');
        }else{
            $result = array('data'=>array(),'message'=>'登录失败,用户名或密码不匹配','status'=>'0');
        }
        $this->ajaxReturn($result);
    }

    public function tags(){
        $list = M('tags')->select();
        if($list){
            $result = array('data'=>$list,'message'=>'标签获取成功','status'=>'1');
        }else{
            $result = array('data'=>array(),'message'=>'标签获取失败','status'=>'0');
        }
        $this->ajaxReturn($result);
    }

    public function uploadImage(){
        $userInfo = $this->userInfo;
        $tagsid = I('tagid');
        if(!$tagsid){
            $tagsid = 1;
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728 ;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
//        $upload->savePath = './Public/Uploads/'; // 设置附件上传目录
        $upload->rootPath = './Public/Uploads/';
        // 上传文件
        $info = $upload->uploadOne($_FILES['photo']);
        if($info) {
            $data['name'] = $info['name'];
            $data['imgurl'] = './Public/Uploads/'.$info['savepath'].$info['savename'];
//            $data['author'] = 'Tom';
            $data['author'] = $userInfo['name'];
            $data['size'] = $info['size'];
            $data['time'] = time();
            $p = M('photos')->data($data)->add();
            unset($data);
            if($p) {
                $data['tag_id'] = $tagsid;
                $data['photo_id'] = $p;
                $pt = M('phototag')->add($data);
                if($pt) {
                    $result = array('data'=>array(),'message'=>'上传成功','status'=>'1');
                }else{
                    $result = array('data'=>array(),'message'=>'上传失败','status'=>'0');
                }
            }else{
                $result = array('data'=>array(),'message'=>'上传失败','status'=>'0');
            }
        }else{
            $this->error($upload->getError());
        }
        $this->ajaxReturn($result);
    }





























}