<?php

namespace App\Http\Controllers;

/*
 * Antvel - Users Controller
 *
 * @author  Gustavo Ocanto <gustavoocanto@gmail.com>
 */

use App\User;
use App\Helpers\File;
use App\Helpers\UserHelper;
use App\Http\Controllers\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Validator;
use App\Notice;
use App\Resume;
use YZM;

class BusinessController extends base\UserController
{
    public function __construct(Request $request)
    {
        if ($request->method() == 'GET')
            parent::__construct();
    }

    private $fillTable = ['certificate', 'education', 'training', 'order', 'job', 'resume'];

    private $paginate = 15;
    private $toArray = 10;

    /**
     * 会员首页
     * @return
     */
    public function profile()
    {
        $user = \Auth::user()->relationsToArray();
        $notices = Notice::auth()->desc()->take(6)->get(['title', 'created_at']);
        $orders = \Auth::user()->hasManyOrder()->orderBy('created_at', 'desc')->take(4)->get(['*'])->toArray();
        $compact['ckey'] = '';
        return view('business.profile', compact('user', 'notices', 'orders'));
    }

    /**
     * 招聘
     */
    public function job(&$compact)
    {
        $_GET['title'] = isset($_GET['title']) ? $_GET['title'] : '';

        $_GET['industryid'] = isset($_GET['industryid']) ? (int)$_GET['industryid'] : 0;
        $_GET['industryid1'] = isset($_GET['industryid1']) ? (int)$_GET['industryid1'] : 0;
        if(empty($_GET['industryid']) && $_GET['industryid1']){
            $_GET['industryid'] = get_first(79, $_GET['industryid1']);
        }
        $_GET['work_nature'] = isset($_GET['work_nature']) ? (int)$_GET['work_nature'] : 0;

        $compact['pagenewslist'] = \Auth::user()->hasManyJob()->where('ty', $GLOBALS['ty'])->where(function ($query) {
            // 招聘职位名称
            $_GET['title'] and $query->where('title', 'like', '%' . $_GET['title'] . '%');
            $_GET['industryid'] and $query->where('industryid', $_GET['industryid']);
            $_GET['work_nature'] and $query->where('work_nature', $_GET['work_nature']);
        })->paginate($this->paginate)->toArray($this->toArray);
        $compact['ckey'] = '';
        foreach ($_GET as $key => $value) {
            if ($key <> 'page' && $value) $compact['ckey'] .= "&$key=$value";
        }

        return $compact;
    }

    /**
     * 简历管理
     */
    public function resume(&$compact)
    {
        // $_GET['orderBy'] = isset($_GET['orderBy']) && $_GET['orderBy'] ? $_GET['orderBy'] : 'created_at';
        // $_GET['orderno'] = isset($_GET['orderno']) && $_GET['orderno'] ? (int)$_GET['orderno'] : '';
        $compact['pagenewslist'] = \Auth::user()->hasManyResume('business_id')
            ->where(function ($query) {
                // empty($_GET['orderno']) or $query->where('orderno', intval($_GET['orderno']));
            })->orderBy('created_at', 'desc')->paginate($this->paginate)->toArray($this->toArray);
        $compact['ckey'] = '';
        foreach ($_GET as $key => $value) {
            if ($key <> 'page' && $value) $compact['ckey'] .= "&$key=$value";
        }
        return $compact;
    }
    /**
     * 简历管理 修改简历状态
     */
    public function resumeChangeStatus($id, Request $request)
    {
        $resume = Resume::whereBusinessId(\Auth::id())->find($id);
        if (is_null($resume)) {
            return handleResponseJson(412, '要操作的简历不存在');
        }
        switch ($request->dao) {
            case 'ok':
                $resume->status = 2;
                break;
            case 'refuse':
                $resume->status = 1;
                break;
            default:
                abort(404);
                break;
        }
        if ($resume->save()) {
            return handleResponseJson(201, '操作成功', '?');
        }
        return handleResponseJson(412, '操作失败,请稍后再试');
    }

    /**
     * 职业培训管理
     */
    public function training(&$compact)
    {
        $_GET['certificate_lid'] = isset($_GET['certificate_lid']) ? (int)$_GET['certificate_lid'] : 0;
        $_GET['title'] = isset($_GET['title']) ? $_GET['title'] : '';
        $_GET['industryid'] = isset($_GET['industryid']) ? intval($_GET['industryid']) : 0;
        $_GET['neixunid'] = isset($_GET['neixunid']) ? intval($_GET['neixunid']) : 0;
        $_GET['publicid'] = isset($_GET['publicid']) ? intval($_GET['publicid']) : 0;
        $_GET['qualificationid1'] = isset($_GET['qualificationid1']) ? intval($_GET['qualificationid1']) : 0;
        $_GET['qualificationid2'] = isset($_GET['qualificationid2']) ? intval($_GET['qualificationid2']) : 0;
        $_GET['qualificationid'] = isset($_GET['qualificationid']) ? intval($_GET['qualificationid']) : 0;
        $_GET['trainingid'] = isset($_GET['trainingid']) ? intval($_GET['trainingid']) : 0;

        if (empty($_GET['qualificationid'])) {
            if ($_GET['qualificationid1']) {
                if (empty($_GET['qualificationid2'])) {
                    $_GET['qualificationid2'] = get_first(76, $_GET['qualificationid1']);
                }
                $_GET['qualificationid'] = get_first(76, $_GET['qualificationid2']);
            } else {
                $_GET['qualificationid'] = 0;
            }

        }
        $compact['pagenewslist'] = \Auth::user()->hasManyTraining()->where('ty', $GLOBALS['ty'])->where('isstate', 1)->where(function ($query) {
            $_GET['trainingid'] and $query->where('trainingid', $_GET['trainingid']);
            $_GET['industryid'] and $query->where('industryid', $_GET['industryid']);
            $_GET['neixunid'] and $query->where('neixunid', $_GET['neixunid']);
            $_GET['publicid'] and $query->where('publicid', $_GET['publicid']);
            $_GET['qualificationid'] and $query->where('qualificationid', $_GET['qualificationid']);
            $_GET['title'] and $query->where('title', 'like', '%' . $_GET['title'] . '%');
        })->paginate($this->paginate)->toArray($this->toArray);
        $compact['ckey'] = '';
        foreach ($_GET as $key => $value) {
            if ($key <> 'page' && $value) $compact['ckey'] .= "&$key=$value";
        }
        return $compact;
    }

    /**
     * 证书管理
     */
    public function certificate(&$compact)
    {
        $_GET['certificate_lid'] = isset($_GET['certificate_lid']) ? (int)$_GET['certificate_lid'] : 0;
        $_GET['title'] = isset($_GET['title']) ? $_GET['title'] : '';
        $compact['pagenewslist'] = \Auth::user()->hasManyCertificate()->where('tty', $GLOBALS['tty'])->where('isstate', 1)->where(function ($query) {
            empty($_GET['certificate_lid']) or $query->where('certificate_lid', intval($_GET['certificate_lid']));
            empty($_GET['title']) or $query->where('title', 'like', '%' . $_GET['title'] . '%');
        })->paginate($this->paginate)->toArray($this->toArray);
        $compact['ckey'] = isset($_GET['certificate_lid']) ? '&certificate_lid=' . intval($_GET['certificate_lid']) : '';
        return $compact;
    }

    /**
     * 国际教育管理
     */
    public function education(&$compact)
    {
        $_GET['title'] = isset($_GET['title']) ? $_GET['title'] : '';
        $compact['pagenewslist'] = \Auth::user()->hasManyEducation()->where('tty', $GLOBALS['tty'])->where('isstate', 1)->where(function ($query) {
            empty($_GET['title']) or $query->where('title', 'like', '%' . $_GET['title'] . '%');
        })->paginate($this->paginate)->toArray($this->toArray);
        $compact['ckey'] = isset($_GET['certificate_lid']) ? '&certificate_lid=' . intval($_GET['certificate_lid']) : '';
        return $compact;
    }

    /**
     * 实名认证
     */
    public function certification(Request $request)
    {
        $request = request();
        $rules = [
//            'org' => 'required|between:2,20',
            'registerid' => 'required|numeric',
            'legal' => 'required|between:2,6',
            'business_time' => 'required_with:business_time|date_format:Y-m-d',
            'islonger' => 'required_with:islonger',
//            'uploadimg' => 'image',
        ];

        $validator = Validator::make($request->all(), $rules);
        $errors = $validator->errors(); // 输出的错误，自己打印看下
        if ($validator->fails()) {
            return noticeResponseJson(412, '执行失败', $errors);
        }
        $img = upload($request, 'uploadimg');
        if (!$img) {
            return noticeResponseJson(412, '执行失败', '营业执照上传失败!');
        }

        $user = \Auth::user();
        $user_id = $user->id;
        $business = $user->profile;
        $business->img = $img;
        $business->certified_status = 1;
        $business->business_time = $request->get('islonger') ? null : $request->get('business_time');
        $business->legal = $request->get('legal');
        $business->registerid = $request->get('registerid');
        if ($business->save()) {
            if (Notice::sendCertification($user_id, 'is_business')) {
                return handleResponseJson(200, '申请认证成功!', '?');
            }
        }
        return handleResponseJson(412, '申请认证失败!');


    }

    /**
     * 订单管理
     */
    public function order(&$compact)
    {
        $_GET['orderBy'] = isset($_GET['orderBy']) && $_GET['orderBy'] ? $_GET['orderBy'] : 'created_at';
        $_GET['orderno'] = isset($_GET['orderno']) && $_GET['orderno'] ? (int)$_GET['orderno'] : '';
        $compact['pagenewslist'] = \Auth::user()->hasManyOrder()
            ->where(function ($query) {
                empty($_GET['orderno']) or $query->where('orderno', intval($_GET['orderno']));
            })->orderBy($_GET['orderBy'], 'desc')->paginate($this->paginate)->toArray($this->toArray);
        $compact['ckey'] = '';
        foreach ($_GET as $key => $value) {
            if ($key <> 'page' && $value) $compact['ckey'] .= "&$key=$value";
        }
        return $compact;
    }

    /**
     * 用户管理
     */
    public function config()
    {
        $request = request();
        $rules = [
//            'org' => 'required|between:2,20',
            'business_name' => 'required|between:2,20',
            'contact' => 'required|between:2,4',
            'weixin' => 'required',
            'qq' => 'required|numeric',
            'location' => 'required|min:2',
            'jing' => 'required|numeric',
            'wei' => 'required|numeric',
            // 'wei' => 'required:business_time|date_format:Y-m-d',
            'size' => 'required|numeric',
            'cate' => 'required|numeric',
            'nature' => 'required|numeric',
            'siteurl' => 'required|url',
            'business_introduction' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        $errors = $validator->errors(); // 输出的错误，自己打印看下
        if ($validator->fails()) {
            return noticeResponseJson(412, '执行失败', $errors);
        }

        $user = \Auth::user();
        $business = $user->profile;

        if ($filename = ifUploadCheckIt($request, 'img', $business->img, 'b_img')) {
            $business->img = $filename;
        }

        if ($filename = ifUploadCheckIt($request, 'logo', $business->logo, 'b_logo')) {
            $business->logo = $filename;
        }

        $business->contact = $request->get('contact');
        $business->weixin = $request->get('weixin');
        $business->qq = $request->get('qq');
        $business->location = $request->get('location');
        $business->jing = $request->get('jing');
        $business->wei = $request->get('wei');
        $business->size = $request->get('size');
        $business->cate = $request->get('cate');
        $business->nature = $request->get('nature');
        $business->siteurl = $request->get('siteurl');
        $business->business_introduction = $request->get('business_introduction');

        // users->business表更新
        if ($business->save()) {
            return handleResponseJson(200, '设置成功!', '?');
        }
        return handleResponseJson(412, '设置失败!');


    }

    /**
     * 修改手机
     */
    public function telphone(Request $request)
    {
        $user = \Auth::user();

        $rules = [
            'telphone' => 'required|regex:/^1[34578][0-9]{9}$/|unique:users',
            'yzm' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        $errors = $validator->errors(); // 输出的错误，自己打印看下
        if ($validator->fails()) {
            return noticeResponseJson(412, '执行失败', $errors);
        }

        $telphone = $request->get('telphone');
        $yzm = new YZM($telphone);
        if ($yzm->legal($request->yzm)) {
            $yzm->pop();
            $user->telphone = $telphone;
            if ($user->save()) {
                return handleResponseJson(200, '手机号修改成功.', route('b_config'));
            }
            return handleResponseJson(412, '手机号修改失败,请稍后再试.');
        }
        return noticeResponseJson(303, '验证码校验失败.', '不匹配或已失效');
    }

    /**
     * 修改邮箱
     */
    public function email(Request $request)
    {
        $user = \Auth::user();

        $rules = [
            'email' => 'required|email|unique:users',
            'yzm' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        $errors = $validator->errors(); // 输出的错误，自己打印看下
        if ($validator->fails()) {
            return noticeResponseJson(412, '执行失败', $errors);
        }

        $email = $request->email;
        $yzm = new YZM($email);
        if ($yzm->legal($request->yzm)) {
            $yzm->pop();
            $user->email = $email;
            if ($user->save()) {
                return handleResponseJson(200, '邮箱修改成功.', route('b_config'));
            }
            return handleResponseJson(412, '邮箱修改失败,请稍后再试.');
        }
        return noticeResponseJson(303, '验证码校验失败.', '不匹配或已失效');
    }

    /**
     * 安全设置
     */
    public function safe(Request $request)
    {

        $rules = [
            'origin' => 'required|between:6,20',
            'new' => 'required|between:6,20',
            'password2' => 'required|between:6,20|same:new',
        ];

        $validator = Validator::make($request->all(), $rules);

        $user = \Auth::user();
        $origin = $request->origin;
        $new = $request->new;
        $user_id = $user->id;
        $hashedPassword = $user->password;

        $validator->after(function ($validator) use ($hashedPassword, $origin, $new) {
            if ($origin == $new) {
                $validator->errors()->add('new', '旧密码与新密码相同');
            }
            if (!\Hash::check($origin, $hashedPassword)) {
                $validator->errors()->add('origin', '旧密码错误');
            }
        });

        $errors = $validator->errors(); // 输出的错误，自己打印看下
        if ($validator->fails()) {
            return noticeResponseJson(412, '执行失败', $errors);
        }
        $user->password = bcrypt($request->new);
        if ($user->save()) {
            return handleResponseJson(200, '密码修改成功!', '?');
        } else {
            return handleResponseJson(412, '密码修改失败,请稍后再试', '?');
        }
    }

    public function dispatch($action = '', $id = '')
    {
        #将一级栏目的路劲名作为表名
        $table = $GLOBALS['uri'][1];
        path2ptt(substr(Request()->path(), 8));
        //dd($GLOBALS);

        $compact = ['user' => \Auth::user()->relationsToArray(), 'table' => $table];
        switch ($action) {
            case 'create':
            case 'update':
                $row = \Auth::user()->{'hasMany' . ucfirst($table)}->find($id);
                $compact['row'] = $row ? $row->toArray() : [];
                return view("business.cu", $compact);
                break;
            default://列表
                if (in_array($table, $this->fillTable)) {
                    if (is_null($GLOBALS['tty'])) {
                        if ($db_tty = v_path(null, $GLOBALS['ty'])) {
                            $GLOBALS['tty'] = $db_tty->id;
                            $GLOBALS['tty_data'] = $db_tty;
                            $GLOBALS['tty_path'] = $db_tty->path;
                        }
                    }
                    $compact = $this->$table($compact);
                }
                if (view()->exists("business.$table")) {
                    return view("business.$table", $compact);
                }
                return view("errors.b_404", $compact);
                break;
        };
    }

    public function delete($table, Request $request)
    {
        if (in_array($table, $this->fillTable)) {
            #出入表名
            $DeleteData = new \App\Helpers\DeleteData($table, $request->get('ids'), \Auth::user()->id);
        }
        return handleResponseJson(200, '');
    }

    /**
     * 添加或修改
     */
    public function with($table = '', $id = '', Request $request)
    {
        path2ptt(substr(Request()->path(), 8));
        if (is_numeric($table)) {
            $id = $table;
            $table = $GLOBALS['pid_path'];
        }
        if (empty($table)) $table = $GLOBALS['pid_path'];
        if (in_array($table, $this->fillTable)) {
            #出入表名
            $WithData = new \App\Helpers\WithData($table, $id, $request->all());
            $return = $WithData->submit(\Auth::user()->id);
            if ($return === false) {
                return $WithData->error;
            }
            $redirect = $request->path();
            $redirect = preg_replace('/cu(.*)$/', '', $redirect);
            return handleResponseJson($return[0], $return[1], u($redirect));
        }
        return handleResponseJson(203, '你没有权限执行此操作', '?');
    }
}
