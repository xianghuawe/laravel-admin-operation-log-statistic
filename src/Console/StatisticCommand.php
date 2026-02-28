<?php

namespace Xianghuawe\Admin\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Xianghuawe\Admin\Mail\AdminOperationLogStatisticWarning;
use Xianghuawe\Admin\Mail\BaseMail;

class StatisticCommand extends Command
{
    protected $signature = 'admin-operation-logs:statistic';

    protected $description = '回收后台日志记录表';

    /**
     * @return int
     *
     * @throws \Exception
     */
    public function handle()
    {
        $configKey  = 'admin-operation-log.request_rate_limit_count';
        $countLimit = (int) config($configKey, 100);
        if ($countLimit < 1) {
            Log::error($this->description . "缺少配置[$configKey]或者配置[$configKey]不正确");

            return self::FAILURE;
        }
        $statisticDate = today()->subDay();

        $data = DB::table(config('admin.database.operation_log_table'))
            ->where('created_at', '>=', $statisticDate->toDateString())
            ->where('created_at', '<', $statisticDate->copy()->addDay()->toDateString())
            ->selectRaw('count(*) as num,user_id,path')
            ->groupBy(['user_id', 'path'])
            ->get();

        $exceptPaths = config('admin.operation_log_statistic.except', []);

        $userCompanyList = $data->pluck('user_id')->unique()->map(function ($item) {
            $user = config('admin.database.users_model')::find($item);

            return [
                'user_id'       => $item,
                'company_id'    => $user?->company?->id,
            ];
        });

        $data = $data->groupBy('user_id')
            ->map(function (Collection $item) use ($countLimit, $statisticDate, $exceptPaths, $userCompanyList) {
                $item = $item->transform(function ($item) {
                    // 合并path里包含参数的路由
                    preg_match('/(.*)\/\d+$/', $item->path, $matches);
                    if (!empty($matches)) {
                        $item->path = $matches[1];
                    }

                    return $item;
                })
                    ->groupBy('path')
                    ->map(function (Collection $item) {
                        $fist = $item->first();

                        return [
                            'user_id' => $fist->user_id,
                            'path'    => $fist->path,
                            'num'     => $item->sum('num'),
                        ];
                    });

                $top = $item->sortByDesc('num')->first();

                $company = $userCompanyList->where('user_id', $top['user_id'])->first();

                $item = [
                    'date'       => $statisticDate->toDateString(),
                    'user_id'    => $top['user_id'],
                    'company_id' => $company['company_id'],
                    'total'      => $item->sum('num'),
                    'top_path'   => $top['path'],
                    'top_num'    => $top['num'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if ($item['total'] < $countLimit) {
                    return null;
                }
                foreach ($exceptPaths as $exceptPath) {
                    if (Str::is($exceptPath, $top['path'])) {
                        return null;
                    }
                }

                return $item;
            })
            ->filter()
            ->sortBy('total');

        config('admin.database.operation_statistic_model')::where('date', $statisticDate->toDateString())->delete();

        if ($data->isNotEmpty()) {
            foreach ($data->chunk(500) as $insertData) {
                config('admin.database.operation_statistic_model')::insert($insertData->toArray());
            }
            if (config('admin-operation-log.admin_email')) {
                $this->createEmailNotification(config('admin-operation-log.admin_email'), new AdminOperationLogStatisticWarning($statisticDate));
            }
        }

        return self::SUCCESS;
    }

    /**
     * 创建邮件通知
     */
    public function createEmailNotification($to, BaseMail $mailableNotification)
    {
        $client = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Client'     => config('admin.notification.client_id'),
        ])
            ->acceptJson()
            ->baseUrl(config('admin.notification.uri'));

        $formParams = [
            'channel'             => 1,
            'send_after'          => 0,
            'route'               => $to,
            'content'             => $mailableNotification->render(),
            'created_by'          => null,
            'priority'            => 1,
            'notification_source' => json_encode($mailableNotification->envelope()),
        ];

        $requestId                  = Str::uuid();
        $formParams['request_id']   = $requestId;
        $formParams['request_time'] = time();
        ksort($formParams);
        $formParams['signature'] = self::signature(Arr::only($formParams, [
            'request_time',
        ]), config('admin.notification.client_secret'));

        $res    = $client->post(config('admin.notification.endpoint', 'notifications'), $formParams);
        $res    = $res->json();
        $status = Arr::get($res, 'status');
        if (!$status) {
            $msg = Arr::get($res, 'message');
            throw new \Exception('send email got error ' . $msg);
        }

        return true;
    }

    /**
     * 签名
     */
    public static function signature(array $data, string $secret): string
    {
        ksort($data);

        return md5(Arr::query($data) . "&secret={$secret}");
    }
}
