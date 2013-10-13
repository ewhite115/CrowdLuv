<?php
$pageTitle = "Contact Mike";
$section = "contact";

 include('inc/header.php'); ?>

<div class="section page">
	
	<div class="wrapper">
		<h1> Contact </h1>
		<p>I&rsquo;d love to hear from you. Complete te form</p>
		<form method="post" action="contact-process.php">
			<table>
				<tr>
					<th>
						<label for="name">Name</label> 
					</th>
					<td>
						<input type="text" name="name" id="name">
					</td>
				</tr>
				<tr>
					<th>
						<label for="email">Email</label> 
					</th>
					<td>
						<input type="text" name="email" id="email">
					</td>
				</tr>
				<tr>
					<th>
						<label for="message">message</label> 
					</th>
					<td>
						<input type="textarea" name="message" id="message">
					</td>
				</tr>
			</table>
			<input type="submit" value="Send">
		</form>	

	</div>
</div>


<?php include('inc/footer.php'); ?>