<?php

namespace App\Admin\Controllers\email;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\config_emails_sender_type as YourModel;
use Illuminate\Http\Request;

class SendTypeController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('用户标签');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->editform()->edit($id));
        });
    }

    public function editTemp(Request $request){
        $template = YourModel::find($request->id);

        $template->content = $request->content;

        $template->save();
        return redirect(admin_url('email/template'));
    }

    public function createTag(Request $request){
        $model = new YourModel;

        $model->name = $request->input('name');

        $model->save();

        return redirect(admin_url('email/sendtype'));

/*        return Admin::form(YourModel::class, function (Form $form) {
            // 抛出成功信息
            $form->saving(function ($form) {

                $success = new MessageBag([
                    'title'   => 'title...',
                    'message' => 'message....',
                ]);

                return back()->with(compact('success'));
            });

        });*/
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('创建用户标签');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(YourModel::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->name('标签名称');

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableDelete();
            });

            $grid->tools(function (Grid\Tools $tools) {
                $tools->batch(function (Grid\Tools\BatchActions $actions) {
                    $actions->disableDelete();
                });
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(YourModel::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->text('name', '标签名称')->rules('required|min:3');
            $form->setAction(admin_url('email/sendtype/createTag'));
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function editform()
    {
        return Admin::form(YourModel::class, function (Form $form) {
            $form->hidden('id');
            // $form->text('name', '模型名称')->rules('required|min:3');
            $form->textarea('content', '模型内容')->rules('required|min:3');
            $form->setAction(admin_url('email/template/edit'));
        });
    }
}
