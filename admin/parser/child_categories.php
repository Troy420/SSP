<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/core/init.php';
$parentID = (int)$_POST['parentID'];
$childQueryy = $db->query("SELECT * FROM categories WHERE parent = '$parentID' ORDER BY category");

ob_start();
?>
<option value=""></option>
<?php while($childz = mysqli_fetch_assoc($childQueryy)): ?>
    <option value="<?=$childz['id']?>">
        <?=$childz['category']?>
    </option>
<?php endwhile;?>
<?php echo ob_get_clean();?>