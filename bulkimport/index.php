
        <?php
        $PageTitle = "bulkimport programbeställningar";
        include ("config-connect.php");
        include 'top.php';
        ?>
        
        <form method="post" action="import.php">
        <textarea rows=70 cols=150 name="data"></textarea>
        <input type="submit">
        </form>
        
        
        
        
        
    </body>
</html>

<?php   
	  sqlsrv_close;
?> 