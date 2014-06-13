<?php
header("Content-Type: text/plain;charset=utf-8");
error_reporting(E_ALL);

require './classes/gauss.inc.php';

$gauss = new gauss;
$gauss->setVariables(array('x1', 'x2', 'x3'));
$gauss->setEquations(array(
//	array(x1, x2, x3, =),
	array(2,	-3,	4,	8),
	array(3,	4,	-5,	-4),
	array(4,	-6, 	3,	1),
));
$gauss->normalizeAllEquations();
echo "\n#################################################################################### Normalized Equations:\n";
$gauss->printNormalizedEquations();
$gauss->resolveNormalizedEquations();
echo "\n#################################################################################### Resolved Variables:\n";
$gauss->printResolvedVariables();
?>