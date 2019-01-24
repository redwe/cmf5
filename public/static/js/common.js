/**
 * Created by Administrator on 2019/1/9 0009.
 */
function setdeafultpic(obj,id){
    if (typeof FileReader == 'undefined') {
        document.getElementById(id).InnerHTML = "<h1>当前浏览器不支持FileReader接口</h1>";
        //使选择控件不可操作
        document.getElementById(id).setAttribute("disabled", "disabled");
    }
    var file = obj.files[0];
    console.log(obj);console.log(file);
    console.log("file.size = " + file.size);  //file.size 单位为byte
    var reader = new FileReader();
    //读取文件过程方法
    reader.onloadstart = function (e) {
        console.log("开始读取....");
    }
    reader.onprogress = function (e) {
        console.log("正在读取中....");
    }
    reader.onabort = function (e) {
        console.log("中断读取....");
    }
    reader.onerror = function (e) {
        console.log("读取异常....");
    }
    reader.onload = function (e) {
        console.log("成功读取....");
        var img = document.getElementById(id+"_img");
        img.src = e.target.result;
        //或者 img.src = this.result;  //e.target == this
    }
    reader.readAsDataURL(file);
}

function isEmpty(content){
    if(content == "undefined" || content == null || content == ""){
        return false;
    }
    else
    {
        return true;
    }
}

function playSound(src,id){
    var _s = document.getElementById(id);
    if(src!='' && typeof src!=undefined){
        _s.src = src;
    }
}