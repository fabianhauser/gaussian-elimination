<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

		<title>Gauss calculator</title>
		<meta name="description" content="Gauss calculator implementation" />
		<meta name="author" content="Fabian Hauser" />

		<meta name="viewport" content="width=device-width; initial-scale=1.0" />
		<style>
			input {
				width: 80px;
				text-align: right;
			}
			th input {
				font-weight: bold;
				text-align: center;
			}
			table {
				border-collapse: collapse;
			}
			
			table tr:first-of-type {
				border-bottom: solid 2px black;
			}
			table td:last-of-type, table th:last-of-type {
				border-left: solid 2px black;
			}
			
			table td, table th {
				border: solid 1px black;
			}
		</style>

	</head>

	<body>
		<div>
			<header>
				<h1>Gauss calculator</h1>
			</header>
			<nav>
				<ul>
					<li><a href="index.php">Calculator</a></li>
					<li><a href="source.php">Source of gauss'class</a></li>
				</ul>
			</nav>

			<div>
				<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
					<?php
						$variables_count = isset($_POST['variables_count'])? intval($_POST['variables_count']) : 3;
					 ?>
					<label>Number of variables: <input type="number" name="variables_count" value="<?php echo htmlspecialchars($variables_count); ?>"><input type="submit" value="Refresh" name="refresh" /></label>
					<table>
						<?php
						// header
						$prefix = 'variables';
						echo '<tr>';
						for($varI = 0; $varI<=$variables_count-1; $varI++) {
							
							$name = $prefix.'['.$varI.']';
							$value = isset($_POST[$prefix][$varI])? $_POST[$prefix][$varI] : 'x'.($varI+1) ;
							
							echo '<th>
								<input name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'" >
								</th>';
						}
						echo '<th>=</th></tr>';
						
						// equations
						$prefix = 'equations';
						for($eqI = 0; $eqI<=$variables_count-1; $eqI++) {
							echo '<tr>';
							for($varI = 0; $varI<=$variables_count; $varI++) {
								
								$name = $prefix.'['.$eqI.']['.$varI.']';
								$value = isset($_POST[$prefix][$eqI][$varI])? $_POST[$prefix][$eqI][$varI] : '';
								
								echo '<td>
									<input name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'" >
									</td>';
							}
							echo '</tr>';
						}
						?>
					</table>
					<input type="submit" value="Calculate!" name="calculate">
				</form>
			</div>
			<?php if(isset($_POST['calculate'])) { ?>
			<div>
				<h2>Resultate</h2>
				<?php
				try {
					require './classes/gauss.inc.php';
					
					$equations = array();
					foreach($_POST['equations'] as $equation_number => $equation) {
						$new_equation = array();
						foreach($equation as $number) {
							$new_equation[] = floatval(eval('return ' . preg_replace('/[^0-9\-\/\+\*]/i', '', $number) . ';'));
						}
						$equations[] = $new_equation;
					}

					$gauss = new gauss;
					$gauss->setVariables($_POST['variables']);
					$gauss->setEquations($equations);
					$gauss->normalizeAllEquations();
					$gauss->resolveNormalizedEquations();
					$result = $gauss->getVariables();
					
					echo '<table><tr><th>Variable</th><th>Value</th></tr>';
					foreach($result as $variable_name => $variable_value) {
						echo '<tr><td>' . htmlspecialchars($variable_name) . '</td><td>' . htmlspecialchars(number_format($variable_value, 3)) . '</td></th>';
					}
					echo '<table>';
					
				} catch(Exception $e) {
					echo '<p>Following error occured at runtime:</p><pre>'.htmlspecialchars($e).'</pre>';
				}
				?>
			</div>
			<?php
				}
			?>
			<footer>
				<p>
					&copy; Copyright by <a href="http://fabianhauser.ch/">Fabian Hauser</a>
				</p>
			</footer>
		</div>
	</body>
</html>
