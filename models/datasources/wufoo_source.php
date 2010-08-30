<?php
/**
 * Wufoo Source
 * DataSource for Wufoo
 * 
 * @author Kyle Robinson Young <kyle at kyletyoung.com>
 * @copyright 2010 Kyle Robinson Young
 * 
 */
class WufooSource extends DataSource {
	
	/**
	 * DESCRIPTION
	 * @var str
	 */
	public $description = 'Wufoo DataSource';
	
	/**
	 * CONFIG
	 * @var array
	 */
	public $config = array(
		'username'		=> '',
		'api_secret'	=> '',
		'version'		=> 'v3',
	);
	
	/**
	 * http
	 * @var object
	 */
	public $http = null;
	
	/**
	 * url
	 * @var str
	 */
	public $url = null;
	
	/**
	 * _errors
	 * @var array
	 */
	protected $_errors = array();
	
	/**
	 * __construct
	 * @param array $config
	 */
	public function __construct($config) {
		$this->init($config);
		parent::__construct($config);
	}
	
	/**
	 * init
	 * @param array $config
	 * @return bool
	 */
	public function init($config=null) {
		$this->config = array_merge($this->config, (array)$config);
		if (!class_exists('HttpSocket')) {
			App::import('Core', 'HttpSocket');
		}
		$this->http = new HttpSocket();
		$this->url = 'https://'.$this->config['username'].'.wufoo.com/api/'.$this->config['version'].'/';
		return true;
	}
	
	/**
	 * findForms
	 * @param str $form
	 * @return array
	 */
	public function findForms($form=null) {
		if (isset($form)) {
			$out = $this->request("forms/$form");
		} else {
			$out = $this->request("forms");
		}
		if (!isset($out['Forms']['Form'])) {
			$this->_errors[] = "No forms were found.";
			return array();
		}
		return $out['Forms']['Form'];
	}
	
	/**
	 * findFields
	 * @param str $form
	 * @return array
	 */
	public function findFields($form=null) {
		if (!isset($form)) {
			return array();
		}
		$out = $this->request("forms/$form/fields");
		if (!isset($out['Fields']['Field'])) {
			$this->_errors[] = "No fields were found.";
			return array();
		}
		return $out['Fields']['Field'];
	}
	
	/**
	 * findEntries
	 * @param str $form
	 * @param str $params
	 * @return array
	 */
	public function findEntries($form=null, $params=null) {
		if (!isset($form)) {
			return array();
		}
		$out = $this->request("forms/$form/entries", array('params' => $params));
		if (!isset($out['Entries']['Entry'])) {
			$this->_errors[] = "No entries were found.";
			return array();
		}
		return $out['Entries']['Entry'];
	}
	
	/**
	 * saveEntry
	 * @param str $form
	 * @param array $data
	 * @return EntryId | false
	 */
	public function saveEntry($form=null, $data=null) {
		if (!isset($form)) {
			return false;
		}
		$url = $this->url."forms/$form/entries.xml";
		$res = $this->http->post($url, $data, $this->__getAuthArray());
		$out = $this->__xmlToArray($res);
		return (!empty($out['PostResponse']['Success'])) ? $out['PostResponse']['EntryId'] : false;
	}
	
	/**
	 * findUsers
	 * @return array
	 */
	public function findUsers() {
		$out = $this->request("users");
		if (!isset($out['Users']['User'])) {
			$this->_errors[] = "No users were found.";
			return array();
		}
		return $out['Users']['User'];
	}
	
	/**
	 * findReports
	 * @param str $report
	 * @return array
	 */
	public function findReports($report=null) {
		if (isset($report)) {
			$out = $this->request("reports/$report");
		} else {
			$out = $this->request("reports");
		}
		if (!isset($out['Reports']['Report'])) {
			$this->_errors[] = "No reports were found.";
			return array();
		}
		return $out['Reports']['Report'];
	}
	
	/**
	 * findWidgets
	 * @param str $report
	 * @return array
	 */
	public function findWidgets($report=null) {
		if (!isset($report)) {
			return array();
		}
		$out = $this->request("reports/$report/widgets");
		if (!isset($out['Widgets']['Widget'])) {
			$this->_errors[] = "No widgets were found.";
			return array();
		}
		return $out['Widgets']['Widget'];
	}
	
	/**
	 * findComments
	 * @param str $form
	 * @return array
	 */
	public function findComments($form=null) {
		if (!isset($form)) {
			return array();
		}
		$out = $this->request("forms/$form/comments");
		if (!isset($out['Comments']['Comment'])) {
			$this->_errors[] = "No comments were found.";
			return array();
		}
		return $out['Comments']['Comment'];
	}
	
	/**
	 * saveWebHook
	 * @param str $form
	 * @param array $data
	 */
	public function saveWebHook($form=null, $data=null) {
		if (!isset($form)) {
			return false;
		}
		$url = $this->url."forms/$form/webhooks.xml";
		$res = $this->http->post($url, $data, array_merge($this->__getAuthArray(), array('method' => 'PUT')));
		$out = $this->__xmlToArray($res, false);
		return (!empty($out['WebHookPutResult']['Hash'])) ? $out['WebHookPutResult']['Hash'] : false;
	}
	
	/**
	 * deleteWebHook
	 * @param str $form
	 * @param str $hash
	 * @return Hash | false
	 */
	public function deleteWebHook($form=null, $hash=null) {
		if (!isset($form) || !isset($hash)) {
			return false;
		}
		$url = $this->url."forms/$form/webhooks/$hash.xml";
		$res = $this->http->post(
			$url, 
			array('hash' => $hash),
			array_merge($this->__getAuthArray(), array('method' => 'DELETE'))
		);
		$out = $this->__xmlToArray($res);
		return (!empty($out['WebHookDeleteResult']['Hash'])) ? $out['WebHookDeleteResult']['Hash'] : false;
	}
	
	/**
	 * login
	 * @param array $data
	 * @return array
	 */
	public function login($data=null) {
		if (!isset($data)) {
			return false;
		}
		$url = "https://wufoo.com/api/{$this->config['version']}/login.xml";
		$res = $this->http->post($url, $data, $this->__getAuthArray());
		return $this->__xmlToArray($res, false);
	}
	
	/**
	 * request
	 * @param str $type
	 * @param array $options
	 * 	output [xml|json]
	 * 	raw [true|false]
	 * 	params array
	 * @return mixed
	 */
	public function request($type='forms', $options=array()) {
		$options = array_merge(array(
			'output' => 'xml',
			'raw' => false,
		), $options);
		$url = $this->url.$type.'.'.$options['output'];
		if (isset($options['params'])) {
			if (is_array($options['params'])) {
				$url .= "?".$this->__parseParams($options['params']);
			} else {
				$url .= "?".$options['params'];
			}
		}
		$res = $this->http->get($url, null, $this->__getAuthArray());
		switch ($options['output']) {
			// JSON
			case "json":
				if (substr($res, 0, 1) != "{") {
					$this->_errors[] = $res;
					return array();
				}
				if ($options['raw']) {
					return $res;
				} else {
					return Set::reverse(json_decode($res));
				}
			// XML
			case "xml":
				if ($options['raw']) {
					return $res;
				} else {
					return $this->__xmlToArray($res);
				}
		}
		return array();
	}
	
	/**
	 * query
	 * Provides an interface to methods.
	 * @param str $query
	 * @param array $data
	 * @return mixed
	 */
	public function query($query=null, $data=null) {
		return call_user_func_array(array($this, $query), $data);
	}
	
	/**
	 * LIST SOURCES
	 */
	public function listSources() {
		return false;
	}
	
	/**
	 * describe
	 * @param obj $model
	 */
	public function describe(&$model) {
		return array('id' => array());
	}
	
	/**
	* calculate
	*
	* @param Model $model
	* @param mixed $func
	* @param array $params
	* @return array
	* @access public
	*/
	public function calculate(&$model, $func, $params=array()) {
		return array('count' => true);
	}
	
	/**
	 * errors
	 * Returns any errors.
	 */
	public function errors() {
		return $this->_errors;
	}
	
	/**
	 * __getAuthArray
	 * @return array
	 */
	private function __getAuthArray() {
		return array(
			'auth' => array(
				'method' => 'Basic',
				'user' => $this->config['api_key'],
				'pass' => 'footastic',
			),
		);
	}
	
	/**
	 * __parseParams
	 * @param array $params
	 * @return str
	 */
	private function __parseParams($params=array()) {
		$out = '';
		// CONDITIONS
		if (!empty($params['conditions'])) {
			$i = 1;
			foreach ($params['conditions'] as $key => $val) {
				if (is_array($val)) {
					foreach ($val as $operator => $v) {
						$out .= 'Filter'.$i++.'='.$key."+".$operator."+".$v."&";
					}
				} else {
					$out .= 'Filter'.$i++.'='.$key."+Contains+".$val."&";
				}
			}
		}
		// ORDER
		if (isset($params['order'])) {
			$e = explode(" ", $params['order']);
			if (!empty($e[0])) {
				$e[1] = (empty($e[1])) ? 'ASC' : $e[1];
				$out .= "sort=".$e[0]."&sortDirection=".$e[1]."&";
			}
		}
		// PAGE
		if (isset($params['page'])) {
			$out .= "pageStart=".$params['page']."&";
		}
		// LIMIT
		if (isset($params['limit'])) {
			$out .= "pageSize=".$params['limit']."&";
		}
		return $out;
	}
	
	/**
	 * __xmlToArray
	 * @param str $xml
	 * @param bool $ifXML
	 * @return array
	 */
	private function __xmlToArray($xml=null, $ifXML=true) {
		if (strpos($xml, "<?xml") === false && $ifXML) {
			$this->_errors[] = $xml;
			return array();
		}
		if (!class_exists('Xml')) {
			App::import('Core', 'Xml');
		}
		$xml = new Xml($xml);
		return $xml->toArray();
	}
	
} // WufooSource
?>