<?php 
class TempModule extends AppModule
{
    /**
	* 引用业务模型
	*/
	public $models = array(
        'user'      => 'ttempuser',
	);

    /**
     * 基础获取求购信息的方法
     *
     * @author  Xuni
     * @since   2015-12-8
     *
     * @access  public
     * @param   array     $r     条件
     *
     * @return  array
     */
    public function find($r)
    {
        return $this->import('user')->find($r);
    }

	/**
     * 判断求购信息是否存在
	 * 
     * @author  Xuni
     * @since   2015-12-8
     *
     * @access  public
     * @param   array   $data   数据
     * @return  void
	 */
	public function isExist( $uname )
	{
        if ( empty($uname) ) return array();
        $r['eq']        = array('username'=>$uname);
        $r['limit']     = 1;
        $data           = $this->import('user')->find($r);
        return $data;
	}

    /**
     * 创建数据
     * @author	Xuni
     * @since	2015-12-8
     *
     * @access	public
     * @param	array	$data	数据
     * @return	void
     */
    public function saveInfo($uname, $upass, $sid, $memo)
    {
        $data = array(
            'username'  => $uname,
            'password'  => $upass,
            'date'      => time(),
            'ip'        => getClientIp(),
            'sid'       => $sid,
            'memo'      => $memo,
            );
        return $this->import("user")->create($data);
    }

	/**
     * 创建数据
     * @author  Xuni
     * @since   2015-12-8
     *
     * @access  public
     * @param   array   $data   数据
     * @return  void
     */
    public function moveTempToReal($userId, $uname, $sid)
    {
        //删除临时用户
        $r['eq'] = array('username' => $uname);
        $this->import('user')->remove($r);

        $role['eq']     = array(
            'mobile' => $uname,
            'status'=>0,
            );
        $temp = $this->load('buy')->findTemp($role);
        if ( empty($temp) )  return true;

        $buy = array(
            'source'        => 10,
            'phone'         => $uname,
            'need'          => $temp['need'],
            //'sid'           => $temp['sid'],
            //'area'          => $temp['area'],
            'contact'       => $temp['name'],
            'date'          => time(),
            'loginUserId'   => $userId,
            );
        //推送求购到分配系统
        $info = $this->pushTrack($temp['need'], $temp['name'], $uname, $temp['sid'], $temp['area']);
        if ( $info['data']['id'] > 0 ){
            $buy['crmInfoId'] = $info['data']['id'];
        }
        $res = $this->load('buy')->create($buy);//创建求购信息
        if ( $res ){
            $rol['eq'] = array('mobile' => $uname);
            $this->load('buy')->removeTemp($rol);
            return true;
        }
        return false;
    }

    public function pushTrack($need, $contact, $mobile, $sid, $area,$pttype = 1,$patent="")
    {
        //出售只分配到新慧
        $post['source']     = 0;
        if ($pttype == 2) $post['username'] = 'Yally';//顾问id
        $post['company']    = '';//公司名称
        $post['pttype']     = $pttype; //类型（1：求购 2：出售）
        if($patent==1){
            $post['subject']    = $pttype == 1 ? "求购专利" : "出售专利";//注册名称
        }else{
            $post['subject']    = $pttype == 1 ? "求购商标" : "出售商标";//注册名称
        }
        
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
