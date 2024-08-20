<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    public function __construct()
    {
        // 未登录的用户可以访问个人信息页面和注册页面
        // 未登录用户访问用户编辑页面时将被重定向到登录页面
        // 已经登录的用户才可以访问个人信息编辑页面
        // except 方法来设定 指定动作 不使用 Auth 中间件进行过滤
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
        ]);

        // 只让未登录用户访问注册页面
        $this->middleware('guest', [
            'only' => ['create']
        ]);

        // 限制一个小时内只能提交注册请求 10 次
        $this->middleware('throttle:10,60', [
            'only' => ['store']
        ]);
    }

    /**
     * 显示用户注册页面
     *
     * @return Factory|View|Application
     */
    public function create(): Factory|View|Application
    {
        return view('users.create');
    }

    /**
     * 显示用户个人信息
     *
     * @param User $user
     * @return Factory|View|Application
     */
    public function show(User $user): Factory|View|Application
    {
        $statuses = $user->statuses()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('users.show', compact('user', 'statuses'));
    }

    /**
     * 创建用户
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已经发送到你的注册邮箱上，请注意查收。');
        return redirect()->route('home');
    }

    /**
     * 编辑用户信息
     *
     * @param User $user
     * @return Factory|View|Application
     * @throws AuthorizationException
     */
    public function edit(User $user): Factory|View|Application
    {
        // 使用 authorize 方法来验证用户授权策略，如果不通过则会抛出 403 异常
        // 只有当前登录的用户为被编辑用户时才能访问编辑页面
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    /**
     * 更新用户信息
     *
     * @param User $user
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function update(User $user, Request $request): RedirectResponse
    {
        // 使用 authorize 方法来验证用户授权策略，如果不通过则会抛出 403 异常
        // 只有当前登录的用户为被编辑用户时才能更新用户信息
        $this->authorize('update', $user);
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        session()->flash('success', '个人资料更新成功！');
        return redirect()->route('users.show', $user->id);
    }

    /**
     * 展示用户列表
     *
     * @return Factory|View|Application
     */
    public function index(): Factory|View|Application
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * 删除用户
     *
     * @param User $user
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        // back() 方法会将用户重定向到之前的页面上
        return back();
    }

    /**
     * 发送注册激活邮件
     *
     * @param $user
     * @return void
     */
    public function sendEmailConfirmationTo($user): void
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = '感谢注册 Weibo 应用！请确认你的邮箱。';

        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    /**
     * 用于激活用户
     *
     * @param $token
     * @return RedirectResponse
     */
    public function confirmEmail($token): RedirectResponse
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

    /**
     * 显示用户的关注列表
     *
     * @param User $user
     * @return Factory|View|Application
     */
    public function followings(User $user): Factory|View|Application
    {
        $users = $user->followings()->paginate(30);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    /**
     * 显示用户的粉丝列表
     *
     * @param User $user
     * @return Factory|View|Application
     */
    public function followers(User $user): Factory|View|Application
    {
        $users = $user->followers()->paginate(30);
        $title = $user->name . '的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
