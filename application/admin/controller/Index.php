<?php
namespace app\admin\controller;
use think\Controller;
use think\facade\Session;
use app\admin\controller\BaseAdminController;

class Index extends BaseAdminController
{

    public function __construct()
    {
        parent::__construct ();
        $this->rightVerify(Session::get('admin'), __HOST__."/index.php/admin/login/login");
    }

    public function index()
    {
        
//        var_dump(config('config.view_replace_str'));
        $this->assign(config('config.view_replace_str'));
        return view();
    }
}
