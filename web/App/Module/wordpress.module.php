<?
/**
* wordpress 得到的文章信息 模块
* @package	Module
* @author	dower
* @since	2016-11-23
*/
class WordpressModule extends AppModule
{
	private $seo_expire = 86400;//seo配置缓存1天
	private $list_num = 15;//每页的数目
	private $seo_suffix = '-一只蝉商标转让平台网';
	private $config = array(
		0=>array(
			'cate'=>1,
			'lable'=>12,
			'name'=>'商标新闻',
		),
		1=>array(
			'cate'=>0,
			'lable'=>14,
			'name'=>'商标法律',
		),
		2=>array(
			'cate'=>11,
			'lable'=>15,
			'name'=>'转让问答',
		),
		3=>array(
			'cate'=>0,
			'lable'=>180,
			'name'=>'求购信息',
		)
	);

    public $models = array(
        'options'=> 'options',
        'postmeta'=> 'postmeta',
        'posts'=> 'posts',
        'termRelationships'=> 'termRelationships',
        'termTaxonomy'=> 'termTaxonomy',
        'terms'=> 'terms',
		'sale' => 'sale',
    );

	/**
	 * 得到分类(wp中的标签)的具体seo设置
	 * @param $id
	 * @return array
	 */
	private function getTagSeo($id){
		//得到tag_seo信息
		$tag_seo = $this->com('redisHtml')->get('tag_seo');
		if(empty($tag_seo)){
			$r = array(
				'eq'=>array('option_name'=>'wpseo_taxonomy_meta'),
				'col'=>array('option_value')
			);
			$res = $this->import('options')->find($r);
			if(!$res) return array();
			$res = unserialize($res['option_value']);
			if(!$res) return array();
			$tag_seo = array();//得到标签的默认seo配置信息
			foreach($res['post_tag'] as $k=>$item){
				$tag_seo[$k] = array(
					'title'=>$this->subTag($item['wpseo_title']),
					'desc'=>$item['wpseo_desc'],
					'keywords'=>$item['wpseo_focuskw'],
				);
			}
			//缓存tagseo信息
			$this->com('redisHtml')->set('tag_seo', $tag_seo, $this->seo_expire);
		}
		//得到具体的信息
		return isset($tag_seo[$id])?$tag_seo[$id]:array();
	}

	/**
	 * 得到文章的具体SEO设置
	 * @param $id
	 * @return array
	 */
	private function getArticleSeo($id){
		//得到tag_seo信息
		$article_seo = $this->com('redisHtml')->get('article_seo'.$id);
		if(empty($article_seo)){
			//查询对应的文章是否设置seo
			$r = array(
				'eq'=>array('post_id'=>$id),
				'like'=>array('meta_key'=>'_yoast_wpseo'),
				'col'=>array('meta_key','meta_value'),
				'limit'=>15,
			);
			$res = $this->import('postmeta')->find($r);
			if(!$res) return array();
			//解析出seo设置
			$article_seo = array();
			foreach($res as $k=>$item){
				if($item['meta_key']=='_yoast_wpseo_title'){
					$article_seo['title'] = $this->subTag($item['meta_value']);
				}elseif($item['meta_key']=='_yoast_wpseo_metadesc'){
					$article_seo['desc'] = $item['meta_value'];
				}elseif($item['meta_key']=='_yoast_wpseo_focuskw'){
					$article_seo['keywords'] = $item['meta_value'];
				}
			}
			//article_seo
			$this->com('redisHtml')->set('article_seo'.$id, $article_seo, $this->seo_expire);
		}
		//得到具体的信息
		return $article_seo;
	}

	/**
	 * 得到文章列表信息
	 * @param $type int 文章类型
	 * @param $page int 分页
	 * @param $limit int 分页数量
	 * @param $flag bool 是否获得seo信息
	 * @return array
	 */
	public function getList($type,$page=1,$limit=0,$flag=true){
		if($page==0) $page=1;
		if($limit==0) $limit = $this->list_num;
		//得到文章的ids
		$conf = $this->config[$type];
		$res = $this->query($conf['cate'],$conf['lable'],$page,$limit);
		if(!$res['rows']) return $res;
		//得到文章的具体信息
		foreach($res['rows'] as &$item){
			$r = array(
				'eq'=>array('ID'=>$item['id']),
				'col'=>array('post_title as title','post_date as date'),
			);
			$rst = $this->import('posts')->find($r);
			if($rst){
				$item['date'] = strstr($rst['date'],' ',true);
				$item['title'] = $rst['title'];
			}else{
				unset($item);
			}
		}
		unset($item);
		$res['rows'] = $this->handleList($res['rows'],$type);
		//seo信息
		if($flag){
			$conf = $this->config[$type];
			$rst = $this->getTagSeo($conf['lable']);
			if($rst['title']){
				$rst['title'] .= '-p'.$page.$this->seo_suffix;
				$res['seo'] = $rst;
			}else{
				$part = $conf['name'].'-p'.$page.$this->seo_suffix;
				$res['seo'] = array(
					'title'=>$part,
					'desc'=>$part,
					'keywords'=>$part,
				);
			}
			return $res;
		}else{
			return $res['rows'];
		}
	}

	/**
	 * 得到文章详细信息
	 * @param $id int 文章id
	 * @param $cate int 分类id
	 * @return array
	 */
	public function getArticle($id,$cate){
		//得到文章的信息
		$r = array(
			'eq'=>array('ID'=>$id),
			'col'=>array('post_title as title','post_content as content','post_date as date'),
		);
		$res = $this->import('posts')->find($r);
		if(!$res) return array();
		//处理内容
		$res['content'] = nl2br($res['content']);
		//得到前后的文章信息
		$lable = $this->config[$cate]['lable'];//得到标签
		$cat = $this->config[$cate]['cate'];//得到标签
		$rst = $this->getPrevAndNext($cat,$lable,$id,$cate);
		//得到文章的seo信息
		$seo = $this->getArticleSeo($id);
		if(!$seo['title']){
			$part = $res['title'].$this->seo_suffix;
			$seo = array(
				'title'=>$part,
				'desc'=>$part,
				'keywords'=>$part,
			);
		}else{
			$seo['title'] .= $this->seo_suffix;
		}
		//返回数据
		return array($rst['prev'],$res,$rst['next'],$seo);
	}

	/**
	 * 得到出新闻和问答外的其他文章(即法律和求购信息)
	 * @param $num
	 * @return array
	 */
	public function getOther($num){
		//得到文章的ids
		$ids[] = $this->config[1]['lable'];
		$ids[] = $this->config[3]['lable'];
		$r = array(
			'in'=>array('term_taxonomy_id'=>$ids),
			'limit'=>$num,
			'order'=>array('object_id'=>'desc'),
			'col'=>array('object_id as id','term_taxonomy_id as cate'),
		);
		$res = $this->import('termRelationships')->findAll($r);
		if(!$res['total']) return $res;
		//得到文章的具体信息
		$conf = $this->config;
		$lable_conf = arrayColumn($conf,'lable');
		$name_conf = arrayColumn($conf,'name');
		foreach($res['rows'] as &$item){
			$r = array(
				'eq'=>array('ID'=>$item['id']),
				'col'=>array('post_title as title','post_date as date'),
			);
			$rst = $this->import('posts')->find($r);
			if($rst){
				$cate = array_search($item['cate'],$lable_conf);
				if($cate===false) $cate=0;
				$item['url'] = "/v-".$cate."-".$item['id'].'/';
				$item['date'] = strstr($rst['date'],' ',true);
				$item['title'] = $rst['title'];
				$item['thumtitle'] = mbSub($item['title'],0,16);
				//得到类型
				$item['type'] = $name_conf[$cate];
				$item['type_url'] = '/n-'.$cate.'/';
			}else{
				unset($item);
			}
		}
		unset($item);
		return $res['rows'];
	}

	/**
	 * 得到文章信息
	 * @param string $cate 分类
	 * @param string $label 标签
	 * @param $page int 页面
	 * @param $limit int 每页大小
	 * @return array
	 */
	public function query($cate,$label,$page,$limit){
		$page = ($page-1)*$limit;
		$slice = " ORDER BY a.object_id DESC LIMIT {$page},{$limit}";
		if($cate){
			$sql1 = "SELECT a.object_id as id FROM ( SELECT object_id FROM `wp_term_relationships` WHERE term_taxonomy_id = {$cate} ) AS a JOIN ( SELECT object_id FROM `wp_term_relationships` WHERE term_taxonomy_id = {$label} ) AS b ON a.object_id = b.object_id".$slice;
			$sql2 = "SELECT count(a.object_id) as count FROM ( SELECT object_id FROM `wp_term_relationships` WHERE term_taxonomy_id = {$cate} ) AS a JOIN ( SELECT object_id FROM `wp_term_relationships` WHERE term_taxonomy_id = {$label} ) AS b ON a.object_id = b.object_id";
		}else{
			$sql1 = "SELECT a.object_id as id FROM `wp_term_relationships` as a WHERE a.term_taxonomy_id = {$label}".$slice;
			$sql2 = "SELECT count(object_id) as count FROM `wp_term_relationships` WHERE term_taxonomy_id = {$label}";
		}
		//得到分页数据
		$rows = $this->import('termRelationships')->rawQuery($sql1);
		//得到总数
		$total = $this->import('termRelationships')->rawQuery($sql2);
		$total = isset($total[0])?$total[0]['count']:0;
		return array('total'=>$total,'rows'=>$rows);
	}

	/**
	 * 得到上一篇和下一篇的文章信息
	 * @param $cate
	 * @param $label
	 * @param $id
	 * @param $cat int 假的分类-0,1,2
	 * @return array
	 */
	public function getPrevAndNext($cate,$label,$id,$cat){
		if($cate){
			$sql1 = "SELECT a.object_id FROM ( SELECT object_id FROM `wp_term_relationships` WHERE term_taxonomy_id = {$cate} ) AS a JOIN ( SELECT object_id FROM `wp_term_relationships` WHERE term_taxonomy_id = {$label} ) AS b ON a.object_id = b.object_id WHERE a.object_id>{$id} ORDER BY a.object_id LIMIT 1";
			$sql2 = "SELECT a.object_id FROM ( SELECT object_id FROM `wp_term_relationships` WHERE term_taxonomy_id = {$cate} ) AS a JOIN ( SELECT object_id FROM `wp_term_relationships` WHERE term_taxonomy_id = {$label} ) AS b ON a.object_id = b.object_id WHERE a.object_id<{$id} ORDER BY a.object_id DESC LIMIT 1";
		}else{
			$sql1 = "SELECT a.object_id FROM `wp_term_relationships` as a WHERE a.term_taxonomy_id = {$label} AND a.object_id>{$id} ORDER BY a.object_id LIMIT 1";
			$sql2 = "SELECT a.object_id FROM `wp_term_relationships` as a WHERE a.term_taxonomy_id = {$label} AND a.object_id<{$id} ORDER BY a.object_id DESC LIMIT 1";
		}
		//得到上一篇文章
		$res = $this->import('termRelationships')->rawQuery($sql1);
		$prev = $this->handleData($res,$cat);
		//得到下一篇的文章
		$res = $this->import('termRelationships')->rawQuery($sql2);
		$next = $this->handleData($res,$cat);
		//返回结果
		return array('prev'=>$prev,'next'=>$next);
	}

	/**
	 * 得到销售中的商标
	 *
	 * @param $limit
	 *
	 * @return array
	 */
	public function getTm($limit)
	{
		$r['eq']    = array('status' => 1);
		$rand       = rand(0, 6666);
		$r['index'] = array($rand, $limit);

		$res = $this->import('sale')->find($r);
		$res = $this->load('search')->getListTips($res);//处理数据
		return $res;
	}

	/**
	 * 处理上一篇和下一片的数据
	 * @param $res
	 * @param $cate int 假的分类-0,1,2
	 * @return array
	 */
	private function handleData($res,$cate){
		if($res && isset($res[0])){
			$id = $res[0]['object_id'];
			$r = array(
				'eq'=>array('ID'=>$id),
				'col'=>array('ID as id','post_title as title'),
			);
			$rst = $this->import('posts')->find($r);
			if($rst){
				$rst['url'] = "/v-$cate-".$rst['id'].'/';
				$rst['thumtitle'] = mbSub($rst['title'],0,20);
				return $rst;
			}
		}
		return array();
	}

	/**
	 * 处理列表信息
	 * @param $rows
	 * @param $type int 假的分类-0,1,2
	 * @return array
	 */
	private function handleList($rows,$type){
		if(!is_array($rows)) return array();
		foreach($rows as &$item){
			$item['url'] = "/v-$type-".$item['id'].'/';
			$item['thumtitle'] = mbSub($item['title'],0,20);
		}
		unset($item);
		return $rows;
	}

	/**
	 * 处理wordpress的字符截取
	 * @param $str
	 * @param string $limiter
	 * @return string
	 */
	private function subTag($str,$limiter='%'){
		return strstr($str,$limiter,true);
	}

	/**
	 * 原始faq数据导入到wordpress中
	 */
	public function oldToWordpress(){
		set_time_limit(0);
		//导入新闻数据-50
		$rst1 = $this->getCatData(50,0);//111条
		if(!$rst1) echo "新闻数据失败!<br>";
		//导入法律数据-51
		$rst2 = $this->getCatData(51,1);//60条
		if(!$rst2) echo "法律数据失败!<br>";
		//商标转让问答-45
		$rst3 = $this->getCatData(45,2);//150条
		if(!$rst3) echo "商标转让问答!<br>";
		//商标求购信息-52
		$rst4 = $this->getCatData(52,3);//74条
		if(!$rst4) echo "商标求购信息!<br>";
		if($rst1 && $rst2 && $rst3 &$rst4){
			echo "导入数据成功!<br>";
		}
	}

	/**
	 * 导入数据
	 * @param $c
	 * @param $type
	 * @return bool
	 */
	private function getCatData($c,$type){
		$param  = array(
			'maxId' => 0,
			'minId' => 0,
			'limit' => 1,
			'order' => array('showOrder' => 'DESC'),
			'page' => 1,
			'categoryId' => $c,
		);
		//得到总数
		$data = $this->requestApi($param);

		if(!$data) return false;
		//分页获取数据
		$count = $data['total'];
		$limit = 10;
		$page_total = ceil($count/$limit);//总页数
		$page_total = intval($page_total);

		//得到分类和标签
		$conf = $this->config;
		$cate = $conf[$type]['cate'];
		$lable = $conf[$type]['lable'];
		$error = array();
		//循环获取数据
		for($i=1;$i<=$page_total;$i++){
			//获取分页数据
			$param  = array(
				'maxId' => 0,
				'minId' => 0,
				'limit' => $limit,
				'order' => array('showOrder' => 'ASC'),
				'page' => $i,
				'categoryId' => $c,
			);
			$temp = $this->requestApi($param);
			if($temp){
				//保存数据
				foreach($temp['rows'] as $item){
					//组装数据
					//文章数据
					$this->begin('posts');
					$d1 = array(
						'post_content'=>$item['content'],
						'post_title'=>$item['title'],
						'post_date'=>date('Y-m-d H:i:s',$item['updated']),
					);
					$id = $this->import('posts')->create($d1);
					if($id){
						//关联数据
						//标签信息
						$d2 = array(
							'object_id'=>$id,
							'term_taxonomy_id'=>$lable,
						);
						$rst = $this->import('termRelationships')->create($d2);
						if($rst){
							if($cate==0){
								$this->commit('posts');
								continue;
							}else{
								//添加分类信息
								$d3 = array(
									'object_id'=>$id,
									'term_taxonomy_id'=>$cate,
								);
								$rst = $this->import('termRelationships')->create($d3);
								if($rst){
									$this->commit('posts');
									continue;
								}
							}
						}
					}
					//保存失败的记录
					$error[] = array(
						'd1'=>$d1,
						'cate'=>$cate,
						'lable'=>$lable,
					);
					$this->rollback('posts');
				}
			}
		}
		//写入文件中
		if($error){
			file_put_contents('import_'.$c.'.txt',unserialize($error));//记录数据
		}
		//返回结果
		if($error){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * 调用接口,重试5次,中间休息4s
	 * @param $param
	 * @return array
	 */
	private function requestApi($param){
		//请求失败,重试4次
		$data = array();
		$j = 0;
		for($i=1;$i<=5;++$i){
			$data = $this->importBi('faq')->getNewsList($param);
			if($data){
				break;
			}
			++$j;
			sleep(4);//睡眠4s后重试
		}
		if($j==5){
			echo $param['categoryId'].'-'.$param['page'].'|请求接口失败<br/>';
		}
		return $data;
	}
}
?>