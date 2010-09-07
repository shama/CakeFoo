<?php
App::import('Helper', array('Wufoo.Wufoo', 'Html'));
class WufooTest extends CakeTestCase {
/**
 * Wufoo property
 *
 * @var object
 * @access public
 */
	public $Wufoo = null;
/**
 * _testLoginData
 * 
 * @var array
 * @access public
 */
	public $testLoginData = array(
		'username' => 'fishbowl',
		'api_key' => '1234',
	);
/**
 * Regexp for CDATA start block
 *
 * @var string
 * @access public
 */
	public $cDataStart = 'preg:/^\/\/<!\[CDATA\[[\n\r]*/';
/**
 * Regexp for CDATA end block
 *
 * @var string
 * @access public
 */
	public $cDataEnd = 'preg:/[^\]]*\]\]\>[\s\r\n]*/';

/**
 * setUp method
 *
 * @access public
 * @return void
 */
	public function startTest() {
		$this->Wufoo = new WufooHelper($this->testLoginData);
		$this->Wufoo->Html = new HtmlHelper();
	}

/**
 * testEmbed method
 *
 * @access public
 * @return void
 */
	public function testEmbed() {
		// TEST BASIC JS EMBED
		$result = $this->Wufoo->embed('test-form', $this->testLoginData);
		$expected = array(
			array('script' => array('type' => 'text/javascript')),
			$this->cDataStart,
			// JS TO WUFOO HERE
			$this->cDataEnd,
			'/script',
			array('script' => array('type' => 'text/javascript')),
			$this->cDataStart,
			'preg:/var a[0-9]+ \= new WufooForm\(\);[\n\r]*/',
			'preg:/a[0-9]+\.initialize\(\{[\n\r]*/',
			'preg:/[\s]*\'userName\'\:\'fishbowl\'\,[\n\r]*/',
			'preg:/[\s]*\'formHash\'\:\'test-form\'\,[\n\r]*/',
			'preg:/[\s]*\'autoResize\'\:1\,[\n\r]*/',
			'preg:/[\s]*\'height\'\:\'590\'\}\);[\n\r]*/',
			'preg:/a[0-9]+\.display\(\);[\n\r]*/',
			$this->cDataEnd,
			'/script',
		);
		$this->assertTags($result, $expected);
		
		// TEST JS EMBED WITH OPTIONS
		$this->Wufoo->__options = null;
		$result = $this->Wufoo->embed('test-form', 
			array_merge($this->testLoginData, array(
				'height' => 300,
				'autoResize' => 0,
			)),
			array(
				'encoding' => 'utf-8',
			)
		);
		$expected = array(
			array('script' => array('type' => 'text/javascript', 'encoding' => 'utf-8')),
			$this->cDataStart,
			// JS TO WUFOO HERE
			$this->cDataEnd,
			'/script',
			array('script' => array('type' => 'text/javascript', 'encoding' => 'utf-8')),
			$this->cDataStart,
			'preg:/var a[0-9]+ \= new WufooForm\(\);[\n\r]*/',
			'preg:/a[0-9]+\.initialize\(\{[\n\r]*/',
			'preg:/[\s]*\'userName\'\:\'fishbowl\'\,[\n\r]*/',
			'preg:/[\s]*\'formHash\'\:\'test-form\'\,[\n\r]*/',
			'preg:/[\s]*\'autoResize\'\:0\,[\n\r]*/',
			'preg:/[\s]*\'height\'\:\'300\'\}\);[\n\r]*/',
			'preg:/a[0-9]+\.display\(\);[\n\r]*/',
			$this->cDataEnd,
			'/script',
		);
		$this->assertTags($result, $expected);
	}
	
/**
 * testIframe method
 *
 * @access public
 * @return void
 */
	public function testIframe() {
		// TEST BASIC IFRAME EMBED
		$result = $this->Wufoo->iframe('test-form', $this->testLoginData);
		$expected = array(
			array('iframe' => array(
				'height' => '590',
				'style' => 'width:100%;border:none;',
				'scrolling' => 'no',
				'frameborder' => '0',
				'allowTransparency' => 'true',
				'src' => 'http://fishbowl.wufoo.com/embed/test-form/'
			)),
			array('a' => array(
				'href' => 'http://fishbowl.wufoo.com/embed/test-form/',
				'title' => 'Wufoo Form',
				'rel' => 'nofollow',
			)),
			'Fill out my Wufoo form!',
			'/a',
			'/iframe',
		);
		$this->assertTags($result, $expected);
	}
}