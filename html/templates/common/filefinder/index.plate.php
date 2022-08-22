<?php $this->layout('common::template/index', ['title' => 'File finder']) ?>
<?php
$this->start('page');
?>
    <div class="filefinder filefinder-directories">
        <ul><?php foreach ($this->data['args']->directories as $index => $dir): ?>
                <li>
                    <div claas="filefinder-link-container">
                        <a class="opendir" data-dir="<?php echo $dir ?>" data-index="<?php echo $index ?>" data-open="false"><?php echo $dir ?></a>
                    </div>
                    <div class="subdir" id="subdir_<?php echo $index ?>"></div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<script>
    jQuery(document).ready(function() {
        jQuery('.opendir').click(function(e) {
            var index = jQuery(this).attr('data-index');
            var dir = jQuery(this).attr('data-dir');
            var open = jQuery(this).attr('data-open');
            var subdir = jQuery('#subdir_'+index);
            if (open == 'true') {
                subdir.css('display', 'none');
                jQuery(this).attr('data-open', 'false');
            } else {
                jQuery.ajax({
                    url: '/replacer/file/finder/files',
                    data: {
                        dir: dir
                    },
                    success: function(response) {
                        var html = '<ul>';
                        for (i = 0; i < response.files.length; i++) {
                            html += '<li><div claas="filefinder-link-inner-container">';
                            html += '<a href="/content/original?path=' + response.files[i] + '" target="_blank">' + response.files[i] + '</a>';
                            html += '&nbsp;';
                            html += '<a href="/content/insert_db?path=' + response.files[i] + '" target="_blank"><button class="bg-amber-700">?</button></a>';
                            html += '<a href="/content/replaced?path=' + response.files[i] + '" target="_blank"><button class="bg-green-500">/</button></a>';
                            html += '</div></li>';
                        }
                        html += '</ul>';
                        subdir.html(html);
                        subdir.css('display', 'block');
                        jQuery(this).attr('data-open', 'true');
                    },
                    error: function() {
                        console.log('error');
                    }
                });
            }

        });
    });
</script>
<?php
$this->stop()
?>


