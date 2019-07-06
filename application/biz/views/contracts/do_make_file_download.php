<html>
<meta charset="utf-8"/>
<?php
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=$alias_title.doc");
?>
<body>
<?php echo $html_string; ?>
</body>
</html>