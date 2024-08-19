<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaticPagesController extends Controller
{
    /**
     * 显示首页
     * 如果当前用户已登录，就渲染微博动态流页面
     *
     * @return Factory|View|Application
     */
    public function home(): Factory|View|Application
    {
        $feed_items = [];
        // 判断当前用户是否已登录
        if (Auth::check()) {
            $feed_items = Auth::user()->feed()->paginate(30);
        }
        return view('static_pages/home', compact('feed_items'));
    }

    /**
     * 显示帮助页面
     *
     * @return Factory|View|Application
     */
    public function help(): Factory|View|Application
    {
        return view('static_pages/help');
    }

    /**
     * 显示关于页面
     *
     * @return Factory|View|Application
     */
    public function about(): Factory|View|Application
    {
        return view('static_pages/about');
    }
}
