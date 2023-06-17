<?php
/**
 * Root file for launching unit testing framework
 * 
 * Moved to root directory in order to provide maximum 
 * compatibility with older care2x API
 * 
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2005-2013, Segworks Technologies Corporation
 */
global $root_path;
$root_path = './';
include 'tests/index.php';