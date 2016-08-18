MediaWiki Cat Traverse
======================

## Installation

    composer require samwilson/mediawiki-cat-traverse

## Usage

```php
use Mediawiki\Api\MediawikiApi;
use Samwilson\MediawikiCatTraverse\Traverser;

$api = new MediawikiApi('http://localhost/w/api.php');
$traverser = new Traverser($api);
```