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
            'except' => ['show', 'create', 'store', 'index']
        ]);

        // 只让未登录用户访问注册页面
        $this->middleware('guest', [
            'only' => ['create']
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
        return view('users.show', compact('user'));
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

        Auth::login($user);
        session()->flash('success', '注册成功！');
        return redirect()->route('users.show', [$user]);
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
}
