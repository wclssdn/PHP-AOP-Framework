<?php
class Session {

	private static $maxLifeTime = 1600;

	public static function start() {
		session_start();
	}

	public static function setMaxLifeTime($maxLifeTime) {
		self::$maxLifeTime = $maxLifeTime;
		return true;
	}

	public static function setHandle(SessionHandleable $sessionHandle) {
		session_write_close();
		return session_set_save_handler(array($sessionHandle, 'open'), array($sessionHandle, 'close'), array($sessionHandle, 'read'), array($sessionHandle, 'write'), array($sessionHandle, 'destroy'), array($sessionHandle, 'gc'));
	}
}