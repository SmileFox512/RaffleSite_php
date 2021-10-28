<?
@session_start();
include_once("config.php");
if($_POST){
if($_POST[user]==$admin_username && $_POST[pass]==$admin_password){
$_SESSION[aname] = 4;
header("location:flvby.php");
}else{
$_SESSION[aname] = 2;
header("location:".$PHP_SELF."?act=n");
}
}
?>
<html>
<head>
<title>
Login
</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language=javascript>
function checkthis()
{
if(document.f1.user.value=="")
{
alert("Enter the Admin Username");
document.f1.user.focus();
return false;
}
if(document.f1.pass.value=="")
{
alert("Enter the Admin Password");
document.f1.pass.focus();
return false;
}
}
</script>
<link rel="stylesheet" href="style.css" type="text/css">
<link href="../style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--

.border {
	border: 1px solid #E1E1E1; height: 18px;
}
.box {
	border: 1px solid #CCCCCC; height: 18px; font-size: 12px; color: #666666; font-weight: bold;
	}

.whitehead {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: bold;
	color: #ffffff;
}
.red {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #0066CC;
}
.bodytxt {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
	font-weight: bold;
}
-->
</style>
</head>
<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="javascript:document.f1.user.focus();"> 
<table width="775" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center" class="font"> 
  <tr> 
    <td>&nbsp;</td> 
  </tr> 
  <tr> 
    <td>&nbsp;</td> 
  </tr> 
  <tr> 
    <td>&nbsp;</td> 
  </tr> 
  <tr> 
    <td align="center" class="red"> <b> 
      <?
	if($_GET[act]=="n")
	echo "<span class=htext>Invalid Username/Password Given!..</span>";
	?>
      </b></td> 
  </tr> 
  <tr> 
    <td>&nbsp;</td> 
  </tr> 
  <tr> 
    <td> <table width="40%" border="0" cellspacing="0" cellpadding="0" bordercolor="#000177" align="center">
        <tr bgcolor="#003399"> 
          <td height="25" align="center" class="whitehead"><span class="style1">Administration 
            Login Panel</span></td> 
        </tr> 
        <tr> 
          <td> <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="outline">
              <tr valign="top"> 
                <td> <form method="POST" action="<?=$PHP_SELF?>" name="f1" onSubmit="return checkthis()"> 
                    <table border="0" cellspacing="0" cellpadding="5" class="border" width="100%" align="center">
                      <tr valign="top"> 
                        <td height="18" ></td>
                      </tr>
                      <tr valign="top"> 
                        <td width="39%" class="bodytxt" align="center" valign="middle">User 
                          Name</td>
                        <td width="61%"> <input name="user" type="text" class="box" size="20"> 
                        </td>
                      </tr>
                      <tr valign="top"> 
                        <td width="39%" class="bodytxt" align="center" valign="middle">Password</td>
                        <td width="61%"> <input name="pass" type="password" class="box" size="20"> 
                        </td>
                      </tr>
                      <tr align="center" valign="top"> 
                        <td> <div align="center"> </div>
                          
                        </td>
                        <td align="left"> 
                          <input type="submit" name="Submit" value="Login" class="Submit"> 
                          <input type="button" name="Cancel" value="Cancel" onClick="javascript:document.location = '../'" class="Submit"> 

                        </td>
                      </tr>
                    </table> 
                  </form></td> 
              </tr> 
            </table></td> 
        </tr> 
      </table></td> 
  </tr> 
</table> 
</body>
</html>
