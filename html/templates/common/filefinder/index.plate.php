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
            const index = jQuery(this).attr('data-index');
            const dir = jQuery(this).attr('data-dir');
            const open = jQuery(this).attr('data-open');
            const subdir = jQuery('#subdir_' + index);
            const self = this;

            if (open == 'true') {
                subdir.css('display', 'none');
                jQuery(this).attr('data-open', 'false');
            } else {
                opendir(dir, 1, 20, function(html) {
                    subdir.html(html);
                    subdir.css('display', 'block');
                    jQuery(self).attr('data-open', 'true');
                });
            }
        });

        jQuery('.subdir').on('click', '.fileslist-open', function(e) {
            const dir = jQuery(this).attr('data-path');
            const page = jQuery(this).attr('data-page');
            const limit = jQuery(this).attr('data-limit');
            const subdir =  jQuery(this).closest('.subdir');
            opendir(dir, page, limit, function(html){
                subdir.html(html);
                subdir.css('display', 'block');
            })
        });

        function opendir(dir, page, limit, callback)
        {
            jQuery.ajax({
                url: '/replacer/file/finder/files',
                data: {
                    dir: dir,
                    page: page,
                    limit: limit,
                },
                success: function(response) {
                    console.log(response);
                    if (response.files.items.length > 0) {
                        var html = '<ul>';
                        for (i = 0; i < response.files.items.length; i++) {
                            let link = response.files.items[i].replace('\/var\/www\/html', '');
                            html += '<li><div claas="filefinder-link-inner-container">';
                            html += '<a href="' + link +'" target="_blank">' + response.files.items[i] + '</a>';
                            html += '&nbsp;';
                            html += '<a href="/content/insert_db?path=' + response.files.items[i] + '" target="_blank"><button class="bg-amber-700">?</button></a>';
                            html += '<a href="/content/replaced?path=' + response.files.items[i] + '" target="_blank"><button class="bg-green-500">/</button></a>';
                            html += '</div></li>';
                        }
                        html += '<li><span class="fileslist-open" ' +
                            'data-path="' + response.path + '" ' +
                            'data-page="' + (response.files.currentPageNumber + 1) + '" ' +
                            'data-limit="' + response.files.numItemsPerPage + '">...</span></li>'
                        html += '</ul>';
                    }
                    callback(html);
                },
                error: function() {
                    console.log('error');
                }
            });
        }
    });
</script>
<?php
$this->stop()
?>


