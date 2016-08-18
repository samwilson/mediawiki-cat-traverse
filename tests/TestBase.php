<?php

namespace Samwilson\MediawikiCatTraverse\Tests;

use Mediawiki\Api\MediawikiFactory;
use Mediawiki\Api\MediawikiApi;
use Mediawiki\DataModel\Revision;
use Mediawiki\DataModel\Content;
use Mediawiki\DataModel\PageIdentifier;
use Mediawiki\DataModel\Title;
use Mediawiki\DataModel\Page;

abstract class TestBase extends \PHPUnit_Framework_TestCase {

    /** @var Mediawiki\Api\MediawikiApi */
    protected $api;

    /** @var Mediawiki\Api\MediawikiFactory */
    protected $factory;

    /**
     * Connect to the MediaWiki instance and create some testing pages.
     */
    public function setUp() {
        parent::setUp();
        $apiUrl = getenv('MEDIAWIKI_API_URL');
        if (empty($apiUrl)) {
            $apiUrl = 'http://localhost/w/api.php';
        } elseif (substr($apiUrl, -7) !== 'api.php') {
            $msg = "URL incorrect: $apiUrl (the MEDIAWIKI_API_URL environment variable should end in 'api.php')";
            throw new \Exception($msg);
        }
        $this->api = new MediawikiApi($apiUrl);
        $this->factory = new MediawikiFactory($this->api);
    }

    public function tearDown() {
        
    }

    /**
     * A convenience wrapper to a RevisionSaver.
     *
     * @param type $name
     * @param type $content
     */
    protected function savePage($name, $content) {
        $pageId = new PageIdentifier(new Title($name));
        $rev = new Revision(new Content($content), $pageId);
        $this->factory->newRevisionSaver()->save($rev);
        //$this->factory->newPagePurger()->purge(new Page($pageId));
    }

}
