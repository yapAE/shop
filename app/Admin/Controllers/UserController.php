<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use function foo\func;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\User';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);

        $grid->column('id', __('用户编号'));
        $grid->column('name', __('用户名'));
        $grid->column('email', __('Email'));
        $grid->column('email_verified_at', __('已验证邮箱'))->display(function ($value){
            return $value ? '是': '否';
        });
        $grid->column('created_at', __('注册时间'));
      //  $grid->column('updated_at', __('Updated at'));

        //暂时不考虑从后台新建用户
        $grid->disableCreateButton();
        //不展示每条记录后面的查看、删除、编辑按钮。
        $grid->actions(function($actions){

            $actions->disableView();

            $actions->disableDelete();

            $actions->disableEdit();
        });

        $grid->tools(function ($tools){

           $tools->batch(function ($batch){
               $batch->disableDelete();
           });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('email_verified_at', __('Email verified at'));
        $show->field('password', __('Password'));
        $show->field('remember_token', __('Remember token'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->text('name', __('Name'));
        $form->email('email', __('Email'));
        $form->datetime('email_verified_at', __('Email verified at'))->default(date('Y-m-d H:i:s'));
        $form->password('password', __('Password'));
        $form->text('remember_token', __('Remember token'));

        return $form;
    }
}
