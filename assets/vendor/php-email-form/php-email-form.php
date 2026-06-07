<?php 
// if "email" variable is filled out, send email
if (isset($_POST['email'])) {

//Email information
$to ="info@rccghabitationofhope.org";
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];

//Send email
mail($to, $subject, $message, "from: " , $email);
        
//Email response
echo "Message Delivered!"; }

//if "email" variable is not filled out, display the form
else { ?>
<form method="post">
Email: <input name="email" type="text" />
Subject: <input name="subject" type="text" />
Message: <textarea name="message" rows="15" cols="40"></textarea>
<input type="submit" value="Send Message" />
</form>
<?php } 
?>
