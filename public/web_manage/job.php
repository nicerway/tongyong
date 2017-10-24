<?php
require './include/common.inc.php';
define('TABLE_NEWS',1);
require WEB_ROOT.'./include/chkuser.inc.php';
$table = 'news';
$showname = 'master';

//条件
$map = array('pid'=>$pid,'ty'=>$ty,'tty'=>0);

###########################筛选开始
$id    =   I('get.id','','trim');if(!empty($id))$map['id'] = array('like',"%$id%");
$title =   I('get.title','','trim');if(!empty($title))$map['title'] = array('like',"%$title%");
$cid =   I('get.cid',0,'intval');
$certificate_lid =   I('get.certificate_lid',0,'intval');
$infotypeid =   I('get.infotypeid',0,'intval');
$trainingid =   I('get.trainingid',0,'intval');
if(!empty($cid)){
    $map['cid'] =$cid;
    $cname=v_id($cid,"name","cmember");
}else{
    $cname='管理员';
}
if(!empty($certificate_lid)) $map['certificate_lid'] = $certificate_lid;
if(!empty($infotypeid)) $map['infotypeid'] = $infotypeid;

if(!empty($tty)) $map['tty'] = $tty;
$psize   =   I('get.psize',30,'intval');
$pageConfig = array(
    /*条件*/'where' => $map,
    /*排序*/'order' => 'isgood desc,disorder desc,sendtime desc',
    /*条数*/'psize' => $psize,
    /*表  */'table' => $table,
    );
list($data,$pagestr) = Page::paging($pageConfig);
//_sql();
########################分页配置结束
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<?php include('js/head'); ?>
</head>
<body>
	<div class="content clr">
        <?php Style::weizhi() ?>
        <div class="right clr">
          <form class="" id="jsSoForm" style="position: relative">
            <input type="hidden" name="pid" value="<?=$pid?>" />
            <input type="hidden" name="ty"  value="<?=$ty?>"  />
            <input type="hidden" name="tty" value="<?=$tty?>" />
            <!-- <b>显示</b><input style="width:50px;" name="psize" type="text" class="dfinput" value="<?=$psize?>"/>条 -->
            <!-- <b>编号</b><input name="id" type="text" class="dfinput" value="<?=$id?>"/> -->
        <?php if ($pid<5): ?>
            <input type="hidden" name="cid" id="cid"  value="0">
             选择企业： <input type="text" id="cname" class="dfinput" value="<?=$cname?>">
            <div class="qyxf" style="display: none">
                <ul>
                    <li data-id="0">平台管理员</li>
                </ul>
            </div>
        <?php endif ?>
              <?php if ($tty==54) {
                  $d = config('webarr.certificate');
                  Output::select2($d, '选择证书类型', 'certificate_lid');
              }elseif($ty==64){
                  $d = config('webarr.infotypeid');
                  Output::select2($d, '院校信息类型', 'infotypeid');
              }elseif($pid==2){
                  $d = config('webarr.trainingid');
                  Output::select2($d, '培训方式', 'trainingid');
              } ?>
        关键字<input name="title" type="text" class="dfinput" value="<?=$title?>"/>
        <input name="search" type="submit" class="btn" value="搜索"/></td>
    </form>
            <script type="text/javascript">
                $(function(){

                    $(".qyxf ul").on("click","li",function(){

                        var cid=$(this).data("id");
                        var cname=$(this).html();
                        $("#cid").val(cid)
                        $("#cname").val(cname)
                        $(".qyxf").hide()
                    })
                })

                $("#cname").click(function(){
                    $("#cname").val('')
                    var key=$(this).val()
                    $.get("include/json.php?action=xzqy&key="+key, function (data) {
                      //  alert(data)
                        $(".qyxf ul").html(data)
                    })
                    $(".qyxf").show()
                })
                $("#cname").keyup(function(){
                    var key=$(this).val()
                    $.get("include/json.php?action=xzqy&key="+key, function (data) {
                      //  alert(data)
                        $(".qyxf ul").html(data)
                    })
                    $(".qyxf").show()
                })
            </script>
    <div class="zhengwen clr">
      <div class="zhixin clr">
        <ul class="toolbar">
            <li>&nbsp;<input style="display:none" type="checkbox"><i id="sall" class="alls" onclick="selectAll(this)">&nbsp;</i><label style="cursor:pointer;font-size:9px" onclick="selectAll(document.getElementById('sall'))" for="">全选</label></li></li>
        </ul>
        <a href="?<?=queryString()?>" class="zhixin_a2 fl"></a><!-- 刷新  -->
        <a href="<?=getUrl(queryString(true),$showname.'_pro')?>" target="righthtml" class="zhixin_a3 fl"></a><!-- 添加  -->
        <input id="del" type="button" class="zhixin_a4 fl"/><!-- 删除  -->
        <?php if (false && 5 == $showtype): // || 3 == $pid ?>
        <a style="background:none;border:1px solid;line-height:28px;text-align:center" href="content.php?<?=queryString()?>" class="fl">编辑详情</a>
    <?php endif ?>
</div>
</div>
<div class="neirong clr">
    <table cellpadding="0" cellspacing="0" class="table clr">
       <tr class="first">
        <td onclick="selectAll(document.getElementById('sall'))" style="font-size:8px;cursor:pointer" width="24px">全选</td>
        <td width="24px">编号</td> <td width="200px">操作</td>

    <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/    if ($showtype==1):/*＜＞＜＞新闻＜＞＜＞*/?>
        <td> 图 </td>
        <td> 标题 <span class="fr"></td>
        <!-- <td> 浏览次数 </td> -->
    <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==5):/*＜＞＜＞单条＜＞＜＞*/?>
        <td> 标题 </td>
    <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==9):/*＜＞＜＞培训方式＜＞＜＞*/?>
        <td> 图 </td>
        <td> 名称 </td>
       <td> 培训方式 </td>
        <td> 报名人数 </td>
        <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==10):/*＜＞＜＞产品分类＜＞＜＞*/?>
        <td> 名称 </td>
    <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==11):/*＜＞＜＞图文列表＜＞＜＞*/?>
        <!-- <td width="24px"> 配图 </td> -->
        <td> 配图 </td>
        <td> 信息 </td>
    <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==12):/*＜＞＜＞路线＜＞＜＞*/?>
    <td> 配图 </td>
    <td> 标题 </td>
    <td> 详情页图片 </td>
    <td> 目的地 </td>
    <td> 报名人数 </td>
    <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==13):/*＜＞＜＞需报名新闻＜＞＜＞*/?>
    <!-- <td width="24px"> 配图 </td> -->
        <?php if($tty<>60){?>
            <td> 配图 </td>
        <?php } ?>

    <td> 标题 </td>
    <td> 报名人数 </td>
   <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==15):/*＜＞＜＞常见问题＜＞＜＞*/?>
       <td> 问题 </td>
        <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==16):/*＜＞＜＞职业证书＜＞＜＞*/?>
        <td> 证书名称 </td>
        <td> 配图 </td>
        <td> 所属分类 </td>
        <td> 报名人数 </td>

   <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/endif?>
           <td width="10%">发布者</td>
    <td width="10%">发布时间</td>
</tr>
<?php
    foreach ($data as $key => $bd) : extract($bd);

                            #生成修改地址
    $query = queryString(true);
    $query['id'] = $id;
    $editUrl = getUrl($query, $showname.'_pro');
                            #时间
    $time =  date('Y-m-d H:i',$sendtime);
    $img1 =  '<img src="'.src($img1).'" width="80" />';
if($cid){
    $publisher=v_id($cid,"name","cmember");
}else{
    $publisher="平台管理员";
}

    // $title = '<a href="' . U('blog/view', ['id'=>$id]) . '" target="_blank">'.$title.'</a>';
?>
<tbody>
    <tr>
        <td><input id="delid<?=$id?>" name="del[]" value="<?=$id?>" type="checkbox"><i class="layui-i">&nbsp;</i></td>
        <td><?=$key+1?></td>
        <td>
            <a href="<?=$editUrl?>" class="thick ">编辑</a>|
            <?php if ($ty==10 || $showtype==1): //团队?>
                <a data-class="btn-warm" class="json <?=$istop==1?'btn-warm':'' ?>" data-url="isindex&id=<?=$id?>"><?=config('webarr.isindex')[$istop] ?></a>|
            <?php endif ?>
            <a data-class="btn-danger" class="json <?=$isgood==1?'btn-danger':'' ?>" data-url="isgood&id=<?=$id?>"><?=Config::get('webarr.isgood')[$isgood] ?></a>|
            <a data-class="btn-warm" class="json <?=$isstate==1?'':'btn-warm' ?>" data-url="isstate&id=<?=$id?>"><?=Config::get('webarr.isstate')[$isstate] ?></a>|
            <!-- <a href="<?=$editUrl?>" class="thick edits">编辑</a>| -->
            <a href="javascript:;" data-id="<?=$id?>" data-opt="del" class="thick del">删除</a>
        </td>
        <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/if ($showtype==1):/*＜＞＜＞新闻＜＞＜＞*/?>
        <td><?=$img1?></td>
        <td><?=$title?></td>
        <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==5):/*＜＞＜＞单条＜＞＜＞*/?>
            <td><?=$title?><!-- <span class="fr"><a href="link.php?showtype=6&istop=<?php echo $id ?>">下属列表</a></span> --></td>
            <td><a href="pic.php?ti=<?=$id?>">图集(<?php echo M('pic')->where("ti=$id and isstate=1")->count()?>条)</a></td>
        <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==9):/*＜＞＜＞产品＜＞＜＞*/?>
        <td><?=$img1?></td>
        <td><?=$title?>)</a></td>
        <td> <?=Config::get('webarr.trainingid')[$trainingid]?> </td>
        <td><a href="baoming.php?bid=<?php echo $id?>">共有（<?php echo M('enroll')->where("bid=$id")->count();?>）报名<span></span>(有<?php echo M('enroll')->where("bid=$id and isstate=0")->count(); ?>未审核)</a></td>
        <?php if ($ty==11): ?><td><?=isset($d1[$istop]) ? $d1[$istop] : '','&emsp;',isset($d2[$istop2]) ? $d2[$istop2] : '' ?></td><?php endif ?>
        <!-- <td><?=$hits?></td> -->
        <!-- <a href="pic.php?ti=<?=$id?>&cid=5">户型介绍(<?//=M('pic')->where("ti=$id and cid=5 and isstate=1")->count()?>条)</a> -->
        <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==10):/*＜＞＜＞产品分类＜＞＜＞*/?>
        <td><?=$title?><span class="fr" style="display:none"><?php echo M('news')->where("istop=$id")->count(); ?></span></td>
<!-- <td><span data-content="<?=$introduce?>" class="lookinfo layui-btn layui-btn-primary layer-demolist">查看简介</span></td> -->
<?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==11):/*＜＞＜＞图文列表＜＞＜＞*/?>
        <td><?=$img1?></td>
        <td><?=$title,'&emsp;',$ftitle,'&emsp;',$name?><a href="pic.php?ti=<?php echo $id?>">图集(<?php echo M('pic')->where("ti=$id")->count(); ?>)</a>&emsp;&emsp;<a href="link.php?showtype=5&istop=<?php echo $id ?>">历程(<?php echo M('news')->where("istop=$id")->count(); ?>)</a></td>
        <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==12):/*＜＞＜＞路线＜＞＜＞*/?>
            <td><?=$img1?></td>
            <td><?=$title?></td>
            <td><a href="pic.php?ti=<?php echo $id?>">图集(<?php echo M('pic')->where("ti=$id")->count(); ?>)</a></td>
            <td><?=$destination?></td>
            <td><a href="baoming.php?bid=<?php echo $id?>">共有（<?php echo M('enroll')->where("bid=$id")->count();?>）报名<span></span>(有<?php echo M('enroll')->where("bid=$id and isstate=0")->count(); ?>未审核)</a></td>
        <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==13):/*＜＞＜＞报名新闻＜＞＜＞*/?>
        <?php if($tty<>60){?>
                <td><?=$img1?></td>
            <?php } if($ty==64){?>
                <td> <?=Config::get('webarr.infotypeid')[$infotypeid]?> </td>
            <?php }?>

            <td><?=$title?></td>
            <td><a href="baoming.php?bid=<?php echo $id?>">共有（<?php echo M('enroll')->where("bid=$id")->count();?>）报名<span></span>(有<?php echo M('enroll')->where("bid=$id and isstate=0")->count(); ?>未审核)</a></td>
        <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==15):/*＜＞＜＞常见问题＜＞＜＞*/?>
            <!-- <td width="24px"> 配图 </td> -->
            <td> <?=$title?> </td>
            <?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/elseif ($showtype==16):/*＜＞＜＞职业证书＜＞＜＞*/?>
            <td> <?=$title?> </td>
            <td><?=$img1?></td>
            <td> <?=Config::get('webarr.certificate')[$certificate_lid]?> </td>
            <td><a href="baoming.php?bid=<?php echo $id?>">共有（<?php echo M('enroll')->where("bid=$id")->count();?>）报名<span></span>(有<?php echo M('enroll')->where("bid=$id and isstate=0")->count(); ?>未审核)</a></td>

<?php /*＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞＜＞*/endif?>

     <td><?=$publisher?></td>
     <td><?=$time?></td>
 </tr>
<?php endforeach?>
<?php include('js/foot'); ?>
<!-- <td><?=$img1?><a class="lookPic" href="javascript:;" data-id="<?=$id?>">添加更多图片(<?=M('pic')->where("ti=$id")->count()?>个)</a></td> -->