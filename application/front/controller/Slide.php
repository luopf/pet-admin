<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/5/19
 * Time: 16:18
 */

namespace app\front\controller;


use think\Controller;

class Slide extends Controller
{

    /**
     * 构造函数
     */
    function __construct() {
        parent::__construct ();

        $this->lib_slide = new SlideModel();

    }

}