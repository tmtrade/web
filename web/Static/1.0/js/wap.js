/**
 * Created with JetBrains PhpStorm.
 * User: Administrator
 * Date: 16-12-29
 * Time: 上午10:38
 * To change this template use File | Settings | File Templates.
 */
(function(doc, win) {

    var docEl = doc.documentElement,
        resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
        recalc = function() {
            var clientWidth = docEl.clientWidth;
            if (!clientWidth)
                return;
            if (clientWidth >= 770) {
                docEl.style.fontSize = '100px';
            } else {
                docEl.style.fontSize = 100 * (clientWidth / 770) + 'px';
            }
            $("body").css({"opacity":"1"})
        };

    if (!doc.addEventListener)
        return;
    win.addEventListener(resizeEvt, recalc, false);
    doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);


//阻止默认事件执行的函数

function stopPropagation(e) {
    if (e.stopPropagation)
        e.stopPropagation();
    else
        e.cancelBubble = true;
}