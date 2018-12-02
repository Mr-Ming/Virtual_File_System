<?php

require "classes/fileSystem.php";

$fs = new FileSystem();

$fs->mkdir('usr');

$fs->cd('usr');
$fs->mkdir('orange');
$fs->cd('orange');
$fs->mkdir('color');
$fs->mkdir('red/yellow');
$fs->cd('red/yellow');
echo $fs->pwd() . "\n";
$fs->cd('..');
echo $fs->pwd() . "\n";
//$fs->cd('usr');
$fs->mkdir('/local');
$fs->mkdir('/usr/food');
$fs->rmdir('/usr/orange/red');
$fs->cd('../');
echo $fs->pwd() . "\n";
$fs->rmdir('/usr/orange/red');

$fs->dumpFileSystem();
/*
$fs = new FileSystem();
$fs->cd('usr');
$fs->mkdir('local');
$fs->cd('local');
echo $fs->pwd();
$fs->cd('..');
$fs->mkdir('share');
$fs->mkdir('share/info');
$fs->cd('share/info');
echo $fs->pwd();
*/
?>