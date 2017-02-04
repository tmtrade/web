<?
/**
 * FAQ wm.chaofan.wang
 * 
 * @access	public
 * @package bi
 * @author	Xuni
 * @since	2016-03-17
 */
class FaqBi extends Bi
{
	/**
	 * 对外接口域名编号
	 */
	public $apiId = 6;

    //获取文章列表
    public function getNewsList($params)
    {
    	if ( empty($params) || !is_array($params) ) return array();
        
		return $this->request("article/getArticleList/", $params,100);
    }


}
?>