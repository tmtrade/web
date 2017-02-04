<?
/**
 * 需求管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/11/22 0022
 * Time: 上午 10:31
 */
class RequireModule extends AppModule
{

    /**
     * 引用业务模型
     */
    public $models = array(
        'require' => 'require',
        'requirebid' => 'requirebid',
    );

    /**
     * 得到列表信息
     * @param $param
     * @return array
     */
    public function getList($param){
        //查询结果
        $r = array(
            'limit'=>$param['limit'],
            'page'=>$param['page'],
            'order'=>array('date'=>'desc'),
            'in'=>array('status'=>array(1,2))//能在前台显示的
        );
        if($param['keyword']){
            $r['like'] = array('desc'=>$param['keyword']);
        }
        $rst = $this->import('require')->findAll($r);
        //处理结果
        if($rst['rows']){
            foreach($rst['rows'] as &$item){
                //得到报价数量
                $s = array('eq'=>array('require_id'=>$item['id']));
                $item['count'] = $this->import('requirebid')->count($s);
                //处理手机号
                $item['m_mobile'] = preg_replace("/(\d{7})\d{3}(\d)?/",'$1***$2',$item['mobile']);
                //处理用户名
//                $len = mb_strlen($item['name'],'utf-8');
                $str = '**';
//                for($i=0;$i<$len;++$i){
//                    $str.='*';
//                }
                if($item['name']){
                    $item['m_name'] = mb_substr($item['name'],0,1,'utf-8').$str;
                }
            }
            unset($item);
        }
        //返回数据
        return $rst;
    }

    /**
     * 得到详情信息
     * @param $id
     * @return array
     */
    public function getView($id){
        //得到需求信息
        $r = array('eq'=>array('id'=>$id));
        $require = $this->import('require')->find($r);
        if(empty($require)) return false;
        //解析手机号码
        $require['m_mobile'] = preg_replace("/(\d{7})\d{3}(\d)?/",'$1***$2',$require['mobile']);
        //处理用户名
        if($require['name']){
//            $len = mb_strlen($require['name'],'utf-8');
            $str = '**';
//            for($i=0;$i<$len;++$i){
//                $str.='*';
//            }
            $require['m_name'] = mb_substr($require['name'],0,1,'utf-8').$str;
        }
        //得到需求的竞标信息
        $require['bids'] = array();
        $r = array(
            'eq'=>array('require_id'=>$id),
            'col'=> array('name','mobile','date','COUNT(*) as count'),
            'order'=>array('date'=>'asc'),
            'group'=> array('date'=>'desc'),
            'limit'=>10000
        );
        $rst = $this->import('requirebid')->find($r);
        //处理竞标信息
        if($rst){
            foreach($rst as &$item){
                //处理电话
                $item['m_mobile'] = preg_replace("/(\d{7})\d{3}(\d)?/",'$1***$2',$item['mobile']);
            }
            unset($item);
            $require['bids'] = $rst;
        }
        //返回结果
        return $require;
    }

    /**
     * 获得上一条与下一条需求
     * @param $id
     * @return array
     */
    public function getPrevAndNext($id){
        //得到前一个
        $r = array(
            'raw'=>' `id`>'.$id,
            'order'=>array('date'=>'desc'),
            'in'=>array('status'=>array(1,2)),
            'col'=>array('desc','id')
        );
        $rst = $this->import('require')->find($r);
        $prev = array();
        if($rst){
            $prev['titile'] = $rst['desc'];
            $prev['thum_titile'] = mbSub($rst['desc'],0,26);
            $prev['url'] = '/require/detail/?id='.$rst['id'];
        }
        //得到后一个
        $r = array(
            'raw'=>' `id`<'.$id,
            'order'=>array('date'=>'asc'),
            'in'=>array('status'=>array(1,2)),
            'col'=>array('desc','id')
        );
        $rst = $this->import('require')->find($r);
        $next = array();
        if($rst){
            $next['titile'] = $rst['desc'];
            $next['thum_titile'] = mbSub($rst['desc'],0,26);
            $next['url'] = '/require/detail/?id='.$rst['id'];
        }
        //返回结果
        return array('prev'=>$prev,'next'=>$next);
    }

    /**
     * 保存竞标信息
     * @param $data
     * @return array
     */
    public function addRequireBid($data){
        //保存竞标信息
        $number = $data['number'];
        $price = $data['price'];
        $data['date'] = time();
        unset($data['number']);
        unset($data['price']);
        //开启事务
        $this->begin('requirebid');
        $temp = $data;
        foreach($number as $k=>$item){
            $temp['number'] = $item;
            unset($temp['price']);
            if(isset($price[$k])){
                $temp['price'] = $price[$k];
            }
            $res = $this->import('requirebid')->create($temp);
            if(!$res){
                $this->rollBack('requirebid');
                return false;
            }
        }
        return $this->commit('requirebid');
    }

    /**
     * 得到指定数量的需求信息
     * @param $num
     * @return array
     */
    public function getRequire($num){
        $r = array(
            'eq'=>array('status'=>1),
            'order'=>array('date'=>'desc'),
            'limit'=>$num,
        );
        $res = $this->import('require')->find($r);
        if($res){
            //得到链接
            foreach($res as &$item){
                $item['url'] = "/require/detail/?id={$item['id']}";
                //加密名字和电话
                $item['m_mobile'] = preg_replace("/(\d{7})\d{3}(\d)?/",'$1***$2',$item['mobile']);
//                $len = mb_strlen($item['name'],'utf-8');
                $str = '**';
//                for($i=0;$i<$len;++$i){
//                    $str.='*';
//                }
                if($item['name']){
                    $item['m_name'] = mb_substr($item['name'],0,1,'utf-8').$str;
                }
            }
            unset($item);
            return $res;
        }else{
            return array();
        }
    }
}
?>