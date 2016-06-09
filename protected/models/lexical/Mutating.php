<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/9/11
 * Time: 1:52 PM
 * To change this template use File | Settings | File Templates.
 */
class Mutating
{
    const ENC = 'utf8';
    const TAG_CH = '#';

    private $_text;
    public $_notMutatedTextVariants;
    private $_mutatedVariantIndex = 0;
    public $_mutatedTextVariants;

    private $_mixture = array(' ', ',', '.', '\'', '"');
    private $_mixtureIndex = 0;
    private $_mixtureNumbersRange = 100;

    private $_extraTags = array();
    private $_tagsPackage = array();
    private $_tagsTemp = array();
    private $_tagsRest = array();
    public $_tagsCombinations = array();

    public function __construct($text, $extraTags = array())
    {
        $this->_text = $text;
        $this->_extraTags = $extraTags;
        $this->_mixture = array_merge($this->_mixture, range(0, $this->_mixtureNumbersRange));

        $this->_initTags();
    }

    private function _initTags()
    {
        if (Tweet::length($this->_text) >= Tweet::TW_LEN) return;
        $this->_tagsPackage = array();
        $this->_tagsTemp = array();
        $this->_tagsRest = $this->_extraTags;

        while (($tag = array_shift($this->_tagsRest)) !== null)
        {
            $tweet = self::tweet($this->_text, array_merge($this->_tagsPackage, array($tag)));
            $length = Tweet::length($tweet);
            if ($length <= Tweet::TW_LEN) {
                $this->_tagsPackage[] = $tag;
            } else {
                array_unshift($this->_tagsRest, $tag);
                break;
            }
        }
    }

    public function getNextVariant()
    {
        if (!empty($this->_extraTags) && $this->nextTagsVariant()) {
            return $this->getVariant();
        } else {
            return $this->nextMutation();
        }
    }

    public function nextTagsVariant()
    {
        if (Tweet::length($this->_text) >= Tweet::TW_LEN) return false;

        if (array_search($this->_tagsPackage, $this->_tagsCombinations) === false) {
            $this->_tagsCombinations[] = $this->_tagsPackage;
            $this->_notMutatedTextVariants[] = $this->getVariant();
            return true;
        }

        if ($this->nextTagsShift()) {
            $this->_tagsCombinations[] = $this->_tagsPackage;
            $this->_notMutatedTextVariants[] = $this->getVariant();
            return true;
        } else {
            if ($this->nextTagsCombination()) {
                $this->_shiftFinished = false;
                $this->nextTagsShift();
                $this->_tagsCombinations[] = $this->_tagsPackage;
                $this->_notMutatedTextVariants[] = $this->getVariant();
                return true;
            }
        }

        return false;
    }

    private $_shiftFinished = false;

    public function nextTagsShift()
    {
        if ($this->_shiftFinished) return false;

        $tag = array_shift($this->_tagsRest);
        if ($tag === null) {
            $this->_shiftFinished = true;
            if (!empty($this->_tagsTemp)) {
                //to init state
                $last = array_pop($this->_tagsPackage);
                $this->_tagsPackage[] = array_shift($this->_tagsTemp);
                $this->_tagsRest = array_merge($this->_tagsTemp, array($last));
                $this->_tagsTemp = array();
            }
            //            $this->_initTags();
            return false;
        } else {
            if (!empty($this->_tagsTemp)) {
                $last = array_pop($this->_tagsPackage);
                $this->_tagsPackage = array_merge($this->_tagsPackage, $this->_tagsTemp, array($last));
                $this->_tagsTemp = array();
            }
        }

        while (($lastTag = array_pop($this->_tagsPackage)) !== null)
        {
            array_unshift($this->_tagsTemp, $lastTag);
            $tweet = self::tweet($this->_text, array_merge($this->_tagsPackage, array($tag)));
            if (Tweet::length($tweet) <= Tweet::TW_LEN) {
                $this->_tagsPackage[] = $tag;
                return true;
            }
        }
        return false;
    }

    private $_i = 0;
    private $_j = 0;

    public function nextTagsCombination()
    {
        $swapped = false;
        for ($this->_i; $this->_i < count($this->_tagsPackage); $this->_i++)
        {
            if ($swapped) return true;
            for ($this->_j; $this->_j < count($this->_tagsPackage); $this->_j++)
            {
                if ($swapped) return true;
                if ($this->_i != $this->_j) {
                    $temp = $this->_tagsPackage[$this->_i];
                    $this->_tagsPackage[$this->_i] = $this->_tagsPackage[$this->_j];
                    $this->_tagsPackage[$this->_j] = $temp;
                    $swapped = true;
                }
            }
            $this->_j = $this->_i + 1;
        }
        return false;
    }

    public function nextMutation()
    {
        if ($this->_mixtureIndex >= count($this->_mixture))
            return false;

        $notMutatedText = !empty($this->_notMutatedTextVariants) ?
            $this->_notMutatedTextVariants[$this->_mutatedVariantIndex] : $this->_text;
        $mixture = $this->_mixture[$this->_mixtureIndex];
        $mixtureLength = mb_strlen($mixture, self::ENC) + 1;

        $tweetText = Tweet::crop($notMutatedText);
        $tweetText = mb_substr($tweetText, 0, Tweet::TW_LEN - $mixtureLength, self::ENC);
        $nextMutation = $tweetText . ' ' . $mixture;

        $this->_mutatedTextVariants[$this->_mutatedVariantIndex][] = $nextMutation;

        $this->_mutatedVariantIndex++;
        if ($this->_mutatedVariantIndex >= count($this->_notMutatedTextVariants)) {
            $this->_mutatedVariantIndex = 0;
            $this->_mixtureIndex++;
        }

        return $nextMutation;
    }

    public static function implodeTags()
    {
        $tags = array();
        foreach (func_get_args() as $arg)
        {
            $tags = array_merge($tags, $arg);
        }
        if (empty($tags)) return '';

        $toImplode = array();
        foreach ($tags as $tag)
        {
            $toImplode[] = self::TAG_CH . $tag;
        }
        return implode(' ', $toImplode);
    }

    protected static function tweet($text, $tags)
    {
        return $text . ' ' . self::implodeTags($tags);
    }

    public function getVariant()
    {
        return self::tweet($this->_text, $this->_tagsPackage);
    }

}
