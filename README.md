# Framework
Made With ♥ By Patryk Janiak (https://morswin22.github.io/)

## How to use
Create new project with framework:

    git clone https://github.com/morswin22/framework.git your_project_dir

Initialize Framework class and add your compontents in `fw/main.php`:

```php
// Framework class contructor takes one argument: link to current workspace
$fw = new Framework('http://your.domain.url/');

$fw->add_component('your_component', '{{domain_url}}fw/components/your_component.html');
// Framework will change {{domain_url}} to specified url above
// This function adds new component to the components list
// You have to pass the component's name and it's path
```

Initialize framework in file with:

```php
include('path_to_fw/main.php'); 
```

Output a component:
 
```php
$fw->component('your_component');
```

Output a component with additional flags:
 
```php
// This will output your component with changed {{name}} to Patrick and {{year}} to 2018
$fw->component('your_component', array( 'replace' => array('name' => 'Patrick', 'year'=>'2018') ));

// This will output your component with changed {{title}} to Document (used in rendering <head> tags)
$fw->component('your_component', array( 'render' => array('title' => 'Document') ));

// This is used in rendering <nav> tags (full explanation at the bottom)
$fw->component('your_component', array( 'render' => array('page' => 'index') ));

// You can use render and replace at the same time!
```

## Login extension
Put `prepareUsers` framework function in your `fw/main.php`

```php
// example user database structure
$fw->prepareUsers(
array('name', 'pass', 'email', 'email-confirmed'), // list of all datebase cols' names
    'name',                                        // unique id col's name
    'pass'                                         // col's name used to verifying login
);
```

Login extension functions:
* `register` - accepts an array with all filled database cols
* `login`    - accepts an array with id and checking parameter 
* `logout`   - does not take any arguments
* `isLogged` - returns true if user is logged in
* `getUsers` - returns an array of all users
* `edit`     - takes as argument param's name and value to change

Tricks with `edit` and `edit_user` functions:

```php
$fw->edit('pass','1234'); // changes pass value in currently logged user

// $user_object = array('name'=>'test','pass'=>'4321','desc'=>'Lorem ipsum');
$fw->edit_user($user_object,'pass','1234'); // changes pass value in given user
```

User data is stored in `$fw->user` variable.

## Database extension
#### Adding new databases
Framework comes with a function called `add_db` which takes three arguments: 

1. database name
1. columns
1. id column name

```php
// code example
$fw->add_db('family', array('id','name','birthday'), 'id');
```

#### Accessing databases from php
Your added databases are stored inside of `$fw->db` array. Every database object comes with functions:

* `query` - returns a new query object ([more about that](#database-query-object))
* `putData` - takes as an argument id and values (all columns must be filled except for id)
* `rmData` - takes as an argument id to delete
* `editData` - takes as an argument id and values to change

```php
// code example

// pass empty string as id to push new row at the back
$fw->db['family']->putData('', array('name'=>'Ana', 'birthday'=>'11.02.1985'));
// you can pass DB_PUSH constant instead of the empty string

// simply removes row from database
$fw->db['family']->rmData(5);

// first argument is an id and the second is an array with values to change
$fw->db['family']->editData(2, array('birthday'=>'19.10.1983'));

```

#### Database query object
When function `query` is fired, it returns new query object which has got some functions:

* `getData` - gets data from database 
* `fetch` - returns following rows or `false` when there is nothing to return
* `sort` - sorts rows in ascending or descending order 
* `putData` - takes as an argument id and values (all columns must be filled except for id)
* `rmData` - takes as an argument id to delete
* `editData` - takes as an argument id and values to change

```php
// code example

// fetches for every row in the database
$query = $fw->db['family']->query();
// sort the result alphabetically using the 'name' column
$query->sort('name', 'asc');
// sort accepts 'asc' for ascending or 'desc' for descending order

// fetches for specific rows in the database
$query = $fw->db['family']->query(array('name'=>'Patrick')); 

// you can pass a regular expression
$query = $fw->db['family']->query(array('name'=>'/patrick/i')); 

// use fetch in if and while
if ($row = $query->fetch()) {
    echo 'Patrick's birthday is on ' . $row['birthday'];
}

// functions like putData, rmData, editData work same as on database object 
```

#### Accessing databases from the GUI
Get access to `fw/db/` by adding to `fw/main.php` new database user: `db_register()`. This function accepts name and password for the new user.

Your added databases are listed at the left side of screen. After selecting a database you will see a table where the columns and rows are displayed. Above that there is an input textarea and operation selector. You can select one out of four functions:

* Get data
* Put data
* Remove data
* Edit data

You write queries in json format. You have to pass the same arguments as you would pass in php. Store the arguments in a json array in the correct order.

Example: (edit data operation is selected)
```
[
    3,
    {
        "name": "Martha"
    }
]
```

## Navbar tricks
In your *navbar* component you can place in class special element `{{is:page}}` (*page* equals to given value in `'render' => array('page' => 'index')`

```html
<!-- example -->
<div class="nav">
    <div class="nav-item {{is:page1.php}}">
        <a href="page1.php">Page 1</a>
    </div>
    <div class="nav-item {{is:page2.php}}">
        <a href="page2.php">Page 2</a>
    </div>
</div>
```

When you output a component with flag render where page name is given, it will change this special element into `active` if given page name matches with the `{{is:page}}`

```html
<!-- result (current page name is 'page1.php') -->
<div class="nav">
    <div class="nav-item active">
        <a href="page1.php">Page 1</a>
    </div>
    <div class="nav-item {{is:page2.php}}">
        <a href="page2.php">Page 2</a>
    </div>
</div>
```

You can change `active` class name to other class names by changing the `$fw->navbarActive` variable.
