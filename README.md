yii-auth
========

Auth is a module for the [Yii PHP framework](http://www.yiiframework.com) that provides a web user interface for Yii's built-in authorization manager (CAuthManager). 
You can read more about Yii's authorization manager in the framework documentation under [Authentication and Authorization](http://www.yiiframework.com/doc/guide/1.1/en/topics.auth#role-based-access-control).

Auth was developed to provide a modern and responsive user interface for managing user permissions in Yii projects.
To achieve its goals it was built using my popular [Twitter Bootstrap extension](http://www.yiiframework.com/extension/bootstrap).

Auth is written according to Yii's conventions and it follows the [separation of concerns](http://en.wikipedia.org/wiki/Separation_of_concerns) priciple and therefore it doesn't require you to extend from its classes.
Instead it provides additional functionality for the authorization manager through a single behavior.

Demo
====

You can try out the live demo [here](http://www.cniska.net/yii-auth/).

Requirements
============

* [Twitter Bootstrap extension for Yii](http://www.yiiframework.com/extension/bootstrap) version 2.0.0 or above

Usage
=====

Download the latest release from [Yii extensions](http://www.yiiframework.com/extension/auth).

Unzip the module under ***protected/modules/auth*** and add the following to your application config:

```php
return array(
  'modules' => array(
    'auth',
  ),
);
```
***protected/config/main.php***

Configure the module to suit your needs. Here's a list of the available configurations (with default values).

```php
'auth' => array(
  'strictMode' => true, // when enabled authorization items cannot be assigned children of the same type.
  'users' => array('admin'), // a list of users who has access to the module.
  'userClass' => 'User', // the name of the user model class.
  'userIdColumn' => 'id', // the name of the user id column.
  'userNameColumn' => 'name', // the name of the user name column.
  'appLayout' => 'application.views.layouts.main', // the layout used by the module.
  'viewDir' => null, // the path to view files to use with this module.
),
```

Changes
=======

#### Soon - version 1.0.0

* Initial release
