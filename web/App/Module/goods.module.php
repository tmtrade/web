<?
/**
 * 商品查询组件
 *
 * 商品查询
 * 
 * @package	Module
 * @author	void
 * @since	2016-03-14
 */
class GoodsModule extends AppModule
{

    /**
     * 引用业务模型
     */
    public $models = array(
        'second'        => 'secondStatus',
    );

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
		$res = $this->importBi('Goods')->search($groupGoodsCode, $num, $page);

		if ( empty($res['rows']) ) return $res;
		$numbers 		= arrayColumn($res['rows'], 'code');
		$list 			= $this->getListInfo($numbers);
		if ( empty($list) ) {
			$res['rows'] 	= array();
			$res['total']	= 0;
			return $res;
		}
		$res['rows'] 	= $this->load('search')->getListTips($list);
		return $res;
	}

	//获取商标信息
	public function getListInfo($data)
	{
		if ( empty($data) ) return array();
		$list = array();
		foreach ($data as $k => $number) {
			$sale = $this->load('sale')->getSaleInfo($number);
			if ( empty($sale) ){
				//----2016-07-25去掉其他数据，只使用一只蝉后台数据
				// $info = $this->load('trademark')->getTmInfo($number);
				// if ( empty($info) ) continue;
				// $list[$k] = array(
				// 	'tid' 		=> $info['tid'],
				// 	'name' 		=> $info['name'],
				// 	'number' 	=> $info['number'],
				// 	'class' 	=> implode(',', $info['class']),
				// 	'group' 	=> $info['group'],
				// 	);
			}else{
				$list[$k] = array(
					'id' 		=> $sale['id'],
					'tid' 		=> $sale['tid'],
					'name' 		=> $sale['name'],
					'number' 	=> $sale['number'],
					'class' 	=> $sale['class'],
					'group' 	=> $sale['group'],
					);
			}
		}
		return $list;
	}


}
?>