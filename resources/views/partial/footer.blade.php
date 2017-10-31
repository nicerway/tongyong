<div id="footer">
    <div class="f_title">
        <p class="fl">{{$boot_config['copyright']}} </p>
        <ul class="fl">
            <li>
                <a target="_blank" href="{{u('about', 'contact')}}" style="border-left: none;"> 联系我们</a>
            </li>
            <li>
                <a target="_blank" href="{{u('about', 'culture')}}">企业文化</a>
            </li>
            <li>
                <a target="_blank" href="{{u('about', 'legal')}}">法律声明</a>
            </li>
            <li>
                <a href="http://www.semfw.cn" target="_blank" class="active">技术支持：科威网络</a>
            </li>
        </ul>
    </div>
    <div class="f_btm">
        <p><span>友情链接：</span>
            {{!! v_show([47,67],'<a href="@linkurl@">@title@</a>',$limit=null) !!}}
        </p>
    </div>
</div>
<!--悬浮框-->
<style type="text/css">
    .key_wx div{
        position: absolute;
        top: 0;
        left: -157px;
        background: url(/img/fx_03.png) no-repeat 160px 150px;
        display: none;
    }
    .key_link div{
        position: absolute;
        top: 0;
        left: -157px;
        background: url(/img/fx_03.png) no-repeat 160px 150px;
        display: none;
    }
</style>
<div class="kf_online">
    <ul>
        <li class="key_call">
            <a href="javascript:void(0);">
                <i></i>

                <p>在线咨询</p>
            </a>

            <div>
                <dl>

                    <dt>业务咨询</dt>
                        <?php $qqarr=explode("|",$boot_config['webqq']);?>
                    @foreach ($qqarr as $qqv)
                    <dd>
                        <?php $carr=explode("：",$qqv);?>
                        <a href="http://wpa.qq.com/msgrd?v=3&uin={{$carr[1]}}&site=qq&menu=yes" target="_blank"><img src="/img/q1.gif" width="33" height="29"> <span>{{$carr[0]}}</span></a>
                    </dd>
                    @endforeach
                </dl>

            </div>

        </li>
        <li class="key_act">
            <a target="_blank" href="{{u('about', 'feedback')}}">
                <i></i>

                <p>意见反馈</p>
            </a>

        </li>
        <li class="key_kf">
            <a target="_blank" href="{{u('about', 'problem')}}">
                <i></i>

                <p>常见问题</p>
            </a>

        </li>
        <li class="key_link">
            <a href="javascript:void(0);">
                <i></i>

                <p>APP下载</p>
            </a>
            <div>
                <img src="{{img($boot_config['logo2'])}}" width="156" height="193"/>
            </div>
        </li>
        <li class="key_wx">
            <a href="javascript:void(0)">
                <i></i>

                <p>官方微信</p>
            </a>

            <div>
                <img src="{{img($boot_config['img1'])}}" width="156" height="193"/>
            </div>

        </li>
        <li class="key_fx">
            <a href="javascript:void(0)">
                <i></i>

                <p>分享</p>
            </a>
            <div class="bdsharebuttonbox fenxiang">

                <a href="javascript:void(0);"class="bds_sqq qqfx" data-cmd="sqq" title="分享到QQ好友" style="width: 10px;float: left"></a>
                <a href="javascript:void(0);"class="bds_weixin wxfx" data-cmd="weixin" title="分享到微信" style="width: 10px;float: right"></a></div>
            <div>
        </li>
        <li class="key_top">
            <a href="javascript:void(0);">
                <i></i>
                <p>返回顶部</p>
            </a>
        </li>

    </ul>
</div>
<script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];</script>
