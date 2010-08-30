<?php
/**
 * Wufoo Helper
 * @author Kyle Robinson Young <kyle at kyletyoung.com>
 * @copyright 2010 Kyle Robinson Young
 *
 */
class WufooHelper extends AppHelper {
	var $helpers = array('Html');
	
	/**
	 * __defaults
	 * @var array
	 */
	var $__defaults = array(
		'autoResize' => true,
		'title' => 'Wufoo Form',
		'noFrames' => 'Fill out my Wufoo form!',
		'height' => '590',
	);
	
	/**
	 * __options
	 * @var array
	 */
	var $__options = null;
	
	/**
	 * __construct
	 * @param array $options
	 */
	function __construct($options=null) {
		parent::__construct($options);
		$this->__defaults = array_merge($this->__defaults, (array)$options);
	}
	
	/**
	 * embed
	 * Embed a Wufoo form using javascript.
	 * @param str $form
	 * @param array $options
	 * @param array $script
	 * @return str
	 */
	function embed($form=null, $options=array(), $script=array()) {
		$options = $this->__defaultOptions($options);
		$script = array_merge(array(
			'inline' => true,
		), $script);
		$out = '';
		$out .= $this->Html->scriptBlock('var host = (("https:" == document.location.protocol) ? "https://secure." : "http://");document.write(unescape("%3Cscript src=\'" + host + "wufoo.com/scripts/embed/form.js\' type=\'text/javascript\'%3E%3C/script%3E"));', $script);
		$name = 'a'.date("Ymdhis");
		$scriptBlock = <<<EOD
var {$name} = new WufooForm();
{$name}.initialize({
	'userName':'{$options['username']}', 
	'formHash':'{$form}', 
	'autoResize':{$options['autoResize']},
	'height':'{$options['height']}'});
{$name}.display();
EOD;
		$out .= $this->Html->scriptBlock($scriptBlock, $script);
		return $this->output($out);
	}
	
	/**
	 * iframe
	 * Embed a Wufoo form using an iframe.
	 * @param str $form
	 * @param array $options
	 * @param array $iframe
	 * @return str
	 */
	function iframe($form=null, $options=array(), $iframe=array()) {
		$options = $this->__defaultOptions($options);
		$iframe = array_merge(array(
			'height' => 590,
			'style' => 'width:100%;border:none;',
			'scrolling' => 'no',
			'frameborder' => '0',
			'allowTransparency' => 'true',
			'src' => "http://{$options['username']}.wufoo.com/embed/{$form}/",
		), $iframe);
		$out = $this->Html->tag(
			'iframe', 
			$this->Html->link(
				$options['noFrames'], 
				"http://{$options['username']}.wufoo.com/embed/{$form}/", 
				array('title' => $options['title'], 'rel' => 'nofollow')
			),
			$iframe
		);
		return $this->output($out);
		
	}
	
	/**
	 * __defaultOptions
	 * Attempts to auto get Wufoo username and defaults options.
	 * @param array $options
	 * @return array
	 */
	function __defaultOptions($options=array()) {
		if (!isset($this->__options)) {
			if (empty($options['username'])) {
				if (!class_exists('ConnectionManager')) {
					App::Import('ConnectionManager');
				}
				$cm = new ConnectionManager();
				foreach (Set::reverse($cm->config) as $ds) {
					if (!isset($ds['datasource'])) {
						continue;
					}
					if (strpos($ds['datasource'], 'wufoo') !== false && !empty($ds['username'])) {
						$options['username'] = $ds['username'];
						break;
					}
				}
			}
			$this->__options = array_merge($this->__defaults, (array)$options);
		}
		return $this->__options;
	}
}
?>