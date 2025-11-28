# Laravel Admin Operation Log Statistic

## 项目介绍

Laravel Admin Operation Log Statistic 是一个为 laravel-admin 框架开发的操作日志统计扩展包。它可以自动统计后台用户的操作日志，提供直观的数据报表，并在操作频率异常时发送警告通知。

## 功能特性

- ✅ 自动统计每日操作日志
- ✅ 提供操作频率异常警告
- ✅ 支持按日期、用户、角色等条件筛选统计数据
- ✅ 可视化展示统计结果
- ✅ 支持邮件通知功能
- ✅ 可配置的统计规则

## 安装

### 1. 安装依赖

使用 Composer 安装扩展包：

```bash
composer require xianghuawe/laravel-admin-operation-log-statistic
```

### 2. 发布配置和迁移文件

```bash
# 发布配置文件
php artisan vendor:publish --tag=operation-log-statistic-config

# 发布迁移文件
php artisan vendor:publish --tag=operation-log-statistic-migrations

# 发布语言文件
php artisan vendor:publish --tag=operation-log-statistic-lang
```

### 3. 运行迁移

```bash
php artisan migrate
```

### 4. 配置环境变量

在 `.env` 文件中添加以下配置：

```env
# 启用操作日志统计功能
OPERATION_LOG_STATISTIC_ENABLE=true

# 统计任务执行时间（24小时制）
OPERATION_LOG_STATISTIC_AT=09:55

# 操作频率限制（超过此值会触发警告）
ADMIN_OPERATION_LOG_REQUEST_RATE_LIMIT_COUNT=100

# 通知相关配置（可选）
ADMIN_NOTIFICATION_CLIENT_ID=
ADMIN_NOTIFICATION_CLIENT_SECRET=
ADMIN_NOTIFICATION_URI=
ADMIN_NOTIFICATION_ENDPOINT=notifications
```

## 配置说明

扩展包的配置文件位于 `config/admin.php`，主要配置项如下：

```php
'operation-log-statistic' => [
    'enable'   => env('OPERATION_LOG_STATISTIC_ENABLE', false), // 是否启用统计功能
    'daily_at' => env('OPERATION_LOG_STATISTIC_AT', '09:55'), // 统计任务执行时间
],
```

## 使用

### 1. 查看统计报表

安装完成后，在 laravel-admin 后台可以看到「操作日志统计」菜单，点击进入即可查看统计报表。

统计报表支持以下筛选条件：
- 用户角色
- 邀请码
- 用户名
- 姓名
- 日期范围
- 操作总数

### 2. 定时任务

扩展包会自动注册一个每日执行的定时任务，用于统计前一天的操作日志。确保你的 Laravel 应用已经配置了定时任务：

```bash
# 启动定时任务处理器
php artisan schedule:work
```

### 3. 手动执行统计

如果需要手动执行统计任务，可以使用以下命令：

```bash
php artisan admin-operation-logs:statistic
```

## 数据结构

### 统计数据表 `admin_operation_log_statistics`

| 字段名       | 类型          | 描述                     |
|-------------|-------------|--------------------------|
| id          | bigint      | 主键                     |
| date        | date        | 统计日期                 |
| user_id     | bigint      | 用户ID                   |
| company_id  | bigint      | 公司ID（可选）           |
| total       | int         | 当日操作总数             |
| top_num     | int         | 最频繁操作的次数         |
| top_path    | string      | 最频繁操作的路径         |
| created_at  | timestamp   | 创建时间                 |
| updated_at  | timestamp   | 更新时间                 |

## 注意事项

1. 确保 laravel-admin 已经安装并配置完成
2. 确保操作日志表 `admin_operation_logs` 存在
3. 定时任务需要正常运行才能自动统计
4. 邮件通知功能需要配置正确的通知服务

## 贡献

欢迎提交 Issue 和 Pull Request！

## 许可证

MIT License

## 作者

xianghuawe - xianghua_we@163.com
