<?php
namespace app\controller;

use app\BaseController;
use app\model\User;
use think\facade\Session;
use think\facade\View;

class Index extends BaseController
{
    /**
     * Created by ngt<ningguoteng.com>.
     * Date: 2020/12/30
     * Time: 1:50 下午
     * 首页框架
     */
    public function index()
    {
        return View::fetch('index');
    }

    /**
     * Created by ngt<ningguoteng.com>.
     * Date: 2021/1/4
     * Time: 3:12 下午
     * 富文本编辑器默认加载的页面找不到什么地方加载的
     *
     */
    public function empty(){

    }

}
