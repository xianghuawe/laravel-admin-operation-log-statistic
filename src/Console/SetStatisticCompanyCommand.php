<?php

namespace Xianghuawe\Admin\Console;

use Illuminate\Console\Command;

class SetStatisticCompanyCommand extends Command
{
    protected $signature = 'admin-operation-logs:set-statistic-company';

    protected $description = '补充公司id-日志记录表';

    /**
     * @return int
     * @throws \Exception
     */
    public function handle()
    {

        config('admin.database.operation_statistic_model')
        ::chunkById(100, function($data) {
            foreach ($data as $value) {
                $value->company_id = $value?->user?->company?->id;
                $value->save();
            }
        });

        return self::SUCCESS;
    }

}
