@extends('auth.layouts/register')
@section('title') 学生@parent @stop

@section('bodyNextLabel')
<body>
    <div class="pager_wrap login_wrap">
@stop

@section('register_header')
    <h1 class="fl">我是<font style="color:red">学生</font>加入中国职业培训网</h1>
    <a class="register-cate fr" href="{{u('register', 'org')}}{{$_r0_}}"><img src="/img/qiye.png"/>我是企业</a>
    <a style="color:red" class="register-cate fr" href="{{u('register')}}{{$_r0_}}"><img src="/img/student.png"/>我是学生</a>
    @stop {{-- end register_header --}}
@section('form')
<form class="form" action="{{url('register')}}">
    <div class="register-form-div">
        <input name="person" type="text" placeholder="请输入真实姓名"/>
    </div>
    @stop
@section('formEnd')
</form>
    @stop {{-- end form (public) --}}
@section('protocal')
    <div class="company-agreement">
        <label>
            <input type="radio" name="protocal" value="我已阅读并接受《中国职业培训网个人会员注册协议》"/>
            <span></span>
        </label>
        <p class="agreement-content">我已阅读并接受<b>《中国职业培训网个人会员注册协议》</b></p>
    </div>
    @stop {{-- end protocal --}}
@section('protocal_content')
    <div class="mask-header">
        <h2 class="fl">中国职业培训网个人会员注册协议</h2>
        <a class="close-cover fr" href="javascript:;"><img src="/img/cover-close.png"/></a>
        <div class="clearfix"></div>
    </div>
      <div class="mask-content">
        {!! htmlspecialchars_decode(v_id(93,'content')) !!}
      </div>
    @stop {{-- end protocal content --}}
