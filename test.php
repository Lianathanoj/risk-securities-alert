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
    
    /*$query = "select sname 
                    from stock 
                    where sid in (select sid 
                                    from owned 
                                    where uid = ".$uid.")";*/
    
    $query = "select * from user";                         

    print("<TR> <TD><FONT color=\"blue\"><B><PRE>\n");
    print("</PRE></B></FONT></TD></TR></TABLE></UL>\n");
    print("<P><HR><P>\n");
          
    if ( ! ( $result = mysqli_query($conn, $query)) )      # Execute query
    {      
       printf("Error: %s\n", mysqli_error($conn));
       exit(1);
    }      
          
    print("<UL>\n");
    print("<TABLE bgcolor=\"lightyellow\" BORDER=\"5\">\n");
          
    $printed = false;

    while ( $row = mysqli_fetch_assoc( $result ) )
    {      
       if ( ! $printed )
       {
         $printed = true;                 # Print header once...
          
         print("<TR bgcolor=\"lightcyan\">\n");
         foreach ($row as $key => $value)
         {
            print ("<TH>" . $key . "</TH>");             # Print attr. name
         }
         print ("</TH>\n");
       }   
          
          
       print("<TR>\n");
       foreach ($row as $key => $value)
       {   
         print ("<TD>" . $value . "</TD>");
       }   
       print ("</TR>\n");
    }      
    print("</TABLE>\n");
    print("</UL>\n");
    print("<P>\n");
    
    
?>
<script>
setTimeout(function () { window.location.reload(); }, 5*60*1000);
// just show current time stamp to see time of last refresh.
document.write(new Date());
</script>