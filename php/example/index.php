<?php
define("_CONST_", 1);

include "ELPClasses.php";

$parser = new ELPParser;

if (empty($_GET)) {
	$parser->addLanguageFromFile("lang1.txt");
	$text = file_get_contents("./snippet.html");
	$parser->setMode(2);
	$t = $parser->parse($text);
	echo "<h2>Original</h2>" . $text . "<br><br><h2>Parsed</h2>";
	echo $t;
	echo "<br><a href=\"index.php?t\">Example 2</a>";
} else {
	$parser->addLanguageFromFile("lang2.txt");
	$text = file_get_contents("./snippet2.html");
	$parser->setMode(3);
	$t = $parser->parse($text);
	echo "<h2>Original</h2>" . $text . "<br><br><h2>Parsed</h2>";
	echo $t;
	echo "<br><a href=\"index.php\">Example 1</a>";
}

?>
