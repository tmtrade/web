<?
/**
 * 专题信息
 *
 * 专题界面
 *
 * @package	Action
 * @author  far
 * @since   2016-3-08
 */
class TopicAction extends AppAction
{
    public $caches      = array('lesson_one','tmall_jd');
    public $cacheId     = 'redisHtml';
    public $expire      = 3600;//1小时
    public $ptype       = 18;
    
    //第一课
    public function lesson_one()
    {
        $this->pageTitle 	= '如何选择商标名称_商标类别选择-商标转让知识学习-一只蝉商标转让平台网';
        $this->pageKey          = '如何选择商标名称,如何挑选商标，商标类别选择,商标注册类别选择,商标注册商 品选择技巧';
        $this->pageDescription  = '如何选择商标名称？如何挑选商标？一只蝉商标转让平台手把手教你如何挑 选价值百万的商标。为你在挑选商标过程中遇到的如：商标注册类别选择,商标注册商标选 择技巧,商标名称选择提供最实用的知识';
        $this->set('title', $this->pageTitle);//页面title
        $this->set('keywords', $this->pageKey);//页面keywords
        $this->set('description', $this->pageDescription);//页面description
        $this->setSeoByTopic("/topic/lesson_one");
        $this->display();
    }
    //第二课
    public function lesson_two()
    {
        $this->pageTitle 	= '商标转让价格_商标值多少钱_一个商标能卖多少钱_商标转让费用_商标转让知识-一 只蝉商标转让平台网';
        $this->pageKey          = '商标转让价格,商标值多少钱,一个商标能卖多少钱,商标价值评估,商标转让费用
,r商标值多少钱';
        $this->pageDescription  = '如何判断一个商标值不值钱?一个商标能卖多少钱？一只蝉商标转让平台网手 把手教你如何判断一个商标值不值钱，一个商标值多少钱？为你解决：商标转让价格,商标 转让费用,r商标值多少钱等等问题。为你解决在商标转让价格的问题。';
        $this->set('title', $this->pageTitle);//页面title
        $this->set('keywords', $this->pageKey);//页面keywords
        $this->set('description', $this->pageDescription);//页面description
        $this->setSeoByTopic("/topic/lesson_two");
        $this->display();
    }
    //天猫与京东
    public function tmall_jd()
    {
        $this->pageTitle 	= '买商标入驻天猫京东,哪些商标可以入驻天猫京东- 一只蝉商标转让网';
        $this->pageKey          = '买商标入驻天猫京东,哪些商标可以入驻天猫京东,入驻天猫京东资质资料';
        $this->pageDescription  = '在一只蝉买商标入驻天猫京东,哪些商标可以入驻天猫京东?入驻天猫京东资质资料需要哪些？一只蝉给你想要的答案,这些商标都是可以入驻天猫京东的。一只蝉商标转让网';
        $this->set('title', $this->pageTitle);//页面title
        $this->set('keywords', $this->pageKey);//页面keywords
        $this->set('description', $this->pageDescription);//页面description
        $this->setSeoByTopic("/topic/tmall_jd");
        $this->display();
    }
    
    //第四月 红动全场
    public function fourth()
    {
        $this->pageTitle 	= '商标转让价格_商标值多少钱_一个商标能卖多少钱_商标转让费用_商标转让知识-一 只蝉商标转让平台网';
        $this->pageKey          = '商标转让价格,商标值多少钱,一个商标能卖多少钱,商标价值评估,商标转让费用,r商标值多少钱';
        $this->pageDescription  = '如何判断一个商标值不值钱?一个商标能卖多少钱？一只蝉商标转让平台网手 把手教你如何判断一个商标值不值钱，一个商标值多少钱？为你解决：商标转让价格,商标 转让费用,r商标值多少钱等等问题。为你解决在商标转让价格的问题。';
        $this->set('title', $this->pageTitle);//页面title
        $this->set('keywords', $this->pageKey);//页面keywords
        $this->set('description', $this->pageDescription);//页面description
        $this->setSeoByTopic("/topic/fourth");
        $this->display();
    }
    
     //买一送一
    public function buyone_getone()
    {
        $this->pageTitle 	= '购买商标送注册商标,注册商标怎么转让,注册商标转让好处|一只蝉商标转让平台网';
        $this->pageDescription  = '活动期间在一只蝉商标转让网购买商标者,就送价值2100元的商标注册。注册商标怎么转让？注册商标有哪些好处？上一只蝉商标转让平台,一只蝉是超凡集团商标交易平台-中国独家签订交易损失赔付协议保障风险平台。提供专业的商标交易,商标买卖等服务';
        $this->set('title', $this->pageTitle);//页面title
        $this->set('description', $this->pageDescription);//页面description
        $this->setSeoByTopic("/topic/buyone_getone");
        $this->display();
    }

    //母亲节
    public function motherday()
    {
        $this->pageTitle = '母亲节特价商标优惠活动--一只蝉商标转让平台';
        $this->pageKey  = '母亲节商标优惠活动,商标优惠活动,服装箱包商标,化妆清洁商标，母婴用品商标交易，儿童玩具商标，珠宝饰品商标，礼品保健商标转让';
        $this->pageDescription = '一只蝉重磅打造母亲节商标优惠活动,商标转让,服装箱包,化妆清洁，母婴用品，儿童玩具，珠宝饰品，礼品保健等商标交易特价优惠活动进行中。业界狂欢，实力打造给力母情节巨惠，有实力，惠到底！更多神秘大礼，火爆进行中！
';
        $this->set('title', $this->pageTitle);//页面title
        $this->set('keywords', $this->pageKey);//页面keywords
        $this->set('description', $this->pageDescription);//页面description
        $this->setSeoByTopic("/topic/motherday");
        $this->display();
    }

    //天猫新规则
    public function tm()
    {
        $this->pageTitle = '天猫2016年招商资质调整细则-- 一只蝉转让平台';
        $this->pageKey  = '天猫2016年招商资质调整细则,天猫降低招商门槛,买商标入驻天猫,2016天猫招商资质细则';
        $this->pageDescription = '天猫2016年招商资质调整细则,天猫降低招商门槛,新品牌入驻取消商标两年限制，对服饰鞋类箱包,运动户外,珠宝配饰,家装家具家纺，汽车配件等均做出相应调整。挑选好商标就上一只蝉商标转让平台。';
        $this->set('title', $this->pageTitle);//页面title
        $this->set('keywords', $this->pageKey);//页面keywords
        $this->set('description', $this->pageDescription);//页面description
        $this->setSeoByTopic("/topic/tm");
        $this->display();
    }
    
        //专利上线专题
    public function ptline()
    {
        $this->pageTitle = '一只蝉专利买卖,专利交易系统,专利交易平台-- 一只蝉专利转让网';
        $this->pageKey  = '一只蝉专利买卖,专利交易系统,专利交易平台';
        $this->pageDescription = '一只蝉全新推出专利买卖平台,为你购买专利提供便捷。专利交易平台提供：购买专利,专利转让信息,买卖专利信息,专利申请等服务。一只蝉是超凡集团旗下资产交易平台,为你提供专业的专利转让交易服务。';
        $this->set('title', $this->pageTitle);//页面title
        $this->set('keywords', $this->pageKey);//页面keywords
        $this->set('description', $this->pageDescription);//页面description
        $this->setSeoByTopic("/topic/ptline");
        $this->display();
    }

    /**
     * 圣诞节专题
     */
    public function chris(){
        $this->pageTitle = '如何选择商标，选商标流程注意事项';
        $this->pageKey  = '一只蝉专利买卖,专利交易系统,专利交易平台';
        $this->pageDescription = '怎么选择到好的商标？选择商标时要有哪些注意事项？百万商标交易网一只蝉用心为你服务，教你挑选好的商标。';
        $this->set('title', $this->pageTitle);//页面title
        $this->set('keywords', $this->pageKey);//页面keywords
        $this->set('description', $this->pageDescription);//页面description
        $this->setSeoByTopic("/topic/chris");
        $this->display();
    }
}
?>