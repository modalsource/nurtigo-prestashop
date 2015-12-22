<?php


$sessionId = $_COOKIE['mautic_session_id'];
$leadId = $_COOKIE[$sessionId];
echo $leadId;
