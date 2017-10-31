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

class BusinessController extends base\UserController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 会员首页
     * @return
     */
    public function profile()
    {
        $user = \Auth::user()->relationsToArray();
        return view('business.profile', compact('user'));
    }

    /**
     * 招聘
     */
    public function job($action = '', $id= '')
    {
        $table = __FUNCTION__;
        return $this->relatedToNewsCats($table, $action, $id);
    }

    /**
     * 简历管理
     */
    public function resume($action = '', $id= '')
    {
        $table = __FUNCTION__;
        return $this->relatedToNewsCats($table, $action, $id);
    }

    /**
     * 职业培训管理
     */
    public function training($action = '', $id= '')
    {
        $table = __FUNCTION__;
        return $this->relatedToNewsCats($table, $action, $id);
    }

    /**
     * 证书管理
     */
    public function certificate($action = '', $id= '')
    {
        $table = __FUNCTION__;
        return $this->relatedToNewsCats($table, $action, $id);
    }

    /**
     * 国际教育管理
     */
    public function education($action = '', $id= '')
    {
        $table = __FUNCTION__;
        return $this->relatedToNewsCats($table, $action, $id);
    }

    /**
     * 实名认证
     */
    public function certification($action = '', $id= '')
    {
        $table = __FUNCTION__;
        return $this->relatedToNewsCats($table, $action, $id);
    }

    /**
     * 订单管理
     */
    public function order($action = '', $id= '')
    {
        $table = __FUNCTION__;
        return $this->relatedToNewsCats($table, $action, $id);
    }

    /**
     * 用户管理
     */
    public function users($action = '', $id= '')
    {
        $table = __FUNCTION__;
        return $this->relatedToNewsCats($table, $action, $id);
    }

    /**
     * 安全设置
     */
    public function safe($action = '', $id= '')
    {
        $table = __FUNCTION__;
        return $this->relatedToNewsCats($table, $action, $id);
    }

    private function relatedToNewsCats($table, $action, $id, $compact = [])
    {
        $haystack = ['user' => \Auth::user()->relationsToArray()];
        switch ($action) {
            case 'create':
            case 'update':
                $row = \Auth::user()->{'hasMany'.ucfirst($table)}->find($id);
                $view = 'cu';
                $haystack['row'] = $row;
                break;
            case 'delete':
                return $this->delete($table, $id);
                break;
            default://列表
                $view = 'list';
                // $list = \Auth::user()->{'hasMany'.ucfirst($table)}->toArray();
                break;
        }
        ;
        return view('business.related-news_cats-'.$view, array_merge($haystack, $compact));
    }
}
