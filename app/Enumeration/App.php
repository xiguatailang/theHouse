<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/7/2
 * Time: 下午12:34
 */

namespace App\Enumeration;


class App
{
    const HTTP_GET_METHOD = 'GET';
    const HTTP_POST_METHOD = 'POST';
    const HTTP_PUT_METHOD = 'PUT';
    const HTTP_DELETE_METHOD = 'DELETE';

    const THE_NUM_ONE = 10000000000;


    //业务处理返回状态码
    const BUSINESS_EXCEPTION_CODE = 900;
    const BUSINESS_SUCCESS_CODE = 200;


    const USER_LOGIN_KEY = 'login';
    const LOGIN_SYNC_MAKE_USER_CACHE = 'login_sync_make_user_cache';
    const USER_LOGIN_EXPIRE_TIME = 3600;

    const LOGIN_POOL = 'house_login_pool';
    const USER_NAME_POOL = 'user_name_pool';



}