<html>
<head>
    <title><?=$this->e($this->args->title)?></title>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?=$this->asset('/public/css/output.css')?>">
</head>
<body>
    <div style="background-color: #0f6674">
        <h1> Common template for page </h1>
    </div>
    <div id="page">
        <?=$this->section('page')?>
    </div>
    <div id="sidebar">
        <?php if ($this->section('sidebar')): ?>
            <?=$this->section('sidebar')?>
        <?php //else: ?>
            <? //echo $this->fetch('default-sidebar')?>
        <?php endif ?>
    </div>
</body>
</html>