<!DOCTYPE html>
<html class="full" lang="en">

    <!-- jQuery -->
    <script src="./assets/js/jquery.js" defer></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="./assets/js/bootstrap.min.js" defer></script>
    
	<link rel="stylesheet" href="./assets/css/custom.css">
    
        
    <body>


    <?php
    include("config.php");
    if(!isset($_POST['username'],$_POST['password'],$_POST['phone'])) {
        echo("Error: fields not set");
        die();
    }
    
    $username = mysqli_real_escape_string($db,$_POST['username']);
    $password = mysqli_real_escape_string($db,$_POST['password']); 
    $phoneNumber = mysqli_real_escape_string($db,$_POST['phone']);

    
    $salt = mcrypt_create_iv(20);
    $uid = hash("sha256", $username);
    $encryptedPass = hash("sha256", $salt.$password);
    

    $query = "INSERT INTO user
        VALUES ('$username',
                '$encryptedPass',
                '$salt',
                '$phoneNumber',
                10,
                '$uid');";
                
    if(!($result = mysqli_query($db, $query)))
    {      
            echo "<h2>Your registration FAILED: The username has been taken or something else went wrong :(</h2>";
       
    } else {
        printf("Registration sucess!"); 
    }
                
    $db->close();
    
    ?> 

    <HR>
    <p>
        This page will move to login in 5 seconds...
    </p>
    
    <BR>
    
    </body>
    
    <script>
    setTimeout(function () { window.location.href = "./login.html"; }, 5*1000);
    </script>
</html> 