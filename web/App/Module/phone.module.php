<?
/**
* 联系电话列表
*
* 联系电话列表创建，修改，删除等
*
* @package	Module
* @author	Xuni
* @since	2015-12-30
*/
class PhoneModule extends AppModule
{
    public $models = array(
        'phone'     => 'phone',
    );

    //获取所有电话号码，可以排除某一个
    public function getAllPhone($not='')
    {
        $r['limit'] = 10000;
        $r['col']   = array('phone');
        $r['order'] = array('date'=>'desc');
        if ( !empty($not) ) $r['raw'] = " phone != '".$not."' ";
        $res = $this->import('phone')->find($r);

        if ( empty($res) ) return array();
        $list = arrayColumn($res, 'phone');
        return $list;
    }

    //随机获取一个电话号码
    public function getRandPhone($not)
    {
        $list = $this->getAllPhone($not);
        if ( empty($list) ) return '0';
        $randKey = array_rand($list, 1);
        return $list[$randKey];
    }

    public function getSexPhone()
    {
        $r['limit'] = 6;
        $r['col']   = array('phone');
        $r['order'] = array('date'=>'asc');
        $res = $this->import('phone')->find($r);

        if ( empty($res) ) return array();
        $list = arrayColumn($res, 'phone');
        return $list;
    }

}
?>