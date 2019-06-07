<?php
require_once '../vendor/restler.php';
use Luracast\Restler\Defaults;
Defaults::$smartAutoRouting = false;
$r = new Restler();
$r->addAPIClass('facture');
$r->addAPIClass('reservation');
$r->addAPIClass('individu');
$r->addAPIClass('templacement');
$r->addAPIClass('activites');
$r->handle();
?>
