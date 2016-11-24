# Skiddle PHP SDK

---

The purpose of the SDK is to allow for easy access to the Skiddle API.  This will allow developers to easily communicate with the Skiddle API to pull through information about events, artists and venues

---

## Table of contents

1. [Requirements](#requirements)
2. [Installation](#installation)
  1. [Download the SDK](#download)
  2. [Get an API Key](#apikey)
  3. [Integrate](#integrate)
3. [Examples](#examples)
  1. [Authenticate Yourself](#authenticate)
  2. [Get the class Ready](#getready)
  3. [Add and Remove conditions](#addremove)
  4. [Get results](#results)
4. [Things to note](#notes)
5. [License](#license)
6. [Contact](#contact)

---

## Requirements<a name="requirements"></a>

The API requires PHP > 5.4.0, due to using autoloading and new array constructs.  It also requires cURL to be enabled, which is normally done by default in PHP, but best to double check.

---

## Installation<a name="installation"></a>

### Download the SDK<a name="download"></a>

There are a couple of ways to get the SDK integrated to your project, the easiest way is probably via composer:

````
"require": {
    "skiddle/skiddle-php-sdk": "dev-master"
}
````

You can also clone the git repository
````
git clone https://github.com/Skiddle/skiddle-php-sdk
````

Or, simply download the zip [here] and unzip to your project.

If using either of the last two methods, you will need to include the ````autoloader.php```` file in your project to load everything up

### Get an API Key<a name="apikey"></a>

Getting an API key is simple and free, simply go to [https://www.skiddle.com/api/join.php](https://www.skiddle.com/api/join.php) to get one now

### Integrate<a name="integrate"></a>

Once you have the code and an API key, you are ready to get started!!

---

## Examples<a name="examples"></a>

You can view code samples in the ````/demo/```` directory included in the repo

### Authenticate yourself<a name="authenticate"></a>

The first step is to simply authenticate yourself - just tell the SDK what your API Key is

```php
try {
    $session = new SkiddleSDK\SkiddleSession(['api_key'=>'APIKEYGOESHERE']);
} catch (SkiddleSDK\SkiddleException $e) {
    echo $e->getMessage();
    exit();
}
```

If you don't want to store this in your code, you can add it to your server environment as ```SKIDDLE_API_KEY``` and the SDK will read from there.

---

### Get the class Ready<a name="getready"></a>

After you have successfully authenticated yourself, you then need to pass your credentials to the revelant class that you want to use.  This combines your authentication info with the endpoint necessary to make the calls.

To do this, simply call the setSession() method of the class you wish to use:

```php
$events = new SkiddleSDK\Events;
try {
    $events->setSession($session);
} catch (SkiddleSDK\SkiddleException $e) {
    echo $e->getMessage();
    exit();
}
```

---

### Add and Remove conditions<a name="addremove"></a>

You are now ready to make calls to the API.  You can now technically make a call to return listings, however the Skiddle SDK allows you to easily add or remove conditions to make your query specific to your needs.

To add a condition, you just need to pass the field and value in the ```addCond()``` method:

```php
$events->addCond('eventcode','CLUB');
$events->addCond('ticketsavailable','1);
```

Likewise, to remove conditions, just use the ```delCond()``` method, using only the field name:

```php
$events->delCond('ticketsavailable');
```


###Get Results<a name="results"></a>

Once you have built up your filter list, you can then get your listings!

```php
$listings = $events->getListings();

foreach($listings->results as $result) {...}
```

For a full list of arguments you can filter by, [have a look here](https://github.com/Skiddle/web-api/wiki)

# Things to note<a name="notes"></a>

1.  When querying eventcodes, try to keeo values uppercase.  Passing CLUB will work, whereas passing club may return an error
2.  When using the minDate and maxDate conditions, timestamps need to be in y-m-d format.
3.  Don't like objects?  You can get results in array format by passing a boolean in ```getListings()```:
    ```php
    $listings = $events->getListings(true);

    foreach($listings['results'] as $result) {...}
    ```
4.  Check back for more

---

# License<a name="license"></a>

This SDK is licensed under the GNU General Public License v3.0.  [View the license here](LICENSE.md)

---

# Contact<a name="contact"></a>

Got any questions, or ways to improve the SDK?  Feel free to log an issue, or pull and fork as much as you want!