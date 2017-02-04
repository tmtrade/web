//简单选项卡
function jc(name,curr,n)
{
    return;
    for(i=1;i<=n;i++)
    {
        var menu=document.getElementById(name+i);
        var cont=document.getElementById("con_"+name+"_"+i);
        menu.className=i==curr ? "up" : "";
        if(i==curr){
            cont.style.display="block";
        }else{
            cont.style.display="none";
        }
    }
}
$(document).ready(function(e) {
    //悬浮窗提醒标题
    _defLogin   = "<h6>登录</h6> <p style=\"margin-left: 10px;\"></p>";
    _xunjia     = "<h6>登录查看此商品出售价格</h6><p>一次登录后可查看全站所有出售商品价格</p>";
    //手机号码验证正则
    mobilereg = /^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d\d\d\d\d\d\d\d$/i;
    //邮箱验证正则
    emailreg = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
    //获取验证码
    $('#dl_wjmm,#dl_fsmm').click(function(){
        if($('#dl_sub').attr('ncc')){
            return false;
        }
        $('#dl_sub').attr('ncc',1);
        mobile 	= $.trim($('#loginUser').val());
        //验证手机号
        if(!mobile){
            $(".ms-errorTips2,#loginTips").show();
            $("#loginTips em").text('请输入手机号码');
            $("#loginTips").attr('flag',1);
            $('#dl_sub').attr('ncc',0);
            return false;
        }
        if(!mobilereg.test(mobile) || mobile.length!=11){
            $(".ms-errorTips2,#loginTips").show();
            $("#loginTips em").text('请输入正确的手机号码');
            $("#loginTips").attr('flag',1);
            $('#dl_sub').attr('ncc',0);
            return false;
        }
        //倒计时期间点击无效
        if((($(this).text()).indexOf('秒'))!=-1){
            $('#dl_sub').attr('ncc',0);
            return false;
        }
        ucNetwork.sendCode(mobile,$(this),'点击获取验证码');
    });
    //用户登录
    $('#dl_sub').click(function(){
        hideLoginTip(0);
        account 	= $.trim($('#loginUser').val());
        password 	= $.trim($('#loginPass').val());
        if(!account){
            $(".ms-errorTips2,#loginTips").show();
            $("#loginTips em").text('请输入手机号码');
            $("#loginTips").attr('flag',1);
            return false;
        }
        if(!mobilereg.test(account) || account.length!=11){
            $(".ms-errorTips2,#loginTips").show();
            $("#loginTips em").text('请输入正确的手机号码');
            $("#loginTips").attr('flag',1);
            return false;
        }
        if(!password){
            $(".ms-errorTips2,#loginTips").show();
            $("#loginTips em").text('请输入密码');
            $("#loginTips").attr('flag',2);
            return false;
        }
        //判断是验证码登录还是密码登录
        if($('#dl_sub').hasClass('yzm')){
            ucNetwork.verifyCode(account,password);//验证码登录
        }else{
            ucNetwork.userLog(account,password);//密码登录
        }
    });
    //登录弹窗失去焦点验证
    $('#loginUser').blur(function(){
        var tel 	= $.trim($('#loginUser').val());
        if(!tel){
            $(".ms-errorTips2,#loginTips").show();
            $("#loginTips em").text('请输入手机号码');
            $("#loginTips").attr('flag',1);
            return false;
        }
        if(!mobilereg.test(tel) || tel.length!=11){
            $(".ms-errorTips2,#loginTips").show();
            $("#loginTips em").text('请输入正确的手机号码');
            $("#loginTips").attr('flag',1);
            return false;
        }
        hideLoginTip(1);
    });
    $('#loginPass').blur(function(){
        var pass = $.trim($('#loginPass').val());
        if(!pass){
            $(".ms-errorTips2,#loginTips").show();
            $("#loginTips em").text('请输入密码');
            $("#loginTips").attr('flag',2);
            return false;
        }
        hideLoginTip(2);
    });
    //搜索框按钮触发
    $(".mj-search-submit-tm").click(function (e){
        var input_kw = $(".tm_kw");
        var kw_value = $.trim(input_kw.val());
        var special = RegExp(/[(\ )(\～)(\！)(\＠)(\＃)(\￥)(\％)(\……)(\＆)(\×)(\（)(\）)(\？)(\｜)(\｝)(\｛)(\【)(\】)(\＋)(\——)(\－)(\＝)(\；)(\：)(\“)(\”)(\‘)(\’)(\，)(\。)(\《)(\》)(\`)(\~)(\!)(\@)(\#)(\$)(\%)(\^)(\&)(\*)(\()(\))(\-)(\_)(\+)(\=)(\[)(\])(\{)(\})(\|)(\\)(\;)(\:)(\')(\")(\,)(\.)(\/)(\<)(\>)(\?)(\)]+/);
        if ( kw_value != '输入你想买的商标名称/商标号' && special.test(kw_value) ){
            layer.tips('不要输入特殊字符哦', input_kw, {
                tips: [3, '#fc7d28'],
                time: 2000
            });
            return false;
        }
        if (kw_value != '输入你想买的商标名称/商标号' && kw_value.length > 30){
            layer.tips('只搜索前30个字符哦，请稍等...', input_kw, {
                tips: [3, '#fc7d28'],
                time: 1000
            });
            kw_value = kw_value.substr(0, 30);
            input_kw.val(kw_value);
            return false;
        }
        if ( kw_value == '输入你想买的商标名称/商标号' || kw_value==""){
            layer.tips('请输入你要查询的商标名称/商标号', input_kw, {
                tips: [3, '#fc7d28'],
                time: 1000
            });
            return false;
        }
        setSearch(kw_value,'商标名称');
        setEvent('搜索-商标名称',kw_value);
        sendBehavior(100,ptype, e.pageX, e.pageY,kw_value,$('#searchForm').submit());
    });
    //专利搜索框按钮触发
    $(".mj-search-submit-pt").click(function (e){
        var input_kw = $(".pt_kw");
        var kw_value = $.trim(input_kw.val());
        var special = RegExp(/[(\ )(\～)(\！)(\＠)(\＃)(\￥)(\％)(\……)(\＆)(\×)(\（)(\）)(\？)(\｜)(\｝)(\｛)(\【)(\】)(\＋)(\——)(\－)(\＝)(\；)(\：)(\“)(\”)(\‘)(\’)(\，)(\。)(\《)(\》)(\`)(\~)(\!)(\@)(\#)(\$)(\%)(\^)(\&)(\*)(\()(\))(\-)(\_)(\+)(\=)(\[)(\])(\{)(\})(\|)(\\)(\;)(\:)(\')(\")(\,)(\/)(\<)(\>)(\?)(\)]+/);
        if ( kw_value != '输入你想买的专利名称/专利号' && special.test(kw_value) ){
            layer.tips('不要输入特殊字符哦', input_kw, {
                tips: [3, '#fc7d28'],
                time: 2000
            });
            return false;
        }
        if ( kw_value == '输入你想买的专利名称/专利号' || kw_value==""){
            layer.tips('输入你想买的专利名称/专利号', input_kw, {
                tips: [3, '#fc7d28'],
                time: 1000
            });
            return false;
        }
        if (kw_value != '输入你想买的专利名称/专利号' && kw_value.length > 30){
            layer.tips('只搜索前30个字符哦，请稍等...', input_kw, {
                tips: [3, '#fc7d28'],
                time: 1000
            });
            kw_value = kw_value.substr(0, 30);
        }
        setSearch(kw_value,'专利名称');
        setEvent('搜索-专利名称',kw_value);
        $('#searchPtForm').submit();
    });
    //登录提示
    $("input").each(function(){
        var spanT1= $(this).parent().parent().find($(".mj-inpuVs")).text();
        $(this).focus(function(){
            $(this).parent().parent().find($(".mj-inpuVs")).text("");
        });
        $(this).blur(function(){
            if($(this).val()===""){
                $(this).parent().parent().find($(".mj-inpuVs")).text(spanT1);
            }else{
                $(this).parent().parent().find($(".mj-inpuVs")).text("");
            }
        });
    });
    $('.mj-inpuVs').click(function(){
        $(this).parent().parent().find($("input")).val('').focus();
    });
    //邮件反馈的js
    $('.feedback_content,.feedback_contact').focus(function(){
        $('.feedback_s').hide();
        $('.feedback_f').hide();
    });
    $('.feedback_content').blur(function(){
        var feedback_content = $.trim($('.feedback_content').val());
        if(feedback_content==''){
            $('.feedback_f').find('span').html('请填写意见反馈!');
            $('.feedback_f').show();
            return false;
        }else if(feedback_content.length>=500){
            $('.feedback_f').find('span').html('意见反馈字数过多!');
            $('.feedback_f').show();
            return false;
        }
        $('.feedback_f').find('span').html('');
        $('.feedback_f').hide();
    });
    $('.feedback_contact').blur(function(){
        var feedback_contact = $.trim($('.feedback_contact').val());
        if(feedback_contact==''){
            //$('.feedback_f').find('span').html('请填写您的联系方式!');
            //$('.feedback_f').show();
            return false;
        }else {
            if(/^\d+$/.test(feedback_contact)){
                if(mobilereg.test(feedback_contact)==false){
                    $('.feedback_f').find('span').html('手机号码不正确!');
                    $('.feedback_f').show();
                    return false;
                }
            }else{
                if(emailreg.test(feedback_contact)==false){
                    $('.feedback_f').find('span').html('邮箱不正确!');
                    $('.feedback_f').show();
                    return false;
                }
            }
        }
        $('.feedback_f').find('span').html('');
        $('.feedback_f').hide();
    });
    //提交事件
    $('#feedback_btn').click(function(){
        //未返回结果点击无效
        if($(this).attr('flag')==1){
            return false;
        }
        $(this).attr('flag',1);
        $('.feedback_s').hide();
        //验证文本域
        var feedback_content = $.trim($('.feedback_content').val());
        if(feedback_content==''){
            $('.feedback_f').find('span').html('请填写意见反馈!');
            $('.feedback_f').show();
            $(this).attr('flag',0);
            return false;
        }else if(feedback_content.length>=500){
            $('.feedback_f').find('span').html('意见反馈字数过多!');
            $('.feedback_f').show();
            $(this).attr('flag',0);
            return false;
        }
        //验证输入框
        var feedback_contact = $.trim($('.feedback_contact').val());
        if(feedback_contact!=''){
            if(/^\d+$/.test(feedback_contact)){
                if(mobilereg.test(feedback_contact)==false){
                    $('.feedback_f').find('span').html('手机号码不正确!');
                    $('.feedback_f').show();
                    $(this).attr('flag',0);
                    return false;
                }
            }else{
                if(emailreg.test(feedback_contact)==false){
                    $('.feedback_f').find('span').html('邮箱不正确!');
                    $('.feedback_f').show();
                    $(this).attr('flag',0);
                    return false;
                }
            }
        }
        $('.feedback_f').find('span').html('');
        $('.feedback_f').hide();
        //提交数据
        var feedback_data = "content="+feedback_content+'&contact='+feedback_contact;
        $.post('/index/sendEmail',feedback_data,function(data){
            if(data.code==1){
                $('.feedback_s').find('span').html('提交成功，感谢您的吐槽!');
                $('.feedback_s').show();
                setEvent('右侧浮动栏7','意见反馈');
                //重置为空
                $('.feedback_content').val('');
                $('.feedback_contact').val('');
            }else{
                $('.feedback_f').find('span').html('提交失败');
                $('.feedback_f').show();
            }
            $('#feedback_btn').attr('flag',0);
        },'json');
    });
});
//成功失败弹窗
function getLayer(obj) {
    $(".mj-bcTips").hide();
    obj.show();
    layer.open({
        type: 1,
        title: false,
        closeBtn: false,
        skin: 'yourclass',
        content: obj
    });
    $(".mj-close").bind("click", function () {
        layer.closeAll();
        $('.ddw_error21').text('可能因为网络问题或其他原因导致');
    });
}
//询价登录回调
function userLogCallback(Obj,data){
    $.each(Obj,function(i,n){
         //用户登录情况下
        if(n.code==1) {
            //弹出成功框
            $(".ms-errorTips2,#loginTips").show();
            $("#loginTips em").text('登录成功');
            $("#loginTips").attr('flag', 2);
            //设置cookie
            addCookie('task_aim',window.nowData);
        }else if(n.code==3){
            $(".ms-errorTips2,#loginTips").show();
            $("#loginTips em").text(n.msg);
            $("#loginTips").attr('flag',0);
        }else{
            $(".ms-errorTips2,#loginTips").show();
            $("#loginTips em").text('用户名或密码错误');
            $("#loginTips").attr('flag',2);
        }
    });
}
//发送验证码
function sendCodeCallback(Obj,htmlobj,title){
    $.each(Obj,function(i,n){
        if(n.code > 0 ){//发送成功
            $('#dl_sub').addClass('yzm');//添加额外的class yzm 判断为验证码登录
            timer(60, $('.aaaa'), title);//倒计时
        }else{
            $(".ms-errorTips2,#loginTips").show();
            var msg = '发送失败';
            if(n.msg){
                msg = n.msg;
            }
            $("#loginTips em").text(msg);
            $("#loginTips").attr('flag',0);
        }
        $('#dl_sub').attr('ncc',0);
    });
}
//验证验证码是否合法
function verifyCodeCallback(Obj,account,code){
    $.each(Obj,function(i,n){
        if(n.code==1){
            $('.Not_logged').hide();
            $('.logged').show();
            //设置cookie
            addCookie('task_aim',window.nowData);
            $("#loginTips em").text('登录成功');
            //登录
            ucNetwork.logCode(account,code);
        }else{
            $(".ms-errorTips2,#loginTips").show();
            $("#loginTips em").text(n.mess);
            $("#loginTips").attr('flag',0);
        }
    });
}
//调用登录弹窗
function getLogin(title,tel,isExist){
    $("#loginUser").val('');
    $("#loginPass").val('');
    $("#dl_ts").hide();
    $('#loginTips').hide();
    var title = (title=='')?_defLogin:title;
    if (!isExist){
        //账户不存在
        $("#dl_wjmm").hide();//忘记密码(已存在账号)
        $("#dl_fsmm").show();//发送密码(不存在账号)
        $("#dl_fsmm").addClass('aaaa');//添加类
        //触发点击事件
        $('.mj-inpuVs').html('');
        $('#loginUser').val(tel);
        $('#dl_fsmm').click();
        //timer(60, $('.aaaa'), title);//倒计时
    }else{
        //账户存在
        $("#dl_wjmm").addClass('aaaa');//添加类
        $('.mj-inpuVs').html('');
        $('#loginUser').val(tel);
    }
    if(!tel){
        $("#loginUser").parent().parent().find($(".mj-inpuVs")).text("请输入手机号");
    }
    $("#loginPass").parent().parent().find($(".mj-inpuVs")).text("请输入密码");
    $("#dl_title").html(title);
    //弹出登录框
    $('#loginFormDiv').show();
    boxBg();
    reCal($('#loginFormDiv'));
}
//用户询价
function xunjia(obj,flag){
    var index = layer.load(1, {
        shade: [0.1,'#fff'] //0.1透明度的白色背景
    });
    if(typeof flag=='undefined'){
        flag = 1;
    }
    var buyData = new Object();
    if(flag==1){ //从li中获取数据
        //组装提交数据
        var mydata = $(obj).closest('li').find('.xunjia_data');
        var remarks = mydata.attr('remarks');
        buyData['tid'] 			= mydata.attr('tid');
        buyData['trademark'] 	= mydata.attr('number');
        buyData['class'] 		= mydata.attr('data_class');
        buyData['type'] 		= mydata.attr('p_type');
        buyData['subject'] 		= '求购信息';
        buyData['remarks'] 		= remarks;
    }else{ //直接读取数据
        buyData = obj;
    }
    //用户是否登录
    if(login_mobile){
        buyData['tel'] = login_mobile;
        buyData['remarks'] += (';电话号码：' + login_mobile);
        //提交到分配系统中
        ucBuy.buyAdd(buyData);
    }else if(nick_name){
        //弹出绑定手机框
        getLayer($('#goCenter'));
        layer.close(index);
    }else{
        //保存数据到全局变量中
        window.nowData = JSON.stringify(buyData);
        //弹出登录框
        getLogin(_xunjia,'',1);
        layer.close(index);
    }
    return false;
}
//添加到分配系统回调函数
function buyAddCallback(Obj){
    $.each(Obj,function(i,n){
        //弹窗提示
        if(n.code==0){
            layer.msg('您已经购买过该商品');
        }else if(n.code==-1){
            if(n.msg=='key验证失败'){
                getLayer($('#mj-submitteFF'));
                countDown();//倒计时
            }else{
                $('.ddw_error21').text(n.msg);
                getLayer($('#mj-submitteF'));
            }
        }else{
            getLayer($('#mj-submitteS'));
            $('.opt_btn').replaceWith("<a href='javascript:;' class='opt_btn'>求购信息已提交</a>");
        }
        layer.closeAll('loading');
    });
}
//聊天函数
function goChat(){
    var type = _YZC_ONLINE_;
    if ( type=='yzc' )
    {
        window.open("http://p.qiao.baidu.com/cps/chat?siteId=9503594&userId=21149642");
    }else{
        window.open("http://chat.looyu.com/chat/chat/p.do?c=46344&f=123997&g=51817");
    }
}
//隐藏登录提示信息
function hideLoginTip(num){
    if($("#loginTips").attr('flag')==num){
        $(".ms-errorTips2").hide();
    }
}
//失败弹窗倒计时刷新
function countDown(){
    var timer = setInterval(function(){
        $('.ddw_time').html(function(n,now){
            if(now==1){
                window.location.reload();
            }else{
                return now-1;
            }
        });
    },1000)
}
//piwik设置事件
function setEvent(module,name,page){
    if(typeof page == 'undefined'){
        page = ptype;
    }
    page = analyzePage(page);
    _paq.push(['trackEvent', page, module, name]);
}
//添加站内搜索
function setSearch(keyword,category){
    if(typeof category == 'undefined'){
        category = '商标';
    }
    _paq.push(['trackSiteSearch', keyword, category]);
}
//绑定超链接事件
$(document).on('click','a',function(e){
    var module = $(this).attr('module');
    if(module){
        var addmsg = $(this).attr('addmsg');//额外信息
        setEvent(module,addmsg);
    }
});
//解析页面
function analyzePage(ptype){
    var str = '';
    switch (ptype){
        case '1':
            str = '首页';break;
        case '2':
            str = '商标筛选页';break;
        case '3':
            str = '特价页';break;
        case '4':
            str = '商标详情页';break;
        case '5':
            str = '案例列表页';break;
        case '6':
            str = '案例详情页';break;
        case '8':
            str = '专题列表页';break;
        case '9':
            str = '专题详情页';break;
        case '10':
            str = '专利筛选页';break;
        case '11':
            str = '专利详情页';break;
        case '12':
            str = '我要买';break;
        case '13':
            str = '我要卖';break;
        case '14':
            str = '商品聚合页';break;
        case '16':
            str = 'faq列表页';break;
        case '17':
            str = 'faq详情页';break;
        case '18':
            str = '活动页';break;
        case '19':
            str = '商标打包页';break;
        case '20':
            str = '需求大厅列表页';break;
        case '21':
            str = '需求大厅详情页';break;
        default:
            str = '未知';
    }
    return str;
}

//生成弹层背景
function boxBg(){
    var boxbg = $(document.createElement('div')),
        docheight = $(document).height();
    boxbg.addClass('ms-boxBg');
    $('body').append(boxbg);

    $('.ms-boxBg').css({
        opacity: "0.6",
        height: docheight
    });
};
//关闭弹层
function boxHide(objId){
    $('.'+objId).hide();
    $('.ms-boxBg').remove();
    isOpen = 0;
    doBuyFunc = '';
};
//弹出层居中
function reCal(obj){
    var screenWidth,screenHeigth,scollTop,objLeft,objTop;
    function box(){
        screenWidth = $(window).width(); //当前窗口的宽度
        screenHeigth = $(window).height();  //当前窗口的高度
        scollTop = $(document).scrollTop(); //获取滚动条距顶部的偏移
        objLeft = (screenWidth - obj.outerWidth()) / 2; //弹出框距离左侧距离
        objTop = (screenHeigth - obj.outerHeight()) / 2; //弹出框距离顶部的距离

        //css定位弹出层
        obj.css({
            left: objLeft,
            top:  objTop
        });
    };
    box();
    isOpen = 1; //弹出框设定一个变量 置为1 说明弹出框是打开状态
    //当浏览器窗口大小改变时或者滚动条滚动时...
    $(window).on('resize scroll',function(){
        //判断此变量是否为1 如果是就执行
        if(isOpen == 1){
            box();
        }
    });
};