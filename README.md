# O2System Curl

O2System Curl is a PHP Lightweight HTTP Request Client Library which is build for working more powerful with O2System Framework, but also can be used for integrated with others as standalone version with limited features.

Features
--------
- Utility methods to call GET, HEAD, POST, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH requests.
- Supports form parameters, file uploads and custom body entities.
- Supports gzip compression.
- Supports Basic, Digest, Negotiate, NTLM Authentication natively.
- Customizable timeout.
- Customizable default headers for every request (DRY).
- Automatic JSON parsing into a native object for JSON responses.
- Multiple request support.

### Composer Instalation
The best way to install O2System Curl is to use [Composer](https://getcomposer.org)
```
composer require o2system/curl --prefer-dist dev-master
```
> Packagist: [https://packagist.org/packages/o2system/curl](https://packagist.org/packages/o2system/curl)

### Usage
```php
use O2System\Curl;
use O2System\Kernel\Http\Message\Uri;

// Single Request
$request = new Curl\Request();
$request->setUri( new Uri() )->withHost( 'api.o2system.id' )->withPath( 'testing');

// Multi Request
$multirequest = new Curl\MultiRequest();
$multirequest->register( $request );

// Get single response
$response = $request->get();

// Get multiple responses
$responses = $multirequest->get();
```

Documentation is available on this repository [wiki](https://github.com/o2system/curl/wiki) or visit this repository [github page](https://o2system.github.io/curl).

### Ideas and Suggestions
Please kindly mail us at [o2system.framework@gmail.com](mailto:o2system.framework@gmail.com])

### Bugs and Issues
Please kindly submit your [issues at Github](http://github.com/o2system/curl/issues) so we can track all the issues along development and send a [pull request](http://github.com/o2system/curl/pulls) to this repository.

### System Requirements
- PHP 7.2+
- [Composer](https://getcomposer.org)
- [O2System Kernel](https://github.com/o2system/kernel)

### Credits
|Role|Name|
|----|----|
|Founder and Lead Projects|[Steeven Andrian Salim](http://steevenz.com)|
|Documentation|[Steeven Andrian Salim](http://steevenz.com)
|Github Pages Designer| [Teguh Rianto](http://teguhrianto.tk)
