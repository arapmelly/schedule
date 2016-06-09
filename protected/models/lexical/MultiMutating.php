<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/28/11
 * Time: 9:35 AM
 * To change this template use File | Settings | File Templates.
 */
class MultiMutating extends Mutating
{
    private $_text;
    private $_tags;
    private $_tagsPackages;

    public function __construct($text, $tags)
    {
        $this->_text = $text;
        $this->_tags = $tags;
    }

    private function _initTags()
    {
        if (Tweet::length($this->_text) >= Tweet::TW_LEN) return;
        $this->_tagsPackages = array();

        $tags = $this->_tags;
        $tagsPackage = array();
        while (($tag = array_shift($tags)) !== null)
        {
            $tweet = self::tweet($this->_text, array_merge($tagsPackage, array($tag)));
            $length = Tweet::length($tweet);
            if ($length <= Tweet::TW_LEN) {
                $tagsPackage[] = $tag;
            } else {
                $this->_tagsPackages[] = $tagsPackage;
                $tagsPackage = array();
            }
        }
    }

    public function getNextVariants()
    {

    }

}
