<?php
$steamauth['apikey'] = STEAM_API_KEY;
$steamauth['domainname'] = STEAM_DOMAIN_NAME;
$steamauth['logoutpage'] = STEAM_LOGOUT_PAGE;
$steamauth['loginpage'] = STEAM_LOGIN_PAGE;
	
if (empty($steamauth['apikey'])) {
	die("<div style='display: block; width: 100%; background-color: red; text-align: center; padding: 20px;'>SteamAuth:<br>Please supply an API-Key!<br>Edit 'skins/class/config.php' and add your Steam API Key.</div>");
}
if (empty($steamauth['domainname'])) {$steamauth['domainname'] = $_SERVER['SERVER_NAME'];}
if (empty($steamauth['logoutpage'])) {$steamauth['logoutpage'] = $_SERVER['PHP_SELF'];}
if (empty($steamauth['loginpage'])) {$steamauth['loginpage'] = $_SERVER['PHP_SELF'];}
?>
