<?php

namespace App\Admin\Controllers\email;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Response;
use Illuminate\Support\MessageBag;
use App\Models\Email_receiver as receiverModel;
use App\Models\Email_sender as senderModel;

use App\Models\config_emails_sender_type as config_emails_sender_type;
use Illuminate\Http\Request;

class UploadController extends Controller
{

    private $type = array(
        1=>'发信邮箱',
        2=>'用户邮箱',
    );

    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('邮箱上传');

            $content->body($this->form());
        });
    }

    public function main(Request $request){
        $type = $request->type;
        $tag = $request->tag;

        $file = $request->file('email');

        // 文件是否上传成功
        if ($file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $typefile = $file->getClientMimeType();     // image/jpeg

            $file = $realPath;

            $file_arr = file($file);

            // 上传文件
            $filename = date('Y-m-d-H-i-s') . '-' . $type . '-' . $tag . '-' . uniqid() . '.' . $ext;
            // 使用我们新建的uploads本地存储空间（目录）
            $bool = \Storage::disk('admin')->put($filename, file_get_contents($realPath));
        }

        $content = [];
        switch ($type){
            case 1:
                for($i=0;$i<count($file_arr);$i++){//逐行读取文件内容

                    $arr = explode(',', $file_arr[$i]);
                    if (count($arr) < 2){
                        $error = new MessageBag([
                            'title'   => 'email',
                            'message' => '文件格式异常,请编辑后重新提交!',
                        ]);

                        return redirect(admin_url('email/upload'))->with(compact('error'));
                    }
                    $content[] = [
                        'email'=>trim($arr[0]),
                        'password'=>trim($arr[1])
                    ];
                }

                // 发信邮箱
                $senderModel = new senderModel;
                $senderModel->insert($content);
            case 2:
                for($i=0;$i<count($file_arr);$i++){//逐行读取文件内容
                    $content[] = [
                        'email'=>trim($file_arr[$i]),
                        'type'=>$tag
                    ];
                }
                $receiverModel = new receiverModel;
                $receiverModel->insert($content);
                break;
            default:
                return redirect(admin_url());
                break;
        }


        $success = new MessageBag([
            'title'   => 'Message',
            'message' => 'Do Success',
        ]);

        return redirect(admin_url('email/upload'))->with(compact('success'));
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

            $content->body($this->form()->edit($id));
        });
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
/*        return Admin::grid(YourModel::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->created_at();
            $grid->updated_at();
        });*/
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(receiverModel::class, function (Form $form) {

            $form->select('type', '邮箱类型')->options($this->type);

            $form->select('tag', '用户类型')->options(
                toSelect(
                    config_emails_sender_type::all()->toArray(),
                    'id',
                    'name'
                )
            );

            $form->file('email', '邮箱文件')->help('暂时只支持txt文件上传,邮箱以,分隔');
            $form->setAction(admin_url('email/upload/main'));
        });
    }
}
