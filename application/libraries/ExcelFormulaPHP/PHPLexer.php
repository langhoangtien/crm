<?php 
 abstract class PHPLexer
 {
    //represent end of file char
    const EOF      = null;
    //reprsent EOF token type
    const EOF_TYPE = 1;
    //input String
    protected $inputStr;
    //index into input of current character
    protected $indexCurrentCharacter = 0;
    //current character
    protected $currentCharacter;
    
    public function PHPLexer($inputStr)
    {
        $this->inputStr = $inputStr;
        //prime lookahead
        $this->currentCharacter = substr($inputStr, $this->indexCurrentCharacter,1);
        
    }
    
    /* 
    * Move one character, dectect "end of file"
    */
    public function consume()
    {
        $this->indexCurrentCharacter++;
        if ($this->indexCurrentCharacter >= strlen($this->inputStr)) {
            $this->currentCharacter = PHPLexer::EOF;
        }
        else {
            $this->currentCharacter = substr($this->inputStr, $this->indexCurrentCharacter, 1);
        }
    }
    
    public abstract function nextToken();
    public abstract function getTokenValue($tokenType);
    
    
 }


?>