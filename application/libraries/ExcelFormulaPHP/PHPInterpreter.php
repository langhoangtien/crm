<?php
require_once('PHPListLexer.php');
require_once('PHPToken.php');
require_once('PHPListParser.php');
require_once (APPPATH.'libraries/PHPExcel/PHPExcel.php');
require_once(APPPATH.'libraries/PHPExcel/PHPExcel/IOFactory.php');


class PHPInterpreter 
{   
    private $strFormula;
    private $originalFormula;
    private $dataVar;
    
    //number of var and value_not_set , if variable_count == variable_not_set_count  return originalFormula
    private $variable_count          = 0;
    private $variable_not_set_count  = 0;
    
    
    // The constructor
    public function PHPInterpreter($strFormula = '', $dataVar = array())
    {
        $this->strFormula = $strFormula;
        $this->dataVar    = $dataVar;
    }
    
    // set formula    
    public function addFormula($strFormula)
    {
        $this->strFormula      = $strFormula;
        $this->originalFormula = $strFormula;
        return $this;
    }
    
    //set variable data to formula
    public function addDataVariableToFormula($dataVar)
    {
        $this->dataVar = $dataVar;
        return $this;
    }
    
    //get list of variable in formula
    public function listOfVariableFormula()
    {
        if (!empty( $this->strFormula)) {
            $lexer   = new PHPListLexer( $this->strFormula);
            $parser  = new PHPListParser($lexer);
            $listVar = $parser->listVar();
            $listVarCode = [];
           
            for ($i = 0;$i < count($listVar);$i++) {
               $listVarCode[] = str_replace('$','',$listVar[$i]->value);
            }
        }
        
        return $listVarCode;
    }

    //execute 
    public function run()
    {
        if(strlen($this->strFormula)>0) {
           return $this->runFormulaUsingExcel();
        }
    }
    
    public function runFormulaUsingExcel()
    {                                                                                                                                                                                                                                    
        $excelCalculation = new PHPExcel_Calculation();
       
        $this->convertFormulaString($this->strFormula, $this->dataVar);
        if ($this->variable_count == $this->variable_not_set_count) {
             return $this->originalFormula;
        } elseif (strpos($this->strFormula,'value_not_set') !== false) {
               return $this->strFormula;
        }

        $parsedFormula      = $excelCalculation->parseFormula($this->strFormula,null);
        $calculatedValue    = $excelCalculation->_processTokenStack($parsedFormula, null, null);
        return $calculatedValue;
    }
    

    
    /* 
    * Convert input formula ,which contains variable, into calculable formula
    * @string $strFormula
    * @array $dataVar('variableName => variableValue')
    * @return void
    */
    
    private function convertFormulaString($strFormula, $dataVar = array()) 
    {
        if (!empty($strFormula) && !empty($dataVar)) {
            $lexer     = new PHPListLexer($strFormula);
            $parser    = new PHPListParser($lexer);
            $listVar   = $parser->listVar();
            $listToken = $parser->listToken();
            $listVarCode  = [];
            $listTokenVal = [];
            
            for ($i = 0;$i < count($listToken);$i++) {
               $listTokenVal[] = $listToken[$i]->value;
            }
            
            for ($i = 0;$i < count($listVar);$i++) {
               $listVarCode[] = str_replace('$','',$listVar[$i]->value);
            }
            // list of value which is derived from list of variable
            if (!empty($listVarCode)) {
                foreach ($listVarCode as $value) {
                    
                    $this->variable_count++;
                    
                    if (!isset($dataVar[$value]) ||  $dataVar[$value]== '') {
                        $this->variable_not_set_count++;
                        $listVariableValue['$'.$value] = '[value_not_set:'.$value.']';
                    } elseif (isset($dataVar[$value]) &&  is_numeric($dataVar[$value])) {
                        $listVariableValue['$'.$value] = $dataVar[$value];
                    }  elseif (isset($dataVar[$value]) &&  is_string($dataVar[$value]) && strpos($dataVar[$value], '$') == false) {
                        $listVariableValue['$'.$value] = '"'.$dataVar[$value].'"';
                    } else {
                        $listVariableValue['$'.$value] = '('.implode('', explode('=',$dataVar[$value], 2)).')';
                    }
                   
                }
                
                for ($i = 0;$i < count($listTokenVal);$i++) {
                    if ( isset($listVariableValue[$listTokenVal[$i]])) {
                        $listTokenVal[$i] = $listVariableValue[$listTokenVal[$i]];
                    }
                }
                //replace variable in string with value 
                $this->strFormula = implode('', $listTokenVal);
                //recursion
                $this->convertFormulaString($this->strFormula, $dataVar);
            }  
        }   
    }
}
?>