<div class="pager-wrap order-center-title">
    <div class="wrap-box order-center-title-box clearfix">
        <div class="order-center-left fl">
            <ul>
                <li class="link-location">
                    <a href="?">会员中心</a>
                </li>
                <li id="person-menu-jianli">
                    <a href="/person/jianli">简历中心</a>
                </li>
                <li id="person-menu-order">
                    <a href="/person/order">订单中心</a>
                </li>
                <li id="person-menu-default">
                    <a href="/person">账户设置</a>
                </li>
                <li id="person-menu-shop">
                    <a href="javascript:alert('上线中');">积分商城</a>
                </li>
            </ul>
        </div>
        <div class="order-center-right fr">
            <div class="integral"><img src="/img/jf-icon.png"/><span>12</span></div>
            <a href="{{u('job')}}" class="my-apply">我要应聘</a>
        </div>
    </div>
</div>
<script>
    document.getElementById("person-menu-{{$_first}}").className = "link-active";
</script>