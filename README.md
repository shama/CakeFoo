![Wufoo][1]

## What Am I?
I am a [CakePHP][2] Plugin (Helper and DataSource) for the most awesome online form building tool known as [Wufoo][3]. 

*Oh yeah and I require CakePHP 1.3+ and PHP5+.*


## Let's Get Going!

  1. [Sign up][4] for a Wufoo account, remember your username.
  2. Extract the contents of this repo into *app/plugins/wufoo/* or use git clone from your plugins folder:
		
	`git clone  git://github.com/shama/Cake-Wufoo-Plugin.git wufoo`

  3. Copy the following lines into *app/config/database.php* and add your username and api_key:

	`var $wufoo = array(
		'datasource' => 'wufoo.wufoo',
		'username' => 'USERNAME-HERE',
		'api_key' => 'API-KEY-HERE',
	);`

*Can't find your API key? Login to wufoo.com, create a form, click on 'Code' then 'API Information'.*
	
## Wufoo Helper (Easily Embed Forms)

    var $helpers = array('Wufoo.Wufoo');

### Embed Form
Embed a Wufoo form directly into your view:

    echo $this->Wufoo->embed('form-name-here');
		
### Embed Form (IFrame)
Or if you prefer to use an iframe:

    echo $this->Wufoo->iframe('form-name-here');

# Wufoo DataSource (Access the [Wufoo API][5]) 
If you have setup wufoo in your *config/database.php* then simply add this to your controller:

    var $uses = array('Wufoo.Wufoo');

Otherwise you can directly use the DataSource anywhere in your code:

	$Wufoo = ClassRegistry::init('WufooSource');
	$Wufoo->init(array(
		'username' => 'USERNAME-HERE',
		'api_key' => 'API-KEY-HERE',
	));
	

### Forms
Get all forms:

	$forms = $this->Wufoo->findForms();

Get specific form:

	$form = $this->Wufoo->findForm('form-name-here');
		
### Fields
Get all fields in a form:
		
	$fields = $this->Wufoo->findFields('form-name-here');
		
### Entries
Find all entries:

	$entries = $this->Wufoo->findEntries('form-name-here');

Filtering your entries:
		
	$entries = $this->Wufoo->findEntries('form-name-here', array(
		'conditions' => array(
			'Field1' => 'Kyle',
			'Field2' => array('Begins_with' => 'Y', 'Ends_with' => 'g'),
		),
		'order' => 'Field1 DESC',
		'limit' => 25,
		'page' => 0
	));

Or if you prefer to use short hand:
		
	$entries = $this->Wufoo->findEntries('form-name-here', 'Filter1=EntryId+Is_after+1&Filter2=EntryId+Is_before+200&match=AND');

*Read more about filtering [here][6].*

Saving entries:
		
	$this->Wufoo->saveEntry('form-name-here', array(
		'Field1' => 'Kyle',
		'Field2' => 'Young',
	));

### Users

	$users = $this->Wufoo->findUsers();
		
### Reports
Get all reports:
		
	$reports = $this->Wufoo->findReports();

Get specific report:
		
	$report = $this->Wufoo->findReports('report-name-here');
		
### Widgets
		
	$widgets = $this->Wufoo->findWidgets('report-name-here');

### Comments
		
	$comments = $this->Wufoo->findComments('form-name-here');
	
### WebHooks
Adding a web hook:
		
	$this->Wufoo->saveWebHook('form-name-here', array(
		'url' => 'your-webhook-url-here'
	));

Deleting a web hook:

	$this->Wufoo->deleteWebHook('form-name-here', 'webhook-hash-here');

*Read more about web hooks [here][7].*
		
### Login
	
	$login = $this->Wufoo->login(array(
		'integrationKey' => 'INTEGRATION-KEY-HERE',
		'email' => 'USER-EMAIL-HERE',
		'password' => 'USER-PASSWORD-HERE',
		'subdomain' => 'USER-SUBDOMAIN-HERE', 
	));

*This function is for logging in other Wufoo users. You must have an [integration key][8] to retrieve an API key and access another Wufoo user. Read more about the login API [here][9].*


## License
The MIT License (http://www.opensource.org/licenses/mit-license.php) Redistributions of files must retain the above copyright notice.

## AUTHOR
Kyle Robinson Young, [kyletyoung.com][10]


  [1]: http://wufoo.com/images/wflogo.png
  [2]: http://cakephp.org/
  [3]: http://wufoo.com/
  [4]: http://wufoo.com/signup/
  [5]: http://wufoo.com/docs/api/v3/
  [6]: http://wufoo.com/docs/api/v3/entries/get/
  [7]: http://wufoo.com/docs/integrations/webhooks/
  [8]: https://master.wufoo.com/forms/integration-key-application/
  [9]: http://wufoo.com/docs/api/v3/login/
  [10]: http://kyletyoung.com