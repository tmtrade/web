<?php
/**
 * 网站地图功能
 *
 * @package Action
 * @author  Dower
 * @since   2016-04-26
 */
class SitemapAction extends ConsoleAction{
    /**
     * xml头部文件
     * @var string
     */
    private $xmlHead = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";

    /**
     * 装载模板文件、解析变量,返回解析后的html
     *
     * @access	protected
     * @param	string	$file	文件名
     * @return	string
     */
    protected function fetch($file = null)
    {
        if ( $file )
        {
            return $this->com('view')->fetch($file);
        }
        else
        {
            $mod    = strtolower($this->mod);
            $action = strtolower($this->action);
            $file   = "{$mod}/{$mod}.{$action}.html";
            return $this->com('view')->fetch($file);
        }

    }

    /**
     * 设置模板变量
     *
     * @access	protected
     * @param	mixed	$key	键
     * @param	mixed	$value	值
     * @return	void
     */
    protected function set($key, $value)
    {
        $this->com('view')->set($key, $value);
    }

    /**
     * 生成所有的xml文件
     */
    public function index(){
        //清空目录
        $root = C('SITEMAP_ROOT');
        $files_all = glob($root.'/*');
        foreach($files_all as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        unset($file);
        //初始化变量
        $flag = true;
        $str = '';
        //生成商标详情数据
        $rst1 = $this->createDetail();
        if($rst1['code']==0){
            $flag = false;
            $str .= $rst1['msg'];
        }
        //生成商标分类数据
        $rst2 = $this->createFenlei();
        if($rst2['code']==0){
            $flag = false;
            $str .= $rst2['msg'];
        }
        //生成栏目数据
        $rst3 = $this->createLanmu();
        if($rst3['code']==0){
            $flag = false;
            $str .= $rst3['msg'];
        }
        //生成新闻详情数据
        $rst4 = $this->createNews();
        if($rst4['code']==0){
            $flag = false;
            $str .= $rst4['msg'];
        }
        //生成新闻详情数据
        $rst6 = $this->createGroup();
        if($rst6['code']==0){
            $flag = false;
            $str .= $rst6['msg'];
        }
        //生成专利分类数据
        $rst7 = $this->createType();
        if($rst7['code']==0){
            $flag = false;
            $str .= $rst7['msg'];
        }
        //生成专利类型数据
        $rst8 = $this->createPtFenLei();
        if($rst8['code']==0){
            $flag = false;
            $str .= $rst8['msg'];
        }
        //生成专利详情数据
        $rst9 = $this->createPtDetail();
        if($rst9['code']==0){
            $flag = false;
            $str .= $rst9['msg'];
        }
        //生成主文件
        $rst5 = $this->createMain();
        if($rst5['code']==0){
            $flag = false;
            $str .= $rst5['msg'];
        }
        //输出结果
        if($flag){
            echo 'success!';
        }else{
            echo 'error! --- '.$str;
        }
    }

    /**
     * 生成详情页xml
     * @throws SpringException
     */
    public function createDetail(){
        $root = C('SITEMAP_ROOT');
        $data = $this->load('sitemap')->getDetail();
        foreach($data as $k=>$v){
            ob_start();
            if($k==0){
                $k = '';
            }
            //获取输出内容,并写文件
            $this->set('data',$v);
            $this->set('level',0.6);
            $str = $this->fetch('sitemap/conten.html');
            $str = $this->xmlHead.$str;
            $rst = file_put_contents($root.'/goods'.$k.'.xml',$str);
            if($rst===false){
                return array('code'=>0,'msg'=>'tm_detail--error ');
            }
        }
        //返回结果
        return array('code'=>1);
    }

    /**
     * 生成详情页xml
     * @throws SpringException
     */
    public function createPtDetail(){
        $root = C('SITEMAP_ROOT');
        $data = $this->load('sitemap')->getPtDetail();
        foreach($data as $k=>$v){
            ob_start();
            if($k==0){
                $k = '';
            }
            //获取输出内容,并写文件
            $this->set('data',$v);
            $this->set('level',0.6);
            $str = $this->fetch('sitemap/conten.html');
            $str = $this->xmlHead.$str;
            $rst = file_put_contents($root.'/patents'.$k.'.xml',$str);
            if($rst===false){
                return array('code'=>0,'msg'=>'pt_detail--error ');
            }
        }
        //返回结果
        return array('code'=>1);
    }

    /**
     * 生成分类xml
     * @throws SpringException
     */
    public function createFenlei(){
        //取数据
        $data = $this->load('sitemap')->getFenlei();
        //获取输出内容,并写文件
        $this->set('data',$data);
        $this->set('level',0.8);
        $str = $this->fetch('sitemap/conten.html');
        $str = $this->xmlHead.$str;
        $rst = file_put_contents(C('SITEMAP_ROOT').'/fenlei.xml',$str);
        //返回结果
        if($rst===false){
            return array('code'=>0,'msg'=>'fenlei--error ');
        }
        return array('code'=>1);
    }

    /**
     * 生成专利类型xml
     * @throws SpringException
     */
    public function createType(){
        //取数据
        $data = $this->load('sitemap')->getPtType();
        //获取输出内容,并写文件
        $this->set('data',$data);
        $this->set('level',0.8);
        $str = $this->fetch('sitemap/conten.html');
        $str = $this->xmlHead.$str;
        $rst = file_put_contents(C('SITEMAP_ROOT').'/pt_type.xml',$str);
        //返回结果
        if($rst===false){
            return array('code'=>0,'msg'=>'pt_type--error ');
        }
        return array('code'=>1);
    }

    /**
     * 生成栏目xml
     * @throws SpringException
     */
    public function createLanmu(){
        //取数据
        $data1 = $this->load('sitemap')->getTopic();
        $data2 = $this->load('sitemap')->getNews();
        $data = array_merge($data1,$data2);
        //获取输出内容,并写文件
        $this->set('data',$data);
        $this->set('level',0.8);
        $str = $this->fetch('sitemap/conten.html');
        $str = $this->xmlHead.$str;
        $rst = file_put_contents(C('SITEMAP_ROOT').'/lanmu.xml',$str);
        //返回结果
        if($rst===false){
            return array('code'=>0,'msg'=>'lanmu--error ');
        }
        return array('code'=>1);
    }

    /**
     * 生成新闻内容xml
     * @throws SpringException
     */
    public function createNews(){
        //得到数据
        $data1 = $this->load('faq')->newsList(array('c'=>45,'limit'=>10000));
        $data2 = $this->load('faq')->newsList(array('c'=>50,'limit'=>10000));
        $data3 = $this->load('faq')->newsList(array('c'=>51,'limit'=>10000));
        $data4 = $this->load('faq')->newsList(array('c'=>52,'limit'=>10000));
        $data5 = $this->load('faq')->newsList(array('c'=>53,'limit'=>10000));
        $data6 = array_merge($data1,$data2,$data3,$data4,$data5);
        $data = array();
        foreach($data6 as $item){
            $data[] = array(
                'url'=>$item['url'],
                'time'=>date('Y-m-d H:i:s',$item['time']),
            );
        }
        //获取输出内容,并写文件
        $this->set('data',$data);
        $this->set('level',0.6);
        $str = $this->fetch('sitemap/conten.html');
        $str = $this->xmlHead.$str;
        $rst = file_put_contents(C('SITEMAP_ROOT').'/news.xml',$str);
        //返回结果
        if($rst===false){
            return array('code'=>0,'msg'=>'news--error ');
        }
        return array('code'=>1);
    }

    /**
     * 生成群组xml
     * @return array
     * @throws SpringException
     */
    public function createGroup(){
        $data = $this->load('sitemap')->getGroup();
        $this->set('data',$data);
        $this->set('level',0.7);
        $str = $this->fetch('sitemap/conten.html');
        $str = $this->xmlHead.$str;
        $rst = file_put_contents(C('SITEMAP_ROOT').'/group.xml',$str);
        //返回结果
        if($rst===false){
            return array('code'=>0,'msg'=>'news--error ');
        }
        return array('code'=>1);
    }

    /**
     * 生成专利类型xml
     * @return array
     * @throws SpringException
     */
    public function createPtFenLei(){
        $data = $this->load('sitemap')->getPtFenLei();
        $this->set('data',$data);
        $this->set('level',0.7);
        $str = $this->fetch('sitemap/conten.html');
        $str = $this->xmlHead.$str;
        $rst = file_put_contents(C('SITEMAP_ROOT').'/pt_fenlei.xml',$str);
        //返回结果
        if($rst===false){
            return array('code'=>0,'msg'=>'pt_fenlei--error ');
        }
        return array('code'=>1);
    }

    /**
     * 生成index.xml文件
     * @return array
     */
    public function createMain(){
        //得到文件夹下的文件信息
        $dir = C('SITEMAP_ROOT');
        $data = array();
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if($file!='.' && $file!='..'){
                    $data[] = $file;
                }
            } closedir($dh);
        }
        //写入index.xml文件中
        $time = date('Y-m-d H:i:s');
        $this->set('data',$data);
        $this->set('time',$time);
        $str = $this->fetch('sitemap/index.html');
        $str = $this->xmlHead.$str;
        $rst = file_put_contents(WebDir.'/sitemap.xml',$str);
        //返回结果
        if($rst===false){
            return array('code'=>0,'msg'=>'index--error');
        }
        return array('code'=>1);
    }
}