<?php
require_once(APPPATH.'libraries/ExcelFormulaPHP/PHPInterpreter.php');
/**
 * ConvertFormula Class
 *
 * @subpackage	BizHelper
 * @category	ExcelFormula
 * @author		BizDev
 */
class ConvertFormula 
{   

    /**
	 * input formula string
	 *
	 * @var	string
	 */
    protected $strCode;
    
    /**
	 * PHPInterpreter
	 *
	 * @var	object
	 */
    protected $interpreter;
    
    
    /**
	 * A array, contains key as variable name get from $strCode and value as variable value
	 *
	 * @var	array(variableName => variableValue)
	 */
    protected $dataVar;
    
    // The constructor
	public function ConvertFormula($strCode = '', $dataVar = array())
    {
        $this->strCode = $strCode;
        $this->dataVar = $dataVar;
        $this->interpreter = new PHPInterpreter();
    }
    
    public function setFormula($strFormula)
    {
        $this->strCode = $strFormula;
        return $this;
    }
    
    public function setDataVariable($dataVar)
    {
        $this->dataVar = $dataVar;
        return $this;
    }
    
    
    // Calculate input string without variable
    public function calculate()
    {
        return $this->interpreter->addFormula($this->strCode)->run();
    }
    
    // Get list of variable in a input string
    public function getListVariable()
    {
        return $this->interpreter->addFormula($this->strCode)->listOfVariableFormula();
    }
    
    //Run a formula, which is a string, with array of variable
    public function executeFormula()
    {
       return $this->interpreter->addFormula($this->strCode)->addDataVariableToFormula($this->dataVar)->run();
    }
}