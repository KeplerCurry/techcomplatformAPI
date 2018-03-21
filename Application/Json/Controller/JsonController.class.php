<?php
namespace Json\Controller;
use Think\Controller;
class JsonController extends Controller {

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
                    //echo json_encode($data_return,JSON_UNESCAPED_UNICODE);
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
    //app热门内容
    public function hotData(){
        $page = I('request.page');
        $techdetail = M('techdetail as a');
        $data = $techdetail -> join('tec_techclassify as b on b.tid = a.tid') ->field('b.tname,a.state,a.isfree,a.tdtitle,a.tdfirsttime')->page($page,4)->select();
        $this->ajaxReturn($data);
    }

    public function addTestData(){
        $i = 0;
        $techdetail = M("techdetail");
        while( $i < 30 )
        {
            $data['tdid'] = "td-".$i;
            $data['tid'] = "t-124";
            $data['tuid'] = "20180319163431";
            $data['tdtitle'] = "code".$i;
            $data['tdcontent'] = "abcdedghijklnopwqrsturvjahjshkjqwhekjqwhuiyfiasgfiewrtfgiewtrf";
            $data['tdfirsttime'] = date("Y:m:d H:m:s" , time());
            $data['isfree'] = "1";
            $data['state'] = "0";
            $techdetail -> add($data);
            $i++;
        }
        
    }
}