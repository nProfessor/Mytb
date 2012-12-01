<?php
$bo = mail("hakimov@reg.ru", "My theme", "Line 1\nLine 2\nLine 3\nLine 4\nLine 5"); 
if ($bo==1) {echo('Sent');}
else {echo('not sent');}
?>
