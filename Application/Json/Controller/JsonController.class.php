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
            $uid = $data['uid'];
            $data_return['uid'] = $data['uid'];
            $data_return['ualiase'] = $data['ualiase'];
            $data_return['ispassed'] = $data['ispassed'];
            $data_return['ulevel'] = $data['ulevel'];
            $data_return['uexp'] = $data['uexp'];
            $data_return['uphoto'] = $data['uphoto'];
            $data_return['usex'] = $data['usex'];
            $data_return['uspecialline'] = $data['uspecialline'];
            $data_return['utype'] = $data['utype'];
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
            $data_save['ulogintime'] = date("Y-m-d H:i:s" , time());
            $data_save['uloginip'] = get_client_ip();
            $userapplyfor = M('userapplyfor');
            $techpersonzone = M('techpersonzone');
            if( $userapplyfor -> where("uid = '$uid' and flag = 0")-> find())
            {
                if( $data1 = $techpersonzone -> where("uid = '$uid'") -> find())
                {
                    $data_return['applyTPZState'] = 2;
                    $data_return['tpzid'] = $data1['tpzid'];
                }
                else
                {
                    $data_return['applyTPZState'] = 1;
                    $data_return['tpzid'] = null;
                }
            }
            else
            {
                $data_return['applyTPZState'] = 0;
                $data_return['tpzid'] = null;
            }
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

    //上传身份证图片
    function uploadIDCard(){
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 10485760; // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $upload->rootPath = './Public/realnamepic/'; // 设置附件上传根目录
        $upload->autoSub = false; //关闭子目录，默认为ture
        $info = $upload->upload(); // 上传文件
        if (!$info) { // 上传错误提示错误信息
            $this->error($upload->getError());
        } else { // 上传成功
            // $this->success('上传成功！');
            return $info;
        }
    }

    //申请实名认证
    public function apply_for_real_name(){
        $userapplyfor = M('userapplyfor');
        $data['uid'] = I('request.uid');
        $data['uafid'] = "uaf-".date("YmdHis" , time());
        $data['createtime'] = date("Y-m-d H:i:s" , time());
        $data['state'] = 0;
        $data['flag'] = 1;
        if ( 0 == $_FILES['pic']['error']) 
        {
            $info=$this->uploadIDCard();
            $data['pic'] = $info['pic']['savename'];
        }
        else
        {
            $data['pic'] = 'default.jpg';
        }
        if( $userapplyfor -> add($data))
        {
            $user = M('user');
            $uid = $data['uid'];
            $userdata['ispassed'] = 1;
            if( $user -> where("uid = '$uid'") ->save($userdata))
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
        else
        {
            $success['success'] = 0;
            $this->ajaxReturn($success);
        }
    }


    //上传头像
    function uploadUser(){
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 10485760; // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $upload->rootPath = './Public/userphoto/'; // 设置附件上传根目录
        $upload->autoSub = false; //关闭子目录，默认为ture
        $info = $upload->upload(); // 上传文件
        if (!$info) { // 上传错误提示错误信息
            $this->error($upload->getError());
        } else { // 上传成功
            // $this->success('上传成功！');
            return $info;
        }
    }

    //修改用户信息-完善个人信息-修改头像
    public function editUserInfoByPic(){
        $user = M('user');
        $uid['uid'] = I('request.uid');
        if( NULL != I('request.ualiase'))
        {
            $data['ualiase'] = I('request.ualiase');
        }
        if( NULL != I('request.usex'))
        {
            $data['usex'] = I('request.usex');
        }
        if( NULL != I('request.uspecialline'))
        {
            $data['uspecialline'] = I('request.uspecialline');
        }
        if( NULL != $_FILES['uphoto'])
        {
            if( 0 == $_FILES['uphoto']['error']) 
            {
                $info=$this->uploadUser();
                $data['uphoto'] = $info['uphoto']['savename'];
            }
            else
            {
                $data['uphoto'] = 'default.jpg';
            } 
        }
        
        if( $user -> where($uid) -> save($data) )
        {
            $success['success'] = 1;
            $success['usex'] = $data['usex'];
            $success['ualiase'] = $data['ualiase'];
            $success['uspecialline'] = $data['uspecialline'];
            $success['uphoto'] = $data['uphoto'];
            $this->ajaxReturn($success);
        }
        else
        {
            $success['success'] = 0;
            $this->ajaxReturn($success);
        }

    }

    //修改用户信息-完善个人信息-不修改头像
    public function editUserInfoNoPic(){
        $user = M('user');
        $uid['uid'] = I('request.uid');
        if( NULL != I('request.ualiase'))
        {
            $data['ualiase'] = I('request.ualiase');
        }
        if( NULL != I('request.usex'))
        {
            $data['usex'] = I('request.usex');
        }
        if( NULL != I('request.uspecialline'))
        {
            $data['uspecialline'] = I('request.uspecialline');
        }       
        if( $user -> where($uid) -> save($data) )
        {
            $success['success'] = 1;
            $success['usex'] = $data['usex'];
            $success['ualiase'] = $data['ualiase'];
            $success['uspecialline'] = $data['uspecialline'];
            $this->ajaxReturn($success);
        }
        else
        {
            $success['success'] = 0;
            $this->ajaxReturn($success);
        }

    }

    //app热门内容
    public function hotData(){
        $page = I('request.page');
        $techdetail = M('techdetail as a');
        $data = $techdetail -> join('tec_techclassify as b on b.tid = a.tid') -> where('a.answer >= 10 and a.attention >= 10 or a.collect >= 10 and a.like >= 10')->field('b.tname,a.state,a.isfree,a.tdtitle,a.tdfirsttime,a.tdid,a.tdcontent,a.attention,a.answer,a.like,a.collect')-> order('a.tdfirsttime DESC')-> page($page,5)->select();
        $this->ajaxReturn($data);
    }

    //技术贴/问题 数据获取
    public function techDetailOrQuestonByTid(){
        $page = I('request.page');
        $tid = I('request.tid');
        $state = I('request.state');
        $techdetail = M('techdetail');
        $data = $techdetail -> where("state = $state and tid = '$tid'") ->page($page,5) -> select();
        $this->ajaxReturn($data);
    }
    //app推荐内容
    public function commentData(){
        $page = I('request.page');
        $techdetail = M('techdetail as a');
        $data = $techdetail -> join('tec_techclassify as b on b.tid = a.tid') -> field('b.tname,a.state,a.isfree,a.tdtitle,a.tdfirsttime,a.tdid,a.tdcontent,a.attention,a.answer,a.like,a.collect')-> order('a.tdfirsttime DESC')-> page($page,5)->select();
        $this->ajaxReturn($data);
    }   

    //app关注内容
    public function attentionData(){
        $uid = I('request.uid');
        $attention = M('attention as a');
        $data['user'] = $attention -> join('tec_user as b on b.uid = a.id') -> where("a.auid = '$uid' and a.state = 11") -> field('b.uid,b.ualiase,b.ulevel,b.utype,b.uphoto') -> select();
        $data['detail'] = $attention -> join('tec_techdetail as b on b.tdid = a.id') -> where("a.auid = '$uid' and a.state = 12") -> field('b.tdid,b.tdtitle,b.attention,b.answer') -> select();
        $data['techpersonzone'] = $attention ->join('tec_techpersonzone as b on b.tpzid = a.id') -> join('tec_user as c on c.uid = b.uid') -> where("a.auid = '$uid' and a.state = 13") -> field('b.tpzname,b.tpzid,c.ualiase,c.uphoto')->select();
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
            $data['tdfirsttime'] = date("Y-m-d H:i:s" , time());
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
        $data = $techdetail -> join('tec_user as b on b.uid = a.tuid') -> join('tec_techclassify as c on c.tid = a.tid')->where("a.tdid = '$tdid'") -> field('b.ualiase,b.uphoto,b.uid,b.ulevel,b.utype,c.tname,a.tdtitle,a.tdcontent,a.tdfirsttime,a.tdaltertime,a.like,b.uspecialline')->find();
        $this->ajaxReturn($data);
    }

    //获取用户是否对技术贴进行关注用户、点赞、收藏操作
    public function getUserA_L_C(){
        $attention = M('attention');
        $auid = I('request.auid');
        $uid = I('request.uid');
        $tdid = I('request.tdid');
        if( $attention -> where("auid = '$auid' and id = '$uid' and state = 11") -> find())
        {
            $data['userflag'] = 1;
        }
        else
        {
            $data['userflag'] = 0;
        }
        if( $attention -> where("auid = '$auid' and id = '$tdid' and state = 21") -> find())
        {
            $data['likeflag'] = 1;
        }
        else
        {
            $data['likeflag'] = 0;
        }
        if( $attention -> where("auid = '$auid' and id = '$tdid' and state = 31") -> find())
        {
            $data['collectflag'] = 1;
        }
        else
        {
            $data['collectflag'] = 0;
        }
        $this->ajaxReturn($data);
    }

    //获取用户是否对专栏贴进行关注用户、点赞、收藏操作
    public function getUserA_L_C_TPZ(){
        $attention = M('attention');
        $auid = I('request.auid');
        $uid = I('request.uid');
        $tpzdid = I('request.tpzdid');
        if( $attention -> where("auid = '$auid' and id = '$uid' and state = 11") -> find())
        {
            $data['userflag'] = 1;
        }
        else
        {
            $data['userflag'] = 0;
        }
        if( $attention -> where("auid = '$auid' and id = '$tpzdid' and state = 23") -> find())
        {
            $data['likeflag'] = 1;
        }
        else
        {
            $data['likeflag'] = 0;
        }
        if( $attention -> where("auid = '$auid' and id = '$tpzdid' and state = 32") -> find())
        {
            $data['collectflag'] = 1;
        }
        else
        {
            $data['collectflag'] = 0;
        }
        $this->ajaxReturn($data);
    }

    //加载技术贴首次评论内容
    public function load_detail_state_0_firstcommentdata(){
        $tdid = I('request.tdid');
        $comment = M('comment as a');
        $data = $comment -> join('tec_user as b on b.uid = a.reviewer') -> where("a.tdid = '$tdid'") -> field('b.ualiase,a.cid,a.content,a.ctime,a.chit,b.uphoto')->select();
        $this->ajaxReturn($data);

    }

    //首次评论技术贴
    public function send_detail_state_0_firstcomment(){
        $data['tdid'] = I('request.tdid');
        $data['reviewer'] = I('request.reviewer');
        $data['content'] = I('request.content');
        $data['chit'] = 0;
        $data['cid'] = "c-".date("YmdHis" , time());
        $data['ctime'] = date("Y-m-d H:i:s" ,time());
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
        $data['catime'] = date("Y-m-d H:i:s" ,time());
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
        $data = $comment -> join('tec_user as b on b.uid = a.reviewer') -> where("a.tdid = '$tdid'") -> field('b.uphoto,b.ualiase,a.cid,a.content,a.ctime,b.ulevel,b.utype')->select();
        $this->ajaxReturn($data);
    }

    //判断用户是否关注该问题
    public function getUserAttentionQuestion(){
        $attention = M('attention');
        $auid = I('request.uid');
        $id = I('request.id');
        if($attention -> where("auid = '$auid' and id = '$id' and state = 12") -> find())
        {
            $success['success'] = 1;
        }
        else
        {
            $success['success'] = 0;
        }
        $this->ajaxReturn($success);
    }

    //回答提问帖
    public function send_detail_state_1_firstAnswer(){
        $comment = M('comment');
        $data['tdid'] = I('request.tdid');
        $data['reviewer'] = I('request.reviewer');
        $data['content'] = I('request.content');
        $data['cid'] = "c-".date("YmdHis" ,time());
        $data['ctime'] = date("Y-m-d H:i:s" , time());
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
        $data['catime'] = date("Y-m-d H:i:s" , time());
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
        $data = $comment -> join('tec_user as b on b.uid = a.reviewer') -> where("a.cid = '$cid'") -> field('b.uid,b.uphoto,b.ualiase,a.cid,a.content,a.chit,a.ctime,b.uspecialline')->find();
        
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

    //获取用户是否对回答进行关注用户、点赞操作
    public function getUserForTheAnswer(){
        $attention = M('attention');
        $auid = I('request.auid');
        $uid = I('request.uid');
        $cid = I('request.cid');
        if( $attention -> where("auid = '$auid' and id = '$uid' and state = 11") -> find())
        {
            $data['userflag'] = 1;
        }
        else
        {
            $data['userflag'] = 0;
        }
        if( $attention -> where("auid = '$auid' and id = '$cid' and state = 22") -> find())
        {
            $data['likeflag'] = 1;
        }
        else
        {
            $data['likeflag'] = 0;
        }
        $this->ajaxReturn($data);
    }

    //查看评论回答列表
    public function load_detail_state_1_commentAnswer(){
        $select['cid'] = I('request.cid');
        $commentagain = M('commentagain as a');
        $data = $commentagain ->join('tec_user as b on b.uid = a.healer')->field('a.catime,a.content,a.cid,b.ualiase,b.uphoto') -> where($select) -> select();
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
        $data['tdid'] = "td-".date("YmdHis" , time());
        $data['tdfirsttime'] = date("Y-m-d H:i:s" , time());
        $data['state'] = 1;
        $data['isfree'] = intval(I('request.isfree'));
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

    //发布技术贴
    public function send_technology_detail(){
        $techdetail = M('techdetail');
        $data['tuid'] = I('request.tuid');
        $data['tdtitle'] = I('request.tdtitle');
        $data['tdcontent'] = I('request.tdcontent');
        $data['tid'] = I('request.tid');
        $data['tdid'] = "td-".date("YmdHis" , time());
        $data['tdfirsttime'] = date("Y-m-d H:i:s" , time());
        $data['state'] = 0;
        $data['isfree'] = intval(I('request.isfree'));
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
        $data['uafid'] = "uaf-".date("YmdHis" , time());
        $data['createtime'] = date("Y-m-d H:i:s" , time());
        $data['state'] = 0;
        $data['flag'] = 0;
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
        $data['isfree'] = intval(I('request.isfree')); 
        if( 0 == isfree )
        {
            $data['price'] = I('request.price');
        }
        $data['tpzdid'] = "tpzd-".date("YmdHis" , time());
        $data['tpzdfirsttime'] = date("Y-m-d H:i:s" , time());
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
        $data = $techpersonzone -> join('tec_user as b on b.uid = a.uid')-> join('tec_techclassify as c on c.tid = a.tid') -> field('c.tname,b.ualiase,a.tpzid,a.tpzname,b.uphoto') ->select();
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
        $data = $techpersonzone -> join('tec_user as b on b.uid = a.uid') -> join('tec_techclassify as c on c.tid = a.tid') -> where("a.tpzid ='$tpzid'") -> field('c.tname,b.ualiase,a.tpzid,a.tpzname,b.uphoto') -> find();
        //加关注 未实现
        $tpzdetail = M('tpzdetail');
        $data['listcount'] = $tpzdetail -> where("tpzid = '$tpzid'") -> count();
        $this->ajaxReturn($data);
    }

    //判断用户是否关注个人专栏
    public function getUserAttentionTPZ(){
        $attention = M('attention');
        $auid = I('request.uid');
        $id = I('request.id');
        if($attention -> where("auid = '$auid' and id = '$id' and state = 13") -> find())
        {
            $success['success'] = 1;
        }
        else
        {
            $success['success'] = 0;
        }
        $this->ajaxReturn($success);
    }

    //查看专栏详细文章
    public function load_tech_person_zone_detail_data(){
        $tpzdetail = M('tpzdetail as a');
        $tpzdid = I('request.tpzdid');
        $data = $tpzdetail -> join('tec_techpersonzone as b on b.tpzid = a.tpzid') -> join('tec_user as c on c.uid = b.uid') -> where("a.tpzdid = '$tpzdid'") -> field('a.tpzdtitle,a.tpzdcontent,a.tpzdfirsttime,a.like,b.tpzname,c.ualiase,c.uphoto,c.uid.c.uspecialline') -> find();
        $this->ajaxReturn($data);
    }

    //加载专栏文章首次评论内容
    public function load_tech_person_zone_detail_firstcommentdata(){
        $tpzdid = I('request.tpzdid');
        $tpzcomment = M('tpzcomment as a');
        $data = $tpzcomment -> join('tec_user as b on b.uid = a.reviewer') -> where("a.tpzdid = '$tpzdid'") -> field('b.ualiase,a.tpzcid,a.content,a.tpzctime,b.uphoto')->select();
        $this->ajaxReturn($data);

    }

    //首次评论专栏文章
    public function send_tech_person_zone_detail_firstcomment(){
        $data['tpzdid'] = I('request.tpzdid');
        $data['reviewer'] = I('request.reviewer');
        $data['content'] = I('request.content');
        $data['tpzcid'] = "c-".date("YmdHis" , time());
        $data['tpzctime'] = date("Y-m-d H:i:s" ,time());
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
        $data['tpzcatime'] = date("Y-m-d H:i:s" ,time());
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
        $uid = I('request.uid');
        $userapplyfor1 = M('userapplyfor as a');
        $data1 = $userapplyfor1 -> join('tec_techpersonzone as b on b.tpzname = a.tpzname')-> where("a.uid = '$uid' and a.flag = 0") -> field('b.tpzid,a.flag,a.createtime,a.state')-> select();
        $userapplyfor2 = M('userapplyfor');
        $data2 = $userapplyfor2 -> where("uid = '$uid' and flag = 1") ->select();
        $data = array_merge($data1,$data2);
        $this->ajaxReturn($data);
    }

    //查看购买记录
    public function load_user_buyed(){
        $rexpendid = I('request.uid');
        $record = M('record as a');
        $data1 = $record ->  join('tec_tpzdetail as c on c.tpzdid = a.rbid') -> where("a.rexpendid = '$rexpendid'") -> field('c.tpzdtitle,c.price,a.rbid,a.rtime') ->select();

        $data2  = $record -> join('tec_techdetail as b on b.tdid = a.rbid') -> where("a.rexpendid = '$rexpendid'") -> field('b.tdtitle,b.price,a.rbid,a.rtime') ->select();

        $data = array_merge($data1,$data2);
        $this->ajaxReturn($data);
        //
    }

    //查看用户信息
    public function load_user_info(){
        $uid = I('request.uid');
        $user = M('user');
        $data = $user -> where("uid = '$uid'") -> field('ualiase,ulevel,utype,uphoto,uspecialline') -> find();
        $attention = M('attention');
        $data['attention_user'] = $attention -> where("id = '$uid' and state = 11") ->count();
        $data['user_attention'] = $attention -> where(" auid = '$uid' and state = 11") -> count();
        $this->ajaxReturn($data);
    }

    //判断用户在查看其他用户页面中是否关注该用户
    public function getUserAttentionUser(){
        $attention = M('attention');
        $auid = I('request.uid');
        $id = I('request.id');
        if($attention -> where("auid = '$auid' and id = '$id' and state = 11") -> find())
        {
            $success['success'] = 1;
        }
        else
        {
            $success['success'] = 0;
        }
        $this->ajaxReturn($success);
    }

    //获取用户个人发布的帖子、回答等列表
    public function load_user_send(){
        $state = I('request.state');
        $uid = I('request.uid');
        switch ($state) {
            case 1:
                $techdetail = M('techdetail as a');
                $data = $techdetail -> join('tec_techclassify as b on b.tid = a.tid') ->where("a.tuid = '$uid'") ->field('a.tdid,a.tdtitle,b.tname') -> select();
                break;
            case 2:
                $comment = M('comment as a');
                $data = $comment -> join('tec_techdetail as b on b.tdid = a.tdid') -> where("a.reviewer = '$uid' and state = 1") -> field('a.cid,a.content,b.tdtitle') -> select();
                break;
            case 3:
                $tpzdetail = M('tpzdetail as a');
                $data = $tpzdetail -> join('tec_techpersonzone as b on b.tpzid = a.tpzid') -> where("b.uid = '$uid'") -> field('a.tpzdtitle,b.tpzname,a.tpzdid') -> select();
                break;
        }
        $this->ajaxReturn($data);
    }

    /*
     *
     *留言
     *
     */

    //发送留言
    public function send_message(){
        $message = M('message');
        $data['receiveid'] = I('request.userid');
        $data['sendid'] = I('request.mineid');
        $data['text'] = I('request.text');
        $data['isread'] = 0;
        $data['createtime'] = date("Y-m-d H:i:s" , time());
        $data['mid'] = "m-".date("YmdHis" ,time());
        if( $message -> add($data))
        {
            $success['success'] = 1;
            $success['createtime'] = $data['createtime'];
            $this->ajaxReturn($success);
        }
        else
        {
            $success['success'] = 0;
            $this->ajaxReturn($success);
        }
    }

    //查看留言列表、信息等
    public function load_message_list(){
        $message = M('message as a');
        $mineid = I('request.mineid');
        $data['count'] = $message -> where("receiveid = '$mineid' and isread = 0") -> count();
        $data['list'] = $message -> join('tec_user as b on b.uid = sendid') -> where("a.receiveid = '$mineid'") -> distinct('a.sendid') -> field('a.sendid,b.ualiase,b.uphoto') -> select();

        $this->ajaxReturn($data);
    }

    //通过指定uid查看留言
    public function load_message_by_uid(){
        $mineid = I('request.mineid');
        $userid = I('request.userid');
        $message = M('message as a');
        $state['isread'] = 1;
        $message -> where("receiveid = '$mineid' and sendid = '$userid'") -> save($state);
        $data = $message -> join('tec_user as b on b.uid = a.sendid') -> where("a.receiveid = '$mineid' and a.sendid = '$userid' or a.receiveid = '$userid' and a.sendid = '$mineid'") -> order('a.createtime ASC') -> field('a.mid,a.receiveid,a.sendid,a.text,a.createtime,a.isread,b.ualiase,b.uphoto') -> select();
        $this->ajaxReturn($data);
    }

    //点赞、收藏、关注(添加、取消)通用接口
    public function common_l_c_a(){
        $attention = M('attention');
        $flag = I('request.flag');
        $data['state'] = I('request.state');
        $state = $data['state'];
        $data['id'] = I('request.id');
        $id = $data['id'];
        $data['auid'] = I('request.uid');
        if( 0 == $flag )
        {
            $data['aid'] = "a-".date('YmdHis',time());
            if($attention -> add($data))
            {
                if( 12 == $state )
                {
                    $data1['attention'] = intval($attention->where("id = '$id' and state = '12'")->count());
                    $techdetail = M('techdetail');
                    if( $techdetail -> where("tdid = '$id'") -> save($data1))
                    {
                        $success['success'] = 1;
                    }
                    else
                    {
                        $success['success'] = 0;
                    }
                }
                else if( 21 == $state )
                {
                    $data1['like'] = intval($attention->where("id = '$id' and state = '21'")->count());
                    $techdetail = M('techdetail');
                    if( $techdetail -> where("tdid = '$id'") -> save($data1))
                    {
                        $success['success'] = 1;
                    }
                    else
                    {
                        $success['success'] = 0;
                    }
                }
                else if( 22 == $state )
                {
                    $data1['chit'] = intval($attention->where("id = '$id' and state = '22'")->count());
                    $comment = M('comment');
                    if( $comment -> where("cid = '$id'") -> save($data1))
                    {
                        $success['success'] = 1;
                    }
                    else
                    {
                        $success['success'] = 0;
                    }
                }
                else if( 31 == $state)
                {
                    $data1['collect'] = intval($attention->where("id = '$id' and state = '31'")->count());
                    $techdetail = M('techdetail');
                    if( $techdetail -> where("tdid = '$id'") -> save($data1))
                    {
                        $success['success'] = 1;
                    }
                    else
                    {
                        $success['success'] = 0;
                    }
                }
                else
                {
                    $success['success'] = 1;
                }
                
            }
            else
            {
                $success['success'] = 0;
            }
        }
        else
        {
            if( $attention -> where($data) -> find())
            {
                if($attention->where($data)->delete())
                {
                    if( 12 == $state )
                    {
                        $data1['attention'] = intval($attention->where("id = '$id' and state = '12'")->count());
                        $techdetail = M('techdetail');
                        if( $techdetail -> where("tdid = '$id'") -> save($data1))
                        {
                            $success['success'] = 1;
                        }
                        else
                        {
                            $success['success'] = 0;
                        }
                    }
                    else if( 21 == $state )
                    {
                        $data1['like'] = intval($attention->where("id = '$id' and state = '21'")->count());
                        $techdetail = M('techdetail');
                        if( $techdetail -> where("tdid = '$id'") -> save($data1))
                        {
                            $success['success'] = 1;
                        }
                        else
                        {
                            $success['success'] = 0;
                        }
                    }
                    else if( 22 == $state )
                    {
                        $data1['chit'] = intval($attention->where("id = '$id' and state = '22'")->count());
                        $comment = M('comment');
                        if( $comment -> where("cid = '$id'") -> save($data1))
                        {
                            $success['success'] = 1;
                        }
                        else
                        {
                            $success['success'] = 0;
                        }
                    }
                    else if( 31 == $state)
                    {
                        $data1['collect'] = intval($attention->where("id = '$id' and state = '31'")->count());
                        $techdetail = M('techdetail');
                        if( $techdetail -> where("tdid = '$id'") -> save($data1))
                        {
                            $success['success'] = 1;
                        }
                        else
                        {
                            $success['success'] = 0;
                        }
                    }
                    else
                    {
                        $success['success'] = 1;
                    }
                }
                else
                {
                    $success['success'] = 0;
                }
            }
            else
            {
                $success['success'] = 0;
            }     
        }
        $this->ajaxReturn($success);
    }

    //获取用户已发布的帖子、问题、专栏贴列表
    //state为标识 0->帖子 1->问题 2->回答 3->专栏贴
    public function getUserSendList(){
        $state = I('request.state');
        $uid = I('request.uid');
        switch ($state) {
            case 0:
                $techdetail = M('techdetail as a');
                $data = $techdetail -> join('tec_techclassify as b on b.tid = a.tid')->where("a.tuid = '$uid' and a.state = 0")-> field('a.tdid,a.tdtitle,b.tname')->select();
                break;
            case 1:
                $techdetail = M('techdetail as a');
                $data = $techdetail -> join('tec_techclassify as b on b.tid = a.tid')->where("a.tuid = '$uid' and a.state = 1")-> field('a.tdid,a.tdtitle,b.tname')->select();
                break;
            case 2:
                $comment = M('comment as a');
                $data = $comment -> join('tec_techdetail as b on b.tdid = a.tdid') -> where("a.reviewer = '$uid' and b.state = 1") -> field('a.cid,a.content,b.tdtitle')->select();
                break;
            case 3:
                $tpzdetail = M('tpzdetail as a');
                $data = $tpzdetail -> join('tec_techpersonzone as b on b.tpzid = a.tpzid') -> field('a.tpzdtitle,a.tpzdid')->select();
                break;
        }
        $this->ajaxReturn($data);
    }

    //修改页面通过id获取数据
    public function getDataByIdFromEdit(){
        $state = I('request.state');
        $id = I('request.id');
        switch ($state) {
            case 0:
                $techdetail = M('techdetail');
                $data = $techdetail -> where("tdid = '$id'")->field('tdtitle,tdcontent,isfree,price')->find();
                break;
            case 1:
                $techdetail = M('techdetail');
                $data = $techdetail -> where("tdid = '$id'")->field('tdtitle,tdcontent')->find();
                break;
            case 2:
                $comment = M('comment as a');
                $data = $comment ->join('tec_techdetail as b on b.tdid = a.tdid')-> where("a.cid = '$id'") -> field('a.content,b.tdtitle')->find();
                break;
            case 3:
                $tpzdetail = M('tpzdetail');
                $data = $tpzdetail -> where("tpzdid = '$id'") -> field('tpzdtitle,tpzdcontent,isfree,price')->find();
                break;
        }
        $this->ajaxReturn($data);
    }

    //修改发表内容通用接口
    //state为标识 0->帖子 1->问题 2->回答 3->专栏贴
    public function editSendByState(){
        $state = I('request.state');
        $state = intval($state);
        switch ($state) {
            case 0:
                $techdetail = M('techdetail');
                $id = I('request.id');
                $data['tdtitle'] = I('request.tdtitle');
                $data['tdcontent'] = I('request.tdcontent');
                $data['tdaltertime'] = date("Y-m-d H:i:s" , time());
                $data['isfree'] = I('request.isfree');
                $data['isfree'] = intval($data['isfree']);
                if( 0 == $data['isfree'] ){
                   $data['price'] = I('request.price'); 
                }
                if( $techdetail -> where("tdid = '$id'") -> save($data))
                {
                    $success['success'] = 1;
                }
                else
                {
                    $success['success'] = 0;
                }
                break;
            case 1:
                $techdetail = M('techdetail');
                $id = I('request.id');
                $data['tdtitle'] = I('request.tdtitle');
                $data['tdcontent'] = I('request.tdcontent');
                if( $techdetail -> where("tdid = '$id'") -> save($data))
                {
                    $success['success'] = 1;
                }
                else
                {
                    $success['success'] = 0;
                }
                break;
            case 2:
                $comment = M('comment');
                $id = I('request.id');
                $data['content'] = I('request.content');
                $data['ctime'] = date("Y-m-d H:i:s" , time());
                if( $comment -> where("cid = '$id'") -> save($data))
                {
                    $success['success'] = 1;
                }
                else
                {
                    $success['success'] = 0;
                }
                break;
            case 3:
                $tpzdetail = M('tpzdetail');
                $id = I('request.id');
                $data['tpzdtitle'] = I('request.tpzdtitle');
                $data['tpzdcontent'] = I('request.tpzdcontent');
                $data['tpzdaltertime'] = date("Y-m-d H:i:s" , time());
                $data['isfree'] = I('request.isfree');
                $data['isfree'] = intval($data['isfree']);
                if( 0 == $data['isfree'] ){
                   $data['price'] = I('request.price'); 
                }
                if( $tpzdetail -> where("tpzdid = '$id'") -> save($data))
                {
                    $success['success'] = 1;
                }
                else
                {
                    $success['success'] = 0;
                }
                break;
        }
        $this->ajaxReturn($success);
    }

    /*
     *搜索模块
     */
    //获取用户搜索历史记录
    public function getUserSearch(){
        $search = M('search');
        $data['uid'] = I('request.uid');
        $list = $search -> where($data) -> select();
        $this->ajaxReturn($list);
    }
    //搜索功能
    public function search(){
        $search = M('search');
        $uid = I('request.uid');
        $searchtext = I('request.searchtext');
        $state = I('request.state');
        switch ($state) {
            case 0:
                $user = M('user');
                $list = $user -> where("ualiase like '%$searchtext%'") -> field('uid,ualiase,uphoto,ulevel,utype')->select();
                break;
            case 1:
                $techdetail = M('techdetail as a');
                $list = $techdetail -> join('tec_techclassify as b on b.tid = a.tid') -> where("(a.tdtitle like '%$searchtext%' or a.tdcontent like '%$searchtext%') and a.state = '0'")->field('a.tdid,a.tdtitle,b.tname') -> distinct('a.tdid')->select();
                break;
            case 2:
                $techdetail = M('techdetail as a');
                $list = $techdetail -> join('tec_techclassify as b on b.tid = a.tid') -> where("(a.tdtitle like '%$searchtext%' or a.tdcontent like '%$searchtext%') and a.state = '1'")->field('a.tdid,a.tdtitle,b.tname') -> distinct('a.tdid')->select();
                break;
            case 3:
                $tpzdetail = M('tpzdetail as a');
                $list = $tpzdetail -> join('tec_techpersonzone as b on b.tpzid = a.tpzid') ->where("a.tpzdtitle like '%$searchtext%' or a.tpzdcontent like '%$searchtext%'")-> field('a.tpzdtitle,a.tpzdid,b.tpzname')->select();
                break;
        }
        if( null != $uid ){
            if( !$search -> where("uid = '$uid' and searchcontent = '$searchtext'") -> find())
            {
                $data['uid'] = $uid;
                $data['searchcontent'] = $searchtext;
                $search -> add($data);
            }
        }
        $this->ajaxReturn($list); 
    }
}