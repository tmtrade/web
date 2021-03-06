<?
/**
* 国内商标
*
* 国内商标商品创建，修改，删除等
*
* @package	Module
* @author	Xuni
* @since	2015-12-30
*/
class BlacklistModule extends AppModule
{
    public $models = array(
        'black'     => 'blacklist',
        'second'    => 'secondStatus',
    );

    //商标是否在黑名单中
    public function isBlack($number)
    {
        if ( empty($number) ) return false;

        $r['eq'] = array(
            'trademark_id' => $number
            );
        $r['raw'] = " `isShow` != 1 ";
        $res = $this->import('second')->setCache(false)->find($r);
        if ( empty($res) ) return false;
        return true;
    }

    //单条执行加入黑名单（无事务）
    public function setBlack($number)
    {
        if ( $this->isBlack($number) ) return true;

        $r['eq'] = array(
            'trademark_id' => $number
            );
        $data = array('isShow'=>2);
        $res = $this->import('second')->modify($data, $r);
        if ( $res ){
            $this->_addBlack($number);
            return true;
        }
        return false;
    }

    //添加到黑名单日志中
    protected function _addBlack($number)
    {
        $info = $this->load('trademark')->getTminfo($number);
        if ( empty($info) ) return false;
        $userId     = empty($this->userId) ?  0 :  $this->userId;
        $username   = empty($this->username) ? '系统' :  $this->username;
        $black = array(
            'tid'       => $info['tid'],
            'number'    => $number,
            'class'     => implode(',', $info['class']),
            'date'      => time(),
            'memberId'  => $userId,
            'memo'      => '操作员：《'.$username.'》将商标：《'.$info['name']."》加入到黑名单中",
            );
        $res = $this->import('black')->create($black);
        if ( $res ) return true;
        return false;
    }

}
?>