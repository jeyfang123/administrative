<html>
<body>
<?php
/*
V5.19  23-Apr-2014  (c) 2000-2014 John Lim (jlim#natsoft.com). All rights reserved.
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
  Set tabs to 4 for best viewing.

  Latest version is available at http://adodb.sourceforge.net
*/
error_reporting(E_ALL | E_STRICT);
include("../adodb.inc.php");
include("../tohtml.inc.php");

if (1) {
	
	
	$db = ADONewConnection('oci8');

	$db->connectSID = true;
	$db->Connect('58.42.251.185','WinInfo','WinInfo', 'orcl');
	
	$rs = $db->Execute('select * from users');
	
	rs2html($rs);
	
}

?>

</body>
</html>
