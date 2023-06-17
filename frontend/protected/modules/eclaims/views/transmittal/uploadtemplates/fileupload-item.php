<!-- The template to display files available for upload -->
<!-- <span>{%=o.formatFileSize(file.size)%}</span> -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    
    <tr class="template-upload fade">
        <td>
            <?php
                $types = CMap::mergeArray(array('' => '-Select type-'), ClaimAttachment::getAttachmentTypes());
                echo CHtml::dropDownList('ClaimAttachmentForm[type]', '', $types, array('class' => 'input-block-level attachment-type'));
            ?>
        </td>
        <td>
            {% var objectURL = window.URL.createObjectURL(file); %}
            <span
                class = "filename"
                data-title="{%=file.name%}"
                data-placement = "right"
                data-html = "true"
                data-content = "<object type='application/pdf' data='{%=objectURL%}' width='100%' height='100%'></object>"
                data-toggle = "popover"
                data-trigger = "manual" >
                {%=file.name%}
            </span>
            <div class="progress progress-success progress-striped active">
                <div class="bar" style="width:0%;"></div>
            </div>
            <div class="start" style='display: none;'>{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>{%=locale.fileupload.start%}</span>
                </button>
            {% } %}</div>
        </td>
        <td class="button-column">
            <span class="cancel">{% if (!i) { %}
            <?php
                $this->widget('bootstrap.widgets.TbButtonGroup', array(
                    'buttonType' => TbButton::BUTTON_BUTTON,
                    'size' => TbButton::SIZE_MINI,
                    'buttons' => array(
                        array(
                            'icon' => 'fa fa-minus',
                            'htmlOptions' => array(
                                'class' => 'attachment-row-cancel cancel',
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
