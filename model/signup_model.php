<?php  
session_start();
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

require 'function.php';

require 'connection.php';

// $response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$fname = strtolower($_POST['fname']) ;
	$lname = strtolower($_POST['lname']) ;
	$email = $_POST['email'];
	$contact_no = $_POST['contact_no'];
	$pword = hash_hmac('md5',$_POST['pword'],'@Bsp1234*');
	// $confirm_pword = $_POST['confirm_pword'];
	$status		= '0';
	$token 			= null;
	$role = '1'; // Role for requestor is always 1

	
	// $GetSender = mysqli_query($conn,"select * from tbl_user where role='2' ");
	// $rowsSender = mysqli_fetch_array($GetSender);
	// $recipient = $rowsSender['email_add'];   

	$sql = mysqli_query($conn,"INSERT into tbl_user (first_name,last_name,email_add,contact_no,password,status,token,role, default_role) values ('$fname','$lname','$email','$contact_no','$pword','$status','$token','$role', '1') ");	
	
	$subject = "Account Registration";
	$message = "Hello <b>".ucfirst($_POST['fname'])." ".ucfirst($_POST['lname'])."</b>,<br><br>"
	. "Our Approver is reviewing your account.<br>"
	. "You will receive a notification once your account is approved.<br>"
	. "If you need any assistance please, feel free to e-mail us at <a href=''>bspops@ebizolution.com</a><br><br>"            
	. "Thank you<br><br><br><br><i>This message is autogenerated. Please do not respond.</i>"; 

	require 'mail.php';

	header("location: http://".$_SERVER['SERVER_NAME']."/model/verification.php?display_name=".convert_string('encrypt',$fname)."&email=".$email_add);
}
?>