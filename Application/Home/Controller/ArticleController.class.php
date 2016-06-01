<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class ArticleController extends HomeController {

    /* 文档模型频道页 */
	public function index(){
		/* 分类信息 */
		$category = $this->category();

		//频道页只显示模板，默认不读取任何内容
		//内容可以通过模板标签自行定制

		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		$this->display($category['template_index']);
	}

	/* 文档模型列表页 */
	public function lists($p = 1){
		/* 分类信息 */
		$category = $this->category();

		/* 获取当前分类列表 */
		$Document = D('Document');
		$list = $Document->page($p, $category['list_row'])->lists($category['id']);
		if(false === $list){
			$this->error('获取列表数据失败！');
		}

		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		$this->assign('list', $list);
		$this->display($category['template_lists']);
	}

	/* 文档模型详情页 */
	public function detail($id = 0, $p = 1){
		/* 标识正确性检测 */
		if(!($id && is_numeric($id))){
			$this->error('文档ID错误！');
		}

		/* 页码检测 */
		$p = intval($p);
		$p = empty($p) ? 1 : $p;

		/* 获取详细信息 */
		$Document = D('Document');
		$info = $Document->detail($id);
		if(!$info){
			$this->error($Document->getError());
		}

		/* 分类信息 */
		$category = $this->category($info['category_id']);

		/* 获取模板 */
		if(!empty($info['template'])){//已定制模板
			$tmpl = $info['template'];
		} elseif (!empty($category['template_detail'])){ //分类已定制模板
			$tmpl = $category['template_detail'];
		} else { //使用默认模板
			$tmpl = 'Article/'. get_document_model($info['model_id'],'name') .'/detail';
		}

		/* 更新浏览数 */
		$map = array('id' => $id);
		$Document->where($map)->setInc('view');

		/* 获取当前订单的banner图*/
        $ids="".$info['banner1'].','.$info['banner2'].','.$info['banner3']."";
        $picModel=M('picture');
        $picRs=$picModel->field('id,path')->where('id in ('.$ids.') ')->select();
        if($picRs){
            //print_r($picRs);
            $info['photos']=$picRs;
        }

		/* 获取当前订单的物流信息*/
		$enrollModel=M('enroll');
		$enrollRs=$enrollModel->where('recruit_id = '.$id)->select();

        //print_r($info);
        /* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		$this->assign('info', $info);
		$this->assign('enrollRs', $enrollRs);
		$this->assign('page', $p); //页码
		//$this->display($tmpl);
		$this->display('Article/article/detail_2');
	}

	/* 文档分类检测 */
	private function category($id = 0){
		/* 标识正确性检测 */
		$id = $id ? $id : I('get.category', 0);
		if(empty($id)){
			$this->error('没有指定文档分类！');
		}

		/* 获取分类信息 */
		$category = D('Category')->info($id);
		if($category && 1 == $category['status']){
			switch ($category['display']) {
				case 0:
					$this->error('该分类禁止显示！');
					break;
				//TODO: 更多分类显示状态判断
				default:
					return $category;
			}
		} else {
			$this->error('分类不存在或被禁用！');
		}
	}

    /**
     * 报名提交
     */
    public function submit(){
        //header("Content-type:text/html;charset=utf-8");
        //获取表单提交
        $name        = I('post.quickBuyName','','string');
        $mobile      = I('post.quickBuyMobile','','string');
        $platenumber = I('post.quickBuyCode','','string');
        $recruit_id  = I('post.recruit_id',0,'int');
        $creattime   = time();

        /*TODO: 会员登陆相关，暂时屏蔽
         $userinfo    = I('session.onethink_home',array());
        $user_id     = $userinfo['user_auth']['uid']; //获取UID
        if(!$user_id){
            redirect(U('User/login'));
        }*/
		$user_id=0;

        if(!$recruit_id || !$mobile || !$name || !$platenumber){
            $this->error('数据不完整，请重新提交！');
        }
        $artileModel=M('document_article'); //物流模型
        $arRs=$artileModel->where(array('id'=>$recruit_id))->find();
        if($arRs){
            //判断报名状态
            if($arRs['recruitstatus']!=0 || $arRs['peoples']==$arRs['enrollcount']){
                $this->ajaxReturn(array('status'=>'error','msg'=>'对不起，报名已经截止了！'),'JSON');
            }
        }

        $enrollModel=M('enroll'); //报名模型
        $enrollRS=$enrollModel->where(array('mobile'=>$mobile,'recruit_id'=>$recruit_id))->find();

        if($enrollRS){//判断是否报过名
			$this->ajaxReturn(array('status'=>'error','msg'=>'对不起，你已经报过名了！'),'JSON');
        }else{
            //提交报名信息
            $data      = array();
            $data['name']       = $name;
            $data['mobile']     = $mobile;
            $data['platenumber']= $platenumber;
            $data['recruit_id'] = $recruit_id;
            $data['creattime']  = $creattime;
            $data['user_id']    = $user_id;
            $id=$enrollModel->data($data)->add();
            if(0<$id){
                //人数加1
                $artileModel->where(array('id'=>$recruit_id))->setInc('enrollcount',1);
                //判断人数是否已经招满
                $res=$artileModel->where(array('id'=>$recruit_id))->find();
                if($res['peoples']==$res['enrollcount']){
                    //如果人数已经招满则更新状态
                    $artileModel->data(array('recruitstatus'=>1))->where(array('id'=>$recruit_id))->save();
                }
				$this->ajaxReturn(array('status'=>'ok','msg'=>'恭喜，报名成功！'),'JSON');

            }else{
				$this->ajaxReturn(array('status'=>'error','msg'=>'报名失败,请重试~'),'JSON');
            }
        }



    }
}
