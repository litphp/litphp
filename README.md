LitPHP
------

> Flexible component collection for modern application

[![Build Status](https://travis-ci.org/litphp/litphp.svg?branch=master)](https://travis-ci.org/litphp/litphp)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/litphp/litphp/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/litphp/litphp/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/litphp/litphp/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/litphp/litphp/?branch=master)

**Quickstart** 

```bash
composer create-project --remove-vcs -s dev litphp/project myproject
# you will be prompt to input your project namespace, we do the replace work for you
cd myproject
php -S 127.0.0.1:3080 public/index.php
```

[and next steps](http://litphp.github.io/docs/quickstart)

**Documentation**

see [[http://litphp.github.io/docs/](http://litphp.github.io/docs/)](http://litphp.github.io/docs/)

**Components included in this repo**

| | |
| ---- | ---- |
| **litphp/air** | dependency injection |
| **litphp/nimo** | middleware organizer |
| **litphp/voltage** | fundamental application structure |
| **litphp/bolt** | the microframework |
| **litphp/router-fast-route** | a router that integrates FastRoute |
| **litphp/runner-zend-sapi** | run you bolt app with zend-diactoros on standard SAPI |
| **litphp/nexus** | utility / interface helps development |
