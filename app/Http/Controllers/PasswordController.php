<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PasswordController extends Controller
{
    public function __construct()
    {
        // 限流，防止暴力破解
        // 请求限流是一种保护措施，可以防止用户在短时间内发送大量请求
        // 在 Laravel 限流的中间件是 throttle，它接受两个参数，第一个参数是最大的请求数，第二个参数是分钟数
        // 例如 throttle:3,10 表示 10 分钟内最多只能发送 3 个请求
        // 超过这个限制，Laravel 会返回一个 429 状态码，表示请求过多

        // 限制发送邮件的频率为 10 分钟 3 次
        $this->middleware('throttle:3,10', [
            'only' => ['showLinkRequestForm']
        ]);
    }

    /**
     * 显示密码重设页面, 填写 Email 的表单
     *
     * @return Factory|View|Application
     */
    public function showLinkRequestForm(): Factory|View|Application
    {
        return view('auth.passwords.email');
    }

    /**
     * 发送密码重设邮件, 处理表单提交，成功的话就发送邮件，附带 Token 的链接
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        // 1. 验证邮箱
        $request->validate(['email' => 'required|email']);
        $email = $request->email;

        // 2. 获取对应的用户
        $user = User::where('email', $email)->first();

        // 3. 如果不存在
        if (is_null($user)) {
            session()->flash('danger', '邮箱未注册');
            return redirect()->back()->withInput();
        }

        // 4. 生成 token 在视图中拼接链接 emails.reset_link
        $token = hash_hmac('sha256', Str::random(40), config('app.key'));

        // 5. 存入数据库, 使用 updateOrInsert 方法来保持 Email 唯一
        //    updateOrInsert 方法会自动判断，如果存在就更新，不存在就插入
        DB::table('password_resets')->updateOrInsert(['email' => $email], [
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => new Carbon
        ]);

        // 6. 将 token 链接发送给用户
        //    使用 Mail::send 方法来发送邮件
        Mail::send('emails.reset_link', compact('token'), function ($message) use ($email) {
            $message->to($email)->subject('忘记密码');
        });

        session()->flash('success', '重置邮件发送成功，请查收');
        return redirect()->back();
    }

    /**
     * 显示重设密码页面，将 url 中的 token 获取并且传递给视图
     *
     * @param Request $request
     * @return Factory|View|Application
     */
    public function showResetForm(Request $request): Factory|View|Application
    {
        $token = $request->route()->parameter('token');
        return view('auth.passwords.reset', compact('token'));
    }

    /**
     * 重设密码操作，验证 token 和 email，正确的话更新密码
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function reset(Request $request): RedirectResponse
    {
        // 1. 验证数据
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);
        $email = $request->email;
        $token = $request->token;
        // 找回密码链接的有效时间
        $expires = 60 * 10;

        // 2. 获取对应用户
        $user = User::where('email', $email)->first();

        // 3. 如果不存在
        if (is_null($user)) {
            session()->flash('danger', '邮箱未注册');
            return redirect()->back()->withInput();
        }

        // 4. 读取重置的记录
        $record = (array)DB::table('password_resets')->where('email', $email)->first();

        // 5. 如果记录存在
        if ($record) {
            // 5.1 检查是否过期
            if (Carbon::parse($record['created_at'])->addSeconds($expires)->isPast()) {
                session()->flash('danger', '链接已过期，请重新尝试');
                return redirect()->back();
            }

            // 5.2 检查是否正确
            if (!Hash::check($token, $record['token'])) {
                session()->flash('danger', '令牌失效');
                return redirect()->back();
            }

            // 5.3 更新用户密码
            $user->update(['password' => bcrypt($request->password)]);

            // 5.4 提示用户更新成功
            session()->flash('success', '密码重置成功，请使用新密码登录');
            return redirect()->route('login');
        }

        // 6. 记录不存在
        session()->flash('danger', '未找到重置记录');
        return redirect()->back();
    }
}
