<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/28/11
 * Time: 10:19 AM
 * To change this template use File | Settings | File Templates.
 */
class CharSubstitutor
{
    private $_text;
    private $_lexicalStruct;

    public $_engToCyr = array(
        'a' => 'а',
        'c' => 'с',
        'e' => 'е',
        'o' => 'о',
        'p' => 'р',
        'y' => 'у',
        'H' => 'Н',
        'T' => 'Т',
    );
    public $_cyrToEng = array();
    public $_variants = array();

    public function __construct($text)
    {
        $this->_text = $text;
        $this->_lexicalStruct = LexicalStruct::parse($text);

        foreach ($this->_engToCyr as $fromC => $toC)
        {
            $fromCU = mb_strtoupper($fromC);
            $toCU = mb_strtoupper($toC);
            if ($fromCU != $fromC && $toCU != $toC) {
                $this->_engToCyr[$fromCU] = $toCU;
            }
        }

        foreach ($this->_engToCyr as $fromC => $toC)
        {
            $this->_cyrToEng[$toC] = $fromC;
        }
    }

    public function getAllSubstitutionVariants()
    {
        return array_merge($this->substitutionVariants($this->_engToCyr), $this->substitutionVariants($this->_cyrToEng));
    }

    private function substitutionVariants($substitution)
    {
        $modifiedStruct = $this->_lexicalStruct;
        $variants = array();
        foreach ($substitution as $engC => $kyrC)
        {
            $allVariants = false;
            while (!$allVariants)
            {
                $offset = 0;
                $tmpStruct = $modifiedStruct;
                while (($offset = LexicalStruct::replaceChar($tmpStruct, $engC, $kyrC, $offset)) !== false)
                {
                    $variants[] = LexicalStruct::implode($tmpStruct);
                    $tmpStruct = $modifiedStruct;
                }
                $allVariants = LexicalStruct::replaceChar($modifiedStruct, $engC, $kyrC) === false;
            }
        }
        return $variants;
    }
}
