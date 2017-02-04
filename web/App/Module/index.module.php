<?php
class IndexModule extends AppModule
{
    /**
	* 引用业务模型
	*/
	public $models = array(
        'indexBasic'            => 'IndexBasic',
        'industry'              => 'Industry',
        'industryClass'         => 'IndustryClass',
        'industryPic'           => 'IndustryPic',
        'industryclassitems'    => 'IndustryClassItems',
        'class'                 => 'Tmclass',
        'module'                => 'Module',
        'moduleClass'           => 'ModuleClass',
        'moduleLink'            => 'ModuleLink',
        'modulePic'             => 'ModulePic',
        'moduleClassItems'      => 'ModuleClassItems',
        'tmclass'               => 'tmclass',
    );

    /**
     * 得到首页基本配置信息
     * @return array
     */
    public function getIndexBasic(){
        //查询所有数据
        $class_all  = $this->getAllClass();
        $r['order'] = array('sort'=>'asc');
        $r['limit'] = 1000;
        $r['neq']   = array('type'=>2);
        $data       = $this->import('indexBasic')->find($r);
        //处理数据
        $banners    = array();
        $ads        = array();
        $case       = array();
        $recommendClasses = array();
        foreach($data as $item){
            switch($item['type']){
                case '1':
                    $banners[] = array(
                        'pic'   =>$item['pic'],
                        'other' =>$item['other'],
                        'link'  =>$item['link'],
                        'alt'   =>$item['alt'],
                    );
                    break;
                case '3':
                    $ads[] = array(
                        'pic'   =>$item['pic'],
                        'link'  =>$item['link'],
                        'alt'   =>$item['alt'],
                        'desc'  =>$item['desc'],
                        'text'  =>$item['text'],
                    );
                    break;
                case '4':
                    //得到分类的名字
                    $class      = explode(',',$item['desc']);
                    $className  = array();
                    foreach($class as $v){
                        $className[] = array($v,$v.'类'.$class_all[$v]);
                    }
                    $recommendClasses[] = array(
                        'pic'       => $item['pic'],
                        'alt'       => $item['alt'],
                        'link'      => $item['link'],
                        'class'     => $className,
                        'classStr'  => $item['desc'],
                    );
                    break;
                case '5':
                    $case[] = array(
                        'url'   => $item['link'],
                        'alt'   => $item['alt'],
                        'pic'   => $item['pic'],
                    );
                    break;
            }
        }
        return array($banners,$ads,$recommendClasses,$case);
    }

    /**
     * 得到菜单信息
     * @return array
     */
    public function getIndustry(){
        //得到一级菜单信息
        $r['order'] = array('sort'=>'desc');
        $r['limit'] = 1000;
        $r['col']   = array('id','title');
        $menuFirst  = $this->import('industry')->find($r);
        $r = array();
        //得到二级菜单信息
        $r['order'] = array('sort'=>'desc');
        $r['limit'] = 1000;
        $r['col']   = array('id','industryId','name','link');
        $menuSecond = $this->import('industryClass')->find($r);
        $r = array();
        //得到三级菜单信息
        $r['order'] = array('sort'=>'asc');
        $r['limit'] = 1000;
        $r['col']   = array('classId','name','link','open','show');
        $menuThird = $this->import('industryclassitems')->find($r);
        $r = array();
        //得到推荐图片信息
        $r['order'] = array('sort'=>'desc');
        $r['limit'] = 1000;
        $r['col']   = array('industryId','pic','link','alt');
        $menuPic    = $this->import('industryPic')->find($r);
        $r          = array();
        //返回结果
        return array($menuFirst,$menuSecond,$menuThird,$menuPic);
    }

    /**
     * 得到模块信息
     * @return array
     */
    public function getModule(){
        //得到所有的模块信息
        $r['order'] = array('sort'=>'asc');
        $r['eq']    = array('isUse'=>1);
        $r['col']   = array('id','name');
        $r['limit'] = 1000;
        $modules    = $this->import('module')->find($r);
        $data       = array();
        foreach($modules as &$module){
            $module['class'] = $this->getModuleClass($module['id']);
            $module['pic'] = $this->getModuleAds($module['id']);
            $module['link'] = $this->getModuleLink($module['id']);
        }
        return $modules;
    }

    /**
     * 首页模块子分类列表信息
     * @param $moduleId
     * @return array
     */
    private function getModuleClass($moduleId)
    {
        $r = array();
        $r['eq']['moduleId'] = $moduleId;
        $r['limit'] = 100;
        $r['col']   = array('id','name','type','link');
        $r['order'] = array('sort'=>'asc');
        $data       = $this->import('moduleClass')->find($r);
        //得到分类的子分类列表
        foreach($data as &$item){
            $item['items'] = $this->getModuleClassItems($item['id'], $item['type']);
        }
        return $data;
    }

    /**
     * 首页模块子分类列表信息
     * @param $classId
     * @return array
     * @throws array
     */
    private function getModuleClassItems($classId, $type)
    {
        $r = array();
        $r['eq']['classId'] = $classId;
        $r['limit'] = 100;
        $r['order'] = array('sort'=>'asc');
        $r['col']   = array('id','name','number');
        $data       = $this->import('moduleClassItems')->find($r);
        if($type==1){ //商标
            $datas= $this->getSaleInfo($data);
        }
        else if($type==2){//专利
            $datas= $this->getPatentInfo($data);
        }

        return $datas;
    }

    //获取模块的商标信息
    private function getSaleInfo($data){
        //添加额外信息
        foreach($data as $k=>&$item){
            //判断商品是否销售中
            $rst = $this->load('Sale')->isSale($item['number']);
            if(!$rst){
                unset($data[$k]);
                continue;
            }
            //得到所属分类名
            $result = $this->load('Sale')->getSaleInfoByNumber($item['number']);
            $item['classStr']   = $result['className'];
            $item['viewUrl']    = 'd-'.$result['tid'].'-'.$result['class'].'.html';
            $item['tid']        = $result['tid'];
            $item['class']      = $result['class'];
            $item['remarks']    = "商标号:".$item['number'].",类别:".$result['class'];
            //得到商标包装图
            $aaa = $this->load('Sale')->getSaltTminfoByNumber($item['number']);
            $item['embellish']  = $aaa['embellish'];
            $item['alt']        = $aaa['alt1'];
            //处理商标名
            $item['thum_name'] = mbSub($item['name'],0,12);
        }
        return $data;
    }

        //获取模块的商标信息
    private function getPatentInfo($data){
        //添加额外信息
        foreach($data as $k=>&$item){
            //判断商品是否销售中
            $rst = $this->load('pt')->isSale($item['number']);
            if(!$rst){
                unset($data[$k]);
                continue;
            }

            $res    = $this->load('pt')->getPatentInfoByNumber($item['number']);
            $result = $this->load('pt')->getTips($res);

            $item['classStr']   = $result['typeName'];
            $item['viewUrl']    = '/pt-'.$item['number'].'.html';
            $item['tid']        = $item['number'];
            $item['class']      = $res['class'];
            $item['remarks']    = "专利号:".$item['number'].",专利名称:".$item['name'];
            //得到商标包装图
            $item['embellish']  = $result['imgUrl'];
            $item['alt']        = $item['name'];
            //处理商标名
            $item['thum_name']  = mbSub($item['name'],0,12);
        }
        return $data;
    }

    /**
     * 首页模块包含广告列表信息
     * @param $moduleId
     * @return array
     */
    private function getModuleAds($moduleId)
    {
        $r = array();
        $r['eq']['moduleId'] = $moduleId;
        $r['limit'] = 100;
        $r['col']   = array('pic','link','alt');
        $r['order'] = array('sort'=>'asc');
        $data       = $this->import('modulePic')->find($r);
        return $data;
    }

    /**
     * 首页模块推广链接列表信息
     * @param $moduleId
     * @return array
     */
    private function getModuleLink($moduleId)
    {
        $r = array();
        $r['eq']['moduleId'] = $moduleId;
        $r['limit'] = 100;
        $r['col']   = array('title','link','show');
        $r['order'] = array('sort'=>'asc');
        $data       = $this->import('moduleLink')->find($r);
        //解析链接的显示
        $data2 = array();
        foreach($data as $k=>&$item){
            if($item['show']==1){
                $item['color'] = 'color-red';
            }elseif($item['show']==2){
                $item['color'] = 'color-blue';
            }elseif($item['show']==3){
                $item['color'] = 'color-orrange';
            }else{
                $item['color'] = '';
            }
            $key = floor($k/2);
            $data2[$key][$k%2] = $item;
        }
        return $data2;
    }

    /**
     * 得到所有的分类
     * @return array
     */
    public function getAllClass(){
        $r['eq']    = array('parent' => "0");
        $r['limit'] = 45;
        $r['col']   = array('number','name');
        $r['order'] = array('sort' => 'asc');
        $res        = $this->import('tmclass')->find($r);
        //转换为值对应名的数组
        $values = arrayColumn($res,'name');
        $keys   = arrayColumn($res,'number');
        $res    = array_combine($keys,$values);

        return $res;
    }

    /**
     * 得到所有的热搜数据
     * @return array
     */
    public function getHotWords(){
        //查询所有数据
        $r['order'] = array('sort'=>'asc');
        $r['limit'] = 1000;
        $r['eq']    = array('type'=>2);
        $data       = $this->import('indexBasic')->find($r);
        $hotwords   = array();
        foreach($data as $item){
            //处理颜色
            if($item['other']==0){
                $color = '';
            }elseif($item['other']==1){
                $color = 'color-red';
            }elseif($item['other']==2){
                $color = 'color-blue';
            }else{
                $color = 'color-orrange';
            }
            $hotwords[] = array(
                'desc'=>$item['desc'],
                'other'=>$color,
                'link'=>$item['link'],
            );
        }
        return $hotwords;
    }

    /**
     * 发送邮件
     * @param $email
     * @param $title
     * @param $content
     * @param string $name
     * @param string $from
     * @return array
     */
    public function sendEmail($email, $title, $content, $name='' , $from='一只蝉'){
        $res = $this->importBi('Message')->sendMail($email, $title, $content, $name , $from);
        $a = ($res['code'] == 1) ? 1 : 0;
        return array('code'=>$a);
    }
}
?>
