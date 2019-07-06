<?php
require_once('PHPLexer.php');

class PHPListLexer extends PHPLexer
{
    const NAME         = 2;
    const STR          = 3;
    const VARIABLE     = 4;
    const CHARACTER    = 5;
 
    
    static $tokenNames = array("n/a", "EOF", 
                               "NAME", "STRING",
                               "VARIABLE", "CHARACTER"
                               );
    
    public function getTokenValue($tokenIndex)
    {
        return PHPListLexer::$tokenNames[$tokenIndex];
    }
    
    public function PHPListLexer($inputStr)
    {
        parent::__construct($inputStr);
    }
    
    public function isLetter()
    {
        return preg_match("/[a-z]/",$this->currentCharacter) ||
               preg_match("/[A-Z]/",$this->currentCharacter);
    }
    
    public function isNotDoubleQuote()
    {
        return preg_match('/[^"]/',$this->currentCharacter);
    }
    
    public function isDoubleQuote()
    {
        return preg_match('/[\"]/',$this->currentCharacter);
    }
    
    
    public function isDollar()
    {
        return preg_match("/[\$]/",$this->currentCharacter);
    } 
    
    public function isNumberOrLetter()
    {
        return preg_match("/[a-z0-9]/",$this->currentCharacter)||
               preg_match("/[A-Z0-9]/",$this->currentCharacter);
    } 
    
    public function nextToken()
    {
        while ( $this->currentCharacter != self::EOF ) {
            switch ( $this->currentCharacter ) {
                case ' ' : case '\r':  case '\t': case '\n': 
                    $this->whiteSpace();
                    continue;
                case '"' :
                    return $this->STR();
                default:
                    if ($this->isLetter() )return $this->NAME();
                    if ($this->isDollar() ) return $this->VARIABLE();
                    return $this->CHARACTER();
            }
        }
        return new PHPToken(self::EOF_TYPE, "<EOF>");
    }
    
    /* 
    * NAME is sequence of >= 1 letter
    */
    public function NAME()
    {
        $buf = '';
        do {
            $buf .= $this->currentCharacter;
            $this->consume();
        }
        while ($this->isLetter());
        
        return new PHPToken(self::NAME, $buf);
    }
    
    /* 
    * STR is a string 
    */
    public function STR()
    {
        $buf = '';
        do {
            $buf .= $this->currentCharacter;
            $this->consume();
        }
        while ($this->isNotDoubleQuote());
        $buf .= $this->currentCharacter;
        $this->consume();
        return new PHPToken(self::STR, $buf);
    }
    
    /* 
    * variable which is sequence of >= 1 letter, contains $ character
    */
    public function VARIABLE()
    {
        $buf = '';
        do {
            $buf .= $this->currentCharacter;
            $this->consume();
        }
        while ($this->isNumberOrLetter());
        
        return new PHPToken(self::VARIABLE, $buf);
    }
    
    /* 
    * CHARACTER 
    */
    public function CHARACTER()
    {
        $buf = '';
        $buf .= $this->currentCharacter;
        $this->consume();
        
        return new PHPToken(self::CHARACTER, $buf);
    }
    
    /*
    * White space : (' '|'\t'|'\n'|'\r') 
    * Ignore any white space
    */
    public function whiteSpace()
    {
        while (ctype_space($this->currentCharacter)){
            $this->consume();
        }
    }
    
}
?>