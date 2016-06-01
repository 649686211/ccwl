<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/28
 * Time: 22:14
 * 物流信息模型
 */

namespace Home\Model;


use Think\Model\RelationModel;

class ArticleModel extends RelationModel {

    protected  $trueTableName='onethink_document';

    protected $_link=array(

        //基础数据
        'article_' => array(
            'mapping_type'  =>  self::HAS_ONE,
            'class_name'    =>  'document_article',
            'foreign_key'   =>  'id',
            'as_fields'     =>  'price,enrollcount,peoples,recruitstatus,thumb',
            'mapping_fields'=>  'price,enrollcount,peoples,recruitstatus,thumb',
            'condition'     =>  '',
            'mapping_limit' =>  '',
            'mapping_order' =>  '',
            'parent_key'    =>  '',
        ),
        //附件关联表
        'thumb' => array(
            'mapping_type'  =>  self::BELONGS_TO,
            'class_name'    =>  'picture',
            'foreign_key'   =>  'thumb',
            'as_fields'     =>  'path',
            'mapping_fields'=>  'path',
            'condition'     =>  '',
            'mapping_limit' =>  '',
            'mapping_order' =>  '',
            'parent_key'    =>  '',
        ),
    );


    /**
     * @param $page
     * @param int $pagesize
     * @param $where
     * @param $field
     * @param bool $relation
     * @return mixed
     */
    public function lists($page,$pagesize=15,$where=array('status'=>1),$field=true,$relation=true){
        $res=$this->field($field)->where($where)->relation($relation)->page($page,$pagesize)->select();
        if($res){
            //数据预格式化
            /*foreach($res as $key => $val){
                if($val['price']==0){
                    $res[$key]['price']='价格面议';
                }
            }*/
        }
        return $res;
    }


}