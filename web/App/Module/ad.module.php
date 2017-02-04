<?
/**
* 获取广告列表
* @package	Module
* @author	Far
* @since	2016-06-07
*/
class AdModule extends AppModule
{
    public $models = array(
        'ad'		=> 'ad',
    );
    
    /**
     * 获取页面位置的所有广告
     * @param int $pages
     * @param int $module
     * @param int $sort
     */
    public function getPagesList($pages){
	$d = date("d");
	if($d<10){
	    $start = strtotime(date("Y-m-10")."-1 month");//上月月10号
	    $end   = strtotime(date("Y-m-10"));
	}else{
	    $start = strtotime(date("Y-m-10"));
	    $end   = strtotime(date("Y-m-10")."+1 month");//下月10号
	}
	$r['col']		= array("pages","module","sort","alt","link","pic");
        $r['eq']['pages']       = $pages;
	$r['eq']['isUse']       = 1;
	$r['raw'] = " startdate >={$start} and enddate <={$end}";
        $r['order'] = array('sort' => 'asc');
        $r['limit'] = 36;
        $res = $this->import('ad')->find($r);
        return $res;
    }
}
?>