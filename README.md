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

## 社交功能
 - 关注用户
 - 取消关注
 - 关注和粉丝列表
 - 社交的统计信息
 - 关注用户动态流

 ## 运行项目
- 将 .env.example 文件复制一份，并重命名为 .env
- 修改 .env 文件中的数据库配置为自己的配置
- 执行 composer install 安装依赖
- 执行 php artisan key:generate 生成应用密钥
- 执行 php artisan migrate 迁移数据库
- 为了数据库名称和大家的项目不冲突，将本地老师项目对应的数据库名称修改为 lu_blog-laravel9 请注意这里的数据库名称，需要和 .env 文件中的配置一致


## 今天干了些什么呢 2024-08-13
 - 访问策略
    - 只有当前用户可以更新自己的个人信息
    - 未登录的用户才可以访问登录注册页面
    - 不需要登录就可以访问的页面
    - 只有登录用户才可以访问的页面
    - 只有管理员才可以删除用户并且才能看到删除按钮
 - 查看用户列表
 - 用户列表分页

 - 今天的部分 laravel 命令
    - `php artisan migrate` 迁移数据库
    - `php artisan make:policy UserPolicy` 创建用户策略
    - `php artisan migrate:refresh` 重置数据库
    - `php artisan migrate:refresh --seed` 重置数据库并填充数据
    - `php artisan make:seed UsersTableSeeder` 创建用户数据填充
    - `php artisan db:seed --class=UsersTableSeeder` 填充用户数据
    - `php artisan make:migration add_is_admin_to_users_table --table=users` 添加管理员字段
  

## 今天做了写什么 2024-08-14

- 账户激活
    - 用于激活新注册的用户

    1. 用户注册成功后，自动生成激活令牌
    2. 将激活令牌以链接的形式附带在注册邮件里面，并将邮件发送到用户的注册邮箱上
    3. 用户在点击注册链接跳到指定路由，路由收到激活令牌参数后映射给相关的控制器来处理
    4. 控制器拿到激活令牌并进行验证，验证通过之后对该用户进行激活，并将其激活状态设置未已激活
    5. 用户激活成功之后，自动登录
- 用户密码重设
    - 帮助用户找回密码
- 邮件发送


## 今天做了写什么 2024-08-15

- 用户密码重设
    - 用于用户忘记密码时，通过邮箱重设密码

@@ -92,17 +93,19 @@

- 配置生产环境中的真实邮件发送
    - MAIL_DRIVER=smtp
      - QQ 邮箱的 SMTP 服务器地址，必须为此值
        - 使用支持 ESMTP 的 SMTP 服务器发送邮件
    - MAIL_HOST=smtp.qq.com
      - QQ 邮箱的 SMTP 服务器端口，必须为此值
        - QQ 邮箱的 SMTP 服务器地址，必须为此值
    - MAIL_PORT=25
      - 请将此值换为你的 QQ + @qq.com
    - MAIL_USERNAME=123456@qq.com
      - 密码是我们第一步拿到的授权码
    - MAIL_PASSWORD=abcdefg
      - 加密类型，选项 null 表示不使用任何加密，其他选项还有 ssl，这里我们使用 tls 即可
        - QQ 邮箱的 SMTP 服务器端口，必须为此值
    - MAIL_USERNAME=xxxxxxxxxxxxxx@qq.com
        - 请将此值换为你的 QQ + @qq.com
    - MAIL_PASSWORD=xxxxxxxxx
        - 密码是我们第一步拿到的授权码
    - MAIL_ENCRYPTION=tls
      - 此值必须同 MAIL_USERNAME 一致
    - MAIL_FROM_ADDRESS=123456@qq.com
      - 用来作为邮件的发送者名称
        - 加密类型，选项 null 表示不使用任何加密，其他选项还有 ssl，这里我们使用 tls 即可
    - MAIL_FROM_ADDRESS=xxxxxxxxxxxxxx@qq.com
        - 此值必须同 MAIL_USERNAME 一致
    - MAIL_FROM_NAME=WeiboApp
        - 用来作为邮件的发送者名称

     
## 今天做了写什么 2024-08-16

- 创建了一个 `user-statuses` 的分支，用于实现微博的 CRUD 功能
    - `git checkout -b user-statuses` 创建并切换到 `user-statuses` 分支
- 创建微博数据模型
    - `php artisan make:migration create_statuses_table --create="statuses"` 创建微博数据表迁移文件
- 显示微博列表
    - `php artisan make:controller StatusesController` 创建微博控制器
    - `php artisan make:model Models/Status` 创建微博模型
    - `php artisan make:policy StatusPolicy` 创建微博策略
- 通过模型工厂和数据填充生成微博数据
    - `php artisan make:factory StatusFactory` 创建微博工厂
    - `php artisan make:seeder StatusesTableSeeder` 创建微博数据填充
    - `php artisan migrate:refresh --seed` 重置数据库并填充数据
- 发布微博
- 首页显示微博列表
- 删除微博
    - 只有微博的作者才可以删除微博，否则不显示删除按钮
