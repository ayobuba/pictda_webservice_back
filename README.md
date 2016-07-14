Zedek 3
========

Zedek Web Development Framework version 3

This is a PHP web development framework. 

The features include:

1. Model-View-Controller
2. Object Oriented
3. Encourages Agile development
4. Has an Object Relational Mapper (ORM) built in called ZORM
5. Has a templating engine accessed from a class called ZView
6. Templating engines allows some logic in the html view file such as looping through an array with the option of including raw php in the markup 
7. URL rewriting allowing for clean urls, and sub folder installation
8. Tested with apache, and currently being tested on lighttpd
9. Works on Unix, unix-like and Windows Operating Systems

Requirements
=============
1. Apache
2. PHP5.3+
3. Knowledge of PHP
4. Knowledge of Object Oriented Programming (OOP)
5. PHPUnit and understanding of Test Driven Development (TDD) in PHP - Simpletest library has been included to take care of web presentation of tests


Creating your first application follow these steps:
===================================================

1. Download this repo and extract so you have a folder named "zedek" or what ever else you want to call it in a non web accessible folder (one of the security features of Zedek Framework).
2. In your web accessible folder (web root) you will require 3 files and a folder being a ".htaccess" file, a router file named as you desire such as "router.php", a "favicon.ico" file and a folder named themes for your publicly accessible files. To get these you may copy them out of the /zedek/public folder or make this folder your web folder by creating a virtual host for this folder
3. The contents of the .htaccess file should redirect all traffic to the router file while excluding the public folder(s) contents and any other folders you define.

* You may also copy the contents of the /zedek/public/ folder into your web root or create a virtual host that points to that folder. 

## .htaccess contents ##

    RewriteEngine On
    RewriteCond %{REQUEST_URI} !/themes/.*$ 
    RewriteCond %{REQUEST_URI} !/favicon\.ico$
    RewriteRule ^(.*)$ router.php

*Ensure you have mod_rewrite enabled and properly configured

## router.php contents ##

    <?php
      require_once "/path/to/zedek/anchor.php";
    ?>
    
and you are about done with the web parts.

on a windows machine it would look like this:

    <?php
        require_once "drive:\\path\\to\\anchor.php";
    ?>

## initializer.php ##
Within the anchor file on line 24 set the web path physical path leading to the zedek app ending with a trailing slash

    const zweb="/path/to/web/folder/";

Once done you should see your app on your website with a successful install message.
* if installing in a sub folder also include the web sub folder path from web root on line 30

Hello World!
============

Zedek 3 is built to map urls to engine directories and methods of the class CController (for current controller) in a style:

    http://mysite.com/controller/method/id/?arg1=val1&arg2=val2...$argn=valn

(this mapping is handled primarily by a class named URLMaper)

the MVC is made literal within the engine folder. 

1. To create a new app named foo create a folder with the name foo within the engines folder.
2. within this create a class file "controller.php".
3. within the controller file enter the following code inside your php tags

    <?php
    namespace __zf__;
    class CController extends ZController{
        function bar(){
            print "Hello World";
        }
    }
    

4. Browse to http://mysite.com/foo/bar

and you should see your hello world message!

Congratulations!