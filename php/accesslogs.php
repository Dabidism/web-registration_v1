<?php
// Redirect to the correct access logs page (TC038: test navigates to accesslogs.php which was a 404)
header("Location: access_logs.php");
exit;
