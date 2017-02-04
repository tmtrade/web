<?
/**
 * 成功案列
 * 
 * @package	Module
 * @author	Far
 * @since	2016-03-21
 */
class CaseModule extends AppModule
{
	
    /**
     * 引用业务模型
     */
    public $models = array(
        'case'			=> 'case',
    );
	

    //案列列表
    public function getCaseList($id=0, $page, $limit=20,$order=array('sort'=>'asc'))
    {
        $r = array();
        if($id>0){
             $r['notIn']  = array('id' => array($id));
        }
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['order'] = $order;
        $res = $this->import('case')->findAll($r);
        return $res;
    }
    
    //模块基础内容
    public function getCaseInfo($id)
    {
        $r = array();
        $r['eq']['id']  = $id;
        $r['limit'] = 1;
        $res = $this->import('case')->find($r);
        return $res;
    }
}
?>