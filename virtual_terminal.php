<?php

require "classes/virtualTerminal.php";

echo "\n";
echo "------------------------------------------------------------\n";
echo "Virtual Terminal 1.0 \n";
echo "-type 'help' to display available command and usage\n";
echo "------------------------------------------------------------\n";

echo "> ";

$vt = new VirtualTerminal();
while($input = fgets(STDIN)){
	$vt->read($input);
	echo "> ";
}

?>
