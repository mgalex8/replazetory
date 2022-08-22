<?php $this->layout('common::template/index', ['title' => 'File finder']) ?>
<?php
$this->start('page');
?>
    <div style="background-color: #0f6674">
        <h1> Common template for page </h1>
        <p>It1s good news for  <?=$this->e('<br>common', 'strip_tags|strtoupper')?></p>

        <?php $this->insert('filefinder/_files_list', ['files' => $args->files]); ?>
    </div>
<?php
$this->stop()
?>


