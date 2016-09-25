<!DOCTYPE html>
<html class="full" lang="en">

    <!-- jQuery -->
    <script src="./assets/js/jquery.js" defer></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="./assets/js/bootstrap.min.js" defer></script>
    
        
    <body>


    <?php
    include("config.php");
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
            echo "     Your registration FAILED\n";
            echo "Error: " . $query . "<br>" . $db->error;
       
    } else {
        printf("Registration sucess!"); 
    }
                
    $db->close();
    
    ?> 

    <P>
    <HR>
    <P>
    
    <BR>

    </body>
</html> 