<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status   数据状态
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_list_count($category, $status = 1){
    static $count;
    if(!isset($count[$category])){
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_nav_url($url){
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url = U($url);
            break;
    }
    return $url;
}


/**
 * @param $idcard
 * @param string $type
 * @return mixed|string
 * 用**号隐藏敏感号码信息
 */
function hide_str($idcard,$type='id'){
    $str='';
    switch($type){
        case 'id':  //身份证号
            $str=strlen($idcard)==15?substr_replace($idcard,"****",8,4):(strlen($idcard)==18?substr_replace($idcard,"****",10,4):"身份证位数不正常！");
            break;
        case 'tel':  //座机号
            $str=strlen($idcard)==11?substr_replace($idcard,"****",3,4):(strlen($idcard)==8?substr_replace($idcard,"****",2,4):"电话号码位数不正常！");
            break;
        case 'mobile': //手机号
            $str=strlen($idcard)==11?substr_replace($idcard,"****",3,4):"手机号位数不正常！";
            break;
        default:
            ;

    }
    return $str;
}