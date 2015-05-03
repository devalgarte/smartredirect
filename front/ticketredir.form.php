<?php

include("../../../inc/includes.php");
require_once("../inc/ticketredir.class.php");

// http://localhost/index.php?redirect=plugin_smartredirect_2015043001

if(!isset($_GET['id'])) {
	if($_SESSION["glpiactiveprofile"]["interface"] == 'helpdesk') {
		Html::redirect($CFG_GLPI["root_doc"]."/front/helpdesk.public.php");
	} else {
		Html::redirect($CFG_GLPI["root_doc"]."/front/central.php");
	}
}

$user_id = Session::getLoginUserID();
$ticket_id = $_GET['id'];

$pref = new PluginSmartredirectPreference();

// Charge les préférences de l'utilisateur, ou s'il n'en n'a pas celles par défaut
if(!$pref->getFromDB($user_id)) {
	$pref->getFromDB('0');
}

// calcule et redirige, puis si nécessaire élargit le champ des entités
if($pref->getField('is_activated')) {
	$roles = PluginSmartredirectTicketredir::getRoles($user_id, $ticket_id);
	
	$profile_id = $pref->getField($roles);
	
	if($profile_id != $_SESSION['glpiactiveprofile']['id']) {
		Session::changeProfile($profile_id);
	}
	$ticket = new Ticket();
	$ticket->getFromDB($ticket_id);
	
	if (!Session::haveAccessToEntity($ticket->getEntityID())) {
		Session::changeActiveEntities("all");
	}
}

// renvoi vers le ticket lui-même
//Html::redirect($CFG_GLPI["root_doc"]."/index.php?redirect=ticket_".$ticket_id."_".$_GET['forcetab']);
Html::redirect($CFG_GLPI["root_doc"]."/front/ticket.form.php?id=".$ticket_id."&forcetab=".$_GET['forcetab']);

