<?php
/**
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/3/28 0028
 * Time: 下午 19:44
 */
class LoginAction extends AppAction{
    /**
     * 获得登录相关的验证参数
     * @return string
     */
    public function getLogin(){
        //获得url参数
        $url = $_SERVER['HTTP_REFERER'];
        $data = array();
        //设置参数
        $jsapiToken	= 'chanshuwang';
        $time = time();
        $nonceStr = md5('nonceStr'.$time.rand(100000000,999999999));
        $data['timestamp'] = $time;
        $data['url'] = $url;
        $data['nonceStr'] = $nonceStr;
        $data['signature'] = sha1("jsapi_ticket={$jsapiToken}&noncestr={$nonceStr}&timestamp={$time}&url={$url}");
        //返回json字符串
        $this->returnAjax($data);
    }
}