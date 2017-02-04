<?php
/**
 * 网站地图module
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/4/26 0026
 * Time: 下午 18:52
 */
class SitemapModule extends AppModule{
    /**
     * 引用业务模型
     */
    public $models = array(
        'topic'		    => 'topic',
        'sale'          => 'sale',
        'group'         => 'group',
        'pt'            => 'patent',
    );

    public $maxSize = 40000;//xml文件中最大的网址数

    /**
     * 得到专题数据
     */
    public function getTopic(){
        //查询所有的专题数据
        $r['col'] = array('id','type','link','updated');
        $r['limit'] = 10000;
        $rst = $this->import('topic')->find($r);
        //处理数据
        $data = array();
        foreach($rst as $item){
            $ii = array();
            if($item['type']==2){
                $ii['url'] = $item['link'];
            }else{
                $ii['url'] = '/zhuanti/view/?id='.$item['id'];
            }
            $ii['time'] = $item['updated'];
            $data[] = $ii;
        }
        return $data;
    }

    /**
     * 得到商标详情页的数据
     * @return array
     */
    public function getDetail(){
        $count = $this->import('sale')->count();
        $pageSize = floor($count/($this->maxSize));
        $dataB = array();
        $r['col'] = array('tid','class','updated');
        for($i=0;$i<=$pageSize;++$i){
            $dataS = array();
            //查询数据
            $r['index'] = array($i*($this->maxSize),$this->maxSize);
            $tmp = $this->import('sale')->find($r);
            //组装数据
            foreach($tmp as $v){
                $ii = array();
                $class = explode(',',$v['class']);
                $ii['url'] = '/d-'.$v['tid'].'-'.$class[0].'.html';
                $ii['time'] = $v['updated'];
                $dataS[] = $ii;
            }
            $dataB[] = $dataS;
        }
        return $dataB;
    }

    /**
     * 得到新闻相关栏目信息(写死)
     */
    public function getNews(){
        $data = array(
            array('url'=>'/about/'),
            array('url'=>'/case/'),
            array('url'=>'/ryzz/'),
            array('url'=>'/lxwm/'),
            array('url'=>'/v-54-987/'),
            array('url'=>'/v-54-988/'),
            array('url'=>'/v-54-989/'),
            array('url'=>'/v-54-990/'),
            array('url'=>'/n-50/'),
            array('url'=>'/n-51/'),
            array('url'=>'/n-45/'),
            array('url'=>'/n-53/'),
            array('url'=>'/n-52/'),
        );
        return $data;
    }
    /**
     * 得到分类数据(写死)
     */
    public function getFenlei(){
        $data = array();
        for($i=1;$i<=45;++$i){
            $data[] = array('url'=>'/s-c-'.$i.'/');
        }
        return $data;
    }

    /**
     * 得到群组数据
     */
    public function getGroup(){
        $r['col'] = array('class','group');
        $r['limit'] = 10000;
        $rst = $this->import('group')->find($r);
        //处理数据
        $data = array();
        foreach($rst as $item){
            $data[] = array('url'=>'/s-c-'.$item['class'].'--g-'.$item['group'].'/');
        }
        return $data;
    }

    /**
     * 得到专利类型数据(从配置文件获得)
     */
    public function getPtType(){
        //得到配置文件
        $ptType     = C('PATENT_TYPE');
        $data = array();
        foreach($ptType as $k=>$v){
            $data[] = array('url'=>'/pt/?t='.$k);
        }
        return $data;
    }

    /**
     * 得到专利类型分类数据(从配置文件获得)
     */
    public function getPtFenLei(){
        //得到配置文件
        $ptType     = C('PATENT_TYPE');
        $ptOne      = C('PATENT_ClASS_ONE');
        $ptTwo      = C('PATENT_ClASS_TWO');
        $data = array();
        foreach($ptType as $k=>$v){
            if($k==3){
                foreach($ptTwo as $k1=>$v1){
                    $data[] = array('url'=>'/pt/?t='.$k.'&amp;c='.$k1);
                }
            }else{
                foreach($ptOne as $k2=>$v2){
                    $data[] = array('url'=>'/pt/?t='.$k.'&amp;c='.$k2);
                }
            }
        }
        return $data;
    }

    /**
     * 得到专利详情页的数据
     * @return array
     */
    public function getPtDetail(){
        $count = $this->import('pt')->count();
        $pageSize = floor($count/($this->maxSize));
        $dataB = array();
        $r['col'] = array('number','updated');
        for($i=0;$i<=$pageSize;++$i){
            $dataS = array();
            //查询数据
            $r['index'] = array($i*($this->maxSize),$this->maxSize);
            $tmp = $this->import('pt')->find($r);
            //组装数据
            foreach($tmp as $v){
                $ii = array();
                $ii['url'] = '/pt-'.$v['number'].'.html';
                $ii['time'] = $v['updated'];
                $dataS[] = $ii;
            }
            $dataB[] = $dataS;
        }
        return $dataB;
    }
}