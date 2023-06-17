<?php
/**
 *
 * PatientSearch.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

Yii::import('bootstrap.widgets.TbSelect2');

/**
 * Description of PatientSearch
 *
 * @package
 */
class EmployerSearch extends TbSelect2 {


    public $asDropDownList = false;

    public $initValue;
    /**
     *
     */
    public function init() {

        $url = Yii::app()->createUrl('eclaims/employer/info');


        $defaultOptions = array(
            'ajax' => array(
                'quietMillis' => 500,
                'url' => Yii::app()->createUrl('eclaims/employer/search'),
                'data' => 'js:function(term, page) { return {q: term}; }',
                'results' => 'js:function(data,page) { return {results: data}; }',
            ),
            'allowClear' => false,
            'id'=>'js:function(m) { return m.pPEN; }',
            'dataType' => 'json',
            'escapeMarkup' => 'js:function(m) { return m; }',
            'minimumInputLength' => '7',
            'placeholder' => 'Enter the search term <small> ex. SEGWORKS TECHNOLOGIES CORPORATION </small>',

            // callbacks
            'escapeMarkup' => 'js:function(m) { return m; }',
            'initSelection' => <<<JAVASCRIPT
js:function(element, callback) { 
    var select = $(element);
    var id = select.val();

    if (id!=='') {
        $.ajax({
            url: "{$url}",
            data: {
                id: id
            },
            dataType: "json",
        }).done( function(response, errorText) {
            console.log("initSelection", response);
            select.val(response.pPEN);
            callback(response);
        });
    }
}
JAVASCRIPT
,'formatResult' => <<<JAVASCRIPT
js:function(data, container, query) {

    var partial = '';

    if (isNaN(parseInt(query.term))) {
        partial += ' <small>' + data.pEmployerName + '</small>'
    } else {
        partial = ' <small>' + data.pEmployerName + '</small>';
    }
    return partial + ' <i class="fa fa-home" style="color:#0218ff"></i>'+
        '<br/>'+
        '<span class="result-pen">' + data.pPEN + ' <i class="fa fa-key" style="color:#ff0000"></i>'+' | </span> '+
        '<span class="result-address">' + data.pEmployerAddress +'</span>';
}
JAVASCRIPT
,'formatSelection' => <<<JAVASCRIPT
js:function(data, container) {

    console.log("formatSelection", data);

    $(this.element).data('person', data);

    $('#EclaimsPhicMember2_employer_no').val(data.pPEN);   

    $('#EclaimsPhicMember2_employer_name').val(data.pEmployerName);

    $('#employer_no').val(data.pPEN);

    return data.pEmployerName;

}
JAVASCRIPT
        );

        if(isset($this->initValue) && !empty($this->initValue)){
            $initdata= CJavaScript::encode($this->initValue);
            $this->options['initSelection'] = "js:function(element,callback){data=$initdata; callback(data); }";
        }

        if (!is_array($this->options))
            $this->options = array();

        $this->options = CMap::mergeArray($defaultOptions, $this->options);
        parent::init();
    }



}
