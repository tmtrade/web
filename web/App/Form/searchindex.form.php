<?
/**
 * 应用公用表单组件
 *
 * 表单数据收集
 *
 * @package	Form
 * @author	Xuni
 * @since	2015-11-11
 */
class SearchIndexForm extends AppForm
{
	
	/**
     * 字段映射(建立表单字段与程序字段或数据表字段的关联)
     */
    protected $map = array(
        'kw' 	=> array( 'field' => 'kw', 'method' => 'fieldString', ),
        'c'    	=> array( 'field' => 'c', 'method' => 'fieldInt', ),
        'g'   	=> array( 'field' => 'g', 'method' => 'fieldString', ),
        'p'     => array( 'field' => 'p', 'method' => 'fieldInt', ),
        't'     => array( 'field' => 't', 'method' => 'fieldInt', ),
        'sn'    => array( 'field' => 'sn', 'method' => 'fieldInt', ),
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
        return empty($value) ? '' : urldecode(htmlspecialchars(trim($value)));
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