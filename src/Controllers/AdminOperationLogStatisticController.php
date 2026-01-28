<?php

namespace Xianghuawe\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;

class AdminOperationLogStatisticController extends AdminController
{
    public function title()
    {
        return __('admin-operation-log-statistic.labels.list');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {

        $model = config('admin.database.operation_statistic_model');

        $grid = new Grid(new $model());

        $grid->filter(function ($filter) {
            $filter->column(1 / 2, function (Grid\Filter $filter) {
                $roleModel = config('admin.database.roles_model');
                $filter->equal('user.roles.id', __('admin-operation-log-statistic.fields.role_name'))
                    ->select($roleModel::all()->pluck('name', 'id'));
                $filter->equal('user.invite_code', __('admin-operation-log-statistic.fields.invite_code'));
                $filter->like('user.username', __('admin-operation-log-statistic.fields.username'));
                $filter->like('user.name', __('admin-operation-log-statistic.fields.name'));
            });
            $filter->column(1 / 2, function (Grid\Filter $filter) {
                $filter->between('date', __('admin-operation-log-statistic.fields.date'))->date();
                $filter->gt('total', __('admin-operation-log-statistic.fields.total'));
            });
        });
        $grid->disableCreateButton();
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableAll();
        });


        $grid->model()->with([
            'user.roles',
            'user' => function($query) {
                $query->with(['company' => function($query) {
                    $query->withDefault();
                }]);
            }
        ]);

        $grid->column('date', __('admin-operation-log-statistic.fields.date'));
        $grid->column('user_id', __('admin-operation-log-statistic.fields.user_id'));
        $grid->column('company', __('admin-operation-log-statistic.fields.company'))->display(function(){
            return $this->user?->company?->name;
        });
        $grid->column('user.username', __('admin-operation-log-statistic.fields.username'));
        $grid->column('user.name', __('admin-operation-log-statistic.fields.name'));
        $grid->column('role_name', __('admin-operation-log-statistic.fields.role_name'))
            ->display(function () {
                return $this->user?->roles?->pluck('name');
            })->label();
        $grid->column('total', __('admin-operation-log-statistic.fields.total'))->sortable();
        $grid->column('top_num', __('admin-operation-log-statistic.fields.top_num'))->sortable();
        $grid->column('top_path', __('admin-operation-log-statistic.fields.top_path'))->limit(60);
        $grid->column('created_at', __('admin-operation-log-statistic.fields.created_at'));

        return $grid;
    }
}
