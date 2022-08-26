<?php $this->layout('common::template/index', ['title' => 'File finder']) ?>
<?php
$this->start('page');
?>
    <div>
        <form method="post">
            <textarea name="text" style="width:800px; height:400px"><?php echo $args->text ?></textarea>
            <input type="submit" value="Синонимизировать">
        </form>
    </div>
    <?php if ($args->synonims): ?>
    <div>
        <?php echo $args->synonims ?>
    </div>
    <?php endif ?>
<?php
$this->stop()
?>