<?php
$to = "versterc74@gmail.com";
$subject = "Event Zone Email Test";
$txt = "test 1!";
$headers = "From: chirumeropafadzo586@gmail.com" . "\r\n" .
"CC: chirumeropafadzo586@gmail.com";

if (mail($to, $subject, $txt, $headers)) {
    echo 'Email sent successfully!';
} else {
    echo 'Email sending failed...';
}

?>  
 