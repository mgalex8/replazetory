<div style="background-color: #0f6674">
    <h1> Common template for page </h1>

    <?php $this->include('filefinder/_files_list'); ?>

    <textarea><?php echo $args->files ?></textarea>
    <?php dump($args) ?>

</div>
