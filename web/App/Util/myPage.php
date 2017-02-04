<?php

/**
 * Class Page
 * 应对新闻页特殊url的分页工具类
 * dower
 */
class myPage
{
    /**
     * 地址栏参数
     */
    public $input  = array();


    /**
     * 页码
     */
    public $page   = 0;

    /**
     * url后缀
     */
    public $suffix = '';

    /**
     * url前缀
     */
    public $prefix = '';

    /**
     * 构造方法
     * @param array $config
     */
    public function __construct(array $config=array()){
        foreach($config as $k=>$item){
            if(property_exists($this,$k)){
                $this->$k = $item;
            }
        }
    }
    /**
     * 分页方法
     */
    public function get($total, $pageRows = 20, $point = 10, $style = 'on')
    {
        $prior   = ceil($point/2) + 1;
        $back    = ceil($point/2);
        $num     = ceil($total/$pageRows);
        $url   =  $_SERVER['REQUEST_URI'];
        $url   = trim($url,'/');
        $temp = explode('-',$url);
        if(count($temp)==3){
            $page = array_pop($temp);
        }else{
            $page = 1;
        }
        if( $page <= 0)   $page = 1;
        if( $page > $num) $page = $num;
        $this->prefix = '/'.implode('-',$temp).'-';
        $result['current']	  = $page;
        $result['first']	  = ($page>1 ? $this->prefix.'1'.$this->suffix : 'javascript:;');
        $result['pre']		  = ($page-1>0 ? $this->prefix.($page-1).$this->suffix : 'javascript:;');
        $result['next']		  = ($page+1<=$num ? $this->prefix.($page+1).$this->suffix : 'javascript:;');
        $result['last']		  = ($num>1 && $page<$num ? $this->prefix.$num.$this->suffix : 'javascript:;');
        $result['recordNum']  = $total;
        $result['pageNum']    = $num;
        $result['jump']       = '';
        $result['start']	  = ($page - 1) * $pageRows + 1 < 0 ? 0 : ($page - 1) * $pageRows + 1;
        $result['end'] 		  = $num > $page ? $page * $pageRows : $total;

        $jumper = $listStr = '';

        if ($page <= $back && ($page + $point-1 <=$num) )
        {
            for($j=1; $j<=$point; $j++)
            {
                $link = $j == $page ? '<a href="javascript:;" class="'.$style.'">'.$j."</a> " : '<a href="'.$this->prefix.$j.$this->suffix.'">'.$j."</a>";
                $listStr .= $link;
            }
        }
        else
        {
            for($i=1; $i<=$num; $i++)
            {
                if ($i < ($page + $back) && $i > ($page - $prior))
                {
                    $point = $i == $page ? '<a href="javascript:;" class="'.$style.'">'.$i."</a>" : '<a href="'.$this->prefix.$i.$this->suffix.'">'.$i."</a>";
                    $listStr .= $point;
                }
            }
        }
        for($i=1; $i<=$num; $i++)
        {
            $page == $i ? $select=' selected' : $select='';
            $jumper.= "<option value=$i$this->suffix $select>$i</option>";
        }
        $result['point']= $listStr;
        $result['jump']	= '
						<script language="JavaScript" type="text/JavaScript">
						function page_jump(targ,selObj,restore)
						{
							eval(targ+".location=\''.$this->prefix.'"+selObj.options[selObj.selectedIndex].value+"\'");
							if (restore) selObj.selectedIndex=0;
						}
						</script>
						<select name=nump onchange="page_jump(\'this\',this,0)">'.$jumper.'</select>
										';
        return $result;
    }
}