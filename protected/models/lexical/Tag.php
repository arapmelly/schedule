<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/28/11
 * Time: 2:45 PM
 * To change this template use File | Settings | File Templates.
 */
class Tag
{
    const TAG_CH = '#';

    public static function tagsPackages($text, $tags)
    {
        $tagsPackages = array();
        $tagsPackage = array();
        while (($tag = array_shift($tags)) !== null)
        {
            $tweet = self::tweet($text, array_merge($tagsPackage, array($tag)));
            $length = Tweet::length($tweet);
            if ($length <= Tweet::TW_LEN) {
                $tagsPackage[] = $tag;
            } else {
                if (!empty($tagsPackage)) {
                    array_unshift($tags, $tag);
                    $tagsPackages[] = $tagsPackage;
                    $tagsPackage = array();
                }
            }
        }
        if (!empty($tagsPackage))
            $tagsPackages[] = $tagsPackage;

        return $tagsPackages;
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

    public static function tweet($text, $tags)
    {
        return $text . ' ' . self::implodeTags($tags);
    }

    public static function variants($text, $tags)
    {
        $variantsTags = array();
        foreach (self::tagsPackages($text, $tags) as $tagPackage) {
            $variantsTags[] = self::tweet($text, $tagPackage);
        }
        return $variantsTags;
    }
}
