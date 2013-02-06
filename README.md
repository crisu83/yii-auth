yii-auth
========

Auth is a module for the [Yii PHP framework](http://www.yiiframework.com) that provides a web user interface for Yii's built-in authorization manager (CAuthManager). 
You can read more about Yii's authorization manager in the framework documentation under [Authentication and Authorization](http://www.yiiframework.com/doc/guide/1.1/en/topics.auth#role-based-access-control).

Auth was developed to provide a modern and responsive user interface for managing user permissions in Yii projects.
To achieve its goals it was built using my popular [Twitter Bootstrap extension](http://www.yiiframework.com/extension/bootstrap).

Auth is written according to Yii's conventions and it follows the [separation of concerns](http://en.wikipedia.org/wiki/Separation_of_concerns) priciple and therefore it doesn't require you to extend from its classes.
Instead it provides additional functionality for the authorization manager through a single behavior.

### Demo

You can try out the live demo [here](http://www.cniska.net/yii-auth/).

### Requirements

* [Twitter Bootstrap extension for Yii](http://www.yiiframework.com/extension/bootstrap) version 2.0.0 or above

## Usage

### Setup

Download the latest release from [Yii extensions](http://www.yiiframework.com/extension/auth).

Unzip the module under ***protected/modules/auth*** and add the following to your application config:

```php
return array(
  'modules' => array(
    'auth',
  ),
  'components' => array(
    'authManager' => array(
      .....
      'behaviors' => array(
        'auth' => array(
          'class' => 'auth.components.AuthBehavior',
          'admins' => array('admin', 'foo', 'bar'), // users with full access
        ),
      ),
    ),
    'user' => array(
      'class' => 'auth.components.AuthWebUser',
    ),
  ),
);
```
***protected/config/main.php***

Please note that while the module doesn't require you to use a database, if you wish to use ***CDbAuthManager*** you need it's schema (it can be found in the framework under web/auth).

### Configuration

Configure the module to suit your needs. Here's a list of the available configurations (with default values).

```php
'auth' => array(
  'strictMode' => true, // when enabled authorization items cannot be assigned children of the same type.
  'userClass' => 'User', // the name of the user model class.
  'userIdColumn' => 'id', // the name of the user id column.
  'userNameColumn' => 'name', // the name of the user name column.
  'appLayout' => 'application.views.layouts.main', // the layout used by the module.
  'viewDir' => null, // the path to view files to use with this module.
),
```

#### Enabling caching

To enable caching for ***CDbAuthManager*** you can use ***CachedDbAuthManager*** that provides caching for access checks. 
Here's an example configuration for the component:

```php
'authManager'=>array(
  'class'=>'auth.components.CachedDbAuthManager',
  'cachingDuration'=>3600,
),
```

### Checking access

When you wish to check if the current user has a certain permission you can use the ***CWebUser::checkAccess()*** method which can be access from anywhere in your application through ***Yii::app()*** like so:

```php
if (Yii::app()->user->checkAccess('itemName')) // itemName = name of the operation
{
  // access is allowed.
}
```

In order to keep your permissions dynamic you should never check for a specific role or task, instead you should always check for an operation. 
For more information on Yii's authorization manager refer to the framework documentation on [Authentication and Authorization](http://www.yiiframework.com/doc/guide/1.1/en/topics.auth#role-based-access-control).

#### Checking access using a filter

You can also use a filter to automatically check access before controller actions are called.
Operations used with this filter has to be named as follows ***(moduleId.)controllerId.actionId***, where ***moduleId*** is optional. 
You can also use a wildcard ***controllerId.**** instead of the actionId to cover all actions in the controller. 

```php
public function filters()
{
  return array(
    array('auth.filters.AuthFilter'),
  ),
}
```

For more information on how filters work refer to the framework documentation on [Controllers](http://www.yiiframework.com/doc/guide/1.1/en/basics.controller#filter).

### Internationalization

Do you wish to provide a translation for Auth? If so, please do a pull request for it. 
Translations should be placed in the messages folder under a folder named according to its locale (e.g. en_us).
