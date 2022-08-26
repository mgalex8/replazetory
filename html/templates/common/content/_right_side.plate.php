<div class="content-replacer-right-side w-full">
    <div class="group-container">
        <label><div>Group:</div>
            <input type="text" name="group_name" value="" style="max-width:300px" />
        </label>
    </div>
    <div class="directory-container">
        <label><div>Directory:</div>
            <input type="text" name="directory" value="" style="max-width:300px" />
        </label>
    </div>
    <div class="type-container">
        <label><div>Type:</div>
            <input type="text" name="type" value="" style="max-width:300px" />
        </label>
    </div>
    <div>
        <code class="xpath-yaml-string" style="background-color: #0e7490; width:100%; padding:10px; margin-top:20px; display: block; word-wrap: break-word;">
            { "xpath": "//title" }
        </code>
    </div>
    <h3 class="content-replacer-title">Include elements</h3>
    <div class="content-replacer-select-element-container">
        <input type="text" name="xpath_selector" id="xpath_selector" value="//title" />
        <button class="xpath_selector_button" id="xpath_selector_button">Select element</button>
    </div>
    <div class="xpath-item-list">
        <div class="xpath-item">
            <div class="match-replacer-container">
                <label><div>Replace from:</div>
                    <input type="text" name="replacer_from_value" id="replacer_from_value" value="" />
                </label>
                <label><div>Replace to:</div>
                    <input type="text" name="replacer_to_value" id="replacer_to_value" value="" />
                </label>
            </div>
            <div class="match-filters-select">
                <label>Filter:
                    <select name="filters" class="filters">
                        <?php foreach ($filters as $filter): ?>
                            <option value="<?php echo $filter->getName() ?>"><?php echo $filter->getName() ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label><button class="add-filter">new</button></label>
            </div>
            <div class="match-filter-container">
                <label>Filter:
                    <select name="filters" class="filters">
                        <?php foreach ($filters as $filter): ?>
                            <option value="<?php echo $filter->getName() ?>" selected><?php echo $filter->getName() ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label><button class="save-filter" data-filter-index="1">del</button></label>
            </div>
            <h3 class="content-replacer-title">Save section</h3>
            <div class="save-container">
                <label>Table:
                    <select name="tables" id="tables">
                        <?php foreach ($tables as $table => $fields): ?>
                            <option value="<?php echo $table ?>" <?php if($selected['table'] == $table) :?>selected<?php endif ?>><?php echo $table ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <div class="table_fields"></div>
                <label>Save:
                    <button class="save-filter" data-filter-index="1">save</button>
                </label>
            </div>
        </div>
    </div>
</div>

<style>
    #selectable .ui-selecting { background: #FECA40; }
    #selectable .ui-selected { background: #F39814; color: white; }
    #selectable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
    #selectable li { margin: 3px; padding: 0.4em; font-size: 1.4em; height: 18px; }
</style>

<script>
$(document).ready(function() {
    var tables = <?php echo json_encode($tables) ?>;
    var filters = <?php echo json_encode($filters) ?>;
    var filters_selected;

    $( "#selectable" ).selectable({
        stop: function() {
            var result = $( "#select-result" ).empty();
            $( ".ui-selected", this ).each(function() {
                var index = $( "#selectable li" ).index( this );
                result.append( " #" + ( index + 1 ) );
            });
        }
    });
    
    $('#tables').on('change', function(e) {
        var html = '';
        var tabname = $(this).val();
        var table = tables[ tabname ];
        $.each(table, function(key, el) {
            html += '<input type="text" name="table['+tabname+']['+el+']" class="'+tabname+' '+el+'" value="" placeholder="'+el+'" />';
        });
        $('.table_fields').html(html);
    });

    $('.table_fields').on('blur', 'input', function(e) {
        if ($(this).val().replace(/\s+/g, '') !== '') {
            const xpathYamlString = $('.xpath-yaml-string');
            const data = JSON.parse(xpathYamlString.text());
            if (!data.save) {
                data.save = {};
            }
            let classes = $(this).attr('class').split(' ');
            const iterator = Object.keys(tables);
            for (const tabname of iterator) {
                if (classes.indexOf(tabname) !== -1) {
                    data.save.table = tabname;
                    for (const value of tables[tabname]) {
                        if (classes.indexOf(value) !== -1) {
                            data.save[value] = this.value;
                            break;
                        }
                    }
                    break;
                }
            }
            xpathYamlString.text(JSON.stringify(data));
        }
    })

    $('#replacer_from_value').on('blur', function(e) {
        var xpathYamlString = $('.xpath-yaml-string');
        var data = JSON.parse(xpathYamlString.text());
        if (!data.replacers) {
            data.replacers = {};
        }
        data.replacers.from = this.value;
        xpathYamlString.text(JSON.stringify(data));
    });
    $('#replacer_to_value').on('blur', function(e) {
        var xpathYamlString = $('.xpath-yaml-string');
        var data = JSON.parse(xpathYamlString.text());
        if (!data.replacers) {
            data.replacers = {};
        }
        data.replacers.to = this.value;
        xpathYamlString.text(JSON.stringify(data));
    });

    var previous;
    $(".filters").on('focus', function () {
        previous = this.value;
    }).change(function() {
        var xpathYamlString = $('.xpath-yaml-string');
        var data = JSON.parse(xpathYamlString.text());
        if (!data.filters) {
            data.filters = {};
        }
        data.filters[previous] = 'undefined';
        if (data.filters.indexOf(this.value) === -1) {
            data.filters[this.value] = {};
        }
        xpathYamlString.text(JSON.stringify(data));
        // Make sure the previous value is updated
        previous = this.value;
    });

    // console.log($("iframe"));

    // $("iframe").contents().find.('body').mousemove(function (e) {
    //     var details = e.target.id;
    //     console.log(details);
    // });
    // //
    // var element = document.elementFromPoint(x, y);
    // $('iframe').contents().on('click', function(e) {
    //     console.log(e.target);
    //     var hover_element = $(':hover').last();
    //     if (hover_element.hasClass('selectable')) {
    //         hover_element.removeClass('selectable');
    //     } else {
    //         hover_element.addClass('selectable');
    //     }
    // });
});
</script>