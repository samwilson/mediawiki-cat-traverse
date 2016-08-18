<?php

namespace Samwilson\MediawikiCatTraverse;

use Mediawiki\Api\MediawikiApi;
use Mediawiki\Api\SimpleRequest;

/**
 * MediaWiki category traverser.
 *
 * Note on spelling: The adjective, "descending from a biological ancestor", may
 * be spelt either with an a or with an e in the final syllable (see descendant).
 * However the noun descendant, "one who is the progeny of someone", may be
 * spelt only with an a.
 */
class Traverser {

    /** @var \Mediawiki\Api\MediawikiApi */
    protected $api;

    /** @var string[] */
    protected $namespaces;

    public function __construct(MediawikiApi $api) {
        $this->api = $api;

        // Find the site's namespace IDs.
        $params = ['meta' => 'siteinfo', 'siprop' => 'namespaces'];
        $namespaces = $this->api->getRequest(new SimpleRequest('query', $params));
        if (isset($namespaces['query']['namespaces'])) {
            $this->namespaces = $namespaces['query']['namespaces'];
        }
    }

    /**
     * Visit every descendant page of $rootCategoryName (which will be a Category
     * page, because there are no desecendants of any other pages).
     *
     * @return string[] Each element is an array of ['pageid','ns','title'].
     */
    public function descendants($rootCategoryName, callable $callback = null) {
        $descendants = [];
        do {
            $continue = (isset($result['continue'])) ? $result['continue']['cmcontinue'] : '';
            $params = [
                'list' => 'categorymembers',
                'cmtitle' => $rootCategoryName,
                'cmcontinue' => $continue,
            ];
            $result = $this->api->getRequest(new SimpleRequest('query', $params));
            if (!array_key_exists('query', $result)) {
                return true;
            }

            foreach ($result['query']['categorymembers'] as $member) {
                $isCat = isset($this->namespaces[$member['ns']])
                        && isset($this->namespaces[$member['ns']]['canonical'])
                        && $this->namespaces[$member['ns']]['canonical'] === 'Category';
                if ($isCat) {
                    $newDescendants = $this->descendants($member['title'], $callback);
                    $descendants = array_merge($descendants, $newDescendants);
                } else {
                    $descendants[$member['pageid']] = $member;
                    if (is_callable($callback)) {
                        call_user_func_array($callback, [$member, $rootCategoryName]);
                    }
                }
            }
        } while (isset($result['continue']));
        return $descendants;
    }

}
