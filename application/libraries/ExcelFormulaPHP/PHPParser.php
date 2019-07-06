<?php

abstract class PHPParser
{
    //from where do we get token
    public $input;
    //the current lookahead
    public $lookahead;
    
    public function PHPParser(PHPListLexer $input)
    {
        $this->input = $input;
        $this->consume();
    }
    
    /* 
    * if lookahead token type maches tokenType, consume and return else return an error
    */
    public function match($tokenType)
    {
        if($this->lookahead->type ==  $tokenType) {
            $this->consume();
        } else {
            throw new Exception("Expecting token " .
                                $this->input->getTokenName($tokenType) .
                                ":Found " . $this->lookahead);
        }
    }
    
    /* 
    * if lookahead token type maches tokenType, consume and return else return an error
    */
    public function matchValue($tokenValue)
    {
        if($this->lookahead->value !=  $tokenValue) {
            $this->consume();
        } else {
            throw new Exception("Expecting token " .
                                $this->input->getTokenValue($tokenValue) .
                                ":Found " . $this->lookahead);
        }
    }
   
    public function consume()
    {
        $this->lookahead = $this->input->nextToken();
    }
}
?>