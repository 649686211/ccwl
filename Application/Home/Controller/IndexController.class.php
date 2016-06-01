<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use OT\DataDictionary;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends HomeController {

	//系统首页
    public function index(){

       /* $category = D('Category')->getTree();
        $lists    = D('Document')->lists(null);*/
        header("Content-type:text/html;charset=utf-8");
        $page=I('get.page',1,'intval');
        $pagesize=10;
        $articleModel = D('article');
        $where      = array();
        $where['status']=1;
        $where['category_id']=2;

        $lists    = $articleModel->lists($page,$pagesize,$where);
        //print_r($lists);
        if(IS_AJAX){
            if($lists){
                $this->ajaxReturn(array('status'=>'ok','data'=>$lists),'JSON');
            }else{
                $this->ajaxReturn(array('status'=>'ok','data'=>'no data'),'JSON');
            }

        }

        $where['category_id']=40;
        $adlists    = $articleModel->lists($page,$pagesize,$where);

        $this->assign('lists',$lists);//列表
        $this->assign('adlists',$adlists);//banner列表

        $this->display('index_2');
    }

}