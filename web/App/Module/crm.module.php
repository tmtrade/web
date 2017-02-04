<?
/**
 * 与CRM交互接口组件
 *
 * 推送求购与出售信息到分配系统、
 * 
 * @package	Module
 * @author	Xuni
 * @since	2016-03-09
 */
class CrmModule extends AppModule
{
	
    /**
     * 推送信息到分配系统
     *
     * 推送求购与出售信息到分配系统
     * 
     * @author  Xuni
     * @since   2016-03-09
     *
     * @return  bool
     */
    public function pushTrack($need, $contact, $mobile, $sid, $area,$pttype = 1)
    {
        //出售只分配到新慧
        $post['source']     = 0;
        if ($pttype == 2) $post['username'] = 'Yally';//顾问id
        $post['company']    = '';//公司名称
        $post['pttype']     = $pttype; //类型（1：求购 2：出售）
        $post['subject']    = $pttype == 1 ? "求购商标" : "出售商标";//注册名称
        $post['remarks']    = $need;//备注
        $post['name']       = $contact;//联系人
        $post['address']    = '';//客户联系地址
        $post['postcode']   = '';//客户邮编
        $post['tel']        = $mobile;//电话
        $post['email']      = '';//邮件
        $post['area']       = $area;//
        $post['sid']        = $sid;
        $json = $this->importBi('CrmPassport')->insertCrmMember($post);//联系人id
        $output             =  json_decode($json, 1);
        return $output;
    }

}
?>