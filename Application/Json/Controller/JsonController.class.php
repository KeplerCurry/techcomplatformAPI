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
            $success['catime'] = $data['catime'];
            $this->ajaxReturn($success);
        }
        else
        {
            $success['success'] = 0;
            $this->ajaxReturn($success);
        }   
    }

    //查看详细回答
    public function load_detail_state_1_answerData(){
        $cid = I('request.cid');
        $comment = M('comment as a');
        $data = $comment -> join('tec_user as b on b.uid = a.reviewer') -> where("a.cid = '$cid'") -> field('b.ualiase,a.cid,a.content,a.chit,a.ctime')->find();
        
        if( null != $data)
        {
            $commentagain = M('commentagain');
            $data['commentcount'] = $commentagain -> where("cid = '$cid'") -> count();
            $data['success'] = 1;
            $this->ajaxReturn($data);
        }
        else
        {
            $data['success'] = 0;
            $this->ajaxReturn($data);
        }
    }

    //查看评论回答列表
    public function load_detail_state_1_commentAnswer(){
        $select['cid'] = I('request.cid');
        $commentagain = M('commentagain as a');
        $data = $commentagain ->join('tec_user as b on b.uid = a.healer')->field('a.catime,a.content,a.cid,b.ualiase') -> where($select) -> select();
        $this->ajaxReturn($data);
    }

    //获取问题分类列表
    public function get_techclassify_data(){
        $techclassify = M('techclassify');
        $data = $techclassify -> select();
        $this->ajaxReturn($data);
    }

    //发布提问内容
    public function send_ask(){
        $techdetail = M('techdetail');
        $data['tuid'] = I('request.tuid');
        $data['tdtitle'] = I('request.tdtitle');
        $data['tdcontent'] = I('request.tdcontent');
        $data['tid'] = I('request.tid');
        $data['tdid'] = "td-".date("YmdHms" , time());
        $data['tdfirsttime'] = date("Y:m:d H:m:s" , time());
        $data['state'] = 1;
        if( $techdetail -> add($data) )
        {
            $success['success'] = 1;
            $success['tdid'] = $data['tdid'];
            $this->ajaxReturn($success);
        }
        else
        {
            $success['success'] = 0;
            $this->ajaxReturn($success);
        }
    }

    //发布技术贴
    public function send_technology_detail(){
        $techdetail = M('techdetail');
        $data['tuid'] = I('request.tuid');
        $data['tdtitle'] = I('request.tdtitle');
        $data['tdcontent'] = I('request.tdcontent');
        $data['tid'] = I('request.tid');
        $data['tdid'] = "td-".date("YmdHms" , time());
        $data['tdfirsttime'] = date("Y:m:d H:m:s" , time());
        $data['state'] = 1;
        $data['isfree'] = I('request.isfree');
        if( 0 == $data['isfree'])
        {
            $data['price'] = I('request.price');
        }
        if( $techdetail -> add($data) )
        {
            $success['success'] = 1;
            $success['tdid'] = $data['tdid'];
            $this->ajaxReturn($success);
        }
        else
        {
            $success['success'] = 0;
            $this->ajaxReturn($success);
        }
    }

    //申请个人专栏
    public function apply_for_tech_person_zone(){
        $userapplyfor = M('userapplyfor');
        $data['uid'] = I('request.uid');
        $data['tpzname'] = I('request.tpzname');
        $data['tid'] = I('request.tid');
        $data['uafid'] = date("YmdHms" , time());
        $data['createtime'] = date("Y:m:d H:m:s" , time());
        $data['state'] = 0;
        if( $userapplyfor -> add($data))
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

    //发表专栏文章
    public function send_tech_person_zone_detail(){
        $tpzdetail = M('tpzdetail');
        $data['tpzid'] = I('request.tpzid');
        $data['tpzdtitle'] = I('request.tpzdtitle');
        $data['tpzdcontent'] = I('request.tpzdcontent');
        $data['isfree'] = I('request.isfree');
        if( 0 == isfree )
        {
            $data['price'] = I('request.price');
        }
        $data['tpzdid'] = "tpzd-".date("YmdHms" , time());
        $data['tpzdfirsttime'] = date("Y:m:d H:m:s" , time());
        if( $tpzdetail -> add($data) )
        {
            $success['tpzdid'] = $data['tpzdid'];
            $success['success'] = 1;
            $this->ajaxReturn($success);
        }
        else
        {
            $success['success'] = 0;
            $this->ajaxReturn($success);
        }
    }

    //获取专栏列表
    public function load_tech_person_zone_list(){
        $techpersonzone = M('techpersonzone as a');
        $data = $techpersonzone -> join('tec_user as b on b.uid = a.uid') -> join('tec_techclassify as c on c.tid = a.tid') -> field('c.tname,b.ualiase,a.tpzid,a.tpzname') ->select();
        $this->ajaxReturn($data);
    }

    //获取专栏文章列表
    public function load_tech_person_zone_detail_list_by_tpzid(){
        $tpzdetail = M('tpzdetail');
        $select['tpzid'] = I('request.tpzid');
        $data = $tpzdetail -> where($select) -> field('tpzdid,tpzdtitle,isfree,price,tpzdfirsttime') -> select();
        $this->ajaxReturn($data);
    }

    //获取查看个人专栏页面用户信息
    public function load_tech_person_zone_userinfo(){
        $techpersonzone = M('techpersonzone as a');
        $tpzid = I('request.tpzid');
        $data = $techpersonzone -> join('tec_user as b on b.uid = a.uid') -> join('tec_techclassify as c on c.tid = a.tid') -> where("a.tpzid ='$tpzid'") -> field('c.tname,b.ualiase,a.tpzid,a.tpzname') -> find();
        //加关注 未实现
        $tpzdetail = M('tpzdetail');
        $data['listcount'] = $tpzdetail -> where("tpzid = '$tpzid'") -> count();
        $this->ajaxReturn($data);
    }

    //查看专栏详细文章
    public function load_tech_person_zone_detail_data(){
        $tpzdetail = M('tpzdetail as a');
        $tpzdid = I('request.tpzdid');
        $data = $tpzdetail -> join('tec_techpersonzone as b on b.tpzid = a.tpzid') -> join('tec_user as c on c.uid = b.uid') -> where("a.tpzdid = '$tpzdid'") -> field('a.tpzdtitle,a.tpzdcontent,a.tpzdfirsttime,b.tpzname,c.ualiase') -> find();
        $this->ajaxReturn($data);
    }

    //加载专栏文章首次评论内容
    public function load_tech_person_zone_detail_firstcommentdata(){
        $tpzdid = I('request.tpzdid');
        $tpzcomment = M('tpzcomment as a');
        $data = $tpzcomment -> join('tec_user as b on b.uid = a.reviewer') -> where("a.tpzdid = '$tpzdid'") -> field('b.ualiase,a.tpzcid,a.content,a.tpzctime')->select();
        $this->ajaxReturn($data);

    }

    //首次评论专栏文章
    public function send_tech_person_zone_detail_firstcomment(){
        $data['tpzdid'] = I('request.tpzdid');
        $data['reviewer'] = I('request.reviewer');
        $data['content'] = I('request.content');
        $data['tpzcid'] = "c-".date("YmdHms" , time());
        $data['tpzctime'] = date("Y:m:d H:m:s" ,time());
        $tpzcomment = M('tpzcomment');
        if( $tpzcomment -> add($data) )
        {
            $success['success'] = 1;
            $success['time'] = $data['tpzctime'];
            $success['tpzcid'] =  $data['tpzcid'];
            $this->ajaxReturn($success);
        }
        else
        {
            $success['success'] = 0;
            $this->ajaxReturn($success);
        }
    }

    //回复首次评论专栏文章
    public function send_tech_person_zone_detail_commentagain(){
        $data['tpzcid'] = I('request.tpzcid');
        $data['healer'] = I('request.healer');
        $data['content'] = I('request.content');
        $data['tpzcatime'] = date("Y:m:d H:m:s" ,time());
        $tpzcommentagain = M('tpzcommentagain');
        if( $tpzcommentagain -> add($data) )
        {
            $success['success'] = 1;
            $success['time'] = $data['tpzcatime'];
            $this->ajaxReturn($success);
        }
        else
        {
            $success['success'] = 0;
            $this->ajaxReturn($success);
        }
    }

    //加载专栏文章对首次评论的回复
    public function load_tech_person_zone_detail_commentagaindata(){
        $tpzcid = I('request.tpzcid');
        $tpzcommentagain = M('tpzcommentagain as a');
        $data = $tpzcommentagain -> join('tec_user as b on b.uid = a.healer') -> where("a.tpzcid = '$tpzcid'") -> field('b.ualiase,a.tpzcid,a.content,a.tpzcatime') -> select();
        $this->ajaxReturn($data);
    }

    /*
     ******
     *个人*
     ******
     */

    //查看个人信息
    //state = 0 -> 查看自己信息 state = 1 ->查看他人信息
    public function load_user_information(){
        $state = I('request.state');
        if( 0 == $state )
        {

        }
        else
        {
            
        }
    }

    /*
    *查看收藏、赞、历史记录等内容
    *state为标识
    * 11->关注用户 12->关注问题 13->关注专栏
    * 21->帖子点赞 22->回答点赞 23->专栏帖子点赞
    * 31->帖子收藏 32->专栏帖子收藏
    * 41->最近浏览(先不加)
    */

    public function load_attention_by_state(){
        $state = I('request.state');
        //测试代码
        //$state = intval($state);
        $uid = I('request.uid');
        $attention = M('attention as a');
        switch ($state) {
            case 11:
               $data = $attention -> join('tec_user as b on b.uid = a.id') -> where("a.auid = '$uid' and a.state = '$state'") -> field('a.id,b.ualiase') -> select();
                break;
            case 12:
                $data = $attention -> join('tec_techdetail as b on b.tdid = a.id') -> where("a.auid = '$uid' and a.state = '$state'") -> field('a.id,b.tdtitle') -> select();
                break;
            case 13:
                $data = $attention -> join('tec_techpersonzone as b on b.tpzid = a.id') -> where("a.auid = '$uid' and a.state ='$state'") -> field('a.id,b.tpzname') -> select();
                break;
            case 21:
                $data = $attention -> join('tec_techdetail as b on b.tdid = a.id') -> join('tec_techclassify as c on c.tid = b.tid') -> where("a.auid = '$uid' and a.state ='$state'") -> field('a.id,b.tdtitle,c.tname') -> select();
                break;
            case 22:
                $data = $attention -> join('tec_comment as b on b.cid = a.id') -> join('tec_techdetail as c on c.tdid = b.tdid') -> where("a.auid = '$uid' and a.state ='$state'") -> field('a.id,b.content,c.tdtitle')-> select();
                break;
            case 23:
                $data = $attention -> join('tec_tpzdetail as b on b.tpzdid = a.id') -> join('tec_techpersonzone as c on c.tpzid = b.tpzid') -> where("a.auid = '$uid' and a.state ='$state'") ->field('a.id,b.tpzdtitle,c.tpzname') -> select();
                break;
            case 31:
                $data = $attention -> join('tec_techdetail as b on b.tdid = a.id') -> where("a.auid = '$uid' and a.state ='$state'") -> field('a.id,b.tdtitle') -> select();
                break;
            case 32:
                $data = $attention -> join('tec_tpzdetail as b on b.tpzdid = a.id') -> where("a.auid = '$uid' and a.state ='$state'") ->field('a.id,b.tpzdtitle') -> select();
                break;
            case 41:
                
                break; 
        }
        $this->ajaxReturn($data);
    }

    //修改密码
    public function alter_user_password(){
        $select['upassword'] = md5(I('request.oldpassword'));
        $select['uid'] = I('request.uid');
        $uid = $select['uid'];
        $user = M('user');
        if( !$user -> where($select) -> find() )
        {
            //原密码错误
            $success['success'] = 2;
            $this->ajaxReturn($success);
        }
        else
        {
            $data['upassword'] = md5(I('request.newpassword'));
            if( $user -> where("uid = '$uid'") -> save($data))
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

    //查看申请
    public function load_apply_for(){
        $select['uid'] = I('request.uid');
        $userapplyfor = M('userapplyfor');
        $data = $userapplyfor -> where($select) -> select();
        $this->ajaxReturn($data);
    }


}