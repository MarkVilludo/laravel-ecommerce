<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
<br/>
<h2>Fashion 21</h2>
<p> Dear <b> {{$user->first_name. ' '.$user->last_name}}</b></p>
<br>
<fieldset>
	<span> 
		<b>Your password has been reset.  </b>
	</span>

	<p>New Temporary Password: {{$user->newPassword}}</p>
		
</fieldset>
<p><strong>P.S. </strong>
	We also love hearing from you and helping you with any issues you have. Please reply to this email if you want to ask a question or just say hi.
</p>
<br>

</body>
 
</html>