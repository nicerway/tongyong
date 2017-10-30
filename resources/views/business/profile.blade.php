@extends('business.layouts.master')

@section('title') @parent @stop

@section('main')
<div class="personal-member-index clearfix">
    <div class="member-index-left fl">
        <div class="member-index-header clearfix">
            <img class="mem-hearimg" src="img/member-headerimg.png"/>
            <div class="member-information">
                <h2>联想集团有限公司<img class="mem-link" src="img/link.png"/>
                    <i><img class="mem-renzheng" src="img/renzhe.png"/>中国职业培训网认证</i>
                </h2>
                <span>安徽  合肥</span>
                <p>公司描述：联想集团公司成立于1984年，由中科院计算所投资20万元人民币和11名科技人员创办，
                    现已发展成为一家在信息产业内多元化发展的大型企业集团，富有创新性的国际化的科技公司，由联想及原IBM个人电脑事业部所组成。</p>
            </div>
        </div>
        <div class="mem-index-recruit clearfix">
            <div class="index-recruit-left fl">
                <h3>职位招聘<i>有人投简历 0 份</i></h3>
                <ul class="index-recruit-ul">
                    <li class="index-recruit-li">
                        <p>高端招聘</p>
                        <a href="javascript:;">编辑招聘信息   > </a>
                    </li>
                    <li class="index-recruit-li">
                        <p>企业招聘</p>
                        <a href="javascript:;">编辑招聘信息   > </a>
                    </li>
                    <li class="index-recruit-li index-recruit-on">
                        <p>校园招聘</p>
                        <a href="javascript:;">暂无校园招聘信息 </a>
                    </li>
                </ul>
            </div>
            <div class="index-recruit-left fr">
                <h3>职业培训管理<a href="javascript:;">查看更多 > </a></h3>
                <ul class="index-recruit-ul">
                    <li class="index-recruit-li">
                        <p>技能培训</p>
                        <a href="javascript:;">编辑上传技能培训信息</a>
                    </li>
                    <li class="index-recruit-li">
                        <p>企业培训</p>
                        <a href="javascript:;">编辑上传企业培训信息</a>
                    </li>
                    <li class="index-recruit-li">
                        <p>在线学习</p>
                        <a href="javascript:;">编辑上传在线学习资料</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="mem-index-recruit">
            <div class="index-orderlists">
                <h3>订单管理<a href="javascript:;">查看更多 ></a></h3>
                <div class="order-manager-content">
                    <form>
                        <table>
                            <tr class="manager-firsttr">
                                <th class="manager-secondth">订单编号</th>
                                <th class="index-fiveth">用户</th>
                                <th class="manager-fourtd">培训课程</th>
                                <th>价格</th>
                                <th>到账时间</th>
                                <th>状态</th>
                            </tr>
                            <tr>
                                <td>123455656767</td>
                                <td>张三</td>
                                <td class="manager-fourtd">
                                    <img class="order-mess-img" src="img/brand-img.png"/>
                                    <div class="order-name">
                                        <p>2017联想集团《技能培训》视频教程</p>
                                        <span>在线支付</span>
                                    </div></td>
                                <td class="pro-price">￥29.00</td>
                                <td>2017-06-29</td>
                                <td>未提现</td>
                            </tr>
                            <tr>
                                <td>123455656767</td>
                                <td>张三</td>
                                <td class="manager-fourtd">
                                    <img class="order-mess-img" src="img/brand-img.png"/>
                                    <div class="order-name">
                                        <p>2017联想集团《技能培训》视频教程</p>
                                        <span>在线支付</span>
                                    </div></td>
                                <td class="pro-price">￥29.00</td>
                                <td>2017-06-29</td>
                                <td>未提现</td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="black36"></div>
        <div class="login-register-box">
            <p class="login-footer-p">北京通用领航咨询服务有限公司 版权所有 京ICP备11031804号<span>|</span><b>技术支持：科威网络</b></p>
        </div>
    </div>
    <div class="member-index-right fr">
       <div class="mem-message">
           <h4>系统消息<a href="javascript:;">更多 > </a></h4>
           <ul class="mem-message-ul">
               <li class="mem-message-li">
                   <a href="javascript:;">新人大礼包等你抢<i></i></a>
                   <p>2017-07-18 11:11</p>
               </li>
               <li class="mem-message-li">
                   <a href="javascript:;">新人大礼包等你抢<i></i></a>
                   <p>2017-07-18 11:11</p>
               </li>
               <li class="mem-message-li">
                   <a href="javascript:;">新人大礼包等你抢</a>
                   <p>2017-07-18 11:11</p>
               </li>
               <li class="mem-message-li">
                   <a href="javascript:;">新人大礼包等你抢</a>
                   <p>2017-07-18 11:11</p>
               </li>
               <li class="mem-message-li">
                   <a href="javascript:;">新人大礼包等你抢</a>
                   <p>2017-07-18 11:11</p>
               </li>
               <li class="mem-message-li">
                   <a href="javascript:;">新人大礼包等你抢</a>
                   <p>2017-07-18 11:11</p>
               </li>
           </ul>
       </div>
        <div class="mem-message">
            <h4>推荐人才<a href="javascript:;">更多 > </a></h4>
            <ul class="mem-message-ul">
                <li class="mem-message-li">
                    <a href="javascript:;">张玲玲<b>合肥</b></a>
                    <span>意向职位：<b>销售经理</b></span>
                </li>
                <li class="mem-message-li">
                    <a href="javascript:;">张玲玲<b>合肥</b></a>
                    <span>意向职位：<b>销售经理</b></span>
                </li>
                <li class="mem-message-li">
                    <a href="javascript:;">张玲玲<b>合肥</b></a>
                    <span>意向职位：<b>销售经理</b></span>
                </li>
                <li class="mem-message-li">
                    <a href="javascript:;">张玲玲<b>合肥</b></a>
                    <span>意向职位：<b>销售经理</b></span>
                </li>
                <li class="mem-message-li">
                    <a href="javascript:;">张玲玲<b>合肥</b></a>
                    <span>意向职位：<b>销售经理</b></span>
                </li>
                <li class="mem-message-li">
                    <a href="javascript:;">张玲玲<b>合肥</b></a>
                    <span>意向职位：<b>销售经理</b></span>
                </li>
            </ul>
        </div>
    </div>
</div>
@stop