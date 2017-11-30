PDO Database Class
============================

A database class which uses the PDO extension.
* Allows one connection with the database and deny duplicate connection, 
* this speeds up to use the database and reduces the load on the server.
* supports many drivers (mysql, sqlite, PostgreSQL, mssql, sybase, Oracle Call Interface -oci-)

If you have any issue please open issue to fix it.

```
any suggestions would you like added or modified write to us at oasintech@gmail.com
```
### install via composer 
```json
{
	"require" : 
	{
		"oasin/db" : "dev-master"
	}
}
```

--------------------
# to use class :

## (config) :
- go to (database_config.php) file
- config class as your project need

## describe configuration :
 - fetch : PDO Fetch Style By default, database results will be returned as instances of the PHP stdClass object.
 - default : Default Database Connection Name (driver) by default (mysql)
 - connections : Database Connections (drivers).
 
###### <<<<<< set database connection information..!  >>>>>>

------------------
# how to use :
### step 1 : 
 - Include the class in your project
 
```php
    <?php
    include_once('vendor/autoload.php');
```
### step 2 :
- Create the instance (connect with database)
```php
    use ODB\DB\Database;
    $db = Database::connect();
```
### step 3 :
- Initialise flip/whoops error  handler
```php
    use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;

$run     = new Whoops\Run;
$handler = new PrettyPageHandler;

// Set the title of the error page:
$handler->setPageTitle("Whoops! There was a problem.");

$run->pushHandler($handler);
if (Whoops\Util\Misc::isAjaxRequest()) {
  $run->pushHandler(new JsonResponseHandler);
}
$run->register();

```

# how it works (methods):

### select() :

very important (select, first, find, paginate) methods __return Database object__
you can use ->results(); to convert to array or object as you configure your "fetch mode" in config file

 - select all data from `test` table :
    ```php
    $allData = $db->table('test')->select();
    
    var_dump($allData);
 
    // try 
    var_dump($allData->results()); // but you cant use any more methods
    ```
- select `id`, `name`, `email` for all users from `users` table
    ```php
    $coustomFields = $db->table('users')->select(['id', 'name', 'email']);
    
    var_dump($coustomFields);
    
  // if configure to return object
    echo $coustomFields->name;
    echo $coustomFields->email;
  
  // if configure to return array
      echo $coustomFields['name'];
      echo $coustomFields['email'];
    
    // or yo can foreach the returned values
    foreach($coustomFields as $fields)
    {
      // ...
    }
    ```
- select `post` where its `id` is equal 5
    ```php
    $post = $db->table('posts')->where('id', '=', 5)->select();
    // or
    $post = $db->table('posts')->where('id', 5)->select();
    // Custom fields
    $post = $db->table('posts')->where('id', 5)->select(['id', 'title', 'body']);
    ```
- multi where :
    ```php
    $post = $db->table('posts')
    ->where('vote', '>', 5)
        ->where('visetors', '>', 200)
        ->select();
    // Custom fields
     $post = $db->table('posts')
        ->where('vote', '>', 5)
        ->where('visetors', '>', 200)
        ->select(['id', 'title', 'body']);
    ```
    you can use `where` method an infinite :)
    
### where types :
- whereBetween() :
    ```php
    $db->table('posts')
        ->whereBetween('data', [$start, $end])
        ->select();
    ```
- likeWhere() :
    ```php
    $db->table('users')
        ->likeWhere('name', 'kelvin')
        ->select();
    ```
- orWhere() :
    ```php
    $db->table('posts')
        ->where('id', 5)
        ->orWhere('id', 3)
        ->select(['title', 'body']);
    ```
### get first row :
```php
 $db->table('posts')
        ->where('id', 5)
        ->orWhere('id', 3)
        ->first();
```
all examples above you can replace `select` with `first` to get only first row selected.

### find($id = 0) method :
find where `id`
```php
$db->table('users')->find(1);
// SELECT * FROM `users` where `id` = 1
```
please note : change $_idColumn variable to id name in table
if the table have no id colum set it will return null.
you can use idName() method or edit from Database class file direct

### setIdName($id = id)
change id column name | by default is id
 ```php
 $db->table('test')->idName('id_name');
 ```

### insert($values = []) :
insert new user to `users` table:
```php
$db->table('users')
    ->insert([
        'name' => 'kelvin',
        'email' => 'kelvin@email.com',
        'password' => 'secret',
    ]);

```
insert new post to `posts` table:
```php
$db->table('posts')
    ->insert([
        'title' => 'my post title',
        'body' => 'post body and description',
        // ....
    ]);
```

### update($values = []) :
if we need to update user name to 'ali' where his id is 5 :
```php
$db->table('users')
    ->where('id', 5)
    ->update([
        'name' => 'ali'
    ]);
```
update all posts title like (`test`) to (`this is a test post`)
```php
$db->table('posts')
    ->likeWhere('title', 'test')
    ->update([
        'title' => 'this is a test post'
    ]);
```
### save()
You can select/find a row and save it as well

In this example we configured "fetch" to FETCH_OBJ in config file

```php
use ODB\DB\\Database;
$db = Database::connect();
$user = $db->table('users')->find('1');
$user->name = 'kelvin';
$user->email = oasintech@gmail.com;
$user->save();
```
is this example we configure "fetch" to array

```php
use ODB\DB\\Database;
$db = Database::connect();
$user = $db->table('users')->find('1');
$user['name'] = 'kelvin';
$user['email'] = oasintech@gmail.com;
$user->save();
```
but you cant use __save__ with multi rows

WRONG WAY :
```php
    $multiUsers = $db->table('users')
        ->where('name', 'kelvin')
        ->select();
    
    $multiUsers->name = 'Kelvin'; // ERROR
    $multiUsers->save(); // ERROR
```

RIGHT WAY :
```php
    $multiUsers = $db->table('users')
        ->where('name', 'kelvin')
        ->select();
    
    foreach($multiUsers as $user)
    {
        $user->name = 'Kelvin';
        $user->save();
    }
```

### delete :
delete user has id 105
```php
$db->table('users')
    ->where('id', 105)
    ->delete();

// or
$db->table('users')->find(105)->delete();

// or 
$user = $db->table('users')->find(105);

if($user->active === 0)
{
    $user->delete();
}

// or

$allUsers = $db->table('users')->select();

foreach($allUsers as $user)
{
    if($user->active === 0)
    {
        $user->delete();
    }
}
```
delete all posts `voted < 2 ` and `visetors < 200 ` or `id is 2`
```php
$db->table('posts')
    ->where('vote', "<", 2)
    ->where('visetors', '<', 200)
    ->orWhere('id', 2)
    ->delete();
    
// or 

$unecessaryPost = $db->table('posts')
                      ->where('vote', "<", 2)
                      ->where('visetors', '<', 200)
                      ->orWhere('id', 2);
                      
$unecessaryPost->delete();
```
### count()
to get selected records count

```php
$allUsers = $db->table('users')->select();

echo $allUsers->count();

```


### limit :
get first 10 rows
```php
$justTenRows = $db->table('posts')
    ->where('vote',">", 3)
    ->limit(10)
    ->select();
```

### offset :
get first 10 rows offset 3
```php
$db->table('posts')
    ->where('vote',">", 3)
    ->limit(10)
    ->offset(3)
    ->select();
```

### in :

```php
$db->table('posts')
    ->in('id', [1, 2, 3, 4, 5])
    ->select();
```

### notIn :

```php
$db->table('posts')
    ->notIn('id', [1, 2, 3, 4, 5])
    ->select();
```


### paginate : 

to paginate results

paginate($recordsCount = 0)
$recordsCount => default value take from database_config.php file

```
"pagination" => [
		"no_data_found_message" => "Oops, No Data to show ..",
		"records_per_page"      => 10,
		"link_query_key"        => "page"
	]
```

```php
$db = ODB\DB\Database::connect();
$results = $db->table("blog")->paginate(15);
var_dump($results);

// to array or object
var_dump($results->results());
```
now add to url this string query (?page=2 or 3 or 4 .. etc)
see (link() method to know how to generate navigation automatically)

### link : 
 create pagination list to navigate between pages
 * compatible with bootstrap and foundation frameworks
 
 ```php
 $db = ODB\DB\Database::connect();
 $posts = $db->table("blog")->where("vote", ">", 2)->paginate(5);
 echo $posts->link();
 ```

### dataView : 
 view query results in table
 we need to create a simple table to view results of query
 
 ```php
$db = ODB\DB\Database::connect();
$data = $db->table("blog")->where("vote", ">", 2)->select();
echo $data->dataView();
```

## recommended TEST Code : 

```php

$db = ODB\DB\Database::connect();
$posts = $db->table("blog")->paginate();
echo $posts->dataView();
echo $posts->link();

```


you can echo out the results directlly that convert 
the results to json format

```php
$results = $db->table('table')->select();
echo $results; // return json format
```
or foreach results as last version.

```php
$results = $db->table('table')->select();

foreach($results as $key => $value)
{
    // ..
}
```

select(), first(), find(), paginate() methods
now returns an instance of Collection class

### last()

get last record selected 

```php

$all = $db->table('my_table')->select();
var_dump($all->last());
```

### all()

all() and results() has same functionallity

```php
$all = $db->table('my_table')->select();
var_dump($all->all());
// var_dump($all->results());
```

### each(callable $callback)
to each results with callback function.

```php

$results = $db->table('table')->select();

$results->each(function($row) {
    echo $row->column_name . " !! <br>";
});

$new = [];
$results->each(function($row) {
    $new[] =  $row->column_name;
});

// you can chain first(), last(), filter(), map(), each(),
// toJson(), keys(), empty() with each() method

$results->each(function($row) {
    $new[] =  $row->column_name;
});

```

### filter(callable $callback)

filter results values.

```php
$results = $db->table('table')->select();

$filterdResults = $results->filter(function($row) {
    return $row->id > 15;
});

//----

$results = $db->table('table')->select();

$filterdResults = $results->filter(function($row, $key) {
    // you can use $key if you want 
    return $row->id > 15;
});

//----

$results = $db->table('table')->select();

// exclude null values !
$filterdResults = $results->filter();

// you can use first(), last(), filter(), map(), each(),
// toJson(), keys(), empty() with $filterdResults variable


```

### map(callable $callback)

```php

$results = $db->table('table')->select();
$newResults = $results->map(function($row) {
    // return ..
});

// you can use first(), last(), filter(), map(), each(),
// toJson(), keys(), empty() with $newResults variable

```

### toJson()

convert results to json format

```php
$results = $db->table('table')->select();

echo $results->toJson();
```

### merge(array|instance of Collection)

merge array with collection or 2 collections

```php
$results = $db->table('table')->select();
$otherResults = $db->table('other_table')->select();

$merge = $results->merge($otherResults);

// you can use first(), last(), filter(), map(), each(),
// toJson(), keys(), empty() with $merge variable

```

### keys()
get results keys

```php
$results = $db->table('table')->select();

var_dump($results->keys());

```


--------------------------------

# Data Definition Language (DDL) :

### Create Table : 

```php
$db = Database::connect();

$db->table('my_new_table_name')->schema('schema as array')->create();
```
EX : 

```php
$db = Database::connect();

$db->table('students')->schema([
		'id' => 'increments',
		'name' => 'string:255 | not_null',
		'number' => 'int|unsigned';
	])->create();
```
the SQL Statment for this :
CREATE TABLE students (
						id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
						name VARCHAR(255) NOT NULL,
						number INT UNSIGNED
					
					)
					
#### PLEASe NOTE: 
'id' => 'increments'
mean the id column will be integer,primary key, auto increment not null,  and unsigned

### ADD Constraints
'number' => 'int|my_constraint|other_constraint|more_constraint';

SO the first one is a column type and other will be Constraints

### Default Value

to set defualt value type :
```php
'number' => 'int|unsigned|default:222';
'name' => 'int|unsigned|default:hello-this-a-default-value';

// note : the charecter (-) replaced with white space
```
### Full Example :
```php
$db = Database::connect();

$schema = [
	'id' => 'increments',
	'username' => 'string:100|not_null',
	'full_name' => 'string:255|defualt:no-name',
	'joined' => 'timestamp',
	'user_email' => 'string:100|not_null',
];

$db->table('users')->schema($schema)->create();

```
# ADD Column :
```php
$db->table('target_table')->alterSchema('condetions is array')->alter();
$db->table('table')->alterSchema(['add', 'column_name', 'type'])->alter();
```
#### EX:
```php
$db->table('users')->alterSchema(['add', 'last_login', 'date'])->alter();
```

# RENAME Column :
```php
$db->table('target_table')->alterSchema('condetions is array')->alter();
$db->table('table')->alterSchema(['rename', 'column_name', 'new_column_name' ,'type'])->alter();
```
#### EX:
```php
$db->table('users')->alterSchema(['rename', 'last_login', 'last_session', 'date'])->alter();
```

# EDIT Column  type:
```php
$db->table('table')->alterSchema(['modify', 'column_name', 'new_type'])->alter();
```
#### EX:
```php
$db->table('users')->alterSchema(['modify', 'full_name', 'text'])->alter();
```

# DROP Column :
```php
$db->table('table')->alterSchema(['drop', 'column_name'])->alter();
```
#### EX:
```php
$db->table('users')->alterSchema(['drop', 'full_name'])->alter();
```
## Deleted Methods
    empty()


### parseWhere(array $cons, $type = "AND")
```php
 $con = [
    [
        'age', '<', '30'
    ],
    'OR' => [
        'sex', '=', 'female'
    ],
    'AND' => [
        'position', '=', 'manager'
    ]
];

// ---

$db->table('table_name')->
    ->where('username', 'ALI')
    ->parseWhere($con)->select();
    
// SELECT * FROM table_name where username='ALI' AND (age<30 OR sex='female AND position='manager')
// OR

$db->table('table_name')->
    ->where('username', 'ALI')
    ->parseWhere($con, 'OR;)->select();
    
// SELECT * FROM table_name where username='ALI' OR (age<30 OR sex='female AND position='manager')
```
### rawQuery()
execute raw sql queries

```php
$sql = 'select name, email from users';
$query = $db->rawQuery($sql);


````

### LastQuery()
````php

 echo  $db->LastQuery();
 
 ````



### lastInsertedId()
after insert into database you can retrieve last inserted ID

```php
$insert = $db->table('table_name')->insert($insertArray);
echo $insert->lastInsertedId();

// or
$db->table('table_name')->insert($insertArray);
echo $db->lastInsertedId();

```
### createOrUpdate($values, $conditionColumn = [])
now you can check if record exist to update or create new one in easy

``` php
$db = \ODB\DBDatabase::connect();

// check in table users if we have username (AL-Azzawi)
// update it if not create new one

$users = $db->table('users')->createOrUpdate([
	'username' => 'kelvin Israel',
	'password' => 'mySecretPass',
	'active'   => 1,
], ['username', 'AL-Anzawi']);
```
note: $conditionColumn is optional but you need to send ID column manually
```php
$users = $db->table('users')->createOrUpdate([
    'id'       => 8
	'username' => 'kelvin Israel',
	'password' => 'mySecretPass',
	'active'   => 1,
]);
```

### findBy($column, $value)
now you can find record with custom field
example :
```php
$user = $db->table('users')->findBy('username', 'kelvin');

var_dump($user->select());
var_dump($user->first());
var_dump($user->paginate());
```
DONT FORGET find($id) method ;)



### THATS IT :) 

### I HOPE THIS HELP'S YOU.

=============================
## Change Log

#### 1.0.0
* First Release


=============================
# License
### APACHE



