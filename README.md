# 这是一个「微博」项目
这是一个使用 Laravel 9 开发的简单的微博项目，包括用户的注册、登录、微博的创建、删除等功能。

## 微博数据
 - 发布微博
 - 删除微博
 - 查看微博列表

## 会话控制
 - 用户注册
 - 用户登录
 - 用户退出

## 用户功能
 - 注册
 - 用户激活
 - 修改密码
 - 邮件发送
 - 个人中心
 - 用户列表
 - 用户删除

## 静态页面
 - 首页
 - 关于
 - 帮助

 - 

## 社交功能
 - 关注用户
 - 取消关注
 - 关注和粉丝列表
 - 社交的统计信息
 - 关注用户动态流

运行项目
将 .env.example 文件复制一份，并重命名为 .env
修改 .env 文件中的数据库配置为自己的配置
执行 composer install 安装依赖
执行 php artisan key:generate 生成应用密钥
执行 php artisan migrate 迁移数据库
为了数据库名称和大家的项目不冲突，将本地老师项目对应的数据库名称修改为 lu_blog-laravel9 请注意这里的数据库名称，需要和 .env 文件中的配置一致
今天干了些什么呢 2024-08-13
访问策略

只有当前用户可以更新自己的个人信息
未登录的用户才可以访问登录注册页面
不需要登录就可以访问的页面
只有登录用户才可以访问的页面
只有管理员才可以删除用户并且才能看到删除按钮
查看用户列表

用户列表分页

今天的部分 laravel 命令

php artisan migrate 迁移数据库
php artisan make:policy UserPolicy 创建用户策略
php artisan migrate:refresh 重置数据库
php artisan migrate:refresh --seed 重置数据库并填充数据
php artisan make:seed UsersTableSeeder 创建用户数据填充
php artisan db:seed --class=UsersTableSeeder 填充用户数据
php artisan make:migration add_is_admin_to_users_table --table=users 添加管理员字段
今天做了写什么 2024-08-14
账户激活

用于激活新注册的用户
用户注册成功后，自动生成激活令牌
将激活令牌以链接的形式附带在注册邮件里面，并将邮件发送到用户的注册邮箱上
用户在点击注册链接跳到指定路由，路由收到激活令牌参数后映射给相关的控制器来处理
控制器拿到激活令牌并进行验证，验证通过之后对该用户进行激活，并将其激活状态设置未已激活
用户激活成功之后，自动登录
用户密码重设

帮助用户找回密码
邮件发送
