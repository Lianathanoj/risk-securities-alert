<!DOCTYPE html>
    
    <script>
    function goBack() {
        window.history.back()
    }
    </script>
        
    <body>

    Registration result 

    <?php
    $servername = "127.0.0.1";
    $username = "root";
    $password = "";
    $dbname = "risk";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $name = $_POST["username"];
    $password = $_POST["password"];
    $salt = mcrypt_create_iv(20);
    $uid = hash("sha256", $salt.$name);
    $encryptedPass = hash("sha256", $salt.$password);
    

    $sql = "INSERT INTO user
        VALUES ('$name',
                '$encryptedPass',
                '$salt',
                '$uid');";
                
    if(!($result = mysqli_query($conn, $query)))
    {      

       printf("Error: please try again");
       exit(1);
       
    } else {
        printf("Registration sucess!"); 
    }
                
    $conn->close();
    
    ?> 

    <P>
    <HR>
    <P>
    
    <BR>

    </body>
</html> 