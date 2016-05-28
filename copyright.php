<?php

require_once 'functions.php';

$Template->BeginTag('h2', array('style' => 'text-align: center;'));
$Template->Append('Copyright');
$Template->EndLastTag();

$Template->BeginTag('h3');
$Template->Append('Plugin Copyright');
$Template->EndLastTag();

$Template->BeginTag('p');
$Template->Append('Plugins hosted on this site are the intellectual property of their respective authors. The maintainers of this site make an effort to ensure that all plugins hosted on this site are licensed such that they may be downloaded, used and redistributed freely by any user of this site.');
$Template->EndLastTag();

$Template->BeginTag('p');
$Template->Append('Rights other than these, including (but not limited to) modification and sale, may be restricted by the authors of plugins made available on this site.');
$Template->EndLastTag();

$Template->BeginTag('p');
$Template->Append('If you wish to report a copyright issue with any plugin on this site, please contact the administrators immediately. The best way to do so is to ');
$Template->BeginTag('a', array('href' => 'https://github.com/cuberite/plugin-repository/issues/new'));
$Template->Append('file a GitHub issue');
$Template->EndLastTag();
$Template->Append('.');
$Template->EndLastTag();

$Template->BeginTag('h3');
$Template->Append('Repository Copyright');
$Template->EndLastTag();

$Template->BeginTag('p');
$Template->Append('Cuberite Plugin Repository itself is public domain software, however it is distributed alongside and contains some non-public domain components. Full information regarding the copyright of Cuberite Plugin Repository is available ');
$Template->BeginTag('a', array('href' => 'https://github.com/cuberite/plugin-repository/blob/master/LICENSE.md'));
$Template->Append('in the GitHub repository');
$Template->EndLastTag();
$Template->Append('.');
$Template->EndLastTag();

?>
