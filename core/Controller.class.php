<?php
/**
 * 控制器基类
 * @author wclssdn <ssdn@vip.qq.com>
 * 
 */
abstract class Controller {

	/**
	 * 请求对象
	 * @var Request
	 */
	protected $request;

	/**
	 * 响应对象
	 * @var Response
	 */
	protected $response;

	/**
	 * 构造方法
	 * @param Request $request
	 * @param Response $response
	 */
	public function __construct() {
		$this->request = Request::getInstance();
		$this->response = Response::getInstance();
	}

	/**
	 * 输出到浏览器
	 */
	public function output() {
		$this->response->send();
	}

	/**
	 * 跳转
	 * @param string $url 目标地址
	 * @param number $timeout 跳转延时
	 * @param boolean $quit 是否跳转完成后结束脚本执行
	 */
	public function redirect($url, $timeout = 0, $quit = true) {
		$this->response->redirect($url, $timeout);
		$quit && $this->quit();
	}

	/**
	 * 失败提示消息
	 * @param string $message
	 * @param array $data
	 */
	public function success($message, array $data = array(), $quit = true) {
		$view = Ioc::loadObject('View');
		if ($this->request->isAjax()){
			$view->ajaxDisplay(CODE_OK, $message, $data);
		} else{
			$view->assign('success', true);
			$view->assign('message', $message);
			$view->setTemplatePath(PATH_VIEW);
			$view->setTemplateFile('message.tpl.php');
			$view->display();
		}
		$quit && $this->quit();
	}

	/**
	 * 失败提示消息
	 * @param string $message
	 * @param number $code
	 * @param array $data
	 */
	public function error($message, $code = CODE_COMMON_ERROR, array $data = array(), $quit = true) {
		$view = Ioc::loadObject('View');
		if ($this->request->isAjax()){
			$view->ajaxDisplay($code, $message, $data);
		} else{
			$view->assign('message', $message);
			$view->setTemplatePath(PATH_VIEW);
			$view->setTemplateFile('message.tpl.php');
			$view->display();
		}
		$quit && $this->quit();
	}

	/**
	 * ajax输出
	 * @param number $code
	 * @param string $message
	 * @param array $data
	 */
	public function ajax($code, $message = '', array $data = array()) {
		echo json_encode(array('code' => $code, 'message' => $message, 'data' => $data));
		$this->quit(0);
	}

	/**
	 * 停止继续执行
	 * @param number $code
	 */
	public function quit($code = 0) {
		throw new QuitException($code);
	}
}