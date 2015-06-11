<?php
namespace Home\Controller;
use Think\Controller;
class AdminController extends BaseController
{
    //管理员登录
    public function login(){
        $logAdminModel = D("admin");
        if(!check_verify(I('verify'))){
            $result     = array('data'=>array(),'status'=>'0','message'=>'网页验证码错误');
            $this->ajaxReturn($result);
            exit;
        }
        $username = $_POST('username');
        $pwd = $_POST('password');
//        $username = 'admin';
//        $pwd = 'admin';
        if(!empty($username) && !empty($pwd)){
            $where = array(
                'name' => $username,
                'password' => MD5($pwd),
            );
        }
        $adm = $logAdminModel->get_admin($where);
        if(!empty($adm)){
            $_SESSION['admin']= $adm;
            $result = array('data'=>array(),'message'=>'登录成功','status'=>'1');
        }else{
            $result = array('data'=>array(),'message'=>'登录失败','status'=>'0');
        }

        $this->ajaxReturn($result);
    }

    //管理员退出
    public function logout(){
        $userInfo = $this->is_log();
        $push_adminModel = D('admin');
        $where = array('id'=>$userInfo['id']);
        $userInfo['logtime'] = time();
        $userInfo['logip'] = Get_ip();
        $userInfo['lognumber']++;
        $log_out = $push_adminModel->push_admin($where,$userInfo);
        if($log_out){
            //清除session
            $_SESSION['admin'] = null;
            $result = array('data'=>array(),'message'=>'退出成功','status'=>'1');
        }else{
            $result = array('data'=>array(),'message'=>'退出失败','status'=>'0');
        }
        $this->ajaxReturn($result);
    }

    //管理员个人中心和数据统计
    public function users(){
        //管理员账户信息
        $userInfo = $this->is_log();
        $userInfo['usertype'] = '管理员';
        //图片统计
        $photosModel = D('photos');
        $photos = $photosModel->get_photos();
        $photos['Nu'] = $photos['0']['id'] + $photos['1']['id'] + $photos['2']['id'];
        //用户统计
        $memberModel = D('members');
        $members = $memberModel->get_members();
        $members['Nu'] = $members['0']['id'] + $members['1']['id'] + $members['2']['id'];
        $data = array(
            'user' =>$userInfo,
            'photoNu' => $photos,
            'members'=>$members,
        );
        if(empty($data)){
            $result = array('data'=>array(),'message'=>'获取信息失败','status'=>'0');
        }else{
            $result = array('data'=>$data,'message'=>'信息获取成功','status'=>'1');
        }
        $this->ajaxReturn($result);
    }

    //用户列表 (用于展示注册1、审核通过2、禁止3、删除4用户功能的列表)
    public function memberList(){
        $this->is_log();
        $status = I('status');
        $list = $this->membersList($status,'5');
        if(empty($list)){
            $this->returns('array()','获取用户列表失败','0');
        }else{
            $this->returns($list,'获取用户列表成功','1');
        }
    }

    //禁止、删除、审核用户
    public function ch_member(){
        $this->is_log();
        $membersId = I('members');
        $status = I('status');
        $ch = $this->chMember($membersId,$status);
        if($ch){
            $this->returns($ch,'操作成功','1');
        }else{
            $this->returns(array(),'操作失败','0');
        }
    }

    //图片列表展示 (用于展示未审核1、审核通过2、删除3 图片功能的列表)
    public function photosList(){
        $this->is_log();
        $status = I('status');
        $list = $this->photoList($status,'5');
        if(empty($list)){
            $this->returns('array()','获取图片失败','0');
        }else{
            $this->returns($list,'获取图片成功','1');
        }
    }
    //删除、审核图片
    public function ch_photo(){
        $this->is_log();
        $photosId = I('photos');
        $status = I('status');
        $ch = $this->chPhoto($photosId,$status);
        if($ch){
            $this->returns($ch,'操作成功','1');
        }else{
            $this->returns(array(),'操作失败','0');
        }
    }

    //获取标签列表
    public function tageList(){
        $this->is_log();
        $tagsModel = M('tags');
        $where = array('status'=>1);
        $count_page = 5;
        $count = $tagsModel->where($where)->count();
        $Page = new \Think\Page($count,$count_page);
        $pages = $Page->show();// 分页显示输出
        $list = $tagsModel->where($where)->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
        $data = array(
            'papges' => $pages,
            'list'   => $list,
        );
        if($data){
            $this->returns($data,'获取成功','1');
        }else{
            $this->returns(array(),'获取失败','0');
        }
    }

    //删除标签
    public function delTags(){
        $this->is_log();
        $tagModel = D('Tags');
        $tagsId = I('tags');
        $where = array(
            'id' => array('in',$tagsId),
        );
        $tagList = $tagModel->get_tagList($where);
        if($tagList){
            $del = $tagModel->change($where,'status=2');
        }
        if($del){
            $this->returns($del,'操作成功','1');
        }else{
            $this->returns(array(),'操作失败','0');
        }

    }

    //登录界面验证码
    public function log()
    {
        Entry();
    }

///////工具型内部函数//////////////////////////////////////////////////////
    //更改图片状态
    private function chPhoto($photos,$status){ //$members $status
        $this->is_log();
        $status = array('status'=>$status);
        $where = array('id'=>array('in',$photos));
        $memberListModel = D('photos');
        $list = $memberListModel->get_List($where);
        if($list){
            $ch = $memberListModel->change($where,$status);
        }
        return $ch;
    }


    //更改用户状态
    private function chMember($members,$status){ //$members $status
        $this->is_log();
        $status = array('status'=>$status);
        $where = array('id'=>array('in',$members));
        $memberListModel = D('members');
        $list = $memberListModel->get_memberList($where);
        if($list){
            $ch = $memberListModel->ch_member($where,$status);
        }
        return $ch;
    }
    //用户列表
    private function membersList($status,$numbers){ //
        $this->is_log();
        $memberModel = M('members');
        $where = array('status'=>$status);
        if(empty($where['status'])){
            $count = $memberModel->count();
        }else{
            $count = $memberModel->where($where)->count();
        }
        $count_page = $numbers ? $numbers : 5;
        $Page = new \Think\Page($count,$count_page);
        $pages = $Page->show();// 分页显示输出
        if(empty($where['status'])){
            $list = $memberModel->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
        }else{
            $list = $memberModel->where($where)->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
        }
        $data = array(
            'papges' => $pages,
            'list'   => $list,
        );
        return $data;
    }


    //判断用户是否登录
    private function is_log(){
        $userInfo = $_SESSION['admin'];
        if(empty($userInfo)){
            $result = array('data'=>array(),'message'=>'您还没有登录','status'=>'2');
            $this->ajaxReturn($result);
        }else{
            return $userInfo;
        }
    }
}






















