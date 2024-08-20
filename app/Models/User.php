<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($user) {
            $user->activation_token = Str::random(10);
        });
    }

    /**
     * 根据邮箱来获取当前用户的头像
     * Gravatar 是一项用于提供全球通用头像服务的免费服务
     * Gravatar 通过对用户的邮箱进行 MD5 加密来生成用户的全球通用头像
     * https://www.gravatar.com/
     *
     * @param $size
     * @return string
     */
    public function gravatar($size = 100): string
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "https://www.gravatar.com/avatar/$hash?s=$size";
    }

    /**
     * 一个用户拥有多条微博
     *
     * @return HasMany
     */
    public function statuses(): HasMany
    {
        return $this->hasMany(Status::class);
    }

    /**
     * 获取当前用户发布的所有微博
     *
     * @return mixed
     */
    public function feed()
    {
        $user_ids = $this->followings->pluck('id')->toArray();
        array_push($user_ids, $this->id);
        return Status::whereIn('user_id', $user_ids)
            ->with('user')
            ->orderBy('created_at', 'desc');
    }

    /**
     * 获取粉丝关系列表
     * 当查询我的粉丝的时候在 followers 表中查找 user_id 为当前用户 id 的记录，这时的 follower_id 就是我的粉丝
     *
     * @return BelongsToMany
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    /**
     * 获取用户关注人列表
     * 当查询我关注的人的时候在 followers 表中查找 follower_id 为当前用户 id 的记录，这时的 user_id 就是我关注的人
     *
     * @return BelongsToMany
     */
    public function followings(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    /**
     * 关注
     *
     * @param $user_ids
     */
    public function follow($user_ids): void
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }

    /**
     * 取消关注
     *
     * @param $user_ids
     */
    public function unfollow($user_ids): void
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    /**
     * 是否关注了某个用户, $user_id 是要检查的用户 id
     * contains 方法是 Collection 类的一个方法，用来判断一个集合是否包含某个元素
     * 成功返回 true，失败返回 false
     *
     * @param $user_id
     * @return bool
     */
    public function isFollowing($user_id): bool
    {
        return $this->followings->contains($user_id);
    }
}
