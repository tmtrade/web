<?

/**
 * 模拟HTTP请求
 *
 * @param	string	$url		请求的地址
 * @param	string	$method		0为GET、1为POST
 * @param	string	$param		提交的参数
 * @param	int		$timeout	超时时间（秒）
 * @return	string
 */
function httpRequest($url, $method = 0, $param = '', $timeout = 10)
{
	$userAgent ="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
	$ch        = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	if ( $method == 1 ) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
	}
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	curl_setopt( $ch,CURLOPT_HTTPHEADER, array(
	'Accept-Language: zh-cn',
	'Connection: Keep-Alive',
	'Cache-Control: no-cache',
	));
	$document = curl_exec($ch);
	$info     = curl_getinfo($ch);
	if ( $info['http_code'] == "405" ) {
		curl_close($ch);
		return 'error';
	}
	curl_close($ch);
	return $document;
}


/**
* 返回数组中指定的一列 (可见php5.5新函数array_column)
* @static
* @access public
* @param array $array 需要取出数组列的多维数组（或结果集）
* @param string $column_key 需要返回值的列，它可以是索引数组的列索引
* @param string $index_key 作为返回数组的索引/键的列，它可以是该列的整数索引，或者字符串键值
* @return array
*/
function arrayColumn(array $array, $column_key, $index_key=null){
	if(function_exists('array_column')){
		return array_column($array, $column_key, $index_key);
	}
	$result = array();
	foreach($array as $arr){
		if(!is_array($arr)) continue;

		if(is_null($column_key)){
			$value = $arr;
		}else{
			$value = $arr[$column_key];
		}
		if(!is_null($index_key)){
			$key = $arr[$index_key];
			$result[$key] = $value;
		}else{
			$result[] = $value;
		}
	}
	return $result;
}

function C($key)
{
	return LoadConfig::get($key);
}

/**
* 获取访问者ip
*
* @return  string
*/
function getClientIp()
{
	if ( getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown') )
	{
		$onlineip = getenv('HTTP_CLIENT_IP');
	}
	elseif ( getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown') )
	{
		$onlineip = getenv('HTTP_X_FORWARDED_FOR');
	}
	elseif ( getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown') )
	{
		$onlineip = getenv('REMOTE_ADDR');
	}
	elseif ( isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown') )
	{
		$onlineip = $_SERVER['REMOTE_ADDR'];
	}
	return $onlineip;
}

/**
* URL跳转
*
* @param	string  $desc		消息文本
* @param	string  $url		跳转地址
* @param	string  $scripts	待执行的多个JS文件地址
* @param	int		$seconds	停留时间(秒)
* @return  void
*/
function goUrl($desc, $url, $scripts = array(), $seconds = 1)
{
	$msgFile = ResourceDir.'/default/redirect.html';
	$path    = '/'.ResourceDir.'/';
	$js      = '';

	foreach ( $scripts as $script ) {
		$js .= $script;
	}

	$tipInfo  = "$desc <br>请稍后,系统正在自动跳转........";
	$gotoUrl  = "<meta http-equiv='Refresh' content='$seconds; url=$url'>";
	require($msgFile);
	exit();
}

/**
* 获取分页条
*
* @param	array   $pager   组合要素
* @param	bool    $script  是否带有下拉
* @return	string
*/
function getPageBar($pager, $script = true)
{
	if ( empty($pager) || !is_array($pager) ) {
		return '';
	}

	$html = '共'.$pager['recordNum'].'条/'. '共' . $pager['pageNum'] . '页' . '&nbsp;';

	if ( $pager['pageNum'] > 10 ) {
		$html .= '<a href="' . $pager['first'] . '">首页</a>' . '&nbsp;' .
		'<a href="' . $pager['pre']   . '">上页</a>' . '&nbsp;' .
		'<a href="' . $pager['next']  . '">下页</a>' . '&nbsp;' .$pager['point'].
		'<a href="' . $pager['last']  . '">尾页</a>' . '&nbsp;' ;
	} else {
		$html .= '<a href="' . $pager['first'] . '">首页</a>' . '&nbsp;' .
		'<a href="' . $pager['pre']   . '>上页</a>' . '&nbsp;' .
		'<a href="' . $pager['next']  . '>下页</a>' . '&nbsp;' .
		'<a href="' . $pager['last']  . '">尾页</a>' . '&nbsp;' ;
	}


	$html .= $script ? $pager['jump'] : '';

	return $html;
}

/**
* 获取分页条
*
* @param	array   $pager   组合要素
* @param	bool    $script  是否带有下拉
* @return	string
*/
function getPageBarNew($pager, $script = true, $isJs = false)
{
	if ( empty($pager) || !is_array($pager) ) {
		return '';
	}

	$html = "";
    if ( $isJs ){
        $html .='<a href="javascript:;" onclick="' . $pager['pre']   . '" class="prev">上页</a>' . '&nbsp;' .$pager['point'].
            '<a href="javascript:;" onclick="' . $pager['next']  . '" class="next">下页</a>' . '&nbsp;' ;
    }else{
        $html .='<a href="javascript:;" onclick="' . $pager['pre']   . '" class="prev">上页</a>' . '&nbsp;' .$pager['point'].
            '<a href="javascript:;" onclick="' . $pager['next']  . '" class="next">下页</a>' . '&nbsp;' ;
    }
        
    $html .= '<span>共' . $pager['pageNum'] . '页</span>' . '&nbsp;';
        
	$html .= $script ? $pager['jump'] : '';

	return $html;
}

/**
* 中断并输出提示
*
* @param  string	$msg	提示信息
* @return void
*/
function halt($msg)
{
	SpringException::throwException($msg);
}

/**
* 数组格式转换[2维转化成1维]
*
* @param  array  $list  2维数组
* @param  array  $cols  2维数组中的列名[array(id)、array(id,name)]
* @return array
*/
function arrayOne($list, $cols = array())
{
	if ( empty($list) || (empty($cols) || !is_array($cols)) ) return $list;

	$temp   = array();
	$length = count($cols);
	foreach ($list as $data) {
		if ( $length == 1 ) {
			$temp[] = isset($data[$cols[0]]) ? $data[$cols[0]] : '';
		} else {
			$temp[$data[$cols[0]]] = isset($data[$cols[1]]) ? $data[$cols[1]] : '';
		}
	}
	return $temp;
}

/**
* 字符串1是否为字符串2的子串
*
* @param  string	$str1	字符串1
* @param  string	$str2	字符串2
* @return bool
*/
function strExist($str1, $str2)
{
	return !(strpos($str2, $str1) === FALSE);
}


/**
* 获取某长度的随机字符编码
* 除纯数字与纯字母选项，其他都不包括(I,i,o,O,1,0)
* @author	Xuni
* @since	2015-11-05
*
* @param	int		$len	编码长度
* @param	string	$format	格式（ALL：大小写字母加数字，CHAR：大小写字母，NUMBER：纯数字，默认为小写字母加数字）
* @return	array
*/
function randCode($len, $format='')
{
	$is_abc = $is_numer = 0;
	$password = $tmp ='';
	switch($format){
		case 'ALL':
			$chars='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjklmnpqrstuvwxyz23456789';
			break;
		case 'CHAR':
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			break;
		case 'NUMBER':
			$chars='0123456789';
			break;
		default :
			$chars='abcdefghjklmnpqrstuvwxyz23456789';
			break;
	}
	mt_srand((double)microtime()*1000000*getmypid());
	while(strlen($password)<$len){
		$tmp =substr($chars,(mt_rand()%strlen($chars)),1);
		if(($is_numer <> 1 && is_numeric($tmp) && $tmp > 0 )|| $format == 'CHAR'){
			$is_numer = 1;
		}
		if(($is_abc <> 1 && preg_match('/[a-zA-Z]/',$tmp)) || $format == 'NUMBER'){
			$is_abc = 1;
		}
		$password.= $tmp;
	}
	if($is_numer <> 1 || $is_abc <> 1 || empty($password) ){
		$password = randCode($len,$format);
	}
	return $password;
}


/**
* 获取目录下指定扩展名的文件
* @author	void
* @since	2014-12-26
*
* @param	string	$path	文件路径
* @param	string	$ext	扩展名
* @return	array
*/
function getFile($path, $ext = '.php')
{
	static $tree = array();
	$list        = array();
	$length      = strlen($ext);

	if ( is_dir($path) ) {
		$files = scandir($path);
		foreach ( $files as $file ) {
			if ( $file != "." && $file != ".." ) {
				if ( is_dir($path."/".$file) ) {
					getFile($path."/".$file);
				} else {
					substr($file, -$length) == $ext && $list[] = $path."/".$file;
				}
			}
		}
		$tree[$path] = $list;
		$list        = array();
	}
	return $tree;
}

/**
* 判断用户类型
* @author	garrett
* @since	2015-04-24
*
* @param	string	$account 用户帐号
* @return	int    类型  1 代表邮箱 2 代表手机 3 代表帐号错误
*/
function isCheck( $account )
{
	$mobile = "/^(13|14|15|16|17|18)\d{9}$/";
	$email  = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a(www.111cn.net)-z]{2,3}([.][a-z]{2})?/i";
	if(preg_match($email ,$account))
	{
		return 1;

	} elseif (preg_match( $mobile ,$account))
	{
		return 2;
	} else
	{
		return 3;
	}
}

/**
* 数据调试
* @author garrett
* @param array $data 数据
* @param string $pattern 输出类型
* @return void
*/
function debug( $data , $pattern = '')
{
	echo "<pre>";
	die(empty($pattern) ? print_r($data) : var_dump($data));
}

/**
* 获取字符长度
* @author garrett
* @param array $data 数据
* @param string $pattern 输出类型
* @return void
*/
function length($str , $iconv = 'utf-8')
{
	return iconv_strlen($str , $iconv);
}

/**
* url参数加密
*
* @author 	garrett
* @since  	2015-4-27  上午10:28
*
* @access 	public
* @param   string $str  加密内容
*
* @return boolean
*/
function urlParamEncode($str)
{
	$data = Encrypt::encode($str);
	return base64_encode($data);
}

/**
* 生成URL地址中的参数
*
* @author   garrett
* @since    2015-4-27
*
* @access   public
* @param    string $email	邮箱
* @param    int    $time   生成时间
* @param 	 int    $uid	用户ID
*
* @return string
*/
function makeToken($email, $time, $uid = 0)
{
	$params = $email . '||' . $uid ;
	$key 	= md5($time);
	return urlParamEncode($key . '||' . $params);
}

/**
* 字符串截取
*
* @author   garrett
* @since    2015-4-27
*
* @access   public
* @param    string $email	邮箱
* @param    int    $time   生成时间
* @param 	 int    $uid	用户ID
*
* @return string
*/
function mbSub( $conect , $min = 0 , $leng = 100 , $en='utf-8')
{
	$output = mb_substr($conect , $min , $leng , $en);
	if(mb_strlen($conect,$en) > $leng ){
		$output .= "...";
	}
	return $output;
}

/**
* 半角转化为全角
*
* 更换数据
* @author 		garrett
* @since  		2015-6-26
*
* @access 	public
* @param string $str  解密内容
*
* @return boolean
*/
function symbol( $name )
{
	$arr = array(
		'０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
		'５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
		'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
		'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
		'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
		'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
		'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
		'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
		'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
		'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
		'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
		'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
		'ｙ' => 'y', 'ｚ' => 'z',
		'（' => '(', '）' => ')', '［' => '[', '］' => ']', '【' => '[',
		'】' => ']', '〖' => '[', '〗' => ']', '「' => '[', '」' => ']',
		'『' => '[', '』' => ']', '｛' => '{', '｝' => '}', '《' => '<',
		'》' => '>',
		'％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
		'：' => ':', '。' => '.', '、' => ',', '，' => ',',
		'；' => ';', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
		'＂' => '"', '＇' => '`', '｀' => '`', '｜' => '|', '〃' => '"',
		'　' => ' ' , '＆'=>'&'
	);
	foreach( $arr as $key=>$val)
	{
		$name = str_replace($key , $val , $name);
	}

	return $name;
}


//快速排序
function quickSort($array)
{

	$length=count($array);
	$left_array=array();
	$right_array=array();
	if($length<=1){
		return $array;
	}

	$key=$array[0];
	//注意这里的i一定要等于1，也就是从数组的第二个值开始比较，因为第一个值已经是被选定为对比值了
	for($i=1;$i<$length;$i++){
		if($array[$i]['id']>$key['id']){
			$right_array[]=$array[$i];
		}else{
			$left_array[]=$array[$i];
		}
	}


	$left_array=quickSort($left_array);
	$right_array=quickSort($right_array);
	$data = array_merge($right_array,array($key),$left_array);
	return $data;
}
/**
* 二维数组按照某个键名的排序
* @author   haydn
* @since    2015-07-17
* @param    array   $arr    数组
* @param    string  $keys   字段排序
* @param    string  $type   排序方式
* @return   Array
* @egxample $list['staff'] = array_sort($list['staff'],'name');
*/
function array_sort($array,$keys,$type='asc')
{
	$keysvalue = array();
	foreach ( $array as $k => $v ){
		$keysvalue[$k] = $v[$keys];
	}
	if( $type == 'asc' ){
		array_multisort($keysvalue,SORT_ASC,$array);
	}else{
		array_multisort($keysvalue,SORT_DESC,$array);
	}
	return $array;
}
/**
* 网页转换成pdf
* @author   haydn
* @since    2016-01-27
* @param    string  $file		网页地址
* @param    string  $filepdf	pdf文件保存地址
* @return   int		$success	返回（0：成功 1：失败）
*/
function makePDF($file,$filepdf)
{
	$pdfexe = C('HTMLTOPDF');
	exec("{$pdfexe} {$file} {$filepdf}",$out,$success);
	return $success;
}

/**
* 创建目录
* @author   haydn
* @since    2016-01-27
* @param    string  $filename	目录地址
* @return   bool	
*/
function mkdirs($filename)
{
	$dir	= $filename;
	$errmsg = '';
	$dir 	= explode('/', $dir);
	foreach($dir as $v){
		if($v){
			@$d .= $v . '/';
			if(!is_dir($d)){
				$state = mkdir($d, 0777);
			}
		}
	}
	return true;
}

/**
 * 得到md5的密码值
 * @param $password
 * @param string $salt 盐
 * @return string
 */
function getPassword($password,$salt = '')
{
	$string = md5(md5($password).$salt);
	return $string;
}

//判断是否手机访问
function checkmobile()
{
	$mobile = array();
	static $touchbrowser_list	= array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
		'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung',
		'palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
		'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
		'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
		'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
		'benq', 'haier', '^lct', '320x320', '240x320', '176x220', 'windows phone');
	static $wmlbrowser_list 	= array('cect', 'compal', 'ctl', 'lg', 'nec', 'tcl', 'alcatel', 'ericsson', 'bird', 'daxian', 'dbtel', 'eastcom',
		'pantech', 'dopod', 'philips', 'haier', 'konka', 'kejian', 'lenovo', 'benq', 'mot', 'soutec', 'nokia', 'sagem', 'sgh',
		'sed', 'capitel', 'panasonic', 'sonyericsson', 'sharp', 'amoi', 'panda', 'zte');

	static $pad_list 			= array('ipad');

	$useragent 					= strtolower($_SERVER['HTTP_USER_AGENT']);

	if( dstrpos($useragent, $pad_list) ) {
		return false;
	}
	if( ($v = dstrpos($useragent, $touchbrowser_list, true)) ){
		return true;
	}
	if( ($v = dstrpos($useragent, $wmlbrowser_list)) ) {
		return true; //wml版
	}
	$brower = array('mozilla', 'chrome', 'safari', 'opera', 'm3gate', 'winwap', 'openwave', 'myop');
	if(dstrpos($useragent, $brower)) return false;

	return false;
}

function dstrpos($string, $arr, $returnvalue = false)
{
	if(empty($string)) return false;
	foreach((array)$arr as $v) {
		if(strpos($string, $v) !== false) {
			$return = $returnvalue ? $v : true;
			return $return;
		}
	}
	return false;
}

?>