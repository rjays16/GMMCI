<?php
/**
 *
 * DoctorSearch.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

Yii::import('bootstrap.widgets.TbSelect2');

/**
 * Description of DoctorSearch
 *
 * @package
 */
class DoctorSearch extends TbSelect2 {


    public $asDropDownList = false;

    /**
     *
     */
    public function init() {
        $url = Yii::app()->createUrl('eclaims/doctor/info');
        $defaultOptions = array(
            'ajax' => array(
                'quietMillis' => 500,
                'url' => Yii::app()->createUrl('eclaims/doctor/search'),
                'data' => 'js:function(term, page) { return {q: term}; }',
                'results' => 'js:function(data,page) { return {results: data}; }',
            ),
            'allowClear' => false,
            'dataType' => 'json',
            'escapeMarkup' => 'js:function(m) { return m; }',
            'minimumInputLength' => '3',
            'placeholder' => 'Enter the search term <small>(LASTNAME, FIRSTNAME)</small>',

            // callbacks
            'escapeMarkup' => 'js:function(m) { return m; }',
            'initSelection' => <<<JAVASCRIPT
js:function(element, callback) {
    var id=$(element).val();
    if (id!=='') {
        $.ajax({
            url: "{$url}",
            data: {
                id: id
            },
            dataType: "json",
        }).done( function(data, errorText) {
            callback(data);
        });
    }
}
JAVASCRIPT
            ,'formatResult' => <<<JAVASCRIPT
js:function(data, container, query) {
    var gender = 'male';
    var genderColor='#00c';
    if ('string' === typeof data.sex && data.sex.toUpperCase()=='F') {
        gender = 'female';
        genderColor='#E200AC';
    }

    var partial = '';
    if (isNaN(parseInt(query.term))) {
        var names = query.term.split(',');
        var pos = data.fullName.indexOf(',');
        var text = data.fullName;
        var openTag = '<b class="result-match">';
        var closeTag = '</b>';

        if ('undefined' == typeof names[1]) {
            names[1] = '';
        }
        
        partial += openTag + text.slice(0, names[0].length) + closeTag;
        partial += text.slice(names[0].length, pos+1);
        partial += openTag + text.slice(pos+2, pos + names[1].length + 2) + closeTag;
        partial += text.slice(pos + names[1].length + 2);
        partial += ' <small>' + data.id + '</small>'
    } else {
        partial = data.fullName + ' <small><b>' + data.id + '</b></small>';
    }
    return partial + ' <i class="fa fa-' + gender + '" style="color:' + genderColor + '"></i>'+
        '<br/>'+
        '<span class="result-title">' + data.title + '</span> '+
        '<span class="result-department">' + data.department + '</span>';
}
JAVASCRIPT
            ,'formatSelection' => <<<JAVASCRIPT
js:function(data, container) {
    $(this.element).data('person', data);
    return data.fullName + '<br><small>' + data.title + '</small>';
}
JAVASCRIPT
        );

        if (!is_array($this->options))
            $this->options = array();

        $this->options = CMap::mergeArray($defaultOptions, $this->options);
        parent::init();
    }

}
