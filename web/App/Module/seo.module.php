<?

/**
 * SEO设置
 *
 * SEO创建，修改，删除等
 *
 * @package    Module
 * @author     Far
 * @since      2016年4月6日10:10:07
 */
class SeoModule extends AppModule
{
    
    public $models = array(
            'seo'            => 'seo',
    );
    
   
    //info 单条
    public function getInfo($type,$vid="")
    {
            $r['eq']    = array(
                    'type' => $type,
                    'isUse' => 1
            );
            if(!empty($vid)){
                 $r['eq']['vid'] = $vid;
            }
            $r['limit'] = 1;
            $r['col'] = array("title","keyword","description");
            $res = $this->import('seo')->find($r);
            return $res;
    }
        
}
?>