<?php
namespace libs;
/**
 * Created by PhpStorm.
 * User: phoenix
 * Date: 8/17/17
 * Time: 1:38 PM
 */

use Illuminate\Support\Facades\Log;

class avgle {

    /**
     * 查询视频分类
     */
    public function getCategories(){
        $url = 'https://api.avgle.com/v1/categories';

        $ret = file_get_contents($url);
        $response = json_decode($ret, true);
        if (isset($response['success']) && $response['success']) {
            $categories = $response['response']['categories'];
            return $categories;
        }else{
            Log::info($ret);
            return [];
        }
    }


    public function getVideos($params){

        $url = 'https://api.avgle.com/v1/videos/';
        $query = http_build_query($params);
        $page = 0;
        $ret = file_get_contents($url . $page . '?' . $query);
        $response = json_decode($ret, true);

        if (isset($response['success']) && $response['success']) {
            $categories = $response['response']['videos'];
            return $categories;
        }else{
            Log::info($ret);
            return [];
        }
    }

    public function getVideosBykey($key, $page, $limit){
        $url = 'https://api.avgle.com/v1/search/';
        $ret = file_get_contents($url . urlencode($key). '/' . $page . '?limit=' .$limit);
        $response = json_decode($ret, true);

        if (isset($response['success']) && $response['success']) {
            $categories = $response['response']['videos'];
            return $categories;
        }else{
            Log::info($ret);
            return [];
        }
    }
}