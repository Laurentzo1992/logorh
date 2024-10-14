<?php
class HtmlHelper extends Helper{
	protected $_tags = array(
		'meta' => '<meta%s/>',
		'metalink' => '<link href="%s"%s/>',
		'link' => '<a href="%s"%s>%s</a>',
		'mailto' => '<a href="mailto:%s" %s>%s</a>',
		'form' => '<form action="%s"%s>',
		'formend' => '</form>',
		'input' => '<input name="%s"%s/>',
		'textarea' => '<textarea name="%s"%s>%s</textarea>',
		'hidden' => '<input type="hidden" name="%s"%s/>',
		'checkbox' => '<input type="checkbox" name="%s" %s/>',
		'checkboxmultiple' => '<input type="checkbox" name="%s[]"%s />',
		'radio' => '<input type="radio" name="%s" id="%s"%s />%s',
		'selectstart' => '<select name="%s"%s>',
		'selectmultiplestart' => '<select name="%s[]"%s>',
		'selectempty' => '<option value=""%s>&nbsp;</option>',
		'selectoption' => '<option value="%s"%s>%s</option>',
		'selectend' => '</select>',
		'optiongroup' => '<optgroup label="%s"%s>',
		'optiongroupend' => '</optgroup>',
		'checkboxmultiplestart' => '',
		'checkboxmultipleend' => '',
		'password' => '<input type="password" name="%s" %s/>',
		'file' => '<input type="file" name="%s" %s/>',
		'file_no_model' => '<input type="file" name="%s" %s/>',
		'submit' => '<input %s/>',
		'submitimage' => '<input type="image" src="%s" %s/>',
		'button' => '<button%s>%s</button>',
		'image' => '<img src="%s" %s/>',
		'tableheader' => '<th%s>%s</th>',
		'tableheaderrow' => '<tr%s>%s</tr>',
		'tablecell' => '<td%s>%s</td>',
		'tablerow' => '<tr%s>%s</tr>',
		'block' => '<div%s>%s</div>',
		'blockstart' => '<div%s>',
		'blockend' => '</div>',
		'hiddenblock' => '<div style="display:none;">%s</div>',
		'tag' => '<%s%s>%s</%s>',
		'tagstart' => '<%s%s>',
		'tagend' => '</%s>',
		'tagselfclosing' => '<%s%s/>',
		'para' => '<p%s>%s</p>',
		'parastart' => '<p%s>',
		'label' => '<label for="%s"%s>%s</label>',
		'fieldset' => '<fieldset%s>%s</fieldset>',
		'fieldsetstart' => '<fieldset><legend>%s</legend>',
		'fieldsetend' => '</fieldset>',
		'legend' => '<legend>%s</legend>',
		'css' => '<link rel="%s" type="text/css" href="%s" %s/>',
		'style' => '<style type="text/css"%s>%s</style>',
		'charset' => '<meta http-equiv="Content-Type" content="text/html; charset=%s" />',
		'ul' => '<ul%s>%s</ul>',
		'ol' => '<ol%s>%s</ol>',
		'li' => '<li%s>%s</li>',
		'error' => '<div%s>%s</div>',
		'javascriptblock' => '<script type="text/javascript"%s>%s</script>',
		'javascriptstart' => '<script type="text/javascript">',
		'javascriptlink' => '<script type="text/javascript" src="%s"%s></script>',
		'javascriptend' => '</script>'
	);
	protected $_minimizedAttributes = array(
		'compact', 'checked', 'declare', 'readonly', 'disabled', 'selected',
		'defer', 'ismap', 'nohref', 'noshade', 'nowrap', 'multiple', 'noresize',
		'autoplay', 'controls', 'loop', 'muted', 'required', 'novalidate', 'formnovalidate'
	);
	
	protected $_attributeFormat = '%s="%s"';
	protected $_minimizedAttributeFormat = '%s="%s"';
	
	protected $_docTypes = array(
		'html4-strict' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
		'html4-trans' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
		'html4-frame' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
		'html5' => '<!DOCTYPE html>',
		'xhtml-strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
		'xhtml-trans' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
		'xhtml-frame' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
		'xhtml11' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'
	);
	
	public $helpers = array();
	public $urlRoot = '';
	public $urlBase = '';
	
	function __construct($view = null, $options = array()) {
		parent::__construct($view, $options);
		$this->urlRoot = $this->view->controller->requestHandler->getUrlRoot();
		$this->urlBase = $this->view->controller->requestHandler->getUrlRoot(true);
	}
	
	public function docType($type = 'xhtml-trans') {
		if (isset($this->_docTypes[$type])) {
			return $this->_docTypes[$type];
		}
		return null;
	}
	
	public function getUrl($type = 'base') {
		$params = $this->view->controller->requestHandler->getParams();
		$params = $params['p'];
		if($type == 'base'){
			return $this->urlBase;
		}elseif ($type == 'url'){
			return $this->urlBase . substr(get_class($this->view->controller), 0, -10) . '/' . $this->view->controller->view;
		}elseif ($type == 'full'){
			return $this->urlBase . substr(get_class($this->view->controller), 0, -10) . '/' . $this->view->controller->view . '/' . implode('/', $params);
		}
		
	}
	
	public function getReturnUrl() {	
		$params = $this->view->controller->requestHandler->getParams();
		return "{$params['model']}/{$params['view']}/" . implode('/', $params['p']);
		
	}
	
	public function meta($type, $url = null, $options = array()) {

		if (!is_array($type)) {
			$types = array(
				'rss' => array('type' => 'application/rss+xml', 'rel' => 'alternate', 'title' => $type, 'link' => $url),
				'atom' => array('type' => 'application/atom+xml', 'title' => $type, 'link' => $url),
				'icon' => array('type' => 'image/x-icon', 'rel' => 'icon', 'link' => $url),
				'keywords' => array('name' => 'keywords', 'content' => $url),
				'description' => array('name' => 'description', 'content' => $url),
			);

			if ($type === 'icon' && $url === null) {
				$types['icon']['link'] = $this->urlRoot . 'app/boot/favicon.ico';
			}

			if (isset($types[$type])) {
				$type = $types[$type];
			} elseif (!isset($options['type']) && $url !== null) {
				if (is_array($url) && isset($url['ext'])) {
					$type = $types[$url['ext']];
				} else {
					$type = $types['rss'];
				}
			} elseif (isset($options['type']) && isset($types[$options['type']])) {
				$type = $types[$options['type']];
				unset($options['type']);
			} else {
				$type = array();
			}
		}

		$options = array_merge($type, $options);
		$out = null;

		if (isset($options['link'])) {
			if (isset($options['rel']) && $options['rel'] === 'icon') {
				$out = sprintf($this->_tags['metalink'], $options['link'], $this->_parseAttributes($options, array('block', 'link'), ' ', ' '));
				$options['rel'] = 'shortcut icon';
			} else {
				$options['link'] = $this->url($options['link'], true);
			}
			$out .= sprintf($this->_tags['metalink'], $options['link'], $this->_parseAttributes($options, array('block', 'link'), ' ', ' '));
		} else {
			$out = sprintf($this->_tags['meta'], $this->_parseAttributes($options, array('block', 'type'), ' ', ' '));
		}
		return $out;
	}
	
	public function charset($charset = null) {
		return sprintf($this->_tags['charset'], (!empty($charset) ? $charset : 'utf-8'));
	}
	
	public function linkReplaceParam($title, $url = null, $options = array(), $confirmMessage = false){
		$requestData = $this->view->controller->requestHandler->getParams();
		$requestData = $requestData['p'];
		if(isset($url['params'])){
			foreach ($url['params'] as $param){
				$parts = explode(':', $param);
				if(count($parts)==2){
					foreach ($requestData as $pos=>$rq){
						$prts = explode(':', $rq);
						if(count($prts)==2 && $parts[0]==$prts[0]){
							unset($requestData[$pos]);
						}
					}
				}
			}
			$url['params'] = array_merge($requestData, $url['params']);
			return $this->link($title, $url, $options, $confirmMessage);
		}
		return null;
	}
	
	public function link($title, $url = null, $options = array(), $confirmMessage = false){
		$escapeTitle = true;
		if ($url !== null) {
			if(is_array($url)){
				$tempUrl = $this->urlBase;
				if(array_key_exists('controller', $url)){
					$tempUrl .= $url['controller'];
				}else{
					$tempUrl .= substr(get_class($this->view->controller), 0, -10);
				}
				if(array_key_exists('view', $url)){
					$tempUrl .=  '/' . $url['view'];
				}else{
					$tempUrl .=  '/' . $this->view->controller->view;
				}
				$params = '';
				if(array_key_exists('params', $url)){			
					foreach ($url['params'] as $param){
						$params .= '/' . $param;
					}
				}
				$url = $tempUrl . $params;
			}
			if(array_key_exists('fullBase', $options)){
				$base = 'http://' . $_SERVER['SERVER_NAME'] . $this->urlRoot;
				$url = $base . $url;
			}
			$options = array_diff_key($options, array('fullBase' => '', 'pathPrefix' => ''));
		} else {
			$url = $this->url($title);
			$title = htmlspecialchars_decode($url, ENT_QUOTES);
			$title = $this->h(urldecode($title));
			$escapeTitle = false;
		}
		
		if (isset($options['escape'])) {
			$escapeTitle = $options['escape'];
		}

		if ($escapeTitle === true) {
			$title = $this->h($title);
		} elseif (is_string($escapeTitle)) {
			$title = htmlentities($title, ENT_QUOTES, $escapeTitle);
		}

		if (!empty($options['confirm'])) {
			$confirmMessage = $options['confirm'];
			unset($options['confirm']);
		}
		if ($confirmMessage) {
			$confirmMessage = str_replace("'", "\'", $confirmMessage);
			$confirmMessage = str_replace('"', '\"', $confirmMessage);
			$options['onclick'] = "return confirm('{$confirmMessage}');";
		} elseif (isset($options['default']) && !$options['default']) {
			if (isset($options['onclick'])) {
				$options['onclick'] .= ' event.returnValue = false; return false;';
			} else {
				$options['onclick'] = 'event.returnValue = false; return false;';
			}
			unset($options['default']);
		}
		return sprintf($this->_tags['link'], $url, $this->_parseAttributes($options), $title);
	
	}
	
	public function url($url, $options = array()){
		if ($url !== null) {
			if(is_array($url)){
				$tempUrl = $this->urlBase;
				if(array_key_exists('controller', $url)){
					$tempUrl .= $url['controller'];
				}else{
					$tempUrl .= get_class($this->view->controller);
				}
				if(array_key_exists('view', $url)){
					$tempUrl .=  '/' . $url['view'];
				}
				$params = '';
				if(array_key_exists('params', $url)){			
					foreach ($url['params'] as $param){
						$params .= '/' . $param;
					}
				}
				$url = $tempUrl . $params;
			}
			if(array_key_exists('fullBase', $options)){
				$base = 'http://' . $_SERVER['SERVER_NAME'] . $this->urlRoot;
				$url = $base . $url;
			}
			$options = array_diff_key($options, array('fullBase' => '', 'pathPrefix' => ''));
		}
		return $url;
	}
	
	public function css($path, $rel = null, $options = array()){
		if(is_array($path)){
			$out = '';
			foreach ($path as $i) {
				$out .= "\n\t" . $this->css($i, $rel, $options);
			}
			return $out . "\n";
		}
		if(strpos($path, 'http://')===false){
			$url =  $this->urlRoot . 'app/boot/css/' . $path . '.css';
		}
		if ($rel == 'import') {
			$out = sprintf($this->_tags['style'], $this->_parseAttributes($options, array('inline', 'block'), '', ' '), '@import url(' . $url . ');');
		} else {
			if (!$rel) {
				$rel = 'stylesheet';
			}
			$out = sprintf($this->_tags['css'], $rel, $url, $this->_parseAttributes($options, array('inline', 'block'), '', ' '));
		}
		return $out . "\n";
		
	}
	public function script($url, $options = array()) {
		
		if (is_array($url)) {
			$out = '';
			foreach ($url as $i) {
				$out .= "\n\t" . $this->script($i, $options);
			}
			return $out . "\n";
			
		}
		if(strpos($url, 'http://')===false){
			$url =  $this->urlRoot . 'app/boot/js/' . $url . '.js';
		}
		$attributes = $this->_parseAttributes($options, array('block', 'once'), ' ');
		$out = sprintf($this->_tags['javascriptlink'], $url, $attributes);

		if (empty($options['block'])) {
			return $out;
		} else {
			$this->_View->append($options['block'], $out);
		}
	}
	public function scriptBlock($script, $options = array()) {
		$options += array('safe' => true, 'inline' => true);
		if ($options['safe']) {
			$script = "\n" . '//<![CDATA[' . "\n" . $script . "\n" . '//]]>' . "\n";
		}
		
		unset($options['inline'], $options['safe']);

		$attributes = $this->_parseAttributes($options, array('block'), ' ');
		$out = sprintf($this->_tags['javascriptblock'], $attributes, $script);

		return $out;
	}
	
	public function tableHeaders($names, $trOptions = null, $thOptions = null) {
		$out = array();
		foreach ($names as $arg) {
			if (!is_array($arg)) {
				$out[] = sprintf($this->_tags['tableheader'], $this->_parseAttributes($thOptions), $arg);
			} else {
				$out[] = sprintf($this->_tags['tableheader'], $this->_parseAttributes(current($arg)), key($arg));
			}
		}
		return sprintf($this->_tags['tablerow'], $this->_parseAttributes($trOptions), implode(' ', $out));
	}
	
	public function tableCells($data, $oddTrOptions = null, $evenTrOptions = null, $useCount = false, $continueOddEven = true) {
		if (empty($data[0]) || !is_array($data[0])) {
			$data = array($data);
		}

		if ($oddTrOptions === true) {
			$useCount = true;
			$oddTrOptions = null;
		}

		if ($evenTrOptions === false) {
			$continueOddEven = false;
			$evenTrOptions = null;
		}

		if ($continueOddEven) {
			static $count = 0;
		} else {
			$count = 0;
		}

		foreach ($data as $line) {
			$count++;
			$cellsOut = array();
			$i = 0;
			foreach ($line as $cell) {
				$cellOptions = array();

				if (is_array($cell)) {
					$cellOptions = $cell[1];
					$cell = $cell[0];
				} elseif ($useCount) {
					$cellOptions['class'] = 'column-' . ++$i;
				}
				$cellsOut[] = sprintf($this->_tags['tablecell'], $this->_parseAttributes($cellOptions), $cell);
			}
			$options = $this->_parseAttributes($count % 2 ? $oddTrOptions : $evenTrOptions);
			$out[] = sprintf($this->_tags['tablerow'], $options, implode(' ', $cellsOut));
		}
		return implode("\n", $out);
	}	
	
	public function nestedList($list, $options = array(), $itemOptions = array(), $tag = 'ul') {
		if (is_string($options)) {
			$tag = $options;
			$options = array();
		}
		$items = $this->_nestedListItem($list, $options, $itemOptions, $tag);
		return sprintf($this->_tags[$tag], $this->_parseAttributes($options, null, ' ', ''), $items);
	}

	protected function _nestedListItem($items, $options, $itemOptions, $tag) {
		$out = '';

		$index = 1;
		foreach ($items as $key => $item) {
			if (is_array($item)) {
				$item = $key . $this->nestedList($item, $options, $itemOptions, $tag);
			}
			if (isset($itemOptions['even']) && $index % 2 === 0) {
				$itemOptions['class'] = $itemOptions['even'];
			} elseif (isset($itemOptions['odd']) && $index % 2 !== 0) {
				$itemOptions['class'] = $itemOptions['odd'];
			}
			$out .= sprintf($this->_tags['li'], $this->_parseAttributes($itemOptions, array('even', 'odd'), ' ', ''), $item);
			$index++;
		}
		return $out;
	}
	
	public function style($data, $oneline = true) {
		if (!is_array($data)) {
			return $data;
		}
		$out = array();
		foreach ($data as $key => $value) {
			$out[] = $key . ':' . $value . ';';
		}
		if ($oneline) {
			return implode(' ', $out);
		}
		return implode("\n", $out);
	}
	
	public function image($path, $options = array()) {
		if(strpos($path, 'http://')===false){		
			$path =  $this->urlRoot . 'app/boot/img/' . $path;
			if(array_key_exists('fullBase', $options)){
				$base = 'http://' . $_SERVER['SERVER_NAME'] . $this->urlRoot;
				$path = $base . $path;
			}
		}
		$options = array_diff_key($options, array('fullBase' => '', 'pathPrefix' => ''));

		if (!isset($options['alt'])) {
			$options['alt'] = '';
		}

		$url = false;
		if (!empty($options['url'])) {
			$url = $options['url'];
			unset($options['url']);
		}

		$image = sprintf($this->_tags['image'], $path, $this->_parseAttributes($options, null, '', ' '));

		if ($url) {
			return sprintf($this->_tags['link'], $this->url($url), null, $image);
		}
		return $image;
	}
	
	public function para($class, $text, $options = array()) {
		if (isset($options['escape'])) {
			$text = $this->h($text);
		}
		if ($class && !empty($class)) {
			$options['class'] = $class;
		}
		$tag = 'para';
		if ($text === null) {
			$tag = 'parastart';
		}
		return sprintf($this->_tags[$tag], $this->_parseAttributes($options, null, ' ', ''), $text);
	}
	
	public function div($class = null, $text = null, $options = array()) {
		if (!empty($class)) {
			$options['class'] = $class;
		}
		return $this->tag('div', $text, $options);
	}
	
	public function tag($name, $text = null, $options = array()) {
		if (is_array($options) && isset($options['escape']) && $options['escape']) {
			$text = $this->h($text);
			unset($options['escape']);
		}
		if (!is_array($options)) {
			$options = array('class' => $options);
		}
		if ($text === null) {
			$tag = 'tagstart';
		} else {
			$tag = 'tag';
		}
		return sprintf($this->_tags[$tag], $name, $this->_parseAttributes($options, null, ' ', ''), $text, $name);
	}
	
	public function useTag($tag) {
		if (!isset($this->_tags[$tag])) {
			return '';
		}
		$args = func_get_args();
		array_shift($args);
		foreach ($args as &$arg) {
			if (is_array($arg)) {
				$arg = $this->_parseAttributes($arg, null, ' ', '');
			}
		}
		return vsprintf($this->_tags[$tag], $args);
	}
	
	protected function _parseAttributes($options, $exclude = null, $insertBefore = ' ', $insertAfter = null) {
		if (!is_string($options)) {
			$options = (array)$options + array('escape' => true);

			if (!is_array($exclude)) {
				$exclude = array();
			}

			$exclude = array('escape' => true) + array_flip($exclude);
			$escape = $options['escape'];
			$attributes = array();

			foreach ($options as $key => $value) {
				if (!isset($exclude[$key]) && $value !== false && $value !== null) {
					$attributes[] = $this->_formatAttribute($key, $value, $escape);
				}
			}
			$out = implode(' ', $attributes);
		} else {
			$out = $options;
		}
		return $out ? $insertBefore . $out . $insertAfter : '';
	}

	protected function _formatAttribute($key, $value, $escape = true) {
		if (is_array($value)) {
			$value = implode(' ' , $value);
		}
		if (is_numeric($key)) {
			return sprintf($this->_minimizedAttributeFormat, $value, $value);
		}
		$truthy = array(1, '1', true, 'true', $key);
		$isMinimized = in_array($key, $this->_minimizedAttributes);
		if ($isMinimized && in_array($value, $truthy, true)) {
			return sprintf($this->_minimizedAttributeFormat, $key, $key);
		}
		if ($isMinimized) {
			return '';
		}
		return sprintf($this->_attributeFormat, $key, ($escape ? $this->h($value) : $value));
	}
	
	public function h($text, $double = true, $charset = null) {
		if (is_array($text)) {
			$texts = array();
			foreach ($text as $k => $t) {
				$texts[$k] = $this->h($t, $double, $charset);
			}
			return $texts;
		}elseif (is_bool($text)) {
			return $text;
		}

		static $defaultCharset = false;
		if ($defaultCharset === false) {
			if ($defaultCharset === null) {
				$defaultCharset = 'UTF-8';
			}
		}
		if (is_string($double)) {
			$charset = $double;
		}
		return htmlspecialchars($text, ENT_QUOTES, ($charset) ? $charset : $defaultCharset, $double);
	}
	
	
}
?>