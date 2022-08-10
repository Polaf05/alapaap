<?php  
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

date_default_timezone_set('Etc/UTC');



require 'function.php';

require 'connection.php';
require '../vendor/autoload.php';

$mail = new PHPMailer(true);

// $response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$fname = strtolower($_POST['fname']) ;
	$lname = strtolower($_POST['lname']) ;
	$email_add = $_POST['email_add'];
	$contact_no = $_POST['contact_no'];
	$pword = hash_hmac('md5',$_POST['pword'],'@Bsp1234*');
	$confirm_pword = $_POST['confirm_pword'];
	$status		= '0';
	$token 			= null;
	$role = '1'; // Role for requestor is always 1
	
	$sql_2 = mysqli_query($conn,"INSERT into tbl_user (first_name,last_name,email_add,contact_no,password,status,token,role, default_role) values ('$fname','$lname','$email_add','$contact_no','$pword','$status','$token','$role', '1') ");	
			
	$message = "Good Day ".ucfirst($_POST['fname'])." ".ucfirst($_POST['lname']).",<br><br>"
	. "Thank you for registering!<br>"
	. "Our Approver is reviewing your form.<br>"
	. "You will received an e-mail confirmation once your registration is approved.<br>"
	. "If you need any assistance please, feel free to e-mail us at <a href=''>bspops@ebizolution.com</a><br><br>"            
	. "Thank you<br>";

	try {
		//Server settings            
		$mail->isSMTP();                                            //Send using SMTP
		$mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
		$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		$mail->Username   = 'alapaapbsp@gmail.com';                     //SMTP username
		$mail->Password   = 'lykcjxwaufpwhznx';      // alapaap@Bsp123                            //SMTP password
		$mail->SMTPSecure = 'tls';           
		$mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
		$mail->SMTPOptions = array (
			'ssl' => array(
				'verify_peer'  => false,
				'verify_peer_name'  => false,
				'allow_self_signed' => true)
		);
		//Recipients
		$mail->setFrom('alapaapbsp@gmail.com', 'BSP Alapaap');
		$mail->addAddress($email_add);         //Add a recipient

		$mail->isHTML(true);                                  
		$mail->Subject = "Password Recovery";
		$mail->Body    = $message;
		$mail->send();    

	} catch (Exception $e) {
		echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	}   
	header("location: http://".$_SERVER['SERVER_NAME']."/model/verification.php?display_name=".convert_string('encrypt',$fname)."&email=".$email_add);
}
?>