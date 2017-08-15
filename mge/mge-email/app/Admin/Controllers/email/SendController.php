<?php

namespace App\Admin\Controllers\email;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Response;
use App\Models\Email_receiver as receiverModel;
use App\Models\Email_sender as senderModel;
use App\Models\Template as template;

use App\Models\config_emails_sender_type as config_emails_sender_type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendController extends Controller
{

    private $type = array(
        1=>'发信邮箱',
        2=>'用户邮箱',
    );

    private $maxSend = 30;

    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('邮件发送');

            $content->body($this->form());
        });
    }

    public function main(Request $request){
        set_time_limit(0);

        $tag = $request->tag;

        // 查询模板内容
        $template = $request->template;
        $content_email = template::where('id', $template)->get()->toArray()[0]['content'];

        // 查询收件人个数
        $users = receiverModel::where('type', $tag)->get()->toArray();
        // 用户个数
        $userCount = count($users);

        // 统计可发送邮箱
        $sendCount = senderModel::sum('number');

        if ($sendCount < $userCount){
            $error = new MessageBag([
                'title'   => 'Message',
                'message' => '发信邮箱不足,请补充...',
            ]);

            return redirect(admin_url('email/send'))->with(compact('error'));
        }

        // 查询全部发信次数存在的邮箱
        $senders = senderModel::where('number', '!=', 0)->get()->toArray();
        // 因为存在最大发信次数，copy数组进行操作
        $copySenders = $senders;

        // 查询该标签全部收件玩家
        foreach ($users as $item) {
            // 随机挑选发信者
            $senderId = array_rand($copySenders);

            $_ENV['MAIL_FROM_ADDRESS'] = $senders[$senderId]['email'];
            $_ENV['MAIL_USERNAME'] = $senders[$senderId]['email'];
            $_ENV['MAIL_PASSWORD'] = $senders[$senderId]['password'];

            $flag = Mail::raw($content_email, function ($message) use ($item) {
                $message->subject(' - ' .date('Y-m-d H:i:s'));
                $message->to($item['email']);
            });

            if ($flag){
                // 更新
                $senders[$senderId]['number'] -= 1;
                if ($copySenders[$senderId]['number'] == 1){
                    unset($copySenders[$senderId]);
                }
            }else{
                // 发送失败记录发送者和收信者
                Log::info('email send error：[ send:' . $senders[$senderId]['email'] . '][user:' . $item['email'] . ']');
            }
        }

        // 更新数据库更新
        senderModel::saved($senders);

        $success = new MessageBag([
            'title'   => 'Message',
            'message' => 'Do Success',
        ]);

        return redirect(admin_url('email/send'))->with(compact('success'));

/*        if($flag){
            $error = new MessageBag([
                'title'   => 'Message',
                'message' => 'Do Error',
            ]);

            return redirect(admin_url('email/send'))->with(compact('error'));
        }else{

        }*/

        // return response()->json(['msg' => 'delete success!']);
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

            $form->select('tag', '用户类型')->options(
                toSelect(
                    config_emails_sender_type::all()->toArray(),
                    'id',
                    'name'
                )
            );

            $form->select('template', '使用模板')->options(
                toSelect(
                    template::all()->toArray(),
                    'id',
                    'name'
                )
            );

            $form->setAction(admin_url('email/send/main'));
        });
    }
}
