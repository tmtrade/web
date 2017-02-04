<?

/**
 * 交易商标
 *
 * 交易商标
 *
 * @package    Module
 * @author     Xuni
 * @since      2015-11-05
 */
class FaqModule extends AppModule
{
    public $area        = array();
    public $type        = array();
    public $class       = array();
    public $label       = array();
    public $source      = array();
    public $status      = array();
    public $offPrice    = array();
    public $saletype    = array();
    public $platform    = array();
    public $sblength    = array();

    /**
     * 引用业务模型
     */
    public $models = array(
        'sale'          => 'sale',
        'tminfo'        => 'saleTminfo',
        'member'        => 'member',
        'proposer'      => 'proposer',
        'saletrademark' => 'saletrademark',
        'group'         => 'group',
    );


    //调用对应接口
    public function getList($array, $act)
    {
        foreach ($array as $k => $v) {
            $len        = mb_strlen($v['title'], 'utf-8');
            $thumtitle  = $len > 20 ? mb_substr($v['title'], 0, 20, 'utf-8') . '...' : $v['title'];
//			$list[$k]['url'] 				= '/faq/views/?id='.$v['id'].'&act='.$act.'&c='.$v['categoryId'];
            $list[ $k ]['url']          = '/v-' . $v['categoryId'] . '-' . $v['id'] . '/';
            $list[ $k ]['thumtitle']    = $thumtitle;
            $list[ $k ]['title']        = $v['title'];
            $list[ $k ]['img']          = $v['attachmentId'];
            $list[ $k ]['content']      = $v['content'];
            $list[ $k ]['title']        = $v['title'];
            $list[ $k ]['keyword']      = $v['keyword'];
            $list[ $k ]['introduction'] = $v['introduction'];
            $list[ $k ]['id']           = $v['id'];
            $list[ $k ]['time']         = $v['updated'];//发布时间->更新时间
            if ($v['label']) {
                $list[ $k ]['label']    = explode(',', $v['label']);//处理标签
            }
        }

        return $list;
    }


    //得到栏目对应的文章
    public function newsList($param)
    {
        $c      = isset($param['c']) ? $param['c'] : 0;
        $id     = isset($param['id']) ? $param['id'] : 0;
        $limit  = isset($param['limit']) ? $param['limit'] : 20;
        $minId  = isset($param['minId']) ? $param['minId'] : 0;
        $maxId  = isset($param['maxId']) ? $param['maxId'] : 0;
        $page   = isset($param['page']) ? $param['page'] : 0;

        $list   = array();
        $param  = array(
            'maxId' => $maxId,
            'minId' => $minId,
            'limit' => $limit,
            'order' => array('showOrder' => 'DESC'),
            'page'  => $page,
        );
        if (!empty($c)) {
            $param['categoryId'] = $c;
        }
        if (!empty($id)) {
            $param['id'] = $id;
        }
        //调用接口
        $data = $this->importBi('faq')->getNewsList($param);

        if (!empty($data['rows'])) {
            $list = $this->getList($data['rows'], 'cfdt');
        }

        return $list;
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
     * 得到3条文章
     *
     * @param $param
     *
     * @return mixed
     */
    public function getNextThree($param)
    {
        $c      = isset($param['c']) ? $param['c'] : 0;
        $id     = isset($param['id']) ? $param['id'] : 0;
        $limit  = 1000000;
        $minId  = isset($param['minId']) ? $param['minId'] : 0;
        $maxId  = isset($param['maxId']) ? $param['maxId'] : 0;
        $page   = isset($param['page']) ? $param['page'] : 0;

        $list   = array();
        $param  = array(
            'maxId' => $maxId,
            'minId' => $minId,
            'limit' => $limit,
            'order' => array('showOrder' => 'DESC'),
            'page'  => $page,
        );
        if (!empty($c)) {
            $param['categoryId'] = $c;
        }
        //调用接口
        $data   = $this->importBi('faq')->getNewsList($param);
        //查找该id对应的键. 得到前后两个信息
        $data   = $data['rows'];
        $res    = arrayColumn($data, 'id');
        $res    = array_search($id, $res);
        $count  = count($data);
        if ($res === false) {
            $result = array();
        } elseif ($res == 0) {
            $result = array(
                array(),
                $data[ $res ],
                $data[ $res + 1 ],
            );
        } elseif ($res + 1 > $count) {
            $result = array(
                $data[ $res - 1 ],
                $data[ $res ],
                array(),
            );
        } else {
            $result = array(
                $data[ $res - 1 ],
                $data[ $res ],
                $data[ $res + 1 ],
            );
        }
        //处理结果
        if (!empty($result)) {
            $list = $this->getList($result, 'cfdt');
        }

        return $list;
    }

    /**
     * 得到出新闻和问答外的其他faq
     *
     * @param $num
     *
     * @return array
     */
    public function getOther($num)
    {
        $param = array(
            'limit' => 50,
            'order' => array('updated' => 'DESC'),
        );
        //调用接口
        $data = $this->importBi('faq')->getNewsList($param);
        $list = array();
        if (!empty($data['rows'])) {
            //处理数据
            foreach ($data['rows'] as $k => $v) {
                //非新闻和问答
                if (in_array($v['categoryId'], array(51, 52, 53))) {
                    $temp               = array();
                    $temp['url']        = '/v-' . $v['categoryId'] . '-' . $v['id'] . '/';
                    $temp['thumtitle']  = mbSub($v['title'], 0, 16);;
                    $temp['title']      = $v['title'];
                    $temp['time']       = $v['updated'];
                    $temp['type']       = $v['categoryId'] == 51 ? '商标法律' : ($v['categoryId'] == 52 ? '求购信息' : '交易百科');
                    $temp['type_url']   = '/n-' . $v['categoryId'] . '/';
                    $list[] = $temp;
                    if (count($list) >= $num) {
                        break;
                    }
                }
            }
        }

        return $list;
    }

}

?>