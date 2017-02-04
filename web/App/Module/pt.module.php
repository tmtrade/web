<?
/**
 * 快速筛选功能组件
 *
 * 快速筛选出商标信息，主要从出售信息列表中搜索数据，次要从商标库里搜索数据
 * 
 * @package Module
 * @author  Xuni
 * @since   2015-11-09
 */
class PtModule extends AppModule
{
    /**
     * 引用业务模型
     */
    public $models = array(
        'pt'          => 'patent',
    );
    
    /**
     * 获取出售信息相关数据
     * 
     * @author  Xuni
     * @since   2016-03-04
     *
     * @access  public
     * @param   int     $class      国际分类(1-45)
     *
     * @return  array   $list       群组号对应群组中文名称的数组
     */
    public function getPtList($params, $page=1, $limit=30, $col=array())
    {
        $result = array(
            'rows'  => array(),
            'total' => 0,
            );
        $r['raw'] = ' 1 ';

        //分类
        if ( !empty($params['type']) ){
            if ( is_array($params['type']) ){
                $r['in']['type'] = $params['type'];
            }else{
                $r['eq']['type'] = $params['type'];

                if ( !empty($params['class'])){
                    $_class = explode(',', $params['class']);
                    if($params['type'] != 3){
                        $r['ft']['class'] = implode(',', array_map('ord', $_class));
                    }else{
                        $r['ft']['class'] = implode(',', $_class);
                    }
                }
            }
        }
        //判断搜索条件
        if($params['keyword']){
            if($params['keytype']==1){
                $r['like'] = array('title'=>$params['keyword']);
            }else{
                $r['like'] = array('number'=>$params['keyword']);
            }
        }
        $r['eq']['status']  = 1;
        $r['eq']['isSale']  = 1;

        if ( empty($col) ){
            $r['col']   = array('id', 'number', 'code', 'class', 'type', 'title', 'price');
        }else{
            $r['col']   = $col;
        }
        $r['page']      = $page;
        $r['limit']     = $limit;

        $r['order']     = array('isTop' => 'desc');
        $res            = $this->import('pt')->findAll($r);
        $res['rows']    = $this->getListTips($res['rows']);

        return $res;
    }

    /**
     * 处理列表数据
     *
     * @author  Xuni
     * @since   2016-03-04
     *
     * @return  array
     */
    public function getListTips($data)
    {
        if ( empty($data) ) return array();
        if ( !is_array(current($data)) ){
            $_tmp = array($data);
        }else{
            $_tmp = $data;
        }
        // if ( !empty($this->loginId) ){
        //     $numbers    = arrayColumn($_tmp, 'number');
        //     $lookList   = $this->load('usercenter')->existLook($numbers);
        // }        
        foreach ($_tmp as $k => $v) {
            // if ( isset($lookList[$v['number']]) ){
            //     $v['isLook'] = $lookList[$v['number']];                
            // }else{
            //     $v['isLook'] = 2;
            // }
            $data[$k] = $this->getTips($v);
        }
        return $data;
    }

    /**
     * 处理列表数据
     *
     * @author  Xuni
     * @since   2016-03-04
     *
     * @return  array
     */
    public function getTips($data, $img=true)
    {
        $ptType     = C('PATENT_TYPE');
        $ptOne      = C('PATENT_ClASS_ONE');
        $ptTwo      = C('PATENT_ClASS_TWO');

        $_class     = array_filter( array_unique( explode(',', $data['class']) ) );
        $_classArr  = $_className = array();
        foreach ($_class as $k => $v) {
            if ( in_array($data['type'], array(1, 2)) ) {
                array_push($_classArr, chr($v));
                if ( isset($ptOne[chr($v)]) ) array_push($_className, $ptOne[chr($v)]);
            }else{
                array_push($_classArr, $v);
                if ( isset($ptTwo[$v]) ) array_push($_className, $ptTwo[$v]);
            }
        }

        $data['class']      = implode(',', $_classArr);
        $data['typeName']   = $ptType[$data['type']];
        $data['className']  = implode(',', $_className);
        if ($img) $data['imgUrl']     = $this->load('pdetail')->getPTImg($data['number']);

        return $data;
    }

    /**
     * 判断专利是否销售中
     * @author dower
     * @param $number
     * @return bool
     */
    public function isSale($number){
        $r['eq'] = array('number'=>$number);
        $r['col'] = array('status');
        $result = $this->import('pt')->find($r);
        if($result && $result['status']==1){
            return true;
        }
        return false;
    }
    
    /**
     * 根据专利得到sale信息(分类第一个)
     * @author dower
     * @param $number
     * @return mixed
     */
    public function getPatentInfoByNumber($number,$col = array('id', 'number', 'code', 'class', 'type', 'title', 'price')){
        $r['eq'] = array('number'=>$number);
        $r['col'] = $col;
        $rst = $this->import('pt')->find($r);
        return $rst;
    }

}
?>