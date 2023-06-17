<?php
/**
 * The file upload form used as target for the file upload widget
 *
 * @var TbFileUpload $this
 * @var array $htmlOptions
 */

echo CHtml::beginForm($this->url, 'post', $this->htmlOptions);

$htmlOptions['style'] = 'display: none;';
if ($this->hasModel()) :
    echo CHtml::activeFileField($this->model, $this->attribute, $htmlOptions)
      . "\n";
else :
    echo CHtml::fileField($name, $this->value, $htmlOptions) . "\n";
endif;

$this->beginWidget(
  'application.widgets.SegBox', array(
    'title' => ' ',
    'headerButtons' => array(
      array(
        'class' => 'bootstrap.widgets.TbButtonGroup',
        'buttons' => array(
          array(
            'label' => 'Add Files',
            'buttonType' => 'button',
            'htmlOptions' => array(
              'class' => 'fileinput-button',
              'onclick' => '$(".multi-upload").trigger("click");',
            ),
          ),
          array(
            'label' => 'Upload attachment/s',
            'type' => TbButton::TYPE_PRIMARY,
            'buttonType' => TbButton::BUTTON_SUBMIT,
            'visible' => $this->extra['service']->checkReturn()
              ? false
              : true,
            'htmlOptions' => array(
              'id' => 'attachments-submit',
              'class' => 'start',

            ),
          ),
          array(
            'label' => 'Generate CF4',
            'buttonType' => 'button',
            'htmlOptions' => array(
              'class' => 'btn btn-success',
              'id' => 'print-cf4',
              'data-url' => Yii::app()->getController()->createUrl('RenderCF4Modal'),
              'data-encounter' => $this->extra['details']['encounter_nr'],
              'data-transmittal' => $this->extra['details']->transmit_no
                // 'onclick' => '$(".multi-upload").trigger("click");',
            ),
          ),
          array(
            'label' => 'Re-Upload Attachment',
            'type' => TbButton::TYPE_INVERSE,
            'buttonType' => TbButton::BUTTON_SUBMIT,
            'visible' => $this->extra['service']->checkReturn(),
            'htmlOptions' => array(
              'id' => 'attachments-submit',
              'class' => 'start',
            ),
          ),
        ),
        'htmlOptions' => array(
          'class' => 'fileupload-buttonbar',
        ),
      ),
    ),
    'footer' => CHtml::tag(
      'div', array('class' => 'form-actions', 'id' => 'footerdiv'),

      $this->widget(
        'bootstrap.widgets.TbButton', array(
        'id' => 'close-button',
        'label' => 'Close',
        'buttonType' => TbButton::BUTTON_LINK,
        'url' => $this->getController()->createUrl(
          'attachments', array(
            'id' => $this->extra['details']->transmit_no
          )
        ),
      ), true
      )
    ),
  )
);
?>

<div class="row-fluid">
    <div class="span12 alert alert-info">
        <i class="fa fa-question-circle"></i> Hover your mouse over the
        <strong>filenames</strong> to preview the selected files
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <div class="grid-view">
            <table id="attachments-grid"
                   class="table table-striped table-condensed table-bordered">
                <thead>
                <tr>
                    <th>Document type</th>
                    <th>Attachment</th>
                    <th class="button-column"></th>
                </tr>
                </thead>
                <tbody class="files" data-toggle="modal-gallery"
                       data-target="#modal-gallery"></tbody>
            </table>
        </div>
    </div>
</div>

<div class="row-fluid">
    <?php
    echo $this->widget('bootstrap.widgets.TbButton', array(
      'encodeLabel' => false,
      'label' => 'Assign <i class="fa fa-question-circle"></i>',
      'buttonType' => TbButton::BUTTON_BUTTON,
      'type' => TbButton::TYPE_SUCCESS,
      'id' => 'btnAssign',
      'htmlOptions' => array(
        'title' => "Auto-assign document file type",
        'data-url' => $this->getController()->createUrl('GetDocumentTypes')
      )
    ), true);
    ?>
</div>

<style type="text/css">
    #print-cf4 {
        margin-left: 3px;
    }
</style>

<?php $this->endWidget() /* Box */ ?>
<?php echo CHtml::endForm(); ?>

<script>
    $('#print-cf4').click(function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).data('url'),
            type: 'POST',
            dataType: 'JSON',
            data: {
                'url': $(this).data('url'),
                'id': $(this).data('encounter'),
                'transmittalNo': $(this).data('transmittal')
            },
            beforeSend: function () {
                $('#cf4Modal').modal('show');
                Alerts.loading({
                    'title': 'Please wait...',
                    content: 'Generating Eclaims CF4'
                });
            },
        }).done(function (data) {
            $('#cf4Modal .modal-body').html(data.form);
            Alerts.close();
        })
    });
</script>
