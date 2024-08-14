<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SessionsController extends Controller
{
    public function __construct()
    {
        // 只让未登录用户访问登录页面
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    /**
     * 显示登录页面
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view('sessions.create');
    }

    /**
     * 登录
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $credential = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        // Laravel 中 Auth 的 attempt 方法可以让我们很方便的完成用户的身份认证操作
        if (Auth::attempt($credential, $request->has('remember'))) {
            // 登录成功
            session()->flash('success', '欢迎回来！');
            $fallback = route('users.show', Auth::user());
            // intended 方法可将页面重定向到上一次请求尝试访问的页面上
            // 如果上一次请求记录为空，则跳转到默认地址, 这里是用户个人页面
            return redirect()->intended($fallback);
        } else {
            // 登录失败
            session()->flash('danger', '抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
        }
    }

    /**
     * 退出登录
     *
     * @return Redirector|Application|RedirectResponse
     */
    public function destroy(): Redirector|Application|RedirectResponse
    {
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }
}
