<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
	
   public function userapply(){
   		$userapplyfor = M('userapplyfor');
   		$list = $userapplyfor -> select();
   		$this->list = $list;
   		$this->display();
   }

   public function userlist(){
   		$user = M('user');
   		$userlistdata = $user->select();
   		$this->userlistdata = $userlistdata;
   		$this->display();
   }

   public function douserapply(){
   		$user = M('user');
   		$userapplyfor = M('userapplyfor');
   		$techpersonzone = M('techpersonzone');
   		$data['uid'] = I('request.uid');
   		$uid = $data['uid'];
   		$data['uafid'] = I('request.uafid');
   		$i =  intval(I('request.i'));
   		$flag = intval(I('request.flag'));
   		if( $i == 1)
   		{
   			if( $flag == 1)
   			{
   				$data1['state'] = 1; 
   				if($userapplyfor -> where($data) -> save($data1))
   				{
   					$data2['ispassed'] = 2;
   					if($user -> where("uid = '$uid'") -> save($data2))
   					{
   						$this->success("操作成功！",U('index/userapply'));
   					}
   					else{
   						$this->error("操作失败！",U('index/userapply'));
   					}
   				}
   				else{
   					$this->error("操作失败！",U('index/userapply'));
   				}
   			}
   			else
   			{
				$data1['state'] = 1; 
   				if($userapplyfor -> where($data) -> save($data1))
   				{
   					$data2['tpzname'] = I('request.tpzname');
   					$data2['tid'] = I('request.tid');
   					$data2['tpzid'] = "tpz-".date("YmdHis", time());
   					$data2['addtime'] = date("Y:m:d H:i:s", time());
   					$data2['uid'] = $uid;
   					if( $techpersonzone -> add($data2))
   					{
   						$this->success("操作成功！",U('index/userapply'));
   					}
   					else
   					{
   						$this->error("操作失败！",U('index/userapply'));
   					}
   				}
   				else{
   					$this->error("操作失败！",U('index/userapply'));
   				}
   			}

   		}
   		else
   		{
			if( $flag == 1)
   			{
				$data1['state'] = 2; 
   				if($userapplyfor -> where($data) -> save($data1))
   				{
   					$data2['ispassed'] = 3;
   					if($user -> where("uid = '$uid'") -> save($data2))
   					{
   						$this->success("操作成功！",U('index/userapply'));
   					}
   					else{
   						$this->error("操作失败！",U('index/userapply'));
   					}
   				}
   				else{
   					$this->error("操作失败！",U('index/userapply'));
   				}
   			}
   			else
   			{
				$data1['state'] = 2; 
   				if($userapplyfor -> where($data) -> save($data1))
   				{
   					$this->success("操作成功！",U('index/userapply'));

   				}
   				else
   				{
   					$this->error("操作失败！",U('index/userapply'));
   				}
   			}

   		}
   }

   public function login(){
   		$this->display();
   }

   public function dologin(){
	   	$data['adminname'] = I('request.adminname');
	   	$data['firstpassword'] = md5(I('request.firstpassword'));
	   	$data['secondpassword'] = md5(I('request.secondpassword'));
	   	$admin = M('admin');
	   	if( $admin -> where($data)->find())
	   	{
	   		$this->success('登录成功！', U('index/userlist'));
	   	}
	   	else
	   	{
	   		$this->error('登录失败！' , U('index/login'));
	   	}
   }

   public function techclassifylist(){
   		$techclassify = M('techclassify');
   		$list = $techclassify -> select();
   		$this->list = $list;
   		$this->display();
   }

   public function delclassify(){
   		$techclassify = M('techclassify');
   		$data['tid'] = I('request.tid');
   		if( $techclassify -> where($data) -> delete())
   		{
   			$this->success('删除分类成功！',U('index/techclassifylist'));
   		}
   		else
   		{
   			$this->error('删除分类失败！',U('index/techclassifylist'));
   		}
   }

   public function addclassify(){
   		$techclassify = M('techclassify');
   		$data['tname'] = I('request.tname');
   		$data['tid'] = "t-".date("YmdHis", time());
   		if( $techclassify -> add($data))
   		{
   			$this->success('添加分类成功！',U('index/techclassifylist'));
   		}
   		else
   		{
   			$this->error('添加分类失败！',U('index/techclassifylist'));
   		}
   }

   public function techpersonzonelist(){
   		$techpersonzone = M('techpersonzone as a');
   		$list = $techpersonzone -> join('tec_techclassify as b on b.tid = a.tid') -> select();
   		$this->list = $list;
   		$this->display();
   }

   public function techdetaillist(){
   		$techdetail = M('techdetail as a');
   		$list1 = $techdetail -> join('tec_user as b on b.uid = a.tuid') ->join('tec_techclassify as c on c.tid = a.tid') -> where("a.state = '0'")-> select();
   		$list2 = $techdetail -> join('tec_user as b on b.uid = a.tuid') ->join('tec_techclassify as c on c.tid = a.tid') -> where("a.state = '1'")-> select();
   		$this->list1 = $list1;
   		$this->list2 = $list2;
   		$this->display();
   }
   public function loginout(){
      $this->success("注销成功！",U('index/login'));
   }
}