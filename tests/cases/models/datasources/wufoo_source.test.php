<?php
App::import('DataSource', 'Wufoo.WufooSource');
App::import('Core', array('HttpSocket', 'Xml'));
App::import('Helper', 'Xml');
Mock::generate('HttpSocket');

class WufooSourceTest extends CakeTestCase {
/**
 * Wufoo
 *
 * @var object
 * @access public
 */
	public $Wufoo = null;
/**
 * testLoginData
 * @var array
 * @access public
 */
	public $testLoginData = array(
		'username' => 'fishbowl',
		'api_key' => '1234',
	);
	
/**
 * Xml
 * @var object
 * @access public
 */
	public $Xml = null;

/**
 * expectedXml
 * @var array
 * @access public
 */
	public $expectedXml = array(
		'Name' => 'Test',
		'Description' => 'Test',
	);

/**
 * setUp method
 *
 * @access public
 * @return void
 */
	public function startTest() {
		$this->Wufoo =& new WufooSource($this->testLoginData);
		$this->Wufoo->http =& new MockHttpSocket();
		$this->Xml = new XmlHelper();
	}

/**
 * testFinds
 */
	public function testFinds() {
		
		$url_base = $this->Wufoo->url;
		
		$this->Wufoo->http->setReturnValue('get', $this->__buildXml('Form'), array($url_base.'forms.xml', null, $this->__getAuthArray()));
		$this->assertEqual($this->Wufoo->findForms(), $this->expectedXml);
		
		$this->Wufoo->http->setReturnValue('get', $this->__buildXml('Field'), array($url_base.'forms/test-form/fields.xml', null, $this->__getAuthArray()));
		$this->assertEqual($this->Wufoo->findFields('test-form'), $this->expectedXml);
		
		$this->Wufoo->http->setReturnValue('get', $this->__buildXml('Entry'), array($url_base.'forms/test-form/entries.xml', null, $this->__getAuthArray()));
		$this->assertEqual($this->Wufoo->findEntries('test-form'), $this->expectedXml);
		
		$this->Wufoo->http->setReturnValue('get', $this->__buildXml('User'), array($url_base.'users.xml', null, $this->__getAuthArray()));
		$this->assertEqual($this->Wufoo->findUsers(), $this->expectedXml);
		
		$this->Wufoo->http->setReturnValue('get', $this->__buildXml('Report'), array($url_base.'reports.xml', null, $this->__getAuthArray()));
		$this->assertEqual($this->Wufoo->findReports(), $this->expectedXml);
		
		$this->Wufoo->http->setReturnValue('get', $this->__buildXml('Widget'), array($url_base.'reports/test-report/widgets.xml', null, $this->__getAuthArray()));
		$this->assertEqual($this->Wufoo->findWidgets('test-report'), $this->expectedXml);
		
		$this->Wufoo->http->setReturnValue('get', $this->__buildXml('Comment'), array($url_base.'forms/test-form/comments.xml', null, $this->__getAuthArray()));
		$this->assertEqual($this->Wufoo->findComments('test-form'), $this->expectedXml);
	}

/**
 * testSaveEntry
 */
	public function testSaveEntry() {
		
		$url_base = $this->Wufoo->url;
		
		$data = array(
			'Field1' => 'Test',
			'Field2' => 'Test Again',
		);
		$expected = array(
			'PostResponse' => array(
				'Success' => '1',
				'EntryId' => '11',
				'EntryLink' => 'http://fishbowl.wufoo.com/api/v3/forms/myform/entries.xml?Filter1=EntryId+Is_equal_to+11',
			),
		);
		$xml = $this->Xml->header().$this->Xml->serialize($expected, array('format' => 'tags'));
		$this->Wufoo->http->setReturnValue('post', $xml, array($url_base.'forms/test-form/entries.xml', $data, $this->__getAuthArray()));
		$this->assertEqual($this->Wufoo->saveEntry('test-form', $data), $expected['PostResponse']['EntryId']);
	}
	
/**
 * testWebHooks
 */
	public function testWebHooks() {
		
		$url_base = $this->Wufoo->url;
		
		$data = array(
			'url' => 'http://example.com',
		);
		$expected = array(
			'WebHookPutResult' => array(
				'Hash' => '1234',
			),
		);
		$xml = $this->Xml->header().$this->Xml->serialize($expected, array('format' => 'tags'));
		$this->Wufoo->http->setReturnValue('post', $xml, array(
			$url_base.'forms/test-form/webhooks.xml',
			$data,
			array_merge($this->__getAuthArray(), array('method' => 'PUT'))
		));
		$this->assertEqual($this->Wufoo->saveWebHook('test-form', $data), $expected['WebHookPutResult']['Hash']);
		
		$expected = array(
			'WebHookDeleteResult' => array(
				'Hash' => '1234',
			),
		);
		$xml = $this->Xml->header().$this->Xml->serialize($expected, array('format' => 'tags'));
		$this->Wufoo->http->setReturnValue('post', $xml, array(
			$url_base.'forms/test-form/webhooks/1234.xml',
			array('hash' => '1234'),
			array_merge($this->__getAuthArray(), array('method' => 'DELETE'))
		));
		$this->assertEqual($this->Wufoo->deleteWebHook('test-form', '1234'), $expected['WebHookDeleteResult']['Hash']);
	}
	
/**
 * testLogin
 */
	public function testLogin() {
		
		$url = 'https://wufoo.com/api/'.$this->Wufoo->config['version'].'/login.xml';
		
		$data = array(
			'integrationKey' => '1234',
			'email' => 'test@example.com',
			'password' => '1234',
			'subdomain' => 'fishbowl',
		);
		$expected = array(
			'LoginResult' => array(
				'ApiKey' => '1234',
				'UserLink' => 'https://fishbowl.wufoo.com/api/v3/users/n7g3z1.xml',
				'Subdomain' => 'fishbowl',
			),
		);
		$xml = $this->Xml->header().$this->Xml->serialize($expected, array('format' => 'tags'));
		$this->Wufoo->http->setReturnValue('post', $xml, array($url, $data, $this->__getAuthArray()));
		$this->assertEqual($this->Wufoo->login($data), $expected);
	}

/**
 * __buildXml
 * @param string $name
 * @access private
 */
	private function __buildXml($name=null) {
		return $this->Xml->header().$this->Xml->serialize(
			array(Inflector::pluralize($name) => array($name => $this->expectedXml)),
			array('format' => 'tags')
		);
	}
	
/**
 * __getAuthArray
 * @return array
 * @access private
 */
	private function __getAuthArray() {
		return array(
			'auth' => array(
				'method' => 'Basic',
				'user' => $this->Wufoo->config['api_key'],
				'pass' => 'footastic',
			),
		);
	}

}