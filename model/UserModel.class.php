<?php
/**
 * 用户信息模型
 * @author wclssdn<ssdn@vip.qq.com>
 *
 */
class UserModel extends Model {

	const FROM_SINA = 'sina';
	
	const FROM_QQ = 'qq';

	const GENDER_MALE = 'm';
	
	const GENDER_FEMALE = 'f';

	/**
	 *
	 * @var KvdbDao
	 */
	private $dao;

	public function __construct() {
		$this->dao = Ioc::loadObject('KvdbDao');
	}

	/**
	 * 注册用户
	 * @param number $from 第三方标识
	 * @param string $uniqid 第三方帐号唯一ID
	 * @param string $nickname 第三方提供用户昵称
	 * @param string $gender 第三方提供用户性别 m/f
	 * @param string $avatar 第三方提供用户头像
	 * @param string $access access token
	 * @param string $name 腾讯帐号
	 * @param string $refresh_token
	 * @return array boolean
	 */
	public function register($from, $uniqid, $nickname, $gender, $avatar, $access, $name, $refresh_token) {
		$counterModel = Ioc::loadObject('CounterModel');
		$uid = $counterModel->inc(KEY_AUTOINC_USER);
		if ($uid === false){
			return false;
		}
		$user = array();
		$user['uid'] = $uid;
		$user['userinfo'][$from]['uniqid'] = $uniqid;
		$name && $user['userinfo'][$from]['name'] = $name;
		$user['userinfo'][$from]['nickname'] = $nickname;
		$user['userinfo'][$from]['gender'] = $gender;
		$user['userinfo'][$from]['avatar'] = $avatar;
		$user['userinfo'][$from]['access'] = $access;
		$refresh_token && $user['userinfo'][$from]['refresh'] = $refresh_token;
		$user['userinfo'][$from]['regtime'] = date('Y-m-d H:i:s');
		$user['newuser'] = true;
		if ($this->dao->add(KEY_USER . $uid, $user)){
			if ($this->dao->add(KEY_USERID_UID . $uniqid, $uid) === false){
				$this->dao->add(KEY_USERID_UID . $uniqid, $uid);
			}
			return $user;
		}
		return false;
	}

	/**
	 * 更新access token
	 * @param unknown_type $uid
	 * @param unknown_type $from
	 * @param unknown_type $access
	 * @return boolean|string
	 */
	public function updateAccessToken($uid, $from, $access){
		$userInfo = $this->getUserByUid($uid);
		if ($userInfo === false){
			return false;
		}
		$userInfo['userinfo'][$from]['access'] = $access;
		$userInfo['userinfo'][$from]['updatetime'] = date('Y-m-d H:i:s');
		$this->dao->update(KEY_USER . $uid, $userInfo);
		return $userInfo;
	}

	/**
	 * 禁止用户
	 * @param number $id
	 * @return boolean
	 */
	public function ban($uid) {
		$userInfo = $this->getUserByUid($uid);
		if ($userInfo === false){
			return false;
		}
		$userInfo['ban'] = true;
		return $this->dao->update(KEY_USER . $uid, $userInfo);
	}

	/**
	 * 根据用户ID获取用户信息
	 * @param number $uid
	 * @return array boolean
	 */
	public function getUserByUid($uid) {
		return $this->dao->get(KEY_USER . $uid);
	}

	/**
	 * 根据第三方唯一ID获取用户信息
	 * @param string $uniqueid
	 * @return array boolean
	 */
	public function getUserByUniqid($uniqid) {
		$uid = $this->dao->get(KEY_USERID_UID . $uniqid);
		if ($uid === false){
			return false;
		}
		return $this->getUserByUid($uid);
	}
	
	public function iamnotnew($uid){
		$userInfo = $this->getUserByUid($uid);
		unset($userInfo['newuser']);
		return $this->dao->update(KEY_USER . $uid, $userInfo);
	}
}