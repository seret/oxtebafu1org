<?php
if (strstr($_POST['recording'],"/")) exit;
if (strstr($_POST['stream'],"/")) exit;
if (strstr($_POST['recording'],"..")) exit;
if (strstr($_POST['stream'],"..")) exit;

if (!$_POST['recording']) exit;
if (!$_POST['stream']) exit;

  // save file
  $fp=fopen("recordings/".$_POST['recording'].".vwr","w");
  if ($fp)
  {
    fwrite($fp, $_POST['stream'].";;;".time().";;;".$_POST['rectime']);
    fclose($fp);
  }

  if (file_exists("snapshots/".$_POST['stream'].".jpg"))  copy("snapshots/".$_POST['stream'].".jpg","snapshots/".$_POST['recording'].".jpg");
?>loadstatus=1
