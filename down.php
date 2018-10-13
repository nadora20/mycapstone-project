<?php
$page_title="Get Database";
$page_header_title='Get Database Page';
include './includes/pref.php';
include HEADER;
?> 
   <fieldset>
       <legend>Get Database</legend>
    <?php 
    echo isset($general_msg)? $general_msg: ''; ?>
       <form action="/dumbdb.php" method="get">
           <p>
            <input type="submit" name="download" value="Download Database" />
           </p>
       </form>
</fieldset>

<?php include FOOTER; ?>
          


  
 
