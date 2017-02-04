<?php

/**
 * 商标打包
 */
class PackModule extends AppModule
{

    public $class = null;
    /**
	* 引用业务模型
	*/
	public $models = array(
        'package'      => 'package',
        'package_items'      => 'packageitems',
        'tmclass'            => 'tmclass',
	);

    /**
     * 得到指定商标的打包信息
     * @param $number string 商标号
     * @return array
     */
    public function getPackInfo($number){
        $r = array(
            'limit'=>1,
            'eq'=>array('number'=>$number),
            'col'=>array('pkgId'),
        );
        $rst = $this->import('package_items')->find($r);
        if($rst){
            //得到pack信息
           $is_all = $this->import('package')->get($rst['pkgId'],'isAll');
            return array('url'=>'/package/?id='.$rst['pkgId'],'isAll'=>$is_all);
        }
        return array();
    }

    /**
     * 得到打包数据
     * @param $id
     * @return array
     */
    public function getAll($id){
        if(!$id){
            return array();
        }
        //打包信息
        $r = array(
            'eq'=>array('id'=>$id),
        );
        $rst = $this->import('package')->find($r);
        if(!$rst){
            return array();
        }
        //详情信息
        $r = array(
            'limit'=>100,
            'order'=>array('sort'=>'asc'),
            'eq'=>array('pkgId'=>$id),
            'col'=>array('number'),
        );
        $rst2 = $this->import('package_items')->find($r);
        if($rst2){
            $count = count($rst2);
            $rst['count'] = $count;
            //得到描述
            $rst['desc'] = preg_replace('/src="/','src="'.TRADE_URL,htmlspecialchars_decode($rst['desc']));//添加上域名
            //处理数据
            $rst['proName'] = '';
            $data = array();
            $ii = 1;
            foreach($rst2 as $k=>$v){
                //得到其他信息
                $temp = $this->load('trademark')->getTmInfo($v['number']);
                if($temp){
                    $class = array_shift($temp['class']);
                    //注册日期
                    $reg_date = $temp['reg_date'];
                    $url = '/d-'.$temp['tid'].'-'.$class.'.html';
                    $name = $temp['name'];
                    //得到申请人
                    if($k==0){
                        $rst['proName'] = $temp['proName'];
                    }
                    //得到商标图片
                    $saleId = $this->load('internal')->existSale($v['number']);//是否出售
                    if($saleId){
                        $img = $this->load('internal')->getViewImg($saleId);
                        $key = ceil($ii/12);
                        ++$ii;
                        if($count>5){
                            $data[$key][] = array(
                                'name'=>$name,
                                'reg_date'=>$reg_date,
                                'class'=>$class,
                                'url'=>$url,
                                'img'=>$img,
                            );
                        }else{
                            $data[] = array(
                                'name'=>$name,
                                'reg_date'=>$reg_date,
                                'class'=>$class,
                                'url'=>$url,
                                'img'=>$img,
                            );
                        }
                    }
                }
            }
            $rst['items'] = $data;
        }
        return $rst;
    }

    /**
     * 得到商标的分类名
     * @param $class
     * @return mixed
     */
    private function getClassName($class){
        $all_class = $this->class;
        if(!$all_class){
            $r['eq']    = array('parent' => "0");
            $r['limit'] = 45;
            $r['col'] = array('number','name');
            $r['order'] = array('sort' => 'asc');
            $res = $this->import('tmclass')->find($r);
            //转换为值对应名的数组
            $values = arrayColumn($res,'name');
            $keys = arrayColumn($res,'number');
            $all_class = array_combine($keys,$values);
            if($all_class){
                $this->class = $all_class;
            }
        }
        return $all_class[$class];
    }
}