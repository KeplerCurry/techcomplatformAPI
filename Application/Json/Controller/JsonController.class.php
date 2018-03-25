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
        $data = $techdetail -> join('tec_techclassify as b on b.tid = a.tid') ->field('b.tname,a.state,a.isfree,a.tdtitle,a.tdfirsttime,a.tdid')->page($page,4)->select();
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

    //判断技术贴能否加载(付费)，若不能，弹出对话框判断是否购买
    public function doDecide(){

        /*
        **  tdid uid 
        */

        $record = M('record');
        $record_find['rexpendid'] = I('request.uid');
        $record_find['rbid'] = I('request.tdid');
        if( $record -> where($record_find) -> find() )
        {
            $data['success'] = 1;//已付费
            $this->ajaxReturn($data);
        }
        else
        {
            //未付费
            $data['success'] = 0;
            $this->ajaxReturn($data);
        }
    }

    //加载技术贴内容
    public function load_detail_state_0(){
        $tdid = I('request.tdid');
        $techdetail = M('techdetail as a');
        $data = $techdetail -> join('tec_user as b on b.uid = a.tuid') -> join('tec_techclassify as c on c.tid = a.tid')->where("a.tdid = '$tdid'") -> field('b.ualiase,b.ulevel,b.utype,c.tname,a.tdtitle,a.tdcontent,a.tdfirsttime,a.tdaltertime')->find();
        $this->ajaxReturn($data);
    }

    //加载技术贴首次评论内容
    public function load_detail_state_0_firstcommentdata(){
        $tdid = I('request.tdid');
        $comment = M('comment as a');
        $data = $comment -> join('tec_user as b on b.uid = a.reviewer') -> where("a.tdid = '$tdid'") -> field('b.ualiase,a.cid,a.content,a.ctime,a.chit')->select();
        $this->ajaxReturn($data);

    }

    //首次评论技术贴
    public function send_detail_state_0_firstcomment(){
        $data['tdid'] = I('request.tdid');
        $data['reviewer'] = I('request.reviewer');
        $data['content'] = I('request.content');
        $data['chit'] = 0;
        $data['cid'] = "c-".date("YmdHms" , time());
        $data['ctime'] = date("Y:m:d H:m:s" ,time());
        $comment = M('comment');
        if( $comment -> add($data) )
        {
            $success['success'] = 1;
            $success['time'] = $data['ctime'];
            $success['cid'] =  $data['cid'];
            $this->ajaxReturn($success);
        }
        else
        {
            $success['success'] = 0;
            $this->ajaxReturn($success);
        }
    }

    //回复首次评论
    public function send_detail_state_0_commentagain(){
        $data['cid'] = I('request.cid');
        $data['healer'] = I('request.healer');
        $data['content'] = I('request.content');
        $data['catime'] = date("Y:m:d H:m:s" ,time());
        $commentagain = M('commentagain');
        if( $commentagain -> add($data) )
        {
            $success['success'] = 1;
            $success['time'] = $data['catime'];
            $this->ajaxReturn($success);
        }
        else
        {
            $success['success'] = 0;
            $this->ajaxReturn($success);
        }
    }

    //加载对首次评论的回复
    public function load_detail_state_0_commentagaindata(){
        $cid = I('request.cid');
        $commentagain = M('commentagain as a');
        $data = $commentagain -> join('tec_user as b on b.uid = a.healer') -> where("a.cid = '$cid'") -> field('b.ualiase,a.cid,a.content,a.catime') -> select();
        $this->ajaxReturn($data);
    }


    //加载提问帖内容
    public function load_detail_state_1(){
        $tdid = I('request.tdid');
        $techdetail = M('techdetail as a');
        $data = $techdetail -> join('tec_user as b on b.uid = a.tuid') -> join('tec_techclassify as c on c.tid = a.tid')->where("a.tdid = '$tdid'") -> field('b.ualiase,b.ulevel,b.utype,c.tname,a.tdtitle,a.tdcontent,a.tdfirsttime,a.tdaltertime')->find();
        $comment = M('comment');
        $data['commentcount'] = $comment -> where("tdid = '$tdid'") -> count();
        $this->ajaxReturn($data);
    }

    //加载提问帖回答数据
     public function load_detail_state_1_firstAnswerdata(){
        $tdid = I('request.tdid');
        $comment = M('comment as a');
        $data = $comment -> join('tec_user as b on b.uid = a.reviewer') -> where("a.tdid = '$tdid'") -> field('b.ualiase,a.cid,a.content,a.ctime,b.ulevel,b.utype')->select();
        $this->ajaxReturn($data);
    }

    //回答提问帖
    public function send_detail_state_1_firstAnswer(){
        $comment = M('comment');
        $data['tdid'] = I('request.tdid');
        $data['reviewer'] = I('request.reviewer');
        $data['content'] = I('request.content');
        $data['cid'] = "c-".date("YmdHms" ,time());
        $data['ctime'] = date("Y:m:d H:m:s" , time());
        $data['chit'] = 0;
        if( $comment -> add($data) )
        {
            $success['success'] = 1;
            $this->ajaxReturn($success);
        }
        else
        {
            $success['success'] = 0;
            $this->ajaxReturn($success);
        }
    }

    //评论回答内容
    public function send_detail_state_1_commentAnswer(){
        $commentagain = M('commentagain');
        $data['cid'] = I('request.cid');
        $data['healer'] = I('request.healer');
        $data['content'] = I('request.content');
        $data['catime'] = date("Y:m:d H:m:s" , time());
        if( $commentagain -> add($data) )
        {
            $success['success'] = 1;
            $this->ajaxReturn($success);
        }
        else
        {
            $success['success'] = 0;
            $this->ajaxReturn($success);
        }   
    }
}