/**
 * Created by phoenix on 5/29/17.
 */

/**
 * 检查是否正常url格式
 * @param dom
 * @returns {boolean}
 */
function checkUrl(dom){
    var reg=/(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?/;
    return reg.test(dom) ? true : false;
}

function checkNum(num){
    var reg = /\d{1,}\.{0,1}\d{0,}/;
    return reg.test(num) ? true : false;
}

function getChinaLength(str, num){
    var reg = /[\u4e00-\u9fa5]/g;
    return str.match(reg).length;
}

function checkFloat(num) {
    
}
