@extends('business.layouts.public')
<?php define('AT_CU', 1);$certified_status = $user['certified_status']?>
@section('inbox')
    @if($certified_status == 0 || $certified_status == 3)
        <div class="authentication-notice">
            <div class="authentication-guide">
                <img src="/img/close-icon.png"/>
                <div class="guide-div">
                    <p class="guide-title">
                        未通过企业认证
                        <img src="/img/gan-tan.png"/>
                    </p>
                    <p class="guide-content">企业还未认证，平台将让企业享受更多的权限</p>
                </div>
                <div class="authentication-guide-txt">
                    根据《中华人民共和国网络安全法》，使用中国职业培训网平台进行互联网招聘或培训的机构或组织，须提交机构与经办人个人的认证材料。经审核预备案通过后，方可正常使用中国职业培训网平台实施招聘、培训或信息发布等行为。根据机构或组织性质不同，须提交不同的资质认证材料。详见本页“上传证照说明”。
                </div>
            </div>
            <div class="authentication-form">
                <form class="form" action="{{route('b_certification')}}">
                    <div class="authentication-form-dv clearfix">
                        <span class="authentication-form-left fl"><b>*</b>企业姓名</span>
                        <div class="authentication-form-right fr">
                            <input readonly class="authen-form-inp" type="text" value="{{$user['business_name']}}"/>
                        </div>
                    </div>
                    <div class="authentication-form-dv clearfix">
                        <span class="authentication-form-left fl"><b>*</b>注册号</span>
                        <div class="authentication-form-right fr">
                            <input name="registerid" class="authen-form-inp" type="text"/>
                        </div>
                    </div>
                    <div class="authentication-form-dv clearfix">
                        <span class="authentication-form-left fl"><b>*</b>法定代表人</span>
                        <div class="authentication-form-right fr">
                            <input name="legal" class="authen-form-inp" type="text"/>
                        </div>
                    </div>
                    <div class="authentication-form-dv clearfix">
                        <span class="authentication-form-left fl"><b>*</b>营业期限</span>
                        <div class="authentication-form-right fr">
                            <div class="authentication-form-dv clearfix">
                                <span class="authen-form-time">xxxx年xx月xx日 至 </span>
                                <input name="business_time" onclick="new Calendar().show(this);" readonly="readonly" class="authen-time-inp" type="text"/>
                            </div>
                            <div class="authentication-form-dv">
                                <input name="islonger" class="authen-form-radio" type="radio"/><i>长期</i>
                                <span class="authentication-span">若无营业期限则选择长期</span>
                            </div>
                        </div>
                    </div>
                    <div class="authentication-form-dv clearfix">
                        <span class="authentication-form-left fl"><b>*</b>上传凭证</span>
                        <div class="authentication-form-right fr">
                            <div class="upload-file">
                                <p class="upload-file-p">上传营业执照图片</p>
                                <div class="upload-img">
                                    <img id="auth-img" src="/img/renzheng-img.jpg"/>
                                    <p>选择电脑图片上传</p>
                                </div>
                                <input name="uploadimg" onchange="previewImage(this,'auth-img')" class="upload-file-inp" type="file"/>
                            </div>
                            <div class="upload-file-intro">
                                <a href="javascript:;">【上传证照说明】</a>
                                <div class="Supplement-content">
                                    <table width="100%">
                                        <tr>
                                            <th class="Supplement-th1">机构类型/单位性质</th>
                                            <th class="Supplement-th2">所需证照资料</th>
                                            <th class="Supplement-th3">说明</th>
                                            <th class="Supplement-th4">认证失效</th>
                                        </tr>
                                        <tr>
                                            <td>有营业执照企业</td>
                                            <td>营业执照</td>
                                            <td></td>
                                            <td>1-3个工作日</td>
                                        </tr>
                                        <tr>
                                            <td>事业单位</td>
                                            <td>事业单位法人证书/组织机构代码证</td>
                                            <td></td>
                                            <td>1-3个工作日</td>
                                        </tr>
                                        <tr>
                                            <td>国家机关</td>
                                            <td>组织机构代码证/上级单位下发的红头成立批复</td>
                                            <td>需体现单位名称、办公地域 上级单位盖章</td>
                                            <td>1-3个工作日</td>
                                        </tr>
                                        <tr>
                                            <td>银行</td>
                                            <td>中国银监会批复文件</td>
                                            <td>需体现单位名称、办公地域 上级单位盖章</td>
                                            <td>1-3个工作日</td>
                                        </tr>
                                        <tr>
                                            <td>医院</td>
                                            <td>医疗机构执业许可证/上级单位下发的红头
                                                设立批复
                                            </td>
                                            <td></td>
                                            <td>1-3个工作日</td>
                                        </tr>
                                        <tr>
                                            <td>学校/下级学院</td>
                                            <td>办学许可证/民办非企业单位登记证书</td>
                                            <td>下级学院：学校办学许可证（加盖章上下级双发公章）</td>
                                            <td>1-3个工作日</td>
                                        </tr>
                                        <tr>
                                            <td>律师事务所</td>
                                            <td>律师事务所执业许可证</td>
                                            <td>需体现单位名称、办公地域</td>
                                            <td>1-3个工作日</td>
                                        </tr>
                                        <tr>
                                            <td>社会团体</td>
                                            <td>社会团体登记法人证书</td>
                                            <td></td>
                                            <td>1-3个工作日</td>
                                        </tr>
                                        <tr>
                                            <td>港澳台公司</td>
                                            <td>公司注册证书/营利事业登记证</td>
                                            <td>需体现届满日期</td>
                                            <td>1-3个工作日</td>
                                        </tr>
                                        <tr>
                                            <td>外商独资</td>
                                            <td>外国机构资质+当地驻华大使馆出具的境外
                                                企业资质审核涵
                                            </td>
                                            <td></td>
                                            <td>1-3个工作日</td>
                                        </tr>
                                        <tr>
                                            <td>代表处</td>
                                            <td>企业常驻机构代表登记证</td>
                                            <td></td>
                                            <td>1-3个工作日</td>
                                        </tr>
                                        <tr>
                                            <td>其他</td>
                                            <td>客户自行填写</td>
                                            <td>研究所/杂志社/报社/记者站</td>
                                            <td>1-3个工作日</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="authentication-form-dv clearfix">
                        <div class="authentication-form-right fr">
                            <p class="authen-tishi">恳请您上传有效、清晰地营业执照图片（最多上传<b>1</b>张,每张最大<b>10M</b>）</p>
                            <p class="authen-tishi">企业营业执照仅用于审核，不会向第三方透露，请放心上传！</p>
                        </div>
                    </div>
                    <div class="authentication-form-dv clearfix">
                        <div class="authentication-form-right fr">
                            <input onclick="return model(this)"  class="authen-sub" type="submit" value="申请认证"/>
                        </div>
                    </div>
                    {{csrf_field()}}
                </form>
            </div>
        </div>
    @elseif($certified_status == 2 || $certified_status == 1)
        <div class="authentication-notice">
            <div class="authentication-guide">
                @if($certified_status == 1)
                    <!-- <img src="/img/close-icon.png"/> -->
                    <div class="guide-div">
                        <p class="guide-title">
                            认证审核中
                            <img src="/img/gan-tan.png"/>
                        </p>
                        <p class="guide-content">企业通过实名认证，平台将让企业享受更多的权限</p>
                    </div>
                @else
                    <img src="/img/pass.png"/>
                    <div class="guide-div">
                        <p class="guide-title">
                            单位资质认证
                            <img src="/img/gan-tan.png"/>
                        </p>
                        <p class="guide-content">企业已通过实名认证，平台将让企业享受更多的权限</p>
                    </div>
                @endif
            </div>
            <div class="authentication-form">
                <div class="authentication-form-dv clearfix">
                    <span class="authentication-form-left fl"><b>*</b>企业姓名</span>
                    <div class="authentication-form-right fr">
                        <p class="authented-mess">{{$user['business_name']}}</p>
                    </div>
                </div>
                <div class="authentication-form-dv clearfix">
                    <span class="authentication-form-left fl"><b>*</b>注册号</span>
                    <div class="authentication-form-right fr">
                        <p class="authented-mess">{{$user['business_name']}}</p>
                    </div>
                </div>
                <div class="authentication-form-dv clearfix">
                    <span class="authentication-form-left fl"><b>*</b>法定代表人</span>
                    <div class="authentication-form-right fr">
                        <p class="authented-mess">{{$user['legal']}}</p>
                    </div>
                </div>
                <div class="authentication-form-dv clearfix">
                    <span class="authentication-form-left fl"><b>*</b>营业期限</span>
                    <div class="authentication-form-right fr">
                        <p class="authented-mess">{{$user['business_time'] == '0000-00-00 00:00:00' ? '长期' : $user['business_time']}}</p>
                    </div>
                </div>
                <div class="authentication-form-dv clearfix">
                    <span class="authentication-form-left fl"><b>*</b>上传凭证</span>
                    <div class="authentication-form-right fr">
                        <div class="authented-img">
                            <img src="{{img($user['img'])}}"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@stop
@section('scripts')
    @parent
    <script>
        $(function(){
            if($(".nav-active a").next().length == 0){
                $(".left-navlists-li.nav-active").css({"background":" #1e2a3c url(/img/safety-setting.png) no-repeat right center"});
            }else{
                $(".left-navlists-li.nav-active").css({"background":" #1e2a3c"});
            }

            $(".upload-file-intro>a").click(function(){
                if ($(".Supplement-content").css('display') == 'block') {
                    $(".Supplement-content").fadeOut("fast");
                } else {
                    $(".Supplement-content").fadeIn("fast");
                }
            });
        });
    </script>
    @stop