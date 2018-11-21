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

	<p>New Temporary Password: {{$new_password}}</p>
		
</fieldset>
<p>Other Details</p>
<span>
	Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
</span>
<br>
<br>

</body>
 
</html>