<?php
header('content-type:text/html;charset=utf-8');
/**
 * 所有模块必须写在函数里
 * */
#主页面 , Ready to start work (准备开始工作)
function index()
{
	session_start();
	#公共文件内容
	include 'subject/'.getThemeDir().'/common.php';
	
	$u_id = $_GET['u_id']==null?'':$_GET['u_id'];
	
	require 'subject/'.getThemeDir().'/template/'.__FUNCTION__.'.html';
}
#管理报名信息
function bim_list()
{
	session_start();
	#公共文件内容
	include 'subject/'.getThemeDir().'/common.php';
		
	$password = $_GET['password'];
	if($password != 'htx2018')
	{#密码
echo '<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,shrink-to-fit=no,user-scalable=no,minimal-ui" name="viewport"/>
<meta name ="format-detection" content="telephone=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge ,chrome=1" /><title>搜索</title></head><body><form action="'.apth_url('index.php').'" mehtod="get">请输入密码：<input type="text" name="password" value=""/> <input type="hidden" name="act" value="bim_list"/><input type="submit" value="提交"/></form></body>
</html>';exit;
	}
		
	#获取名单展示
	$json = vcurl(EXTLINK,'act=BIM2AllInfo_2&page='.($_GET['page']==null?1:$_GET['page']));
	if($json!='')
	{
		$rows = ParsingJson($json);
	}
	
	$totalshow = $rows['totalshow'];//展示
    $totalpage = $rows['totalpage'];//页数
    $page = $rows['page'];//当前页
    $totalRows = $rows['totalRows'];//总数    
	$row = $rows['txt'];//列表
	$state = $rows['error'];//状态
	
	require 'subject/'.getThemeDir().'/template/'.__FUNCTION__.'.html';
}
#查询通道
function search()
{
	session_start();
	#公共文件内容
	include 'subject/'.getThemeDir().'/common.php';
	
	require 'subject/'.getThemeDir().'/template/'.__FUNCTION__.'.html';
}
function SendSearch()
{
	session_start();
	$_SESSION['flag_allsinfo'] = $_POST['flag'];
	header('location:'.apth_url('?act=bim_list2'));
}
#管理报名信息-佣金
function bim_list2()
{
	session_start();
	#公共文件内容
	include 'subject/'.getThemeDir().'/common.php';
	
	if(!isset($_SESSION['flag_allsinfo'])||$_SESSION['flag_allsinfo']==null)
	{
		header('location:'.apth_url());exit;
	}
	
	$flag = $_SESSION['flag_allsinfo'];
		
	#获取名单展示
	$json = vcurl(EXTLINK,'act=BIM2AllInfo_3&flag='.$flag.'&page='.($_GET['page']==null?1:$_GET['page']));
	if($json!='')
	{
		$rows = ParsingJson($json);
	}
	
	$totalshow = $rows['totalshow'];//展示
    $totalpage = $rows['totalpage'];//页数
    $page = $rows['page'];//当前页
    $totalRows = $rows['totalRows'];//总数    
	$row = $rows['txt'];//列表
	$state = $rows['error'];//状态
	
	require 'subject/'.getThemeDir().'/template/'.__FUNCTION__.'.html';
}
#报名成功
function bim_success()
{
	session_start();
	#公共文件内容
	include 'subject/'.getThemeDir().'/common.php';
	if(!isset($_SESSION['bim2_info'])||$_SESSION['bim2_info']==null)
	{
		header('location:'.apth_url());exit;
	}
	$data = $_SESSION['bim2_info'];
	$json = vcurl(EXTLINK,'act=BIM2Info2Info&'.http_build_query($data));
	if($json!=null)
	{
		$rows = ParsingJson($json);
	}
	
	require 'subject/'.getThemeDir().'/template/'.__FUNCTION__.'.html';
}
#提交报名信息
function SendNameInfo()
{
	//session_start();
	
	$data['name'] = $_POST['name'];
	if( $data['name'] == '' )
	{
		echo json_encode(array('error'=>1,'txt'=>'请输入姓名'));exit;
	}
	$data['tel'] = $_POST['tel'];
	if( $data['tel'] == '' )
	{
		echo json_encode(array('error'=>1,'txt'=>'请输入手机'));exit;
	}
	if(!preg_match("/^0?(13|14|15|17|18)[0-9]{9}$/", $data['tel']) )
	{
		echo json_encode(array('error'=>1,'txt'=>'手机号不正确'));exit;
	}
	$data['qq'] = $_POST['qq'];
	if( $data['qq'] == '' )
	{
		echo json_encode(array('error'=>1,'txt'=>'请输入QQ号码'));exit;
	}
	$data['flag'] = $_POST['flag'];
	
	//$_SESSION['bim2_info'] = $data;
	
	$int = vcurl(EXTLINK,'act=BIM2Info2&'.http_build_query($data));

	if( $int )
	{
			
		$json = vcurl(EXTLINK,'act=BIM2Info2Info&'.http_build_query($data));
		if($json!=null)
		{
			$rows = ParsingJson($json);
		}
			
		$html = '';
		$html .= '<div class="pay-succ-div">';
		$html .= '<div class="div-icon"><i class="layui-icon">&#xe605;</i></div>';
		$html .= '<p style="font-size:1rem;">报名成功!</p>';
		$html .= '<p style="text-align:left; text-align:justify" >请保持电话畅通，广西BIM培训中心老师稍后会与你联系，并安排试课、培训、考试、工作等流程</p>';
		$html .= '<p style="font-size:1rem; text-align:left">报名信息：</p>';
		$html .= '<div class="font8" style="text-align:center; border: 1px solid #e0e0e0; border-bottom:none;padding:0.4rem 0;">'.$rows['txt']['username'].'</div>';
		$html .= '<table border="1" class="bim-table bim-table1">';
		$html .= '<tr>';
		$html .= '<th width="30%">电话</th>';
		$html .= '<th width="70%">'.$rows['txt']['tel'].'</th>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th width="30%">QQ</th>';
		$html .= '<th width="70%">'.$rows['txt']['qq'].'</th>';
		$html .= '</tr>';
		$html .= '</table>';
		$html .= '</div>';
		
		
		echo json_encode(array('error'=>0,'txt'=>$html));
		
		yeslisttofrom();
	}
	else
	{
		echo json_encode(array('error'=>1,'txt'=>'系统错误，报名失败'));
	}
	
}
#微信支付
function weixinpay()
{
	session_start();
	$info = $_SESSION['wap_payment_info'];	
	$tel = $_GET['tel'];
	$json = vcurl(EXTLINK,'act=BimGetRow2&tel='.$tel);
	$row = ParsingJson($json);
	if($row['txt']['state']!=1)
	{
		vcurl(EXTLINK,'act=update_bimreg&tel='.$tel.'&pay='.$info['pay'].'&subject='.$info['subject'].'&ordersn='.$info['ordersn']);
	
		//记录监听
		vcurl(EXTLINK,'act=WeChatRecord&data='.json_encode(array('ordersn'=>$info['ordersn'],'objtype'=>1,'state'=>1)));
		
		#POST参数
		$post_url = 'http://newjob.htxgcw.com/wapweixin/pay_2.php?pay=wapweixin';//提交地址
		$subject = $info['subject'];//商品名称
		$out_trade_no = $info['ordersn'];
		$total_fee = $info['pay']*100;
			
		echo '<form action="'.$post_url.'" method="post" id="frm">
				<input type="hidden" name="subject" value="'.$subject.'"/>
				<input type="hidden" name="ordersn" value="'.$out_trade_no.'"/>
				<input type="hidden" name="amount" value="'.$total_fee.'"/>
			 </form><script>document.getElementById("frm").submit();</script>';
	}
}
#微信支付返回结果
function WeixinVideoNotify()
{
	$json = vcurl(EXTLINK,'act=checked_bimord&ordersn='.$_REQUEST['out_trade_no']);
	$row = ParsingJson($json);
	if( $row['txt']['state'] == 0 )
	{
		$data['publitime'] = time();//支付时间
		$data['state'] = 1;//待支付状态
			
		//UpdatePayMentInfo_order记录数据
		vcurl(EXTLINK,'act=UpdatePayMentInfo_bim&ordersn='.$_REQUEST['out_trade_no'].'&data='.json_encode($data));
	}
}
#微信支付成功结果页面
function bim_result()
{
	#公共文件内容
	include 'subject/'.getThemeDir().'/common.php';
	
	$ordersn = $_GET['trade_no'];
	$json = vcurl(EXTLINK,'act=BimGetRow&ordersn='.$ordersn);
	$row = ParsingJson($json);
	
	require 'subject/'.getThemeDir().'/template/bim-pay.html';
}