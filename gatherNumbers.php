<?php
    $uid
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    //instantiate phone number array
    $phoneNumbers = array();
    
    // get falling stocks array
    $fallingStocks;
    
    
    // query table here
    foreach ($fallingStocks as $stock){
        $sql ="select phoneNumber 
                from users 
                where uid in (select uid 
                                from owned 
                                where sid = ".$stock.")";
        if(!($result = mysqli_query($conn, $query)))
        {      
    
           printf("Error: %s\n", mysqli_error($conn));
           exit(1);
           
        } else {
            
            while ( $row = mysqli_fetch_assoc( $result ) )
            {

               foreach ($row as $key => $value)
               {
                     array_push($phoneNumbers, $value);
               }

            }
            
        }
    }
?>