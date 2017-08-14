<?php

namespace App\Admin\Controllers\email;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Template as YourModel;
use Illuminate\Http\Request;

class TemplateController extends Controller
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
            $content->header('邮件模板');

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

    public function createTemp(Request $request){
        $template = new YourModel;

        $template->name = $request->input('name');
        $template->content = $request->input('content');

        $template->save();

        return redirect(admin_url('email/template'));

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


            $content->header('header');
            $content->description('description');

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

            $grid->name('模型名称');

            $grid->content('模型内容');

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

            $form->text('name', '模型名称')->rules('required|min:3');
            $form->textarea('content', '模型内容')->rules('required|min:3');
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
