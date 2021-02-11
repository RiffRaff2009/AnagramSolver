<form action="solver.php" method="post">
	To Solve: <input type="text" name="toSolve"> <br>
	<input type="submit">
</form>

<?php
/*
 Plugin Name: 	Anagram Solver
 Plugin URI: 	http://www.enginehouse.xyz/anagram-solver/
 Description: 	
 Version:     	1.0
 Author:      	Engine House
 Author URI:  	http://www.enginehouse.xyz/
 License:     	GPL3
 License URI: 	https://www.gnu.org/licenses/gpl-3.0.html
 Text Domain: 	wporg
 Domain Path: 	/languages
 
 Anagram Solver is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 any later version.
 
 Anagram Solver is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with Anagram Solver. If not, see https://www.gnu.org/licenses/gpl.txt
*/


//Function returns true if exact match is found
function is_anagram($string_1, $string_2) 
    { 
        if (count_chars($string_1, 1) == count_chars($string_2, 1)) 
            return true; 
        else 
            return false;        
    } 

//Function returns array of matches including partial matches	
function anagrams($localDictionary, $toSolve)	
	{
		$dictionaryMatches = [];
				
		for($index = 0; $index < count($localDictionary); $index++){
			$pattern = "/^[$localDictionary[$index]]+\$/im";
			if(preg_match($pattern, $toSolve)){
				if(is_anagram($toSolve, $localDictionary[$index])){
					array_push($dictionaryMatches, $localDictionary[$index]);
				}
			}
		}
		return $dictionaryMatches;	
	}

//Possible answer
function anagramsV2($localDictionary, $toSolve){

	$dictionaryMatches = [];
	//for each word in the dictionary
	foreach ($localDictionary as $testWord){
		
		if ($toSolve != $testWord){
			//get array of letters in my anagram
			$toSolveArray = str_split($toSolve);
			//get array of letters in this word
			$testWordArray = str_split($testWord);
			$match = false;
			//for each letter in word
			foreach ($testWordArray as $letter){
				//is this letter also in the anagram?
				if (in_array($letter, $toSolveArray)){
					//if yes, remove letter from anagram
					$key = array_search($letter, $toSolveArray);
					unset($toSolveArray[$key]);
					$match = true;
				} else {
					//if no, move on to the next word
					$match = false;
					break;
				}						
			}
			//if we get here, it's an anagram! Add word to matches
			if($match){
				array_push($dictionaryMatches, $testWord);
			}
		}
	}
	return $dictionaryMatches;
}

function mysort($a,$b){
    return strlen($b)-strlen($a);
}

function printMatches($matches){
	echo ("Matches found: <br>");
	$wordLength;
	$numberOfColumns = 4;
	$columnIndex = 0;
	usort($matches, 'mysort');
	$previousWordLength = strlen($matches[0]);
	$anagramNumber = 1;
	echo ("<table>");
	echo ("<tr>");
	foreach($matches as $match){
		$wordLength = strlen($match);
		if ($wordLength > 1 && $anagramNumber !=1){
			if ($wordLength < $previousWordLength){
				echo ("</tr>");
				echo ("<tr>");
				echo ("<td colspan=\"4\"><b>$wordLength Letter Words</b></td>");
				echo ("</tr>");
				echo ("<tr>");
				echo ("<td>$anagramNumber. $match</td>");
				$columnIndex = 0;
			} else if ($wordLength == $previousWordLength){
				if ($columnIndex < $numberOfColumns){
					echo ("<td>$anagramNumber. $match</td>");
					$columnIndex++;
				} else {
					echo ("<tr>");
					echo ("<td>$anagramNumber. $match</td>");
					$columnIndex = 0;
				}
			}
			
		} else if ($anagramNumber == 1){
			echo ("<tr>");
			echo ("<td colspan=\"4\"><b>$wordLength Letter Words</b></td>");
			echo ("</tr>");
			echo ("<tr>");
			echo ("<td>$anagramNumber. $match</td>");
		}
		$anagramNumber++;
		$previousWordLength = $wordLength;
	}
	echo ("</tr>");
	echo ("</table>");
}

$filename = "Lang/en_GB.txt";
$localDictionary = file($filename, FILE_IGNORE_NEW_LINES);
$toSolve = strtolower($_POST["toSolve"]);
$toSolve = preg_replace('/\s+/', '', $toSolve);
$toSolve = preg_replace('/\d+/', '', $toSolve);
echo("Anagram to solve is: $toSolve <br>");

$matches = anagramsV2($localDictionary, $toSolve);
if (count($matches) > 0){
	printMatches($matches);	
}

?>