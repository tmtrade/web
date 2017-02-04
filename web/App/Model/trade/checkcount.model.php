<?
/**
* 工作日志
*
* 获取工作日志id分页列表、添加工作日志、编辑工作日志、删除工作日志、删除所有工作日志
*
* @package	Model
* @author	Jeany
* @since	2015-07-22
*/
class CheckcountModel extends AppModel
{
    /**
    * 点击数加1
    * @author	haydn
    * @since	2015-12-08
    * @access	public
    * @return	int     $id
    */
    public function clickAdd($data)
    {
        $id = $this->create($data);
        return $id;
    }
    /**
    * 获取商标号的点击数
    * @author   haydn
    * @since    2015-12-08
    * @access   public
    * @param    int     $number 商标号
    * @return   int     $count  总数
    */
    public function getCount($number)
    {
        $r['eq']    = array("number" => $number);
        $r['col']   = array('hits');
        $data       = $this->find($r);
        return $data['hits'];
    }
    /**
    * 修改点击数
    * @author   haydn
    * @since    2015-12-08
    * @access   public
    * @param    array     $data 值
    * @return   array     $r    条件
    */
    public function update($data,$r)
    {
        return $this->modify($data,$r);
    }
}
?>