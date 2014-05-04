<?php
require_once("inc/config.php");
require_once("inc/cl_datafunctions.php");
require_once("inc/cl_init.php");
?>

	</div>

	<br>

	<div class="fluid-row footer">

		<div class="col-md-2 col-md-offset-10">
			<br>
			<p>&copy;<?php echo date('Y'); ?> CrowdLuv</p>
		</div>

	</div>

</div> 


</body>


<?php if(CL_DEBUG_MODE){  ?>
	<br>
	<div class="cl_debug clwhitebg" style="overflow:hidden;width:90%;max-width:900px;margin:0 auto;color:lightgray;background:gray;padding:.25%;">
		CrowdLuv Diagnostics...<br>
		<?php foreach($_SESSION["debugmsgs"] as $dbgmsg){ echo $dbgmsg . "<br>";} ?>
	</div>
	<br><br><br>
<?php } ?>





</html>