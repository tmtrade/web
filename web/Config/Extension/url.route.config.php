<?
/**
 * 重新定义url规则
 */

//定义商标详情规则(旧规则，需要支持)
$rules[] = array(
    '#/d-#',
    array('mod' => 'detail', 'action' => 'view'),
    array(
        'short' => '#((\d+-)([\w-])*)#',
        ),
);
//定义专利详情规则
$rules[] = array(
    '#/pt-#',
    array('mod' => 'patent', 'action' => 'view'),
    array(
        'short' => '#(-\w+(\.[\dxX])?)#',
    ),
);
//定义文章相关规则
//详情页
$rules[] = array(
    '#/v-#',
    array('mod' => 'article', 'action' => 'view'),
    array(
        'short' => '#(\d+-\d+)#',
    ),
);
//列表页
$rules[] = array(
    '#/n-#',
    array('mod' => 'article', 'action' => 'lists'),
    array(
        'short' => '#(\d+(-\d+)?)#',
    ),
);
//特殊需求
$rules[] = array(
    '#/about/$#',
    array('mod' => 'topic', 'action' => 'fourth'),
);
$rules[] = array(
    '#/ryzz/$#',
    array('mod' => 'article', 'action' => 'ryzz'),
);
$rules[] = array(
    '#/lxwm/$#',
    array('mod' => 'faq', 'action' => 'lxwm'),
);

//定义筛选页规则
$rules[] = array(
    '#/s(-|/)#',
    array('mod' => 'select', 'action' => 'rewriteSearch'),
    array(
        'short' => '#^/s-(([\w-])*)#',
        ),
);

//定义着落页规则
$rules[] = array(
    '#/g(-|/)#',
    array('mod' => 'goods', 'action' => 'rewriteSearch'),
    array(
        'short' => '#^/g-(([\w-])*)#',
        ),
);
?>