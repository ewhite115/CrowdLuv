<?php
// require_once("inc/init_config.php"); 
// require_once("inc/cl_datafunctions.php");
// require_once("inc/cl_init.php");
?>


<?php if(CL_DEBUG_MODE){  ?>
	<br><br>
	<div class="cl_debug clwhitebg" style="overflow:hidden;width:90%;max-width:900px;margin:0 auto;color:lightgray;background:gray;padding:.25%;">
		CrowdLuv Diagnostics...<br>
		<?php foreach($_SESSION["debugmsgs"] as $dbgmsg){ echo $dbgmsg . "<br>";} ?>
	</div>
	<br><br><br>
<?php } ?>





</html>