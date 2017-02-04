<?
/**
 * 应用公用表单组件
 *
 * 表单数据收集
 *
 * @package	Form
 * @author	Far
 * @since	2016-05-11
 */
class PatentaddsellForm extends AppForm
{
	
	/**
     * 字段映射(建立表单字段与程序字段或数据表字段的关联)
     */
    protected $map = array(
        'number'  	=> array( 'field' => 'number', 'match' => array('', '', ''), ),
        'price'    	=> array( 'field' => 'price', 'match' => array('', '', ''), ),
        'contact'   => array( 'field' => 'contact', 'method' => 'fieldString', ),
        'phone'  	=> array( 'field' => 'phone', 'method' => 'fieldString', ),
    );

    /**
     * 处理字符串
 	 * @author	Xuni
 	 * @since	2015-11-11
     *
     * @access	public
     * @param	array	$value	字符串
     * @return	string
     */
    public function fieldString($value)
    {
        return empty($value) ? '' : htmlspecialchars(trim($value));
    }

    /**
     * 处理数字
 	 * @author	Xuni
 	 * @since	2015-11-11
     *
     * @access	public
     * @param	array	$value	字符串
     * @return	string
     */
    public function fieldInt($value)
    {
        return intval(trim($value));
    }
}
?>