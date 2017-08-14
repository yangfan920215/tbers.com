<?php
/**
 * Created by PhpStorm.
 * User: phoenix
 * Date: 5/25/17
 * Time: 5:55 PM
 */

namespace Qwadmin\Controller;

class ClientsubController extends ComController{

    public function Review(){
        // 获取id
        $uid = isset($_SESSION['uid']) ? intval($_SESSION['uid']) : $this->error('无法读取用户信息');

        $vars = M('client')
            ->join('__MEMBER__ on __CLIENT__.uid = __MEMBER__.uid')
            ->where("qw_client.uid = $uid")
            ->select();

        $vars[0] = _parseJson($vars[0]['img_json'], $vars[0]);
        $vars[0]['edit'] = true;

        if (isset($vars[0]['dom_time'])){
            $vars[0]['dom_time'] = date('Y-m-d', $vars[0]['dom_time']);
        }

        $this->assign('var', $vars[0]);
        $this->display();
    }

    public function auditret(){
        $vars = M('audit')->where('uid=' . $_SESSION['uid'])->select();

        foreach ($vars as &$var) {
           if ($var['_status'] == '0'){
               $var['extmsg'] = '已通过';
           }elseif($var['_status'] == '1'){
               $var['extmsg'] = '审核中';
           }elseif($var['_status'] == '2'){
               $var['extmsg'] = '已拒绝：' . $var['msg'];
           }
        }

        $this->assign('vars', $vars);
        $this->display();
    }

    public function update(){
        // 非必须选项
        $data['nickname'] = I('post.nickname', '', 'strip_tags');
        $data['gold_show'] = I('post.gold_show', 0);
        $data['qq_json'] = I('post.qq_json', '', 'strip_tags');
        $data['_url1'] = I('post._url1', '', 'strip_tags');
        $data['_url2'] = I('post._url2', '', 'strip_tags');

        // 检测qq是否为三个
        if (count(explode(',',$data['qq_json'])) > 3){
            $this->error('QQ最多填写三个');
        }

            // 图片
        $head1 = I('post.head1', '', 'strip_tags');
        $head2 = I('post.head2', '', 'strip_tags');
        $head3 = I('post.head3', '', 'strip_tags');
        $head4 = I('post.head4', '', 'strip_tags');
        $head5 = I('post.head5', '', 'strip_tags');
        $data['img_json'] = json_encode(array('head1'=>$head1, 'head2'=>$head2, 'head3'=>$head3, 'head4'=>$head4, 'head5'=>$head5), JSON_UNESCAPED_UNICODE);


        $data['uid'] = isset($_POST['uid']) && $_POST['uid'] != '' ? intval($_POST['uid']) : $this->error('页面渲染异常,无法获取用户ID');

        $data['sub_time'] = date('Y-m-d H:i:s');

        // 受审核列表
        M('audit')->data($data)->add();

        addlog('会员提交编辑信息请求，会员UID：' . $data['uid']);

        $this->success('提交成功,请等待管理员审核!');
    }
}