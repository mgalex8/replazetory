<?php $this->layout('common::template/index', ['title' => 'File finder']) ?>
<?php
$this->start('page');
?>
    <div class="content-replacer-container">
        <div style="width: 80%; float: left">
            <iframe style="width:100%; height:100%" frameboder="0" frameorigin="0" src="<?php echo $args->content_url ?>"></iframe>
        </div>
        <div style="width: 18%; float: right; margin: 0 5px">
            <?php $this->insert('content/_right_side', ['filters' => $args->filters, 'tables' => $args->tables, 'selected'=> $args->selected]); ?>
        </div>
    </div>
<?php
$this->stop()
?>