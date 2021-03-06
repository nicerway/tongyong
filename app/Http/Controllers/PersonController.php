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
use Auth;
use App\Order;
use App\CVS;
use App\NewsCats;
use DB;
use App\Notice;
use YZM;


class PersonController extends base\UserController
{
    private $paginate = 15;
    private $toArray = 10;

    /**
     * Inicializa variables para la validacion de perfil.
     */
    public function __construct()
    {
        $first = isset($GLOBALS['uri'][1]) ? $GLOBALS['uri'][1] : 'default';
        $next = isset($GLOBALS['uri'][2]) ? $GLOBALS['uri'][2] : 'default';
        /*$menu_first = trans("users.menu.$first");
        if ($menu_first != "users.menu.$first") {

            if($next && isset($menu_first['next'][$next])) {
                $next =
            }
        }*/

        /*$next = isset($GLOBALS['uri'][2]) ? $GLOBALS['uri'][2] : '';
        $menu_first = trans("business.menu.$first");
        if ($menu_first != "business.menu.$first") {
            $title = [$menu_first['title']];
            if (isset($menu_first['next'])) {
                if (!$next) {
                    $next = key($menu_first['next']);
                }
                array_unshift($title, $menu_first['next'][$next]['title']);
            }
        }

        view()->share('_title', $title);*/
        view()->share('_first', $first);
        view()->share('_next', $next);
    }

    /**
     * 重置密码
     */
    public function password()
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;

        return view('user.profile', $data);
    }

    /**
     * 重置密码-xiugai
     */
    public function xgmm(Request $request)
    {
        $data = $request->all(); //接收所有的数据
        $oldpass = $request->input('oldpass');
        $password = $request->input('pass1');
        $rules = [
            'oldpass' => 'required|between:6,20',
            'pass1' => 'required|between:6,20|confirmed',
        ];
        $messages = [
            'required' => '密码不能为空',
            'between' => '密码必须是6~20位之间',
            'confirmed' => '新密码和确认密码不匹配'
        ];
        $validator = Validator::make($data, $rules, $messages);
        $user = \Auth::user();
        $validator->after(function ($validator) use ($oldpass, $user) {
            if (!\Hash::check($oldpass, $user->password)) { //原始密码和数据库里的密码进行比对
                $validator->errors()->add('oldpass', '原密码错误'); //错误的话显示原始密码错误
            }
        });
        if ($validator->fails()) {      //判断是否有错误
            return back()->withErrors($validator);  //重定向页面，并把错误信息存入一次性session里
        }
        $user->password = bcrypt($password);       //使用bcrypt函数进行新密码加密
        $user->save();      //成功后，保存新密码
        return 200;
    }

    /**
     * 修改手机
     */
    public function telphone()
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;
        return view('user.profile', $data);
    }

    /**
     * 修改邮箱
     */
    public function email()
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;

        return view('user.profile', $data);
    }

    /**
     * 修改手机
     */
    public function post_telphone(Request $request)
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
                return handleResponseJson(200, '手机号修改成功.', '/person');
            }
            return handleResponseJson(412, '手机号修改失败,请稍后再试.');
        }
        return noticeResponseJson(303, '验证码校验失败.', '不匹配或已失效');
    }

    /**
     * 修改邮箱
     */
    public function post_email(Request $request)
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
                return handleResponseJson(200, '邮箱修改成功.', '/person');
            }
            return handleResponseJson(412, '邮箱修改失败,请稍后再试.');
        }
        return noticeResponseJson(303, '验证码校验失败.', '不匹配或已失效');
    }


    /**
     * 认证
     */
    public function certified()
    {
        $user = \Auth::user()->relationsToArray();
        return view('user.profile', compact('user'));
    }


    /**
     * 认证   关于认证，certified=0未填写，1为待审核，2为审核通过
     * 此处逻辑以及图片需完善
     */
    public function smrz(Request $request)
    {
        $rules = [
            'real_name' => 'required|between:2,6',
            'gendar' => 'required|not_in',
            'year' => 'required|numeric',
            'month' => 'required|numeric',
            'day' => 'required|numeric',
            'card' => 'required|numeric|digits:18',
            'front' => 'image|mimes:jpg,jpeg,bmp,png',
            'back' => 'image|mimes:jpg,jpeg,bmp,png',
        ];

        $validator = Validator::make($request->all(), $rules);
        $errors = $validator->errors(); // 输出的错误，自己打印看下
        if ($validator->fails()) {
            return noticeResponseJson(412, '执行失败', $errors);
        }
        $front = upload($request, 'front');
        if (!$front) {
            return noticeResponseJson(412, '执行失败', '身份证正面照上传失败!');
        }
        $back = upload($request, 'back');
        if (!$back) {
            return noticeResponseJson(412, '执行失败', '身份证背面照上传失败!');
        }

        $person = \Auth::user()->profile;

        $person->year = intval($request->year);
        $person->month = intval($request->month);
        $person->day = intval($request->day);
        $person->birthday = date("$person->year-$person->month-$person->day");

        $person->card = $request->card;
        $person->card_front = $front;
        $person->card_back = $back;
        $person->certified_status = 1;
        $person->gendar = $request->gendar;
        $person->real_name = $request->real_name;
        if ($person->save()) {
            #发送认证请求
            if (Notice::sendCertification($person->user_id)) {

                return handleResponseJson(200, '申请认证成功!', '?');
            }
        }
        return handleResponseJson(412, '申请认证失败!');
    }

    public function viewmessage()
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        return view('user.profile', compact('user'));
    }


    /*
     * 简历
     */
    public function jianli()
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;
        $data['nums'] = DB::table("jianli")->where("user_id", $user['id'])->count();
        $data['list'] = DB::table("jianli")->where("user_id", $user['id'])->get();


        return view('user.profile', $data);
    }

    public function jianliadd()
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;
        return view('user.jianliadd', $data);
    }

    //置顶
    public function jianlitop($id)
    {
        $id = (int)$id;
        $user = \Auth::id();
        DB::table("jianli")->where("id", "<>", $id)->where("user_id", $user)->update(array("istop" => 0));
        $id = DB::table("jianli")->where("id", $id)->where("user_id", $user)->update(array("istop" => 1));
        if ($id) {
            $data['status'] = 200;
            $data['content'] = 0;
        } else {
            $data['status'] = 300;
            $data['content'] = "网络故障！";

        }
        return json_encode($data);
    }

    //默认
    public function jianlimr($id)
    {
        $id = (int)$id;
        $user = \Auth::id();
        DB::table("jianli")->where("id", "<>", $id)->where("user_id", $user)->update(array("ismr" => 0));
        $id = DB::table("jianli")->where("id", $id)->where("user_id", $user)->update(array("ismr" => 1));
        if ($id) {
            $data['status'] = 200;
            $data['content'] = 0;
        } else {
            $data['status'] = 300;
            $data['content'] = "网络故障！";

        }
        return json_encode($data);
    }

    //删除
    public function jianlidel($id)
    {
        $id = (int)$id;
        $user = \Auth::id();
        $id = DB::table("jianli")->where("id", $id)->where("user_id", $user)->delete();
        if ($id) {
            $data['status'] = 200;
            $data['content'] = 0;
        } else {
            $data['status'] = 300;
            $data['content'] = "网络故障！";

        }
        return json_encode($data);
    }

    public function jianlipostadd(Request $request, $id)
    {
        dd($requset);
        $rules = [
            'title' => 'required',
            'ftitle' => 'required',
            'from' => 'required',
            'destination' => 'required',
            'introduce' => 'required',
            'content' => 'required',
            'starttime' => 'required|date',
            'endtime' => 'required|date',
            'bstarttime' => 'required|date',
            'bendtime' => 'required|date',
            'content' => 'required',
            'content2' => 'required',
        ];
        $errors = $validator->errors(); // 输出的错误，自己打印看下
        if ($validator->fails()) {
            $this->error = noticeResponseJson(412, '执行失败', $errors);
            return false;
        }

        $user = \Auth::id();
        if ($user) {
            $info = $request->all();
            $info['user_id'] = $user;
            $info['addtime'] = time();
            $info['ip'] = $request->getClientIp();

            if ($id) {
                $res = DB::table("jianli")->where("user_id", $user)->where("id", $id)->update($info);
            } else {
                $res = DB::table("jianli")->insert($info);
            }

            if ($res) {
                $data['status'] = 200;
                $data['id'] = DB::getPdo()->lastInsertId();
                $data['content'] = "<div class=\"form-line clearfix\">
                                <span class=\"resume-table-trone font-bold\">简历名称：</span>
                                <div class=\"my-joinjob-textar\">{$info['nid']}</div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">姓名：</span>
                                    <div class=\"resume-table-trtwo chose-sex\">
                                        <p class=\"user-information-p\">{$info['name']}  <span style=\"color:#999\">{$info['sex']}</span></p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">户口所在地：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['address']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">出生日期：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['year']}年{$info['month']}月</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">现居住地：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['address_xjd']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">参加工作年份：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['gznf']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">联系方式：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['tel']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">最高学历：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['zgxl']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">电子邮箱：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['email']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">国籍</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['gj']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">婚姻状况：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['hyzk']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">证件号：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['zjhm']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">政治面貌：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['zzmm']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <span class=\"resume-table-trone font-bold\">海外工作/学习经历：</span>
                                <div class=\"my-joinjob-textar\">{$info['remark']}</div>
                            </div>";
            } else {
                $data['status'] = 300;
                $data['id'] = 0;
                $data['content'] = 0;
            }
            return json_encode($data);
        }
    }

    public function jianlipostadd2(Request $request, $id)
    {
        $user = \Auth::id();

        if ($user) {
            $info = $request->all();
            $id = DB::table("jianli")->where("id", $id)->where("user_id", $user)->update($info);
            if ($id) {
                $data['status'] = 200;
                $data['content'] = " <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">期望工作性质：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['gzxz']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">期望从事行业：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['cshy']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">期望工作地点：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['gzdd']}-{$info['gzdd2']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">期望月薪（税前）：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['price']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">期望从事职业：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['cszy']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">求职状态：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['qzzt']}</p>
                                    </div>
                                </div>
                            </div>";
            } else {
                $data['status'] = 300;
                $data['content'] = 0;
            }
            return json_encode($data);
        }
    }

    public function jianlipostadd3(Request $request, $id)
    {
        $user = \Auth::id();
        if ($user) {
            $info = $request->all();
//            var_dump($info);die;
            $gzjy = "";
            $data = array();
            foreach ($info['qymc'] as $key => $vf) {
                if ($info['qymc'][$key]) {
                    $data[] = array(
                        "qymc" => $info['qymc'][$key],
                        "qylb" => $info['qylb'][$key],
                        "gzsj" => $info['gzsj'][$key],
                        "zwmc" => $info['zwmc'][$key],
                        "price" => $info['price'][$key],
                        "gzms" => $info['gzms'][$key],
                        "qygm" => $info['qygm'][$key],
                        "qyxz" => $info['qyxz'][$key]
                    );
                }
            }
            if (count($data) > 0) {
                $up['gzjy'] = json_encode($data);
                $id = DB::table("jianli")->where("id", $id)->where("user_id", $user)->update($up);
                $content = "";
                foreach ($data as $key => $vf) {
                    $content .= " <div class=\"form-line clearfix\">
                            <span class=\"resume-table-trone font-bold\">企业名称：</span>
                            <div class=\"resume-table-trseven\">
                                <p class=\"user-information-p\">{$vf['qymc']}</p>
                            </div>
                        </div>
                        <div class=\"form-line clearfix\">
                            <div class=\"form-line-left fl\">
                                <span class=\"resume-table-trone font-bold\">行业类别：</span>
                                <div class=\"resume-table-trtwo\">
                                    <p class=\"user-information-p\">{$vf['qylb']}</p>
                                </div>
                            </div>
                            <div class=\"form-line-right fr\">
                                <span class=\"resume-table-trone font-bold\">工作时间：</span>
                                <div class=\"resume-table-trtwo\">
                                    <p class=\"user-information-p\">{$vf['gzsj']}</p>
                                </div>
                            </div>
                        </div>
                        <div class=\"form-line clearfix\">
                            <div class=\"form-line-left fl\">
                                <span class=\"resume-table-trone font-bold\">职位名称：</span>
                                <div class=\"resume-table-trtwo\">
                                    <p class=\"user-information-p\">{$vf['zwmc']}</p>
                                </div>
                            </div>
                            <div class=\"form-line-right fr\">
                                <span class=\"resume-table-trone font-bold\">职位月薪（税前）：</span>
                                <div class=\"resume-table-trtwo\">
                                    <p class=\"user-information-p\">{$vf['price']}</p>
                                </div>
                            </div>
                        </div>
                        <div class=\"form-line clearfix\">
                            <span class=\"resume-table-trone font-bold\">工作描述：</span>
                            <div class=\"resume-table-trseven\">
                                <div class=\"my-joinjob-textar\">
                                    <p class=\"user-information-p\">{$vf['gzms']}</p>
                                </div>
                            </div>
                        </div>
                        <div class=\"form-line clearfix\">
                            <div class=\"form-line-left fl\">
                                <span class=\"resume-table-trone font-bold\">企业规模：</span>
                                <div class=\"resume-table-trtwo\">
                                    <p class=\"user-information-p\">{$vf['qygm']}</p>
                                </div>
                            </div>
                            <div class=\"form-line-right fr\">
                                <span class=\"resume-table-trone font-bold\">企业性质：</span>
                                <div class=\"resume-table-trtwo\">
                                    <p class=\"user-information-p\">{$vf['qyxz']}</p>
                                </div>
                            </div>
                        </div>";
                }
                $res['status'] = 200;
                $res['content'] = $content;
            } else {
                $res['status'] = 300;
                $res['content'] = 0;
            }

            return json_encode($res);
        }
    }


    public function jianlipostadd4(Request $request, $id)
    {
        $user = \Auth::id();
        if ($user) {
            $info = $request->all();
            $gzjy = "";
            $data = array();
            foreach ($info['xxmc'] as $key => $vf) {
                if ($info['xxmc'][$key]) {
                    $data[] = array(
                        "xxmc" => $info['xxmc'][$key],
                        "sj" => $info['sj'][$key],
                        "zy" => $info['zy'][$key],
                        "istz" => $info['istz'][$key],
                        "xl" => $info['xl'][$key]
                    );
                }
            }
            if (count($data) > 0) {
                $up['jybj'] = json_encode($data);
                $id = DB::table("jianli")->where("id", $id)->where("user_id", $user)->update($up);
                $content = "";
                foreach ($data as $key => $vf) {
                    $content .= "<div class=\"form-line clearfix\">
                                <span class=\"resume-table-trone font-bold\">学校名称：</span>
                                <div class=\"resume-table-trseven\">
                                    <p class=\"user-information-p\">{$vf['xxmc']}</p>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">时间：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['sj']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">专业名称：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['zy']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">是否统招：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['istz']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">学历/学位：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['xl']}</p>
                                    </div>
                                </div>
                            </div>";
                }
                $res['status'] = 200;
                $res['content'] = $content;
            } else {
                $res['status'] = 300;
                $res['content'] = 0;
            }

            return json_encode($res);
        }
    }


    public function jianlipostadd5(Request $request, $id)
    {
        $user = \Auth::id();
        if ($user) {
            $info = $request->all();
            $gzjy = "";
            $data = array();
            foreach ($info['hdjx'] as $key => $vf) {
                if ($info['hdjx'][$key]) {
                    $data[] = array(
                        "hdjx" => $info['hdjx'][$key],
                        "jxsj" => $info['jxsj'][$key],
                        "xnzw" => $info['xnzw'][$key],
                        "zwsj" => $info['zwsj'][$key]
                    );
                }
            }
            if (count($data) > 0) {
                $up['zxqk'] = json_encode($data);
                $id = DB::table("jianli")->where("id", $id)->where("user_id", $user)->update($up);
                $content = "";
                foreach ($data as $key => $vf) {
                    $content .= " <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">获得奖项：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['hdjx']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">时间：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['jxsj']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">校内职务：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['xnzw']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">时间：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['zwsj']}</p>
                                    </div>
                                </div>
                            </div>";
                }
                $res['status'] = 200;
                $res['content'] = $content;
            } else {
                $res['status'] = 300;
                $res['content'] = 0;
            }

            return json_encode($res);
        }
    }


    public function jianlipostadd6(Request $request, $id)
    {
        $user = \Auth::id();
        if ($user) {
            $info = $request->all();
            $gzjy = "";
            $data = array();
            foreach ($info['jnyy'] as $key => $vf) {
                if ($info['jnyy'][$key]) {
                    $data[] = array(
                        "jnyy" => $info['jnyy'][$key],
                        "zdy" => $info['zdy'][$key],
                        "zwcd" => $info['zwcd'][$key]
                    );
                }
            }
            if (count($data) > 0) {
                $up['jntc'] = json_encode($data);
                $id = DB::table("jianli")->where("id", $id)->where("user_id", $user)->update($up);
                $content = "";
                foreach ($data as $key => $vf) {
                    if ($vf['zdy']) $zdy = "【{$vf['zdy']}】"; else {
                        $zdy = "";
                    }
                    $content .= " <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">技能/语言：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['jnyy']}{$zdy}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">掌握程度：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['zwcd']}</p>
                                    </div>
                                </div>
                            </div>";
                }
                $res['status'] = 200;
                $res['content'] = $content;
            } else {
                $res['status'] = 300;
                $res['content'] = 0;
            }

            return json_encode($res);
        }
    }


    public function jianlipostadd7(Request $request, $id)
    {
        $user = \Auth::id();
        if ($user) {
            $info = $request->all();
            $gzjy = "";
            $data = array();
            foreach ($info['sj'] as $key => $vf) {
                if ($info['sj'][$key]) {
                    $data[] = array(
                        "sj" => $info['sj'][$key],
                        "kc" => $info['kc'][$key],
                        "jg" => $info['jg'][$key],
                        "dd" => $info['dd'][$key],
                        "zs" => $info['zs'][$key],
                        "ms" => $info['ms'][$key]
                    );
                }
            }
            if (count($data) > 0) {
                $up['pxjl'] = json_encode($data);
                $id = DB::table("jianli")->where("id", $id)->where("user_id", $user)->update($up);
                $content = "";
                foreach ($data as $key => $vf) {
                    $content .= " <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">时间：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['sj']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">培训课程：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['kc']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone font-bold\">培训机构：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['jg']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone font-bold\">培训地点：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['dd']}.</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <span class=\"resume-table-trone font-bold\">获得证书：</span>
                                <div class=\"resume-table-trseven\">
                                    <p class=\"user-information-p\">{$vf['zs']}</p>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <span class=\"resume-table-trone font-bold\">详细描述：</span>
                                <div class=\"resume-table-trseven\">
                                    <p class=\"user-information-p\">{$vf['ms']}</p>
                                </div>
                            </div>";
                }
                $res['status'] = 200;
                $res['content'] = $content;
            } else {
                $res['status'] = 300;
                $res['content'] = 0;
            }

            return json_encode($res);
        }
    }


    public function jianlipostadd8(Request $request, $id)
    {
        $user = \Auth::id();
        if ($user) {
            $info = $request->all();
            $gzjy = "";
            $data = array();
            foreach ($info['kc'] as $key => $vf) {
                if ($info['kc'][$key]) {
                    $data[] = array(
                        "kc" => $info['kc'][$key],
                        "zdy" => $info['zdy'][$key],
                        "sj" => $info['sj'][$key]
                    );
                }
            }
            if (count($data) > 0) {
                $up['zs'] = json_encode($data);
                $id = DB::table("jianli")->where("id", $id)->where("user_id", $user)->update($up);
                $content = "";
                foreach ($data as $key => $vf) {
                    if ($vf['zdy']) $zdy = "【{$vf['zdy']}】"; else {
                        $zdy = "";
                    }
                    $content .= "<div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\">证书名称：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['kc']}{$zdy}</p>
                                    </div>
                                </div>
                            </div>

                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\">获得时间：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['sj']}</p>
                                    </div>
                                </div>
                            </div>";
                }
                $res['status'] = 200;
                $res['content'] = $content;
            } else {
                $res['status'] = 300;
                $res['content'] = 0;
            }

            return json_encode($res);
        }
    }


    public function jianlipostadd9(Request $request, $id)
    {
        $user = \Auth::id();
        if ($user) {
            $info = $request->all();
            $up['qt'] = json_encode($info);
            $id = DB::table("jianli")->where("id", $id)->where("user_id", $user)->update($up);
            $content = " <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\">兴趣爱好：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['xqah']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">宗教信仰：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['zjxy']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\">获得荣誉：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['hdry']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">特长职业目标：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['zymb']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\">专业组织：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['zyzz']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">特殊技能：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['tsjn']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\">著作/论文：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['zzlw']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">社会活动：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['shhd']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\">专利：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['zl']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">荣誉：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$info['ry']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <span class=\"resume-table-trone\">其他：</span>
                                <div class=\"my-joinjob-textar\">
                                    <p class=\"user-information-p\">{$info['qt']}</p>
                                </div>
                            </div>";
            $res['status'] = 200;
            $res['content'] = $content;
        } else {
            $res['status'] = 300;
            $res['content'] = 0;
        }

        return json_encode($res);
    }


    public function jianlipostadd10(Request $request, $id)
    {
        $user = \Auth::id();
        if ($user) {
            $info = $request->all();
            $gzjy = "";
            $data = array();
            foreach ($info['xmmc'] as $key => $vf) {
                if ($info['xmmc'][$key]) {
                    $data[] = array(
                        "xmmc" => $info['xmmc'][$key],
                        "sj" => $info['sj'][$key],
                        "xmms" => $info['xmms'][$key],
                        "zrms" => $info['zrms'][$key]
                    );
                }
            }
            if (count($data) > 0) {
                $up['xmjy'] = json_encode($data);
                $id = DB::table("jianli")->where("id", $id)->where("user_id", $user)->update($up);
                $content = "";
                foreach ($data as $key => $vf) {
//                    if($vf['zdy'])$zdy="【{$vf['zdy']}】";else{$zdy="";}
                    $content .= " <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\">项目名称：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['xmmc']}</p>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">时间：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <p class=\"user-information-p\">{$vf['sj']}</p>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <span class=\"resume-table-trone\">项目描述：</span>
                                <div class=\"my-joinjob-textar\">
                                    <p class=\"user-information-p\">{$vf['xmms']}</p>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <span class=\"resume-table-trone\">责任描述：</span>
                                <div class=\"my-joinjob-textar\">
                                    <p class=\"user-information-p\">{$vf['zrms']}</p>
                                </div>
                            </div>";
                }
                $res['status'] = 200;
                $res['content'] = $content;
            } else {
                $res['status'] = 300;
                $res['content'] = 0;
            }

            return json_encode($res);
        }
    }


    public function jianliview($id)
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;
        $data['list'] = DB::table("jianli")->find($id);
        return view('user.jianliview', $data);
    }

    //简历修改
    public function jianliedit($id)
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;
        $data['list'] = DB::table("jianli")->find($id);
        return view('user.jianliedit', $data);
    }


    public function jianlitoudi()
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;
        $data['pagenewslist'] = \Auth::user()->hasManyResume('person_id')
            ->where(function ($query) {
            })->orderBy('created_at', 'desc')/*->paginate($this->paginate)*/->get()->toArray(/*$this->toArray*/);
        $data['user'] = $user;
        return view('user.profile', $data);
    }


    public function jianlimsyq()
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;
        $data['pagenewslist'] = \Auth::user()->hasManyResume('person_id')
            ->where(function ($query) {
                $query->whereStatus(2);
            })->orderBy('created_at', 'desc')/*->paginate($this->paginate)*/->get()->toArray(/*$this->toArray*/);
//        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;
        return view('user.profile', $data);
    }

    public function jianlidown()
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;
        return view('user.profile', $data);
    }

    public function jianliwdsc()
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;
        return view('user.profile', $data);
    }

    /*
     * 订单中心
     */

    public function order()
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();

        // 支付方式
        $config_pay_style = config('config.order.pay_style');
        // 订单状态
        $config_status = config('config.order.status');

        $_GET['orderno'] = isset($_GET['orderno']) && $_GET['orderno'] ? $_GET['orderno'] : '';
        $_GET['status'] = isset($_GET['status']) && $_GET['status'] ? $_GET['status'] : '';
        $order = \Auth::user()->hasManyOrder();
        $pagenewslist = $order->where(function ($query) {
            empty($_GET['orderno']) or $query->ordernoLike($_GET['orderno']);
            empty($_GET['status']) or $query->theStatus($_GET['status']);
        })->latest('created_at')->paginate($this->paginate)->toArray($this->toArray);
        $order_count_new = $order->theStatus('new')->count();
        $order_count_paid = $order->theStatus('paid')->count();
        $order_count_refund = $order->theStatus('refund')->count();
        $order_count_cancelled = $order->theStatus('cancelled')->count();
        $ckey = $ckey_no_status = '';
        foreach ($_GET as $key => $value) {
            if ($key <> 'page' && $value) $ckey .= "&$key=$value";
            if ($key <> 'status' && $key <> 'page' && $value) $ckey_no_status .= "&$key=$value";
        }
        return view('user.profile', compact(
            'user',
            'config_pay_style',
            'config_status',
            'pagenewslist',
            'ckey_no_status',
            'order_count_new',
            'order_count_paid',
            'order_count_refund',
            'order_count_cancelled',
            'ckey'));
    }

    public function orderview($id)
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $order = Order::findOrFail($id)->toArray();
        // $order['pay_style'] = config('config.order.pay_style.' . $order['pay_style']);
        // $order['status'] = config('config.order.status.' . $order['status']);
        return view('user.orderview', compact('user', 'order'));
    }


    public function ordersqtk(Request $id)
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;
        return view('user.ordersqtk', $data);
    }

    public function orderpay(Request $id)
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;
        return view('user.orderpay', $data);
    }

    public function orderpaysucc(Request $id)
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;
        return view('user.orderpaysucc', $data);
    }


    public function orderbmbzypx($type = null)
    {
        $tyarr = (new NewsCats)->getNavigation(['catname', 'id'], 2);
        $user = User::findOrFail(\Auth::id())->relationsToArray();

        // 支付方式
        $config_pay_style = config('config.order.pay_style');
        // 订单状态
        $config_status = config('config.order.status');
        $_GET['key'] = isset($_GET['key']) && $_GET['key'] ? $_GET['key'] : '';
        $_GET['typeid'] = isset($_GET['typeid']) && $_GET['typeid'] ? $_GET['typeid'] : 28;
        $order = \Auth::user()->hasManyOrder();

        $pagenewslist = $order->where(function ($query) {
            empty($_GET['key']) or $query->where('training_title','like','%'.$_GET['key'].'%');
            empty($_GET['typeid']) or $query->where('typeid','=',$_GET['typeid']);
        })->latest('created_at')->paginate($this->paginate)->toArray($this->toArray);
        $ckey = $ckey_no_status = '';
        foreach ($_GET as $key => $value) {
            if ($key <> 'page' && $value) $ckey .= "&$key=$value";
            if ($key <> 'status' && $key <> 'page' && $value) $ckey_no_status .= "&$key=$value";
        }
        return view('user.profile', compact(
            'user',
            'pagenewslist',
            'tyarr',
            'ckey'));
    }


    public function orderbmbzyzs($type = null)
    {
        $tyarr = (new NewsCats)->getNavigation(['catname', 'id'], 3);
        $user = User::findOrFail(\Auth::id())->relationsToArray();

        // 支付方式

        $_GET['key'] = isset($_GET['key']) && $_GET['key'] ? $_GET['key'] : '';
        $_GET['typeid'] = isset($_GET['typeid']) && $_GET['typeid'] ? $_GET['typeid'] : 9;
        $order = \Auth::user()->hasManyEnroll();

        $pagenewslist = $order->where(function ($query) {
            empty($_GET['key']) or $query->where('title','like','%'.$_GET['key'].'%');
            empty($_GET['typeid']) or $query->where('typeid','=',$_GET['typeid']);
        })->latest('created_at')->paginate($this->paginate)->toArray($this->toArray);
        $ckey = $ckey_no_status = '';
        foreach ($_GET as $key => $value) {
            if ($key <> 'page' && $value) $ckey .= "&$key=$value";
            if ($key <> 'status' && $key <> 'page' && $value) $ckey_no_status .= "&$key=$value";
        }
        return view('user.profile', compact(
            'user',
            'pagenewslist',
            'tyarr',
            'ckey'));
    }
    public function orderbmbgjjy($type = null)
    {
        $tyarr = (new NewsCats)->getNavigation(['catname', 'id'], 4);
        $user = User::findOrFail(\Auth::id())->relationsToArray();


        $_GET['key'] = isset($_GET['key']) && $_GET['key'] ? $_GET['key'] : '';
        $_GET['typeid'] = isset($_GET['typeid']) && $_GET['typeid'] ? $_GET['typeid'] : 12;
        $education = \Auth::user()->hasManyEnroll();

        $pagenewslist = $education->where(function ($query) {
            empty($_GET['key']) or $query->where('title','like','%'.$_GET['key'].'%');
            empty($_GET['typeid']) or $query->where('typeid','=',$_GET['typeid']);
        })->latest('created_at')->latest('id')->paginate($this->paginate)->toArray($this->toArray);
        $ckey = $ckey_no_status = '';
        foreach ($_GET as $key => $value) {
            if ($key <> 'page' && $value) $ckey .= "&$key=$value";
            if ($key <> 'status' && $key <> 'page' && $value) $ckey_no_status .= "&$key=$value";
        }
        return view('user.profile', compact(
            'user',
            'pagenewslist',
            'tyarr',
            'ckey'));
    }
    public function orderbmbview(Request $id)
    {
        $user = User::findOrFail(\Auth::id())->relationsToArray();
        $data['user'] = $user;
        return view('user.orderbmbview', $data);
    }
    public function jianlimbxz($type)
    {
        switch ($type) {
            case "gzjy":
                $content = " <div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\"><b>*</b>企业名称：</span>
                                    <div class=\"resume-table-trseven\" colspan=\"3\">
                                        <input type=\"text\" class=\"resume-company-name\" name=\"qymc[]\"/>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>行业类别：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <div class=\"my-joinjob-year\">
                                                <select name=\"qylb[]\">
                                                    <option value=\"金融\">金融</option>
                                                    <option value=\"服务业\">服务业</option>
                                                </select>
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                        <span class=\"resume-table-trone\"><b>*</b>工作时间：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"gzsj[]\"/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>职位名称：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"zwmc[]\"/>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                        <span class=\"resume-table-trone\"><b>*</b>职位月薪（税前）：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"price[]\"/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\"><b>*</b>工作描述：</span>
                                    <div class=\"resume-table-trseven\">
                                        <div class=\"my-joinjob-textar\">
                                            <textarea  name=\"gzms[]\"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\">企业规模：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"qygm[]\"/>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">企业性质：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <div class=\"my-joinjob-year\">
                                            <select name=\"qyxz[]\">
                                                <option value=\"国企\">国企</option>
                                                <option value=\"外资\">外资</option>
                                                <option value=\"合资\">合资</option>
                                                <option value=\"民营\">民营</option>
                                                <option value=\"其他\">其他</option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                </div>";
                break;
            case "jybj":
                $content = " <div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\"><b>*</b>学校名称：</span>
                                    <div class=\"resume-table-trseven\" colspan=\"3\">
                                        <input type=\"text\" class=\"resume-company-name\" name=\"xxmc[]\"/>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>时间：</span>
                                        <div class=\"resume-table-trseven\" colspan=\"3\">
                                            <input type=\"text\" class=\"resume-company-name\"  name=\"sj[]\"/>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                        <span class=\"resume-table-trone\"><b>*</b>专业名称：</span>
                                        <div class=\"resume-table-trseven\" colspan=\"3\">
                                            <input type=\"text\" class=\"resume-company-name\"   name=\"zy[]\"/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\"><b>*</b>是否统招：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <div class=\"my-joinjob-year\">
                                            <select   name=\"istz[]\">
                                                <option value=\"是\">是</option>
                                                <option value=\"否\">否</option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\"><b>*</b>学历/学位：</span>
                                    <div class=\"resume-table-trseven\" colspan=\"3\">
                                        <input type=\"text\" class=\"resume-company-name\" name=\"xl[]\"/>
                                    </div>
                                </div>
                                </div>";
                break;
            case "zxqk":
                $content = "<div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\">获得奖项：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"hdjx[]\"/>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                        <span class=\"resume-table-trone\">时间：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"jxsj[]\"/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\"><b>*</b>校内职务：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\"  name=\"xnzw[]\"/>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\"><b>*</b>时间：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\"  name=\"zwsj[]\"/>
                                    </div>
                                </div>
                            </div>";
                break;
            case "jntc":
                $content = " <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>技能/语言：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <div class=\"my-joinjob-year\">
                                                <select name=\"jnyy[]\">
                                                    <option value=\"外语\">外语</option>
                                                    <option value=\"编程\">编程</option>
                                                </select>
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"resume-table-treight fr\">
                                        <div class=\"my-default\">
                                            <span>自定义</span>
                                            <input type=\"text\" class=\"my-default-inp\" name=\"zdy[]\">
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>掌握程度：</span>
                                        <div class=\"resume-table-trseven\" colspan=\"3\">
                                            <input type=\"text\" class=\"resume-company-name\"   name=\"zwcd[]\"/>
                                        </div>
                                    </div>
                                </div>";
                break;
            case "pxjl":
                $content = "<div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>时间：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"sj[]\"/>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                        <span class=\"resume-table-trone\"><b>*</b>培训课程：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\"  name=\"kc[]\"/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>培训机构：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\"  name=\"jg[]\"/>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                        <span class=\"resume-table-trone\">培训地点：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\"  name=\"dd[]\"/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\">获得证书：</span>
                                    <div class=\"resume-table-trseven\">
                                        <div class=\"my-joinjob-textar\">
                                            <textarea  name=\"zs[]\"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\">详细描述：</span>
                                    <div class=\"resume-table-trseven\">
                                        <div class=\"my-joinjob-textar\">
                                            <textarea  name=\"ms[]\"></textarea>
                                        </div>
                                    </div>
                                </div>";
                break;
            case "zs":
                $content = " <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>培训课程：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"kc[]\"/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"resume-table-treight fr\">
                                        <div class=\"my-default\">
                                            <span>自定义</span>
                                            <input type=\"text\" class=\"my-default-inp\" name=\"zdy[]\">
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\"><b>*</b>获得时间：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"sj[]\"/>
                                    </div>
                                </div>
                            </div>";
                break;
            case "xmjy":
                $content = "  <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>项目名称：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"xmmc[]\"/>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                        <span class=\"resume-table-trone\"><b>*</b>时间：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"sj[]\"/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\"><b>*</b>项目描述：</span>
                                    <div class=\"resume-table-trseven\">
                                        <div class=\"my-joinjob-textar\">
                                            <textarea  name=\"xmms[]\"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\">责任描述：</span>
                                    <div class=\"resume-table-trseven\">
                                        <div class=\"my-joinjob-textar\">
                                            <textarea  name=\"zrms[]\"></textarea>
                                        </div>
                                    </div>
                                </div>";
                break;
            default:
                $content = 0;
                break;
        }
        if ($content) {
            $data['status'] = 200;
            $data['content'] = $content;
        } else {
            $data['status'] = 300;
            $data['content'] = "网络故障，请联系管理员";
        }
        return json_encode($data);
    }

    /*
     * 简历-获取对应的修改信息
     */
    function jianlimbxg(Request $request, $type)
    {
        $id = (int)$request->id;
        $user = \Auth::id();
        $res = DB::table("jianli")->where("id", $id)->where("user_id", $user)->first();
        $csrf = csrf_field();
        switch ($type) {
            case "jbxx":
                if($res->sex=='女'){$sex1='';$sex2='checked';}else{$sex1='checked';$sex2='';}
                if($res->hyzk=='已婚'){
                    $hyzk1='';$hyzk2='selected';$hyzk3='';
                }elseif($res->hyzk=='未婚'){
                    $hyzk1='';$hyzk2='';$hyzk3='selected';
                }else{
                    $hyzk1='selected';$hyzk2='';$hyzk3='';
                }
                if($res->zzmm=='党员'){
                    $zzmm1='selected';$zzmm2='';$zzmm3='';$zzmm4='';
                }elseif($res->zzmm=='团员'){
                    $zzmm1='';$zzmm2='selected';$zzmm3='';$zzmm4='';
                }elseif($res->zzmm=='其他党社'){
                    $zzmm1='';$zzmm2='';$zzmm3='selected';$zzmm4='';
                }else{
                    $zzmm1='';$zzmm2='';$zzmm3='';$zzmm4='selected';
                }
                $content = "<form onsubmit=\"return ck_jladd1(this)\">
                            {$csrf}
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\"><b>*</b>简历名称：</span>
                                    <div class=\"resume-table-trseven\" colspan=\"3\">
                                        <input type=\"text\" class=\"resume-company-name\" name=\"nid\" id=\"nid\" value='{$res->nid}'/>
                                    </div>
                                </div>
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\"><b>*</b>姓名：</span>
                                    <div class=\"resume-table-trtwo chose-sex\">
                                        <input class=\"myname\" type=\"text\" name=\"name\" value='{$res->name}'/>
                                        <label class=\"chose-sex-boy\">
                                            <input type=\"radio\" name=\"sex\" value=\"男\" {$sex1}/>
                                            <span>男</span>
                                        </label>
                                        <label class=\"chose-sex-girl\">
                                            <input type=\"radio\" name=\"sex\"  value=\"女\" {$sex2}/>
                                            <span>女</span>
                                        </label>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\"><b>*</b>户口所在地：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"address\"  value='{$res->address}'/>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\"><b>*</b>出生日期：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <div class=\"my-born-year\">
                                            <input type=\"text\" name=\"year\"  value='{$res->year}'/>
                                            <span>年</span>
                                        </div>
                                        <div class=\"my-born-year\">
                                            <input type=\"text\" name=\"month\"  value='{$res->month}'/>
                                            <span>月</span>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\"><b>*</b>现居住地：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"address_xjd\"  value='{$res->address_xjd}'/>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\"><b>*</b>参加工作年份：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"gznf\"  value='{$res->gznf}'/>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\"><b>*</b>联系方式：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"tel\"  value='{$res->tel}'/>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\"><b>*</b>最高学历：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"zgxl\"  value='{$res->zgxl}'/>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\"><b>*</b>电子邮箱：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"email\"  value='{$res->email}'/>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\">国籍</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"gj\"  value='{$res->gj}'/>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">婚姻状况：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <div class=\"my-joinjob-year\">
                                            <select name=\"hyzk\">
                                                <option value=\"保密\" {$hyzk1}>保密</option>
                                                <option value=\"已婚\" {$hyzk2}>已婚</option>
                                                <option value=\"未婚\" {$hyzk3}>未婚</option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\"><b>*</b>证件号码：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <div class=\"my-joinjob-year\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"zjhm\"  value='{$res->zjhm}'/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">政治面貌：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <div class=\"my-joinjob-year\">
                                            <select name=\"zzmm\">
                                                <option value=\"党员\" {$zzmm1}>党员</option>
                                                <option value=\"团员\" {$zzmm2}>团员</option>
                                                <option value=\"其他党社\" {$zzmm3}>其他党社</option>
                                                <option value=\"无党派人士\" {$zzmm4}>无党派人士</option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <span class=\"resume-table-trone\">海外工作/学习经历：</span>
                                <div class=\"my-joinjob-textar\">
                                    <textarea name=\"remark\">{$res->remark}</textarea>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"resume-table-trsix fr\">
                                    <input class=\"resume-table-inp\" type=\"submit\" value=\"保存\"/>
                                </div>
                            </div>
                        </form>";
                break;

            case "qzyx":
                $content = " <form onsubmit=\"return ck_jladd2(this)\">
                            {$csrf}
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\"><b>*</b>期望工作性质：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <div class=\"my-joinjob-year\">
                                            <select name=\"gzxz\">
                                                <option value=\"全职\">全职</option>
                                                <option value=\"兼职\">兼职</option>
                                                <option value=\"时工\">时工</option>
                                                <option value=\"不限\">不限</option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\"><b>*</b>期望从事行业：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <div class=\"my-joinjob-year\">
                                            <select name=\"cshy\">
                                                <option value=\"金融\">金融</option>
                                                <option value=\"教育\">教育</option>
                                                <option value=\"管理\">管理</option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\"><b>*</b>期望工作地点：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <div class=\"my-city\">
                                            <input type=\"text\" name=\"gzdd\" value='{$res->gzdd}'>
                                            <span>市</span>
                                        </div>
                                        <div class=\"my-city-select\">
                                            <select name=\"gzdd2\">
                                                <option></option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\"><b>*</b>期望月薪（税前）：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"price\"  value='{$res->price}'/>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\"><b>*</b>期望从事职业：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <div class=\"my-joinjob-year\">
                                            <select name=\"cszy\">
                                                <option value=\"服务业\">服务业</option>
                                                <option value=\"工业\">工业</option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\"><b>*</b>求职状态：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <div class=\"my-joinjob-year\">
                                            <select name=\"qzzt\">
                                                <option value=\"求职中\">求职中</option>
                                                <option value=\"在职中\">在职中</option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"resume-table-trsix fr\">
                                    <input class=\"resume-table-inp\" type=\"submit\" value=\"保存\"/>
                                </div>
                            </div>";
                break;

            case "qt":
                $content = "  <form onsubmit=\"return ck_jladd9(this)\" id=\"add_zs_form\">
                            {{ csrf_field() }}
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\">兴趣爱好：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"xqah\" value='{$res->xqah}'/>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">宗教信仰：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"zjxy\"  value='{$res->zjxy}'/>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\">获得荣誉：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\"  name=\"hdry\"  value='{$res->hdry}'/>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">特长职业目标：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\"  name=\"zymb\"  value='{$res->zymb}'/>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\">专业组织：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\"  name=\"zyzz\"  value='{$res->zyzz}'/>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">特殊技能：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\"  name=\"tsjn\"  value='{$res->tsjn}'/>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\">著作/论文：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"zzlw\"  value='{$res->zzlw}'/>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">社会活动：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"shhd\"  value='{$res->shhd}'/>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\">专利：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"zl\"  value='{$res->zl}'/>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">荣誉：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"ry\"  value='{$res->ry}'/>
                                    </div>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <span class=\"resume-table-trone\">其他：</span>
                                <div class=\"my-joinjob-textar\">
                                    <textarea  name=\"qt\"> {$res->qt}</textarea>
                                </div>
                            </div>
                            <div class=\"form-line clearfix\">
                                <div class=\"resume-table-trsix fr\">
                                    <input class=\"resume-table-inp\" type=\"submit\" value=\"保存\"/>
                                </div>
                            </div>
                        </form>";
                break;


            case "gzjy":
                $info = json_decode($res->gzjy, true);
                $content = "<form onsubmit=\"return ck_jladd3(this)\" id=\"add_gzjy_form\">
                            {$csrf}
                            <div id=\"add_gzjy_div\">";
                foreach ($info as $key => $vf) {
                    $content .= "<div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\"><b>*</b>企业名称：</span>
                                    <div class=\"resume-table-trseven\" colspan=\"3\">
                                        <input type=\"text\" class=\"resume-company-name\" name=\"qymc[]\"/>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>行业类别：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <div class=\"my-joinjob-year\">
                                                <select name=\"qylb[]\">
                                                    <option value=\"金融\">金融</option>
                                                    <option value=\"服务业\">服务业</option>
                                                </select>
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                        <span class=\"resume-table-trone\"><b>*</b>工作时间：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"gzsj[]\"/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>职位名称：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"zwmc[]\"/>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                        <span class=\"resume-table-trone\"><b>*</b>职位月薪（税前）：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"price[]\"/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\"><b>*</b>工作描述：</span>
                                    <div class=\"resume-table-trseven\">
                                        <div class=\"my-joinjob-textar\">
                                            <textarea  name=\"gzms[]\"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\">企业规模：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"qygm[]\"/>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\">企业性质：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <div class=\"my-joinjob-year\">
                                            <select name=\"qyxz[]\">
                                                <option value=\"国企\">国企</option>
                                                <option value=\"外资\">外资</option>
                                                <option value=\"合资\">合资</option>
                                                <option value=\"民营\">民营</option>
                                                <option value=\"其他\">其他</option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                </div>";
                }
                $content .= "</div>
                            <div class=\"form-line clearfix\">
                                <div class=\"resume-table-trsix fr\">
                                    <input class=\"resume-table-inp\" type=\"submit\" value=\"保存\"/>
                                </div>
                            </div>
                        </form>";
                break;


            case "jybj":
                $info = json_decode($res->jybj, true);
                $content = " <form onsubmit=\"return ck_jladd4(this)\" id=\"add_jybj_form\">
                            {$csrf}
                             <div id=\"add_jybj_div\">";
                foreach ($info as $key => $vf) {
                    $content .= "<div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\"><b>*</b>学校名称：</span>
                                    <div class=\"resume-table-trseven\" colspan=\"3\">
                                        <input type=\"text\" class=\"resume-company-name\" name=\"xxmc[]\" value='{$vf['xxmc']}'/>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>时间：</span>
                                        <div class=\"resume-table-trseven\" colspan=\"3\">
                                            <input type=\"text\" class=\"resume-company-name\"  name=\"sj[]\"  value='{$vf['sj']}'/>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                        <span class=\"resume-table-trone\"><b>*</b>专业名称：</span>
                                        <div class=\"resume-table-trseven\" colspan=\"3\">
                                            <input type=\"text\" class=\"resume-company-name\"   name=\"zy[]\"  value='{$vf['zy']}'/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\"><b>*</b>是否统招：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <div class=\"my-joinjob-year\">
                                            <select   name=\"istz[]\">
                                                <option value=\"是\">是</option>
                                                <option value=\"否\">否</option>
                                            </select>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\"><b>*</b>学历/学位：</span>
                                    <div class=\"resume-table-trseven\" colspan=\"3\">
                                        <input type=\"text\" class=\"resume-company-name\" name=\"xl[]\"  value='{$vf['xl']}'/>
                                    </div>
                                </div>
                                </div>";
                }
                $content .= "</div>
                            <div class=\"form-line clearfix\">
                                <div class=\"resume-table-trsix fr\">
                                    <input class=\"resume-table-inp\" type=\"submit\" value=\"保存\"/>
                                </div>
                            </div>
                        </form>";
                break;


            case "zxqk":
                $info = json_decode($res->zxqk, true);
                $content = " <form onsubmit=\"return ck_jladd5(this)\" id=\"add_zxqk_form\">
                            {$csrf}
                             <div id=\"add_zxqk_div\">";
                foreach ($info as $key => $vf) {
                    $content .= "<div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\">获得奖项：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"hdjx[]\"   value='{$vf['hdjx']}'/>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                        <span class=\"resume-table-trone\">时间：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"jxsj[]\"   value='{$vf['jxsj']}'/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\"><b>*</b>校内职务：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\"  name=\"xnzw[]\"   value='{$vf['xnzw']}'/>
                                    </div>
                                </div>
                                <div class=\"form-line-right fr\">
                                    <span class=\"resume-table-trone\"><b>*</b>时间：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\"  name=\"zwsj[]\"   value='{$vf['zwsj']}'/>
                                    </div>
                                </div>
                            </div>";
                }
                $content .= "</div>
                            <div class=\"form-line clearfix\">
                                <div class=\"resume-table-trsix fr\">
                                    <input class=\"resume-table-inp\" type=\"submit\" value=\"保存\"/>
                                </div>
                            </div>
                        </form>";
                break;


            case "jntc":
                $info = json_decode($res->jntc, true);
                $content = " <form onsubmit=\"return ck_jladd6(this)\" id=\"add_jntc_form\">
                            {$csrf}
                             <div id=\"add_jntc_div\">";
                foreach ($info as $key => $vf) {
                    $content .= " <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>技能/语言：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <div class=\"my-joinjob-year\">
                                                <select name=\"jnyy[]\">
                                                    <option value=\"外语\">外语</option>
                                                    <option value=\"编程\">编程</option>
                                                </select>
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"resume-table-treight fr\">
                                        <div class=\"my-default\">
                                            <span>自定义</span>
                                            <input type=\"text\" class=\"my-default-inp\" name=\"zdy[]\"  value='{$vf['zdy']}'>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>掌握程度：</span>
                                        <div class=\"resume-table-trseven\" colspan=\"3\">
                                            <input type=\"text\" class=\"resume-company-name\"   name=\"zwcd[]\"  value='{$vf['zwcd']}'/>
                                        </div>
                                    </div>
                                </div>";
                }
                $content .= "</div>
                            <div class=\"form-line clearfix\">
                                <div class=\"resume-table-trsix fr\">
                                    <input class=\"resume-table-inp\" type=\"submit\" value=\"保存\"/>
                                </div>
                            </div>
                        </form>";
                break;


            case "pxjl":
                $info = json_decode($res->pxjl, true);
                $content = " <form onsubmit=\"return ck_jladd7(this)\" id=\"add_pxjl_form\">
                            {$csrf}
                             <div id=\"add_pxjl_div\">";
                foreach ($info as $key => $vf) {
                    $content .= "<div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>时间：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"sj[]\"   value='{$vf['sj']}'/>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                        <span class=\"resume-table-trone\"><b>*</b>培训课程：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\"  name=\"kc[]\"   value='{$vf['kc']}'/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>培训机构：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\"  name=\"jg[]\"   value='{$vf['jg']}'/>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                        <span class=\"resume-table-trone\">培训地点：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\"  name=\"dd[]\"   value='{$vf['dd']}'/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\">获得证书：</span>
                                    <div class=\"resume-table-trseven\">
                                        <div class=\"my-joinjob-textar\">
                                            <textarea  name=\"zs[]\"> {$vf['zs']}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\">详细描述：</span>
                                    <div class=\"resume-table-trseven\">
                                        <div class=\"my-joinjob-textar\">
                                            <textarea  name=\"ms[]\">{$vf['ms']}</textarea>
                                        </div>
                                    </div>
                                </div>";
                }
                $content .= "</div>
                            <div class=\"form-line clearfix\">
                                <div class=\"resume-table-trsix fr\">
                                    <input class=\"resume-table-inp\" type=\"submit\" value=\"保存\"/>
                                </div>
                            </div>
                        </form>";
                break;


            case "zs":
                $info = json_decode($res->zs, true);
                $content = "<form onsubmit=\"return ck_jladd8(this)\" id=\"add_zs_form\">
                            {$csrf}
                             <div id=\"add_zs_div\">";
                foreach ($info as $key => $vf) {
                    $content .= "<div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>培训课程：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"kc[]\"   value='{$vf['kc']}'/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <div class=\"resume-table-treight fr\">
                                        <div class=\"my-default\">
                                            <span>自定义</span>
                                            <input type=\"text\" class=\"my-default-inp\" name=\"zdy[]\"   value='{$vf['zdy']}'>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                <div class=\"form-line-left fl\">
                                    <span class=\"resume-table-trone\"><b>*</b>获得时间：</span>
                                    <div class=\"resume-table-trtwo\">
                                        <input class=\"resume-table-inp\" type=\"text\" name=\"sj[]\"   value='{$vf['sj']}'/>
                                    </div>
                                </div>
                            </div>";
                }
                $content .= "</div>
                            <div class=\"form-line clearfix\">
                                <div class=\"resume-table-trsix fr\">
                                    <input class=\"resume-table-inp\" type=\"submit\" value=\"保存\"/>
                                </div>
                            </div>
                        </form>";
                break;


            case "xmjy":
                $info = json_decode($res->xmjy, true);
                $content = "<form onsubmit=\"return ck_jladd10(this)\" id=\"add_xmjy_form\">
                            {$csrf}
                             <div id=\"add_xmjy_div\">";
                foreach ($info as $key => $vf) {
                    $content .= "<div class=\"form-line clearfix\">
                                    <div class=\"form-line-left fl\">
                                        <span class=\"resume-table-trone\"><b>*</b>项目名称：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"xmmc[]\"    value='{$vf['xmmc']}'/>
                                        </div>
                                    </div>
                                    <div class=\"form-line-right fr\">
                                        <span class=\"resume-table-trone\"><b>*</b>时间：</span>
                                        <div class=\"resume-table-trtwo\">
                                            <input class=\"resume-table-inp\" type=\"text\" name=\"sj[]\"    value='{$vf['sj']}'/>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\"><b>*</b>项目描述：</span>
                                    <div class=\"resume-table-trseven\">
                                        <div class=\"my-joinjob-textar\">
                                            <textarea  name=\"xmms[]\">{$vf['xmms']}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"form-line clearfix\">
                                    <span class=\"resume-table-trone\">责任描述：</span>
                                    <div class=\"resume-table-trseven\">
                                        <div class=\"my-joinjob-textar\">
                                            <textarea  name=\"zrms[]\">{$vf['zrms']}</textarea>
                                        </div>
                                    </div>
                                </div>";
                }
                $content .= "</div>
                            <div class=\"form-line clearfix\">
                                <div class=\"resume-table-trsix fr\">
                                    <input class=\"resume-table-inp\" type=\"submit\" value=\"保存\"/>
                                </div>
                            </div>
                        </form>";
                break;


            default:
                $content = 0;
                break;
        }
        if ($content) {
            $data['status'] = 200;
            $data['content'] = $content;
        } else {
            $data['status'] = 300;
            $data['content'] = "网络故障，请联系管理员";
        }
        return json_encode($data);
    }


}
