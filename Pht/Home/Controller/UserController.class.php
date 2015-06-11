<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends BaseController{


    //用户注册
    public function signUp(){
        $memberModel = D('members');
        $name = I('name');  //前段传递 用户名
        $where = array();
        if($name){
            $where['name'] = $name;
            $id = $memberModel->find($where);
            if($id){
                $this->returns(array(),'用户名存在','3');
            }
        }
        $pwd = I('pwd');  //前段传递  密码
        $repwd = I('repwd'); //前段传递  重复密码
        if($pwd !== $repwd){
            $this->returns(array(),'两次密码输入不一致，请重新输入','0');
        }
        $password = md5($pwd);
        $phone = I('phone');  //前段传递  手机号
        $email = I('email');  //前段传递  邮箱
        $data = array(
            'name'          => $name,
            'password'      =>$password,
            'phone'         =>$phone,
            'email'         =>$email,
            'logtime'       =>time(),
        );
        $adds = $memberModel->add_member($data);
        if($adds){
            $this->returns($adds,'注册成功','1');
        }else{
            $this->returns('array()','注册失败','0');
        }
    }
//登录
    public function login(){
        $memberModel = D('members');
        if(!check_verify(I('verify'))){
            $result     = array('data'=>array(),'status'=>'4','message'=>'验证码错误');
            $this->ajaxReturn($result);
            exit;
        }
        $username = $_POST('username');
        $pwd = $_POST('password');
//        $username = 'Tom';
//        $pwd = '123';
        if(!empty($username) && !empty($pwd)){
            $where = array(
                'name' => $username,
                'password' => MD5($pwd),
            );
        }else{
            $this->returns('array()','用户名或密码不能为空','0');
        }
        $mem = $memberModel->find_member($where);

        if(!empty($mem)){
            $_SESSION['mem']= $mem;
            $this->returns('array()','登录成功','1');
        }else{
            $this->returns('array()','登录失败','0');
        }
    }

    //用户退出
    public function logout(){
        $userInfo = $this->is_log();
        $push_adminModel = D('members');
        $where = array('id'=>$userInfo['id']);
        $userInfo['logtime'] = time();
        $userInfo['logip'] = Get_ip();
        $userInfo['lognumber']++;
        $log_out = $push_adminModel->ch_member($where,$userInfo);
        if($log_out){
            //清除session
            $_SESSION['mem'] = null;
            $result = array('data'=>array(),'message'=>'退出成功','status'=>'1');
        }else{
            $result = array('data'=>array(),'message'=>'退出失败','status'=>'0');
        }
        $this->ajaxReturn($result);
    }

    //我的下载
    public function myDownload(){
        $memInfo = $this->is_log();
        $userId = $memInfo['id'];
        $doModel = M('download');
        $where = array(
            'member_id' => $userId,
        );
        $count = $doModel->where($where)->count();
        $Page = new \Think\Page($count,10);
        $pages = $Page->show();
        $list = $doModel->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
        $data = array(
            'page'  => $pages,
            'list'  =>$list
        );
        if($data){
            $this->returns($data,'成功','1');
        }else{
            $this->returns('array()','失败','0');
        }
    }

    //我的收藏
    public function myColle(){
        $memInfo = $this->is_log();
        $userId = $memInfo['id'];
        $coModel = M('Collect');
        $where = array(
            'member_id' => $userId,
        );
        $count = $coModel->where($where)->count();
        $Page = new \Think\Page($count,10);
        $pages = $Page->show();
        $list = $coModel->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
        $data = array(
            'page'  => $pages,
            'list'  =>$list
        );
        if($data){
            $this->returns($data,'成功','1');
        }else{
            $this->returns('array()','失败','0');
        }
    }

    //删除收藏
    public function del_Colle(){
        $memInfo = $this->is_log();
        $colle = I('collectId');    //前段传递  数组格式，收藏记录Id
        $where = array(
            'id' => array('in',$colle),
        );
        $data = array('status'=>2);
        $coModel = D('collect');
        $ch = $coModel->ch_collect($where,$data);
        if($ch){
            $this->returns($ch,'成功','1');
        }else{
            $this->returns(array(),'失败','0');
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

    ////登录界面验证码
    public function log()
    {
        Entry();
    }

}