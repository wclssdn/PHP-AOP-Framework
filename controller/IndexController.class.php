<?php
/**
 * 首页控制器
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class IndexController extends Controller {

	/**
	 * 视图对象
	 * @var View
	 */
	private $view;

	private $loginUserInfo;

	public function __construct() {
		parent::__construct();
		$this->view = Ioc::loadObject('View');
		if (!Ioc::callStatic('UserController', 'isLogin')){
			$this->view->display(PATH_VIEW . 'user', 'login.tpl.php');
			$this->output();
			$this->quit();
		}
		$this->loginUserInfo = Ioc::callStatic('UserController', 'getLoginUserInfo');
		$this->view->assign('loginuserinfo', $this->loginUserInfo);
		$this->view->setTemplatePath(PATH_VIEW . 'index');
	}

	public function indexAction() {
		$page = $this->request->getParam('page');
		$page = max(1, $page);
		$gender = $this->request->getParam('gender');
		if (!$gender){
			$gender = $this->loginUserInfo['gender'] == 'm' ? 'f' : 'm'; //反过来
		}
		$weiboModel = Ioc::callStatic('WeiboFactory', 'getUserWeiboModel', array($this->loginUserInfo));
		$friends = $weiboModel->getFriends($page, $gender); //我的好友
		$lastpage = $weiboModel->isLastpage();
		if ($friends === false){
			$this->error('微博抽风了, 请稍后访问吧.', CODE_SYSTEM_ERROR, array('second' => 3, 'url' => '/'));
		}
		$uniqids = array_keys($friends);
		$signModel = Ioc::loadObject('SignModel', array($this->loginUserInfo['uid'], $this->loginUserInfo['uniqid'], $this->loginUserInfo['from']));
		$signs = $signModel->isSigned($uniqids); //好友是否标记过我
		$messageModel = Ioc::loadObject('MessageModel', array($this->loginUserInfo['from']));
		$unread = $messageModel->getUnread($this->loginUserInfo['uid']);
		$this->view->assign('page', $page);
		$this->view->assign('friends', $friends);
		$this->view->assign('signs', $signs);
		$this->view->assign('unread', $unread);
		if ($this->request->isAjax()){
			$data['page'] = $page;
			$data['lastpage'] = $lastpage;
			$data['unread'] = $unread;
			$data['html'] = $this->view->fetch(PATH_VIEW . 'index', 'ajax_index.tpl.php', array('page' => $page, 'friends' => $friends, 'signs' => $signs));
			$this->view->ajaxDisplay(CODE_OK, '', $data);
			$this->output();
			$this->quit();
		}
		$userInfo = Ioc::callStatic('UserController', 'getUserInfo');
		if (isset($userInfo['newuser'])){
			$this->view->assign('newuser', true);
		}
		$this->view->setTemplateFile('index.tpl.php');
		$this->view->display();
		$this->output();
	}

	public function signAction(){
		$uniqid = $this->request->getParam('uniqid');
		$name = $this->request->getParam('name');
		$nick = $this->request->getParam('nick');
		$token = $this->request->getParam('token');
		if (Encryption::token($uniqid, $nick, $name) !== $token){
			$this->error('你丫蛋疼？', CODE_REQUEST_HACK);
		}
		$signModel = Ioc::loadObject('SignModel', array($this->loginUserInfo['uid'], $this->loginUserInfo['uniqid'], $this->loginUserInfo['from']));
		if ($signModel->sign($uniqid)){
			$userModel = Ioc::loadObject('UserModel');
			$userInfo = $userModel->getUserByUniqid($uniqid);
			if ($userInfo && ($signed = $signModel->amiSigned($userInfo['uid'])) !== false){
				$message = array();
				$message['type'] = MessageModel::TYPE_SIGN;
				$message['uid'] = $this->loginUserInfo['uid'];
				$message['from'] = $this->loginUserInfo['from'];
				$message['nickname'] = $this->loginUserInfo['nickname'];
				$this->loginUserInfo['from'] == UserModel::FROM_QQ && $message['name'] = $this->loginUserInfo['name'];
				$messageModel = Ioc::loadObject('MessageModel', array($this->loginUserInfo['from']));
				$messageModel->send($userInfo['uid'], $message);
				$message2me = array();
				$message2me['type'] = MessageModel::TYPE_SIGN_TO_ME;
				$message2me['uid'] = $userInfo['uid'];
				$message2me['from'] = $this->loginUserInfo['from'];
				$message2me['nickname'] = $nick;
				$this->loginUserInfo['from'] == UserModel::FROM_QQ && $message2me['name'] = $name;
				$messageModel->send($this->loginUserInfo['uid'], $message2me);
				$this->success("恭喜了！对方之前也标记过你了！\n你们双方会收到一个互相标记的通知，此时你们双方都知道了互相标记过了。\n接下来就私下联系啪啪啪吧。。。注意安全！！！", array('amisigned' => true));
			}
			$data = array(1);
			if (!$userInfo){
				$data['showinvite'] = true;
			}
			$this->success("你已经标记了对方，如果对方也来标记你，你们将收到互相标记的通知！\n注意！此时对方是不知道有没有人标记过Ta的，当然更不会知道你标记过Ta。\n现在你要做的就是等Ta来标记你，当然你也可以继续标记其他人～～～", $data);
		}
		$this->error('你个屌丝！点一次就行了！', CODE_REQUEST_HACK);
	}

	public function cancelAction(){
		$uniqid = $this->request->getParam('uniqid');
		$signModel = Ioc::loadObject('SignModel', array($this->loginUserInfo['uid'], $this->loginUserInfo['uniqid'], $this->loginUserInfo['from']));
		if ($signModel->cancel($uniqid)){
			$userModel = Ioc::loadObject('UserModel');
			$userInfo = $userModel->getUserByUniqid($uniqid);
			if ($userInfo){
				$message = array();
				$message['type'] = MessageModel::TYPE_CANCEL;
				$message['uid'] = $this->loginUserInfo['uid'];
				$message['from'] = $this->loginUserInfo['from'];
				$message['name'] = $this->loginUserInfo['name'];
				$message['nickname'] = $this->loginUserInfo['nickname'];
				$messageModel = Ioc::loadObject('MessageModel', array($this->loginUserInfo['from']));
				$messageModel->send($userInfo['uid'], $message);
			}
			$this->success('不要害羞嘛，骚年!', array(1));
		}
		$this->error('你个屌丝！点一次就行了！', CODE_REQUEST_HACK);
	}

	public function inviteAction(){
		$uniqid = $this->request->getParam('uniqid');
		$nick = $this->request->getParam('nick');
		$name =$this->request->getParam('name');
		$token = $this->request->getParam('token');
		if (Encryption::token($uniqid, $nick, $name) !== $token){
			$this->error('你丫蛋疼？', CODE_REQUEST_HACK);
		}
		if ($this->loginUserInfo['from'] == UserModel::FROM_QQ){
			$nick = $name;
		}
		$inviteModel = Ioc::loadObject('InviteModel');
		$result = $inviteModel->invite($uniqid, $nick, $this->loginUserInfo['from'], $this->loginUserInfo['uid']);
		if ($result === false){
			$this->error('什么情况， 邀请失败了！', CODE_SYSTEM_ERROR);
		}
		$this->success('邀请一会儿就发，骚等～～～', array(1));
	}

	public function messagesAction(){
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		$page = $this->request->getParam('page');
		$page = max(1, $page);
		$every = 7;
		$messageModel = Ioc::loadObject('MessageModel', array($this->loginUserInfo['from']));
		$messages = $messageModel->get($this->loginUserInfo['uid'], $page, $every);
		if ($page == 1){
			$messageModel->resetUnread($this->loginUserInfo['uid']);
		}
		if ($messages === false){
			$this->success('', array(1));
		}
		foreach ($messages as $key => &$message){
			if ($this->loginUserInfo['from'] == UserModel::FROM_SINA){
				$nick = "@{$message['message']['nickname']}";
			}else{
				$nick = "{$message['message']['nickname']}(@{$message['message']['name']})";
			}
			switch ($message['message']['type']){
				case MessageModel::TYPE_SIGN:
					$message['message'] = "恭喜你，你标记过的<b>{$nick}</b>也标记你了～　接下来，你们就私下联系啪啪啪吧～～～";
					break;
				case MessageModel::TYPE_SIGN_TO_ME:
					$message['message'] = "恭喜你，<b>{$nick}</b>之前也标记过你～　接下来，你们就私下联系啪啪啪吧～～～";
					break;
				case MessageModel::TYPE_CANCEL:
					$message['message'] = '话说有人之前标记了你，但又取消了！';
					break;
			}
		}
		$this->success('', array('page' => $page, 'lastpage' => count($messages) < $every || empty($messages), 'messages' => $messages));
	}

	public function iamnotnewAction(){
		$userModel = Ioc::loadObject('UserModel');
		if ($userModel->iamnotnew($this->loginUserInfo['uid'])){
			$userInfo = Ioc::callStatic('UserController', 'getUserInfo');
			unset($userInfo['newuser']);
			Ioc::callStatic('UserController', 'login', array($userInfo, $this->loginUserInfo['from']));
			$this->success('', array(1));
		}
		$this->error('...', CODE_SYSTEM_ERROR);
	}
}