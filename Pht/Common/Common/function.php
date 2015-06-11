<?php

//验证码
function Entry(){
    $config = array(
        'length' => 4,
        'codeSet'=>'0123456789',
        'useNoise' =>true,
        'imageW' => 120,
        'imageH' => 30,
        'fontSize'=>15,
        'useCurve'=>false,
    );
    $Verify = new \Think\Verify($config);
    $Verify->entry();
}
//获取IP
function Get_ip(){
    $ip = get_client_ip();
    return $ip;
}
//判断用户验证码输入
function check_verify($code, $id = ''){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}



