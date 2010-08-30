<?php echo $this->Html->image('http://wufoo.com/images/wflogo.png', array('alt' => 'Wufoo')); ?>
<ul>
	<li><h3>What Am I?</h3>
	I am a <?php echo $this->Html->link('CakePHP', 'http://cakephp.org/', array('target' => '_blank')); ?> Plugin (Helper and DataSource) for the most awesome online form building tool known as <?php echo $this->Html->link('Wufoo', 'http://wufoo.com/', array('target' => '_blank')); ?>.<br />
	<em>Oh yeah and I require CakePHP 1.3+ and PHP5+.</em>
	<p>&nbsp;</p></li>
	
	<li><h3>Let's Get Going!</h3><ol>
		<li><?php echo $this->Html->link('Sign up', 'http://wufoo.com/signup/', array('target' => '_blank')); ?> for a Wufoo account, remember your username.</li>
		<li>Copy the following lines into <em>app/config/database.php</em> and add your username and api_key:<br />
<pre>var $wufoo = array(
	'datasource' => 'wufoo.wufoo',
	'username' => 'USERNAME-HERE',
	'api_key' => 'API-KEY-HERE',
);</pre>
		<em>Can't find your API key? Login to wufoo.com, create a form, click on 'Code' then 'API Information'.</em>
		</li>
	</ol><p>&nbsp;</p></li>
	
	<li><h3>Wufoo Helper (Easily Embed Forms)</h3>
	<pre>var $helpers = array('Wufoo.Wufoo');</pre><br />
	<ul>
		<li><strong>Embed Form</strong><br />
		Embed a Wufoo form directly into your view:<br />
		<pre>echo $this->Wufoo->embed('form-name-here');</pre><br />
		</li>
		
		<li><strong>Embed Form (IFrame)</strong><br />
		Or if you prefer to use an iframe:<br />
		<pre>echo $this->Wufoo->iframe('form-name-here');</pre><br />
		</li>
	</ul><p>&nbsp;</p></li>
	
	<li><h3>Wufoo DataSource (Access the <?php echo $this->Html->link('Wufoo API', 'http://wufoo.com/docs/api/v3/', array('target' => '_blank')); ?>)</h3>
	If you have setup wufoo in your <em>config/database.php</em> then simply add this to your controller:<br />
	<pre>var $uses = array('Wufoo.Wufoo');</pre><br />
	Otherwise you can directly use the DataSource anywhere in your code:<br />
	<pre>$Wufoo = ClassRegistry::init('WufooSource');
$Wufoo->init(array(
	'username' => 'USERNAME-HERE',
	'api_key' => 'API-KEY-HERE',
));</pre><br />
	<ul>
		<li><strong>Forms</strong><br />
		Get all forms:<br />
		<pre>$forms = $this->Wufoo->findForms();</pre><br />
		Get specific form:<br />
		<pre>$form = $this->Wufoo->findForm('form-name-here');</pre><br />
		</li>
		
		<li><strong>Fields</strong><br />
		Get all fields in a form:<br />
		<pre>$fields = $this->Wufoo->findFields('form-name-here');</pre><br />
		</li>
		
		<li><strong>Entries</strong><br />
		Find all entries:<br />
		<pre>$entries = $this->Wufoo->findEntries('form-name-here');</pre><br />
		Filtering your entries:<br />
		<pre>$entries = $this->Wufoo->findEntries('form-name-here', array(
	'conditions' => array(
		'Field1' => 'Kyle',
		'Field2' => array('Begins_with' => 'Y', 'Ends_with' => 'g'),
	),
	'order' => 'Field1 DESC',
	'limit' => 25,
	'page' => 0
));</pre>
		Or if you prefer to use short hand:<br />
		<pre>$entries = $this->Wufoo->findEntries('form-name-here', 'Filter1=EntryId+Is_after+1&Filter2=EntryId+Is_before+200&match=AND');</pre>
		<em>Read more about filtering <?php echo $this->Html->link('here', 'http://wufoo.com/docs/api/v3/entries/get/', array('target' => '_blank')); ?>.</em><br />
		<br />
		Saving entries:<br />
		<pre>$this->Wufoo->saveEntry('form-name-here', array(
	'Field1' => 'Kyle',
	'Field2' => 'Young',
));</pre><br />
		</li>
		
		<li><strong>Users</strong><pre>$users = $this->Wufoo->findUsers();</pre><br /></li>
		
		<li><strong>Reports</strong><br />
		Get all reports:<br />
		<pre>$reports = $this->Wufoo->findReports();</pre><br />
		Get specific report:<br />
		<pre>$report = $this->Wufoo->findReports('report-name-here');</pre><br />
		</li>
		
		<li><strong>Widgets</strong><br />
		<pre>$widgets = $this->Wufoo->findWidgets('report-name-here');</pre><br />
		</li>
		
		<li><strong>Comments</strong><br />
		<pre>$comments = $this->Wufoo->findComments('form-name-here');</pre><br />
		</li>
		
		<li><strong>WebHooks</strong><br />
		Adding a web hook:<br />
		<pre>$this->Wufoo->saveWebHook('form-name-here', array(
	'url' => 'your-webhook-url-here'
));</pre><br />
		Deleting a web hook:<br />
		<pre>$this->Wufoo->deleteWebHook('form-name-here', 'webhook-hash-here');</pre>
		<em>Read more about web hooks <?php echo $this->Html->link('here', 'http://wufoo.com/docs/integrations/webhooks/', array('target' => '_blank')); ?>.</em><br />
		<br />
		</li>
		
		<li><strong>Login</strong><br />
		<pre>$login = $this->Wufoo->login(array(
	'integrationKey' => 'INTEGRATION-KEY-HERE',
	'email' => 'USER-EMAIL-HERE',
	'password' => 'USER-PASSWORD-HERE',
	'subdomain' => 'USER-SUBDOMAIN-HERE', 
));</pre>
		<em>This function is for logging in other Wufoo users. You must have an <?php echo $this->Html->link('integration key', 'https://master.wufoo.com/forms/integration-key-application/', array('target' => '_blank')); ?> to retrieve an API key and access another Wufoo user. Read more about the login API <?php echo $this->Html->link('here', 'http://wufoo.com/docs/api/v3/login/', array('target' => '_blank')); ?>.</em><br />
		</li>
		
	</ul><p>&nbsp;</p></li>
</ul>