<?php

/**
 * 视图
 * @author wclssdn <ssdn@vip.qq.com>
 *
 */
class View{

	/**
	 * 模板文件
	 * @var string
	 */
	private $templateFile;

	/**
	 * 模板路径
	 * @var string
	 */
	private $templatePath = PATH_VIEW;

	/**
	 * 模板中可访问变量
	 * @var array
	 */
	private $vars = array();

	/**
	 * 获取模板文件
	 * @return string
	 */
	public function getTemplateFile(){
		return $this->templateFile;
	}

	/**
	 * 设置模板文件
	 * @param string $templateFile 模板文件名
	 * @return View
	 */
	public function setTemplateFile($templateFile){
		$this->templateFile = $templateFile;
	}

	/**
	 * 获取模板路径
	 * @return string
	 */
	public function getTemplatePath(){
		return $this->templatePath;
	}

	/**
	 * 设置模板路径
	 * @param string $templatePath 模板路径
	 * @return View
	 */
	public function setTemplatePath($templatePath){
		$templatePath = strrpos($templatePath, '/') === strlen($templatePath) - 1 ? $templatePath : $templatePath . '/';
		$this->templatePath = $templatePath;
	}

	/**
	 * 为模板中可使用变量赋值
	 * @param string $key
	 * @param mixed $value
	 * @return View
	 */
	public function assign($key, $value){
		$this->vars[$key] = $value;
	}

	/**
	 * 获取模板变量
	 * @param string $key
	 * @return Ambigous <NULL, multitype:>
	 */
	public function get($key){
		return isset($this->vars[$key]) ? $this->vars[$key] : null;
	}

	/**
	 * 小部件
	 * @param string $widget 部件模板文件, 相对于PATH_VIEW, 可使用路径.例如widget/demo
	 * @param array $args 部件参数数组 键值对.部件内使用$键 访问参数数据.可覆盖之前模板assign过的变量
	 */
	public function widget($widget, array $args = array()){
		extract($this->vars);
		!empty($args) && extract($args);
		include PATH_VIEW . $widget . '.tpl.php';
	}

	/**
	 * 解析并显示模板
	 * @param string $templatePath 模板绝对路径
	 * @param string $templateFile 模板文件名称
	 * @throws Exception
	 */
	public function display($templatePath = '', $templateFile = ''){
		$templatePath && $this->setTemplatePath($templatePath);
		$templateFile && $this->setTemplateFile($templateFile);
		$templateFile = $this->templatePath . $this->templateFile;
		extract($this->vars);
		if (file_exists($templateFile)){
			include $templateFile;
		}else{
			throw new Exception("templateFile:{$templateFile} not found! templatePath:{$this->templatePath} templateFile:{$this->templateFile}");
		}
	}

	/**
	 * 解析模板并返回结果
	 * @param string $templatePath 模板绝对路径
	 * @param string $templateFile 模板文件名称
	 * @param boolean $deleteSpace 是否删除空白字符
	 * @throws Exception
	 */
	public function fetch($templatePath = '', $templateFile = '', $deleteSpace = true){
		$templatePath && $this->setTemplatePath($templatePath);
		$templateFile && $this->setTemplateFile($templateFile);
		$templateFile = $this->templatePath . $this->templateFile;
		extract($this->vars);
		if (file_exists($templateFile)){
			ob_end_clean();
			ob_start();
			include $templateFile;
			$result = ob_get_clean();
			ob_start();
			return $deleteSpace ? str_replace(array("\t", "\r", "\n"), '', $result) : $result;
		}else{
			throw new Exception("templateFile:{$templateFile} not found! templatePath:{$this->templatePath} templateFile:{$this->templateFile}");
		}
	}

	/**
	 * ajax方式输出
	 * @param number $code 成功为0
	 * @param string $message 提示消息
	 * @param array $data 数据数组
	 */
	public function ajaxDisplay($code, $message = '', array $data = array()){
		$result = array();
		$result['code'] = intval($code);
		$result['message'] = $message;
		$result['data'] = $data ? $data : ($code == CODE_OK ? $this->vars : array());
		echo json_encode($result);
	}
}