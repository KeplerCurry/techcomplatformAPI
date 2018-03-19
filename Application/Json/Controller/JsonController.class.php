<?php
namespace Json\Controller;
use Think\Controller;
class JsonController extends Controller {
    public function index(){
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用Json接口 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
    }

    public function test(){
    	$user = M("user");
    	$c = $user ->select();
    	dump($c);
    }

    //手机号注册
    public function doRegForTelephone(){
    	$user  = M("user");
    	$user_data['utelephone'] = I('request.telephone');
    	if( !$user->where($user_data)->find() )
    	{
    		$user_data['upassword'] = md5( I('request.password'));
    		$user_data['uid'] = date("YmdHis",time());
    		$user_data['ualiase'] = "tch_".date("YmdHis" , time());
    		$user_data['ispassed'] = 0;
    		$user_data['ulevel'] = 1;
    		$user_data['uexp'] = 0;
    		$user_data['utype'] = 0;
    		if( $user->add($user_data) )
    		{
    			$data['success'] = 1;
    			$this->ajaxReturn($data);
    		}
    		else
    		{
    			$data['success'] = 0;
    			$this->ajaxReturn($data);
    		}
    	}
    	else
    	{
    		$data['success'] = 2;
    		$this->ajaxReturn($data);
    	}
    }
    //测试获取IP
    public function getIPTest(){
        $ip = get_client_ip();
        dump($ip);
    }

    //用户登录
    public function doLogin(){
        $user = M('user');
        $login_data['utelephone'] = I('request.telephone');
        $login_data['upassword'] = md5( I('request.password'));
        if( $data = $user->where($login_data)->find() )
        {
            $id['uid'] = $data['uid'];
            $data_return['uid'] = $data['uid'];
            $data_return['ualiase'] = $data['ualiase'];
            $data_return['ispassed'] = $data['ispassed'];
            $data_return['ulevel'] = $data['ulevel'];
            $data_return['uexp'] = $data['uexp'];
            if( NULL != $data['ulogintime'])
            {
                $data_return['ulogintime'] = $data['ulogintime'];
            }
            else
            {
                $data_return['ulogintime'] = "0";
            }
            if( NULL != $data['uloginip'])
            {
                $data_return['uloginip'] = $data['uloginip'];
            }
            else
            {
                $data_return['uloginip'] = "0";
            }
            $data_save['ulogintime'] = date("Y:m:d H:m:s" , time());
            $data_save['uloginip'] = get_client_ip();
            if( $user -> where($id) ->save($data_save) )
            {
                $loginannal = M('loginannal');
                $data_save['uid'] = $id['uid'];
                if( $loginannal ->add($data_save) )
                {
                    $data_return['success'] = 1;
                    $this->ajaxReturn($data_return);
                }
            }
            
        }
        else
        {
            $data['success'] = 0;
            $this->ajaxReturn($data);
        }
    }
}