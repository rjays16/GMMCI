<!-- The template to display files available for download -->
<!-- <span>{%=o.formatFileSize(file.size)%}</span> -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    
    <tr class="template-download fade">
        <td>
            {% if (!file.error) { %}
                <span class="label label-info">
                    {%=file.attachment_type%}
                </span>
            {% } else { %}
                <span class="label label-error">{%=file.error %}</span>
            {% } %}
        </td>
        <td>
            <span>{%=file.name%}</span>
        </td>
        <td class="button-column">
            <span class="delete">{% if (!i) { %}
            <?php
                $this->widget('bootstrap.widgets.TbButtonGroup', array(
                    'buttonType' => TbButton::BUTTON_BUTTON,
                    'size' => TbButton::SIZE_MINI,
                    'buttons' => array(
                        array(
                            'icon' => 'fa fa-minus',
                            'htmlOptions' => array(
                                'class' => 'attachment-row-delete delete',
                                'data-toggle' => 'tooltip',
                                'title' => 'Remove this attachment',
                            )
                        ),
                    ),
                ));
            ?>
            {% } %}</span>
        </td>
    </tr>

{% } %}
</script>


