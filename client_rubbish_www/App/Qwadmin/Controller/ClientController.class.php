<?php
/**
 * Created by PhpStorm.
 * User: phoenix
 * Date: 5/24/17
 * Time: 9:39 AM
 */
namespace Qwadmin\Controller;

class ClientController extends ComController{

    private $dom_status = array(
        0=>'开启',
        1=>'关闭',

    );

    private $_status = array(
        0=>'通过',
        1=>'审核中',
        2=>'已拒绝',
    );

    public function manage(){
        // 查询渠道信息和用户信息
        $vars = M('client')->join('__MEMBER__ on __CLIENT__.uid = __MEMBER__.uid')->select();

        $this->assign('vars', $vars);
        $this->display();
    }

    public function createStaticHtml(){
        $id = isset($_GET['id']) && $_GET['id'] != '' ? intval($_GET['id']) : $this->error('渠道信息获取失败,请尝试刷新页面！');

        // 查询渠道信息
        $clientInfo = M('client')->where('id = ' . $id)->select();
        $clientInfo = isset($clientInfo[0]) ? $clientInfo[0] : $this->error('渠道信息获取失败1,请尝试刷新页面！');

        if(!isset($clientInfo['dom']) || !filter_var($clientInfo['dom'], FILTER_VALIDATE_URL)){
            $this->error('域名不合法,请修改该渠道域名！');
        }

        $client_dom_status = isset($clientInfo['dom_status']) ? $clientInfo['dom_status'] : $this->error('渠道域名状态获取失败,请尝试刷新页面！');
        $client_dom_time = isset($clientInfo['dom_time']) ? $clientInfo['dom_time'] : $this->error('渠道域名截止时间获取失败,请尝试刷新页面！');

        // 查询默认信息
        $def = M('client_attr')->select();
        // 赋值默认信息
        $dayu_brand = '###';
        $dayu_help = '###';
        $dayu_mobile = '###';
        $dayu_pcclient = '';
        $dayu_preventHijack = '';
        $dayu_signin = '';
        $dayu_nickname = '';
        $dayu_money = '';
        $dayu_image = '';
        $dayu_link1 = '';
        $dayu_link2 = '';
        $dayu_qq = '';
        $dayu_gambling = '';
        foreach ($def as $val){
            // 昵称
            if ($val['_name'] == 'dayu_brand'){
                $dayu_brand = $val['_val'];
            }

            if ($val['_name'] == 'dayu_help'){
                $dayu_help = $val['_val'];
            }

            if ($val['_name'] == 'dayu_mobile'){
                $dayu_mobile = $val['_val'];
            }

            if ($val['_name'] == 'dayu_pcclient'){
                $dayu_pcclient = $val['_val'];
            }

            if ($val['_name'] == 'dayu_preventHijack'){
                $dayu_preventHijack = $val['_val'];
            }

            if ($val['_name'] == 'dayu_signin'){
                $dayu_signin = $val['_val'];
            }

            if ($val['_name'] == 'dayu_nickname'){
                $dayu_nickname = $val['_val'];
            }

            if ($val['_name'] == 'dayu_money'){
                $dayu_money = $val['_val'];
            }

            if ($val['_name'] == 'dayu_image'){
                $dayu_image = $val['_val'];
            }

            if ($val['_name'] == 'dayu_link1'){
                $dayu_link1 = $val['_val'];
            }

            if ($val['_name'] == 'dayu_link2'){
                $dayu_link2 = $val['_val'];
            }

            if ($val['_name'] == 'dayu_qq'){
                $dayu_qq = $val['_val'];
            }

            if ($val['_name'] == 'dayu_gambling'){
                $dayu_gambling = $val['_val'];
            }

        }

        // 在client站点建立模板文件目录
        $md5file = md5(substr(strstr(parse_url($clientInfo['dom'])['host'], '.'), 1));
        $dir1 = get_img_upload_firesfile($md5file);
        $dir2 = get_img_upload_basefile($md5file);
        $dir3 = get_img_upload_filename($md5file);
        $clientDir = dirname($_SERVER['DOCUMENT_ROOT']) . '/client/' . $dir1 . '/' . $dir2 . '/';
        if (file_exists($clientDir)){
            mkdirs($clientDir);
        }

        // 迁移轮播图到client站点
        $img_json = json_decode($clientInfo['img_json'], true);
        // 修改模板文件图片路径和迁移图片
        foreach ($img_json as $key=>&$item) {
            if ($item == ''){
                unset($img_json[$key]);
            }
            $old_pic_file = dirname($_SERVER['DOCUMENT_ROOT']) . '/mge' . $item;

            $new_pic_file = str_replace('mge/Public/attached/', '', $old_pic_file);

            if (!file_exists(dirname($new_pic_file))){
                mkdirs(dirname($new_pic_file));
            }

            $ret = copy($old_pic_file, $new_pic_file);

            if (!$ret){
                addlog('debug:' . $old_pic_file . '->' . $new_pic_file . '失败');
            }

            // 将图片引用路径改为client站点下的文件
            $item = str_replace('/Public/attached/client', '', $item);

        }

        // 如果域名状态关闭或超过截止时间，则全部使用默认
        if ($client_dom_status != 0 || time() > $client_dom_time){
            // 默认模板ID
            $tmp_id = 1;
            $qq_json =  explode(',', $dayu_qq);
            $clientInfo['nickname'] = $dayu_nickname;
            $clientInfo['gold_show'] = $dayu_money;
            $clientInfo['_url1'] = $dayu_link1;
            $clientInfo['_url2'] = $dayu_link2;
        }else{
            // 模板ID
            $tmp_id = isset($clientInfo['tmp_id']) ? $clientInfo['tmp_id'] : 1;
            $qq_json = isset($clientInfo['qq_json']) && $clientInfo['qq_json'] != '' ? explode(',', $clientInfo['qq_json']) : explode(',', $dayu_qq);

            // 昵称判定
            if (!isset($clientInfo['nickname']) || $clientInfo['nickname'] == ''){
                $clientInfo['nickname'] = $dayu_nickname;
            }

            // 显示金额判定
            if (!isset($clientInfo['gold_show']) || $clientInfo['gold_show'] == ''){
                $clientInfo['gold_show'] = $dayu_money;
            }

            // 两个URL
            if (!isset($clientInfo['_url1']) || $clientInfo['_url1'] == ''){
                $clientInfo['_url1'] = $dayu_link1;
            }
            if (!isset($clientInfo['_url2']) || $clientInfo['_url2'] == ''){
                $clientInfo['_url2'] = $dayu_link2;
            }
        }



        $this->assign('val', $clientInfo);
        $this->assign('qq_json', $qq_json);
        $this->assign('img_json', $img_json);

        $this->assign('dayu_brand', $dayu_brand);
        $this->assign('dayu_help', $dayu_help);
        $this->assign('dayu_mobile', $dayu_mobile);
        $this->assign('dayu_pcclient', $dayu_pcclient);
        $this->assign('dayu_preventHijack', $dayu_preventHijack);
        $this->assign('dayu_signin', $dayu_signin);
        $this->assign('dayu_nickname', $dayu_nickname);

        if (file_exists($clientDir)){
            unlink($clientDir . $dir3 . '.html');
        }

        $this->buildHtml($dir3 . '.html', $clientDir,'Client:front:' . $tmp_id . ':index','utf8');

        $this->success('页面生成成功');
    }

    public function add()
    {
        $this->display('form');
    }

    public function update()
    {
        // 检查是否配置常量渠道组id
        $client_group_id = $this->check_client_group_id() ? $this->check_client_group_id() : $this->error('请配置渠道组ID,KEY为CLIENTGROUBID');

        // 必须选项
        $data['dom'] = isset($_POST['dom']) && $_POST['dom'] != '' ? trim($_POST['dom']) : $this->error('网站域名必须填写');
        $data['dom_time'] = isset($_POST['dom_time']) ? strtotime($_POST['dom_time']) : $this->error('域名截止时间必须填写');
        $data['_name'] = isset($_POST['_name']) && $_POST['_name'] != '' ? trim($_POST['_name']) : $this->error('代理名称必须填写');
        $data['gold_group'] = isset($_POST['gold_group']) && $_POST['gold_group'] != '' ? trim($_POST['gold_group']) : $this->error('代理奖金组必须填写');
        $data['dom_status'] = isset($_POST['dom_status']) && $_POST['dom_status'] != '' ? trim($_POST['dom_status']) : $this->error('网站状态必须选择');
        $data['_description'] = isset($_POST['_description']) && $_POST['_description'] != '' ? trim($_POST['_description']) : $this->error('备注信息必须填写');

        // 登录账户只有在修改用户是才为空
        $data['user'] = isset($_POST['user']) && $_POST['user'] != '' ? trim($_POST['user']) : false;
        $data['password'] = isset($_POST['password']) && $_POST['password'] != '' ? password(trim($_POST['password'])) : false;

        // 非必须选项
        // $data['nickname'] = I('post.nickname', '', 'strip_tags');
        // $data['gold_show'] = I('post.gold_show', 0, 'intval');
        $data['qq_json'] = I('post.qq_json', '', 'strip_tags');

        // 检测qq是否为三个
        if (count(explode(',',$data['qq_json'])) > 3){
            $this->error('QQ最多填写三个');
        }


        $data['_url1'] = I('post._url1', '', 'strip_tags');
        $data['_url2'] = I('post._url2', '', 'strip_tags');
        $data['dom_status'] =I('post.dom_status', 1, 'intval');
        // 图片
        $head1 = I('post.head1', '', 'strip_tags');
        $head2 = I('post.head2', '', 'strip_tags');
        $head3 = I('post.head3', '', 'strip_tags');
        $head4 = I('post.head4', '', 'strip_tags');
        $head5 = I('post.head5', '', 'strip_tags');
        $data['img_json'] = json_encode(array('head1'=>$head1, 'head2'=>$head2, 'head3'=>$head3, 'head4'=>$head4, 'head5'=>$head5), JSON_UNESCAPED_UNICODE);

        // 必须为0或1
        if ($data['dom_status'] != 0 && $data['dom_status'] != 1){
            $this->error('网站状态参数异常');
        }

        // 创建用户
        if ($data['user'] !== false){
            // 用户信息使用默认值
            $dataUser['user'] = $data['user'];
            $dataUser['password'] = $data['password'];
            $dataUser['sex'] = 0;
            $dataUser['head'] = '/Public/attached/2017/05/23/5923fecfc9de3.png';
            $dataUser['birthday'] = strtotime('2000-01-01');
            $dataUser['phone'] = '';
            $dataUser['qq'] = '';
            $dataUser['email'] = '';
            $dataUser['t'] = time();

            if (M('member')->where("user='" . $data['user'] ."'")->count()) {
                $this->error('用户名已被占用！');
            }

            // 新增用户
            $data['uid'] = M('member')->data($dataUser)->add();
            // 分配角色
            M('auth_group_access')->data(array('group_id' => $client_group_id, 'uid' => $data['uid']))->add();
            // 记录日志
            addlog('新增会员，会员UID：' . $data['uid']);

            // 新增渠道
            M('client')->data($data)->add();

            addlog('新增渠道商，名称：' . $data['_name']);

            $this->success('操作成功！');
        }else{
            // 修改用户
            // 获取uid
            $where['uid'] = isset($_POST['uid']) && $_POST['uid'] != '' ? intval($_POST['uid']) : $this->error('页面渲染异常,无法获取用户ID');
            unset($data['user']);
            if (empty($data['password'])){
                unset($data['password']);
            }else{
                $dataUser['password'] = $data['password'];

                M('member')->data($dataUser)->where($where)->save();
            }

            M('client')->data($data)->where($where)->save();

            addlog('编辑会员信息，会员UID：' . $where['uid']);


            $this->success('操作成功！', '/index.php/Qwadmin/client/manage.html');
        }


        //}

    }

    public function audit(){
        // 查询渠道未审核信息
        $vars = M('audit')->field('qw_audit.*,qw_member.user')->join('__MEMBER__ on __AUDIT__.uid = __MEMBER__.uid')->select();


        foreach ($vars as &$var) {
            $var = _parseJson($var['img_json'], $var);
            $var['status_name'] = isset($this->_status[$var['_status']]) ? $this->_status[$var['_status']] : '未知状态';
            $var['sub_time'] = $var['sub_time'] == '' ? '旧数据,时间已丢失' : $var['sub_time'];
        }

        $this->assign('vars', $vars);
        $this->display();
    }

    /**
     * 拒绝
     */
    public function refuse(){
        if (isset($_POST['msg'])){
            // 修改审核状态为拒绝，补录拒绝理由
            $data['_status'] = 2;
            $data['msg'] = I('post.msg', '', 'strip_tags');
            $where['id'] = isset($_POST['id']) ? $_POST['id'] : $this->error('无法获取审核信息ID');

            M('audit')->data($data)->where($where)->save();

            $this->success('拒绝成功！');
        }else{
            $var['id'] = isset($_GET['id']) ? $_GET['id'] : $this->error('无法获取审核信息ID');


            $this->assign('var', $var);
            $this->display();
        }
    }

    /**
     * 设置共通配置
     */
    public function setCommAttr(){
        $vars = M('client_attr')->select();

        $this->assign('vars', $vars);
        $this->display();
    }

    public function addAttr(){
        $this->display();
    }

    /**
     * 修改配置
     */
    public function editAttr(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : $this->error('参数异常');

        $vars = M('client_attr')->where('id = ' . $id)->select();

        if(!isset($vars[0])){
            $this->error('没有找到对应的变量信息');
        }
        $vars[0]['edit'] = true;
        $this->assign('var', $vars[0]);
        $this->display('addAttr');
    }

    /**
     * 更新配置和新增配置
     */
    public function attrUpdate(){
        $where['id'] = isset($_POST['id']) && $_POST['id'] != '' ? intval($_POST['id']) : false;
        $data['_val'] = isset($_POST['_val']) && $_POST['_val'] != '' ? $_POST['_val'] : $this->error('变量值不能为空');

        // 非必须选项
        $data['_describe'] = I('post._describe', '', 'strip_tags');

        if ($where['id'] === false){
            $data['_name'] = isset($_POST['_name']) && $_POST['_name'] != '' ? $_POST['_name'] : $this->error('变量名称不能为空');
            M('client_attr')->data($data)->add();
            $this->success('变量新增成功');
        }else{
            M('client_attr')->data($data)->where($where)->save();
            $this->success('变量编辑成功');
        }


    }


    /**
     * 审核通过
     */
    public function adopt(){
        // 获取审核信息的ID
        $id = isset($_GET['id']) ? intval($_GET['id']) : $this->error('参数异常');

        // 获取审核信息内容
        $vars = M('audit')->field('uid,nickname,gold_show,img_json,_url1,_url2,qq_json')->where('id=' . $id)->select();

        $data = isset($vars[0]) ? $vars[0] : $this->error('无法找到渠道提交修改的信息');

        $where['uid'] = isset($data['uid']) ? $data['uid'] : $this->error('无法找到渠道绑定的用户信息');
        unset($data['uid']);

        // 更新用户状态
        M('client')->data($data)->where($where)->save();
        // 修改审核信息的状态为已完成
        M('audit')->data(array('_status'=>0))->where(array('id'=>$id))->save();
        addlog('通过，会员UID：' . $where['uid'] . '的申请，ID：' . $id);
        $this->success('操作成功！');
    }

    public function edit()
    {
        // 获取id
        $id = isset($_GET['id']) ? intval($_GET['id']) : false;

        if ($id) {
            $vars = M('client')
                ->join('__MEMBER__ on __CLIENT__.uid = __MEMBER__.uid')
                ->where("qw_client.id = $id")
                ->select();
        } else {
            $this->error('参数错误！');
        }

        $vars[0] = _parseJson($vars[0]['img_json'], $vars[0]);
        $vars[0]['edit'] = true;

        if (isset($vars[0]['dom_time'])){
            $vars[0]['dom_time'] = date('Y-m-d', $vars[0]['dom_time']);
        }

        $this->assign('var', $vars[0]);
        $this->display('form');
    }

    public function del()
    {
        $uids = isset($_REQUEST['uids']) ? $_REQUEST['uids'] : false;

        // uid为1的禁止删除
        if ($uids == 1 || !$uids) {
            $this->error('参数错误！');
        }

        if (is_array($uids)) {
            foreach ($uids as $k => $v) {
                //uid为1的禁止删除
                if ($v == 1) {
                    unset($uids[$k]);
                }
                $uids[$k] = intval($v);
            }
            if (!$uids) {
                $this->error('参数错误！');
                $uids = implode(',', $uids);
            }
        }

        $map['uid'] = array('in', $uids);

        if (M('member')->where($map)->delete()) {
            M('auth_group_access')->where($map)->delete();

            // 删除渠道
            M('client')->where($map)->delete();

            addlog('删除会员UID：' . $uids);
            addlog('删除渠道UID：' . $uids);
            $this->success('渠道删除成功！');
        } else {
            $this->error('参数错误！');
        }
    }
}