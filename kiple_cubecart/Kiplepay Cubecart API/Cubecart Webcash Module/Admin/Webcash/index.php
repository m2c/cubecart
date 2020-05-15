<?php
/*
+--------------------------------------------------------------------------
|   CubeCart v3.0.17
|   ========================================
|   by Alistair Brookbanks
|	CubeCart is a Trade Mark of Devellion Limited
|   Copyright Devellion Limited 2005 - 2006. All rights reserved.
|   Devellion Limited,
|   5 Bridge Street,
|   Bishops Stortford,
|   HERTFORDSHIRE.
|   CM23 2JU
|   UNITED KINGDOM
|   http://www.devellion.com
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Tuesday, 17th July 2007
|   Email: sales (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	index.php
|   ========================================
|	Configure Webcash
+--------------------------------------------------------------------------
*/
include("../../../../includes/ini.inc.php");
include("../../../../includes/global.inc.php");
require_once("../../../../classes/db.inc.php");
$db = new db();
include_once("../../../../includes/functions.inc.php");
$config = fetchDbConfig("config");

include_once("../../../../language/".$config['defaultLang']."/lang.inc.php");
$enableSSl = 1;
include_once("../../../../includes/sslSwitch.inc.php");
include("../../../includes/auth.inc.php");
include("../../../includes/header.inc.php");

if(permission("settings","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}

if(isset($_POST['module'])){
	include("../../status.php");
	include("../../../includes/functions.inc.php");
	$module = fetchDbConfig($_GET['folder']);
	$msg = writeDbConf($_POST['module'], $_GET['folder'], $module);
	
}
$module = fetchDbConfig($_GET['folder']);
?>
<p><a href="http://www.webcash.com.my"><img src="logo.gif" alt="" border="0" title="" /></a></p>
<?php 
if(isset($msg)){ 
	echo stripslashes($msg); 
} 
?>
<p class="copyText"><b>Webcash - Malaysia e-Money Service</b></p>

<form action="<?php echo $GLOBALS['rootRel'];?>admin/modules/<?php echo $_GET['module']; ?>/<?php echo $_GET['folder']; ?>/index.php?module=<?php echo $_GET['module']; ?>&amp;folder=<?php echo $_GET['folder']; ?>" method="post" enctype="multipart/form-data">
<table border="0" cellspacing="0" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle">Configuration Settings </td>
  </tr>
  <tr>
    <td align="left" class="tdText"><strong>Status:</strong></td>
    <td class="tdText">
	<select name="module[status]">
		<option value="1" <?php if($module['status']==1) echo "selected='selected'"; ?>>Enabled</option>
		<option value="0" <?php if($module['status']==0) echo "selected='selected'"; ?>>Disabled</option>
    </select>
	</td>
  </tr>
   <tr>
  	<td align="left" class="tdText"><strong>Description:</strong>
	</td>
    <td class="tdText"><input type="text" name="module[desc]" value="<?php if( $module['desc']!='' ) echo $module['desc']; else echo 'Webcash'; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
  <td align="left" class="tdText"><strong>Webcash Merchant ID:</strong></td>
    <td class="tdText"><input type="text" name="module[productId]" value="<?php echo $module['productId']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
  <td align="left" class="tdText"><strong>Webcash Merchant Name:</strong></td>
    <td class="tdText"><input type="text" name="module[productName]" value="<?php echo $module['productName']; ?>" class="textbox" size="30" /></td>
  </tr>
  <tr>
  <td align="left" class="tdText"><strong>Currency:</strong></td>
    <td class="tdText">Ensure the "Default Currency" set in the "<a href="../../../settings/">General Settings</a>" is the same as the currency that 
			Webcash will process the order in.</td>
  </tr>
  <tr>
  <td align="left" class="tdText"><strong>Default:</strong></td>
      <td class="tdText">
	<select name="module[default]">
		<option value="1" <?php if($module['default'] == 1) echo "selected='selected'"; ?>>Yes</option>
		<option value="0" <?php if($module['default'] == 0) echo "selected='selected'"; ?>>No</option>
	</select>
	</td>
  </tr>
  <tr>
   <td align="left" class="tdText"><strong>Return URL:</strong><br/>This has to be set in the Return URL  <br/>in the Webcash merchant control panel. <br /><b>DO NOT CHANGE THIS VALUE</b></td>
      <td class="tdText">
	<input type="textbox" name="relayUrl" size="40" value="<?php echo $GLOBALS['storeURL']."/modules/gateway/Webcash/confirmed.php";?>" />
	</td>
  </tr>  
  <tr>
    <td align="right" class="tdText">&nbsp;</td>
    <td class="tdText"><input type="submit" class="submit" value="Edit Config" /></td>
  </tr>
</table>
</form>
<?php include("../../../includes/footer.inc.php"); ?>