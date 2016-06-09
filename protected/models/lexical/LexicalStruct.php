<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/28/11
 * Time: 10:38 AM
 * To change this template use File | Settings | File Templates.
 */
class LexicalStruct
{
    const ENC = 'utf8';

    const CHAR = 'char';
    const WORD = 'word';
    const TAG = 'tag';
    const URL = 'url';

    private $_text;
    private $_struct;

    private static $_types = array(
        self::URL => '^(?#Protocol)(?:(?:ht|f)tp(?:s?)\:\/\/|~\/|\/)?(?#Username:Password)(?:\w+:\w+@)?(?#Subdomains)(?:(?:[-\w]+\.)+(?#TopLevel Domains)(?:com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum|travel|[a-z]{2}))(?#Port)(?::[\d]{1,5})?(?#Directories)(?:(?:(?:\/(?:[-\w~!$+|.,=]|%[a-f\d]{2})+)+|\/)+|\?|#)?(?#Query)(?:(?:\?(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=?(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)(?:&(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=?(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)*)*(?#Anchor)(?:#(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)?',
        self::TAG => '^(#\w+)\W*',
        self::WORD => '^(\w+)\W*',
        self::CHAR => '^(\W)'
    );

    public function __construct($text)
    {
        $this->_text = $text;
        $this->_struct = self::parse($text);
    }

    public static function parse($text)
    {
        $struct = array();
        while (!empty($text))
        {
            $value = '';
            foreach (self::$_types as $type => $regex)
            {
                $matches = array();
                if (mb_ereg($regex, $text, $matches)) {
                    $value = !empty($matches[1]) ? $matches[1] : $matches[0];
                    $struct[] = array('type' => $type, 'value' => $value);
                    break;
                }
            }
            $text = mb_substr($text, mb_strlen($value, self::ENC), mb_strlen($text, self::ENC), self::ENC);
        }

        return $struct;
    }

    public static function implode($struct)
    {
        $text = '';
        foreach ($struct as $s)
        {
            $text .= $s['value'];
        }
        return $text;
    }

    public static function replaceChar(&$lexicalStruct, $search, $replacement, $offset = 0, $first = true)
    {
        $lastReplacedIdx = false;
        $currLength = 0;
        foreach ($lexicalStruct as &$item)
        {
            if ($item['type'] == LexicalStruct::WORD) {
                mb_ereg_search_init($item['value']);
                $pos = mb_ereg_search_pos($search);
                if ($pos !== false && $currLength + $pos[0] >= $offset) {
                    $item['value'] = substr_replace($item['value'], $replacement, $pos[0], $pos[1]);
                    $lastReplacedIdx = $currLength + $pos[0] + 1;
                    if ($first)
                        break;
                }
            }
            $currLength += mb_strlen($item['value']);
        }
        return $lastReplacedIdx;
    }

}
