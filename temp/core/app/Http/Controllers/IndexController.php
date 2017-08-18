<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use libs\avgle;

use App\Models\Contacters;

class IndexController extends Controller
{
    // 女优目录
    private $actresses_names = [
        '三上悠亜',
    ];

    // 用户联系方式参数验证
    private $contact_rules = [
        'qq' => 'required|regex:/^\d{6,14}$/|unique:contacters,qq,',
        'email' => 'required|email|between:0, 50|unique:contacters,email,',
    ];

    private $messages_params = [
        'qq.required' => 'QQ号码必须填写!',
        'qq.regex' => 'QQ号码格式不正确!',
        'qq.unique' => 'QQ号码已被占用！',
        'email.required' => '邮箱必须填写!',
        'email.email' => '邮箱格式不正确!',
        'email.between' => '邮箱格式不正确!!',
        'email.unique' => '邮箱已被占用!',
    ];


    /**
     * index数据结构
     * @return string
     */
    public function avgleInit(){
        $data = $this->publicData();

        return retJson(0, $data);
    }

    /**
     * 页面渲染公共数据
     * @return array
     */
    private function publicData(){
        $data = [];

        $avgle = new avgle();

        // 获取视频类型数据
        $categories = $avgle->getCategories();
        $data['categories'] = $categories;
        $data['actresses_names'] = $this->actresses_names;

        // 获取热点视频数据
        $data['hotVideos'] = $avgle->getVideos(array(
            'o'=>'mv',
            'limit'=>8,
        ));

        return $data;
    }

    /**
     * @return string
     */
    public function archive(){
        $data = $this->publicData();
        $avgle = new avgle();

        $key = isset($_GET['key']) ? $_GET['key'] : urlencode('三上悠亜');
        $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
        $limit = 5;

        $data['searchs'] = $avgle->getVideosBykey($key, $page, $limit);

        //
        return retJson(0, $data);
    }

    public function contact(Request $request){
        // 验证
        $validator = Validator::make($request->all(), $this->contact_rules, $this->messages_params);
        // 异常处理
        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
                $message = $error . ' ';
            }
            // 返回参数异常信息
            return retJson(2, [], $message);
        }
        $contacters = new Contacters();
        // 请求来源站点
        $contacters->source = $_SERVER['HTTP_ORIGIN'];
        $contacters->qq = $request->input('qq');
        $contacters->email = $request->input('email');


        if($contacters->save()){
            return retJson(0, [], '信息提交成功!');
        };

        return retJson(1, [], '数据提交异常,请稍后再试!');
    }



    public function mvDirs(){

        $data = [];

        $response = $this->getAvgle($this->avgle_dirs_url);

        if ($response){
            $data['categories'] = $response['response']['categories'];
            $data['actresses_names'] = $this->actresses;
            return retJson(0, $data);
        }

        return retJson(1, '获取视频失败');
    }


    public function mvs()
    {
        $url = 'https://api.avgle.com/v1/videos/';
        $page = 0;
        $limit = '?limit=2';
        $response = json_decode(file_get_contents($url . $page . $limit), true);
        var_dump($response);
        if ($response['success']) {
            $videos = $response['response']['videos'];
            doSomethingWith($videos);
        }
    }

    private function getAvgle($url, $query = '', $page = 0, $limit = 0){
        if ($query == ''){
            $response = json_decode(file_get_contents($url), true);
        }

        // 返回数据出现异常
        if (!isset($response['success']) || !isset($response['response'])) {
            \Log::info($response);
            return false;
        }

        return $response;

    }
}
