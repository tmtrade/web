<?php
/**
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/3/3 0003
 * Time: 下午 15:55
 */
class SaleModule extends AppModule{
    public $models = array(
        'saleTminfo'		=> 'SaleTminfo',
        'sale'		=> 'Sale',
        'class' => 'Tmclass',
    );

    /**
     * 根据商标得到sale信息(分类第一个)
     * @author dower
     * @param $number
     * @return mixed
     */
    public function getSaleInfoByNumber($number,$col = array('tid','class','name')){
        //得到商标的第一个分类
        $r['eq'] = array('number'=>$number);
        $r['col'] = $col;
        $rst = $this->import('sale')->find($r);
        $rst['className'] = $this->import('class')->get(array_pop(explode(',',$rst['class'])),'name');
        //返回商标的分类名
        return $rst;
    }

    /**
     * 根据商标得到tminfo
     * @author dower
     * @param $number
     * @param string $field
     * @return array|bool
     */
    public function getSaleTmByNumber($number){
        $r['eq'] = array('number'=>$number);
        $r['col']   = array('embellish');
        $info = $this->import('saleTminfo')->find($r);
        if($info==false){
            return false;
        }
        //返回包装数据
        if($info['embellish']){
            return TRADE_URL.$info['embellish'];
        }

        return $this->load('trademark')->getImg($number);
    }

    /**
     * 根据商标号得到商标的包装信息(无美化图,获得商标图)
     * @param $number
     * @return array|bool
     * @throws SpringException
     */
    public function getSaltTminfoByNumber($number){
        $r['eq'] = array('number'=>$number);
        $info = $this->import('saleTminfo')->find($r);
        //无销售数据
        if($info==false){
            $info = array();
            $info['alt1'] = '';
            $info['embellish'] = $this->load('trademark')->getImg($number);
            return $info;
        }
        //返回包装数据
        if($info['embellish']){
            $info['embellish'] = TRADE_URL.$info['embellish'];
        }else{
            $info['embellish'] = $this->load('trademark')->getImg($number);
        }

        return $info;
    }

    /**
     * 获得商标的图片alt描述
     * @param $number
     * @return string
     */
    public function getAlt($number){
        $r['eq']['number'] = $number;
        $r['col'] = array('alt1');
        $rst = $this->import('saleTminfo')->find($r);
        if($rst){
            return $rst['alt1'];
        }
        return '';
    }
    /**
     * 判断商标是否销售中
     * @author dower
     * @param $number
     * @return bool
     */
    public function isSale($number){
        $r['eq'] = array('number'=>$number);
        $r['col'] = array('status');
        $result = $this->import('sale')->find($r);
        if($result && $result['status']==1){
            return true;
        }
        return false;
    }
    
    //获取商品信息
    public function getSaleInfo($number,$col = array())
    {
        $arr['eq'] = array(
            'number' => $number,
            );
        if(!empty($col)){
            $arr['col'] = $col;
        }
        $info = $this->import('sale')->find($arr);
        if ( empty($info) ) return array();
        return $info;
    }
    
    //递增点击率
    public function click($number)
    {
        $record = array(
                'hits' => array('hits', 1),
            );
        $r['eq']['number']    = $number;
        return $this->import('sale')->modify($record,$r);
    }
}