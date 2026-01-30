<?php

namespace Xianghuawe\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminOperationLogStatistic extends Model
{
    protected $table = 'admin_operation_log_statistics';

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        parent::__construct($attributes);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(config('admin.database.users_model'));
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(config('admin.database.company_model'),'company_id', 'id');
    }
}
