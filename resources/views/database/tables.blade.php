<?php
/**
 * @var $table_names array
 */
?>
<ul><?php foreach($table_names as $table_name){?>
    <li>
        <strong><?php echo $table_name?></strong>
        <a href="/database/getModel?table=<?php echo $table_name?>" target="_blank">查看Model</a>
        |
        <a href="/database/getModel?table=<?php echo $table_name?>&download=1" target="_blank">下载Model</a>
    </li>
    <?php }?></ul>