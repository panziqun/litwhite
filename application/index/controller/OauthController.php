<?php
namespace app\index\controller;
use think\Controller;
use kuange\qqconnect\QC;
class OauthController extends Controller
{
    public function qqAction()
    {
        $qc = new QC();
        return redirect($qc->qq_login());
    }
}