<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统异常风险操作监控报告</title>
    <!-- Outlook专属兼容：解决表格布局和换行问题 -->
    <!--[if mso]>
    <style type="text/css">
        body { font-family: "Microsoft YaHei", Arial !important; }
        table { border-collapse: collapse !important; table-layout: fixed !important; }
        td, th { word-break: break-all !important; }
    </style>
    <![endif]-->
    <style type="text/css">
        /* 全局重置：适配所有邮箱客户端 */
        body, p, table, tr, td, th { margin: 0; padding: 0; font-family: "Microsoft YaHei", Arial, "Helvetica Neue", sans-serif; line-height: 1.6; }
        body { color: #333; background-color: #f9f9f9; }

        /* 移动端核心适配：解决小屏宽度溢出 */
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; padding: 0 !important; }
            .content-table { width: 100% !important; table-layout: fixed !important; }
            .content-table th, .content-table td { padding: 8px 4px !important; font-size: 12px !important; }
            .col-path { width: 40% !important; } /* 路由列优先占宽 */
            .col-narrow { width: 10% !important; } /* 短内容列缩窄 */
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f9f9f9;">
<!-- 外层表格：解决不同邮箱的边距/背景问题 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f9f9f9">
    <tr>
        <td align="center" valign="top" style="padding: 20px 10px;">
            <!-- 内容容器：扩大宽度+自适应 -->
            <table class="container" width="720" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="border: 1px solid #eee; border-radius: 6px; overflow: hidden; max-width: 100%;">
                <!-- 邮件头部 -->
                <tr>
                    <td bgcolor="#2563eb" style="padding: 20px; text-align: center;">
                        <h1 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: bold;">系统异常风险操作监控报告</h1>
                        <p style="margin: 5px 0 0 0; color: #e0e7ff; font-size: 12px;">{{ date('Y-m-d H:i:s') }} 自动生成 | 监控周期：最近24小时</p>
                    </td>
                </tr>
                <!-- 邮件内容 -->
                <tr>
                    <td style="padding: 20px 25px;">
                        <p style="margin: 0 0 15px 0; color: #333; font-size: 14px;">尊敬的系统管理员：</p>
                        <p style="margin: 0 0 20px 0; color: #333; font-size: 14px; line-height: 1.8;">
                            系统安全监控模块检测到<span style="color: #2563eb; font-weight: bold;">最近24小时内</span>平台出现<span style="color: #dc2626; font-weight: bold;">前十条高风险异常操作记录</span>，此类操作可能存在账号盗用、恶意攻击、权限越权等安全隐患。请管理员及时核查操作来源、处置风险，并采取针对性的防护措施。
                        </p>

                        <!-- 核心表格：优化列宽+强制换行 -->
                        @if(!empty($riskRecords))
                            <table class="content-table" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin: 0 0 20px 0; border-collapse: collapse; table-layout: fixed; width: 100%;">
                                <tr style="background-color: #f8f8f8;">
                                    <!-- 列宽按业务优先级分配 -->
                                    <th class="col-narrow" style="padding: 12px; text-align: left; color: #333; font-size: 13px; font-weight: bold; border-bottom: 1px solid #eee; width: 8%;">用户ID</th>
                                    <th style="padding: 12px; text-align: left; color: #333; font-size: 13px; font-weight: bold; border-bottom: 1px solid #eee; width: 18%;">用户名(名称)</th>
                                    <th style="padding: 12px; text-align: left; color: #333; font-size: 13px; font-weight: bold; border-bottom: 1px solid #eee; width: 12%;">角色</th>
                                    <th class="col-narrow" style="padding: 12px; text-align: left; color: #333; font-size: 13px; font-weight: bold; border-bottom: 1px solid #eee; width: 10%;">访问次数</th>
                                    <th class="col-narrow" style="padding: 12px; text-align: left; color: #333; font-size: 13px; font-weight: bold; border-bottom: 1px solid #eee; width: 12%;">最高路由次数</th>
                                    <th class="col-path" style="padding: 12px; text-align: left; color: #333; font-size: 13px; font-weight: bold; border-bottom: 1px solid #eee; width: 40%;">访问最高路由</th>
                                </tr>
                                @foreach($riskRecords as $index => $riskRecord)
                                    <tr>
                                        <!-- 所有单元格强制换行，避免溢出 -->
                                        <td style="padding: 12px; text-align: left; color: #333; font-size: 13px; border-bottom: 1px solid #eee; word-break: break-all;">{{ $riskRecord->user_id }}</td>
                                        <td style="padding: 12px; text-align: left; color: #333; font-size: 13px; border-bottom: 1px solid #eee; word-break: break-all;">{{ sprintf('%s(%s)', $riskRecord->adminUser->name, $riskRecord->adminUser->username) }}</td>
                                        <td style="padding: 12px; text-align: left; color: #333; font-size: 13px; border-bottom: 1px solid #eee; word-break: break-all;">{{ $riskRecord->adminUser->getRoleName()['CN'] ?? '未知角色' }}</td>
                                        <td style="padding: 12px; text-align: left; color: #333; font-size: 13px; border-bottom: 1px solid #eee; word-break: break-all;">{{ $riskRecord->total ?? 0 }}</td>
                                        <td style="padding: 12px; text-align: left; font-size: 13px; border-bottom: 1px solid #eee; word-break: break-all;">{{ $riskRecord->top_num ?? 0 }}</td>
                                        <!-- 路由列单独优化行高，提升换行可读性 -->
                                        <td style="padding: 12px; text-align: left; color: #333; font-size: 13px; border-bottom: 1px solid #eee; word-break: break-all; line-height: 1.5;">{{ $riskRecord->top_path }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            <p style="margin: 0 0 20px 0; color: #6c757d; font-size: 14px; text-align: center; padding: 20px; border: 1px dashed #eee; border-radius: 4px;">监控周期内未检测到异常风险操作，系统运行状态安全</p>
                        @endif

                        <!-- 处置建议 -->
                        <div style="margin: 20px 0; padding: 15px; background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 4px;">
                            <p style="margin: 0 0 10px 0; color: #2563eb; font-size: 14px; font-weight: bold;">★ 管理员处置建议：</p>
                            <p style="margin: 0 0 5px 0; color: #333; font-size: 13px;">1. 核查高危操作账号：若为异常账号，立即冻结并通知用户；若为正常账号，提醒用户修改密码并开启二次验证；</p>
                            <p style="margin: 0 0 5px 0; color: #333; font-size: 13px;">2. 分析高频访问路由：若为非核心接口的异常访问，可临时限制该接口的访问频率，降低攻击风险；</p>
                            <p style="margin: 0 0 5px 0; color: #333; font-size: 13px;">3. 检查接口权限：若为权限越权操作，及时修复接口的权限校验逻辑，避免漏洞被利用；</p>
                            <p><?=$riskDate?></p>

                            <p style="margin: 0; color: #333; font-size: 13px;">4. 查看详细日志：可通过<a target="_blank" href="{{ route('admin.system.admin-operation-log-statistic.index', ['date' => ['start'=>$riskDate,'end'=>$riskDate]]) }}" style="color: #2563eb; text-decoration: underline;">系统日志中心</a>查看完整操作记录，定位风险根源。</p>
                        </div>

                        <p style="margin: 0 0 0 0; color: #333; font-size: 14px;">请管理员尽快处理上述风险，确保平台数据和用户账号的安全稳定！</p>
                    </td>
                </tr>
                <!-- 邮件底部 -->
                <tr>
                    <td bgcolor="#f8f8f8" style="padding: 15px; text-align: center;">
                        <p style="margin: 0; color: #999; font-size: 12px; line-height: 1.5;">
                            本邮件由系统安全监控模块自动发送 | 版权所有 © {{ date('Y') }} {{ env('APP_NAME', '系统管理平台') }} | 管理后台：{{ env('APP_URL') }}/admin
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
