<?php

/**
 * This class resolves multiple equations useing the Gauss method.
 *
 * @author	Fabian Hauser <fabianYY@fh2.ch>
 * @license	Creative Commons by-sa: https://creativecommons.org/licenses/by-sa/3.0/
 */
class gauss {
	
	/**
	 * Array of variables and results
	 * 
	 * @var	array	$variables:	Array of $variable => $value. (value initially 0)
	 */
	private $variables = array();
	
	/**
	 * array of equations
	 * 
	 * @var	array	$equations: Array of equations; last number is result.
	 */
	private $equations = array();
	
	/**
	 * Prepared (normalized) equations
	 * 
	 * @var	array	$normalized_equations: Array of equations in a normalized form
	 */
	private $normalized_equations = array();
	
	
	
	/**
	 * Set array of variable names
	 * 
	 * @param	array	$variables:	Array of variable names, eg: array('x1', 'x2')
	 * 
	 * @return	bool	variables successfull set?
	 */
	public function setVariables($variables) {
		
		// clear this class variables
		$this->variables = array();
		
		// loo pthrough and set
		foreach($variables as $variable) {
			$this->variables[$variable] = 0;
		}

		return !($this->variables === array());
	}
	
	/**
	 * Get variable array
	 * 
	 * @return	array	Array with $variable => $value
	 */
	public function getVariables(){
		return $this->variables;
	}
	
	/**
	 * Set array of equations
	 * 
	 * @param	array	$variables:	Array of equations including result as last value, eg: array(array(3,6,9))
	 * 
	 * @return	bool	variables successfull set?
	 */
	public function setEquations($equations) {
		
		// clear this class variables
		$this->equations = array();
		
		// loo pthrough and set
		$this->equations = $equations;

		return !($this->equations === array());
	}
	
	
	/**
	 * Normalize all equations 
	 * 
	 * @return	void
	 */
	public function normalizeAllEquations() {
		
		$equations_stack = $this->equations;
		$total_equations = count($equations_stack);
		
		while(count($equations_stack) > 0) {
		
			// get first element from stack
			$root_equation = array_shift($equations_stack);
			
			// save computed equation
			$this->normalized_equations[] = $root_equation;
			
			// calculate the current variable number (one less, to match array index)
			$current_variable_number = $total_equations - count($equations_stack) -1;

			// go trough rest of stack, if not the last equation
			if(count($equations_stack) !== 0) {
				foreach($equations_stack as &$current_equation) {
					
					// check that value is not allready zero
					if(floatval($current_equation[$current_variable_number]) !== floatval(0)) {
						
						// normalize equation for next calculation
						$current_equation = $this->normalizeTo($current_equation, $current_variable_number, floatval($root_equation[$current_variable_number]), true);
			
						// summate last equation and current equation.
						$current_equation = $this->summateEquations($current_equation, $root_equation);	
					}
				}
			}
		}
		
	}
	
	
	/**
	 * Normalize a specific variable in the equation to @param $destination.
	 * 
	 * @param	array	$equation:	All numbers from the equation
	 * @param	int		$field:		Index of number to normalize
	 * @param	int		$destination: The number that should be received
	 * 
	 * @return	array	The normalized equation
	 */
	private function normalizeTo($equation, $field = 0, $destination = 0, $negate = false) {
		
		// get the basis number that should be set to 1/-1
		$divisor = floatval($equation[$field]);

		// ...and check that this is really a number!
		if(!(is_float($divisor) || is_integer($divisor)) || $divisor === floatval(0)) {
			throw new Exception('Invalid Value as divisor passed to normalizeTo! ('.$divisor.')', 1);
		}
		
		// normalize every number in equation
		foreach($equation as &$number) {
			$number = floatval(floatval($number) * floatval($destination) / floatval($divisor));
			if($negate) {
				$number = floatval(floatval($number) * -1);
			}
		}
		
		return $equation;
	}
	
	
	/**
	 * Summate two passed equation arrays.
	 * 
	 * @param	array	$equation_1:	First equation
	 * @param	array	$equation_2:	Second equation
	 * 
	 * @return	array	Sum equation of both equations.
	 */
	private function summateEquations($equation_1, $equation_2) {
		$max = count($equation_1);
		$sum = array();
		
		for($i=0; $i < $max; $i++) {
			$sum[$i] = floatval(floatval($equation_1[$i]) + floatval($equation_2[$i])); 
		}
		return $sum;
	}
	
	
	/**
	 * Resolve all normalized equations
	 * 
	 * @return void
	 */
	public function resolveNormalizedEquations() {
		
		// get normalized equations (in normal order, later this will be read trough array_pop)
		$normalized_equations = $this->normalized_equations;
		
		// get variables in reverse
		$variables = array_reverse(array_keys($this->variables), true);

		$variables_count = count($variables);
		
		// loop through variables
		foreach($variables as $variable_number => $variable_name) {
			
			// get corresponding equation
			$current_equation = array_pop($normalized_equations);
			
			// get current equation result
			$current_equation_result = array_pop($current_equation);
			
			// reset variables sum
			$variables_sum = 0;
			
			// summate all variables multiplicated by theyr worth
			foreach($variables as $sub_variable_number => $sub_variable_name) {
				$variables_sum += floatval(floatval($current_equation[$sub_variable_number]) * floatval($this->variables[$sub_variable_name]));
			}

			$current_variable_worth = floatval($current_equation[$variable_number]);
			if($current_variable_worth == 0) {
				throw new Exception('Variable '.$variable_name.' could be any number.', 1);
				
			} else {
				$this->variables[$variable_name] = floatval((floatval($current_equation_result) - floatval($variables_sum)) / floatval($current_variable_worth));
			}
			// calculate variable
			
		}
		
	}
	
	
	/**
	 * Print out all normalized equations
	 * 
	 * @return	void
	 */
	public function printNormalizedEquations() {
		print_r($this->normalized_equations);
	}
	
	/**
	 * Print out all normalized equations
	 * 
	 * @return	void
	 */
	public function printResolvedVariables() {
		print_r($this->variables);
	}
}

?>
