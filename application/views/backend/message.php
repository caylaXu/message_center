<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/2/18
 * Time: 10:10
 */

defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!-- start: Content -->
<div id="content" class="span10">
    <div style="text-align: center;font-size: 20px;margin-top: 30px;">
        <?php echo(isset($msg) ? $msg : '消息'); ?>
    </div>
</div>

<?php include('layout/footer.php'); ?>

</body>
</html>