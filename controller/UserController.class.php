<?php
/**
 * 用户控制器
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class UserController extends Controller {

	/**
	 *
	 * @var View
	 */
	private $view;

	public function __construct() {
		parent::__construct();
		$this->view = Ioc::loadObject('View');
		$this->view->setTemplatePath(PATH_VIEW . 'user/');
	}

	/**
	 * 登录
	 */
	public function loginAction() {
		$from = $this->request->getParam('from');
		$froce = $this->request->getParam('force');
		if ($this->request->getParam('backurl') !== null){
			self::setBackUrl($this->request->getParam('backurl') ? $this->request->getParam('backurl') : $_SERVER['HTTP_REFERER']);
		}
		$oauth = Config::loadFile('oauth');
		switch ($from){
			case UserModel::FROM_SINA:
				$oauth = $oauth[$from];
				$sinaOauth = new SinaOauth($oauth['key'], $oauth['secret']);
				// oauth第二步
				if (($code = $this->request->getParam('code')) !== null){
					$keys = array();
					$keys['code'] = $code;
					$keys['redirect_uri'] = $oauth['callback'];
					try{
						$token = $sinaOauth->getAccessToken('code', $keys);
					} catch (OAuthException $e){
						$this->redirect('/user/login/error');
					}
					$userModel = Ioc::loadObject('UserModel');
					if (($userInfo = $userModel->getUserByUniqid($token['uid'])) === false){
						$sinaClient = new SaeTClientV2($oauth['key'], $oauth['secret'], $token['access_token']);
						$tmp = $sinaClient->show_user_by_id($token['uid']);
						$userInfo = $userModel->register(UserModel::FROM_SINA, $token['uid'], $tmp['screen_name'], $tmp['gender'], $tmp['avatar_large'], $token['access_token']);
					}else{
						$userInfo = $userModel->updateAccessToken($userInfo['uid'], $from, $token['access_token']);
					}
					if ($userInfo === false){
						$this->redirect('/user/login/error');
					}
					if (isset($userInfo['ban']) && $userInfo['ban']){
						$this->redirect('/user/login/ban');
					}
					Ioc::callStatic('UserController', 'login', array($userInfo, $from));
					$this->redirect('/');
				}
				// oauth第一步
				$url = $sinaOauth->getAuthorizeURL($oauth['callback'], 'code', null, null, $froce);
				$this->redirect($url);
				break;
			case UserModel::FROM_QQ:
				$oauth = $oauth[$from];
				TencentOauth::init($oauth['key'], $oauth['secret']);
				// oauth第二步
				if (($code = $this->request->getParam('code')) !== null){
					$openid = $this->request->getParam('openid');
					$result = TencentOauth::getAccessToken($code, $oauth['callback']);
					if (!$result['access_token']){
						$this->redirect('/user/login/error');
					}
					$userModel = Ioc::loadObject('UserModel');
					if (($userInfo = $userModel->getUserByUniqid($openid)) === false){
						$config = Config::loadFile('oauth');
						$config = $config['qq'];
						$tencent = Ioc::loadObject('Tencent', array($config['key'], $config['secret'], $result['access_token'], $openid));
						$tmp = $tencent->api('user/info');
						if ($tmp === false){
							$this->redirect('/user/login/error');
						}
						$userInfo = $userModel->register(UserModel::FROM_QQ, $openid, $tmp['nick'], $tmp['sex'] == 1 ? 'm' : 'f', $tmp['head'] . '/180', $result['access_token'], $result['name'], $result['refresh_token']);
					}else{
						$userInfo = $userModel->updateAccessToken($userInfo['uid'], $from, $result['access_token']);
					}
					if ($userInfo === false){
						$this->redirect('/user/login/error');
					}
					if (isset($userInfo['ban']) && $userInfo['ban']){
						$this->redirect('/user/login/ban');
					}
					Ioc::callStatic('UserController', 'login', array($userInfo, $from));
					$this->redirect('/');
				}
				// oauth第一步
				$url = TencentOauth::getAuthorizeURL($oauth['callback']);
				$this->redirect($url);
				break;
			default:
				$this->view->setTemplateFile('login.tpl.php');
				$this->view->display();
				break;
		}
		$this->output();
	}

	public function logoutAction() {
		$this->logout();
		$this->redirect('/');
	}

	public function loginerrorAction(){
		$this->view->setTemplateFile('loginError.tpl.php');
		$this->view->display();
		$this->output();
	}

	public static function login($userinfo, $from) {
		$_SESSION['userinfo'] = $userinfo;
		$_SESSION['loginuserinfo'] = $userinfo['userinfo'][$from];
		$_SESSION['loginuserinfo']['uid'] = $userinfo['uid'];
		$_SESSION['loginuserinfo']['from'] = $from;
		return true;
	}

	public static function isLogin() {
		if (isset($_SESSION['loginuserinfo'])){
			return true;
		}
		return false;
	}

	public static function logout() {
		unset($_SESSION['userinfo'], $_SESSION['loginuserinfo']);
		return true;
	}

	public static function getLoginUserInfo() {
		return $_SESSION['loginuserinfo'];
	}
	
	public static function getUserInfo(){
		return $_SESSION['userinfo'];
	}
}
