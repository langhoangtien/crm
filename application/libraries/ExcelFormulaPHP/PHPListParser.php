<?php
require_once('PHPParser.php');

class PHPListParser extends PHPParser
{
    public $variable  = [];
    public $allTokens = [];
    public function PHPListParser(PHPLexer $input)
    {
        parent::__construct($input);
    }
    
    public function listVar()
    {
        $result = $this->elements()['listVar'];
        return $result;
    }
    
    public function listToken()
    {
        $result = $this->elements()['listToken'];
        return $result;
    }
    
    public function elements() {
        while ($this->lookahead->type != PHPListLexer::EOF_TYPE ) {
            if ($this->lookahead->type == PHPListLexer::NAME ) {
                $this->allTokens[] = $this->lookahead;
                $this->match(PHPListLexer::NAME);
            } elseif ($this->lookahead->type == PHPListLexer::CHARACTER ) {
                $this->allTokens[] = $this->lookahead;
                $this->match(PHPListLexer::CHARACTER);
            } elseif ($this->lookahead->type == PHPListLexer::VARIABLE ) {
                $this->allTokens[] = $this->lookahead;
                $this->variable[] = $this->lookahead;
                $this->match(PHPListLexer::VARIABLE); 
            } elseif ($this->lookahead->type == PHPListLexer::STR ) {
                $this->allTokens[] = $this->lookahead;
                $this->match(PHPListLexer::STR); 
            }
        }
        return array('listVar' => $this->variable, 'listToken' => $this->allTokens) ;
    }

}
?>