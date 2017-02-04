<?
/**
 * 商品查询
 *
 * 商品查询
 *
 * @package	Bi
 * @author	void
 * @since	2016-03-14
 */
class GoodsBi extends Bi
{
	/**
	 * 接口标识
	 */
	public $apiId = 3;

	/**
	 * 商品查询
	 *
	 * @author	void
	 * @since	2016-03-14
	 *
	 * @access	public
	 * @param	string	$groupGoodsCode	群组、商品编码【多个用逗号分隔】
	 * @param	int		$num			每页多少条
	 * @param	int		$page			当前页码
	 *
	 * @return	array
	 */
	public function search($groupGoodsCode, $num, $page)
	{
		$param = array(
			'groupGoodsCode' => $groupGoodsCode,
			'num'            => $num,
			'page'           => $page,
			);

		$data = $this->invoke("Goods/search/", $param);

		return array(
			'total'     => isset($data['total']) ? $data['total'] : 0,
			'rows'      => isset($data['rows'])  ? $data['rows']  : array(),
			'groupName' => isset($data['groupName'])  ? $data['groupName']  : array(),
			'goodsName' => isset($data['goodsName'])  ? $data['goodsName']  : array(),
			'className' => isset($data['className'])  ? $data['className']  : '',
			);
	}
}
?>