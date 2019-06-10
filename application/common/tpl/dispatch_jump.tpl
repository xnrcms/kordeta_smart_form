{__NOLAYOUT__}<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<!-- Apple devices fullscreen -->
<meta name="apple-mobile-web-app-capable" content="yes">
<!-- Apple devices fullscreen -->
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<!--CSS File Start-->
<link type="text/css" rel="stylesheet" href="/3.0/css/main.css">
<link type="text/css" rel="stylesheet" href="/3.0/css/page.css">
<link type="text/css" rel="stylesheet" href="/3.0/font/css/font-awesome.min.css"/>
<!--<link type="text/css" rel="stylesheet" href="/3.0/font/css/bootstrap.min.css"/>-->
<!--[if IE 7]>
  <link rel="stylesheet" href="/3.0/font/css/font-awesome-ie7.min.css">
<![endif]-->
<link type="text/css" rel="stylesheet" href="/3.0/package/jquery-ui/jquery-ui.min.css"/>
<link type="text/css" rel="stylesheet" href="/3.0/package/scrollbar/perfect-scrollbar.min.css"/>
<link type="text/css" rel="stylesheet" href="/3.0/css/form.css"/>
<link type="text/css" rel="stylesheet" href="/3.0/package/datetime/jedate/skin/jedate.css">
<style type="text/css">
    html, body { overflow: visible;}
</style>
<!--CSS File End-->
</head>
<body style="background-color: rgb(255, 255, 255); overflow: auto; cursor: default;">
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
    <a class="back" href="javascript:history.back();" title="返回列表">
        <i class="fa fa-arrow-circle-o-left"></i>
    </a>
      <div class="subject">
        <h3>操作信息提示&nbsp;&nbsp;</h3>
        页面即将在(<b id="wait" style="color:red;font-size:14px;"><?php echo($wait);?></b>)秒钟后自动跳转
      </div>
    </div>
  </div>
  <div id="explanation" class="explanation">
    <div id="checkZoom" class="title" style="margin-left: 10px;">
        <?php switch ($code) {
         case 1:
        ?>
        <div style="margin-left:10px;background: url('/3.0/images/icon.png') -180px 0 no-repeat; width: 30px;height: 30px;"></div>
        <h4 title="提示相关信息" style="color: blue;">操作成功</h4>
        <?php 
        break;
        case 0:
        ?>
        <div style="margin-left:10px;background: url('/3.0/images/icon.png') -150px 0 no-repeat; width: 30px;height: 30px;"></div>
        <h4 title="提示相关信息" style="color: red;">操作失败</h4>
        <?php
        break; 
        }
        ?>
        <span title="收起提示" id="explanationZoom"></span>
    </div>
    <ul>
        <?php switch ($code) {?>
            <?php case 1:?>
            <li style="background: none;padding-left: 0px;font-size: 16px;font-weight: bold;"><?php echo(strip_tags($msg));?></li>
            <?php break;?>
            <?php case 0:?>
            <li style="background: none;padding-left: 0px;font-size: 16px;font-weight: bold;"><?php echo(strip_tags($msg));?></li>
            <?php break;?>
        <?php } ?>
        <li>
        </li>
    </ul>
  </div>
</div>
<script type="text/javascript">
(function(){
    var jumpurl     = "<?php echo($url);?>";
    var wait        = document.getElementById('wait');
    var interval    = setInterval(function(){
        var time    = --wait.innerHTML;
        if(time <= 0) {
            location.href = jumpurl;
            clearInterval(interval);
        };
    }, 1000);
})();
</script>
</body>
</html>