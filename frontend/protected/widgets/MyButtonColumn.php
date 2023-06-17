<?php

/**
 * ButtonColumn class file.
 * Extends {@link CButtonColumn}
 *
 * Allows additional evaluation of ID in options.
 *
 * @version $Id$
 *
 */
class MyButtonColumn extends ButtonColumn {

    /**
     * @var boolean whether the ID in the button options should be evaluated.
     */
    public $evaluateID = false;

    /**
     * Renders the button cell content.
     * This method renders the view, update and delete buttons in the data cell.
     * Overrides the method 'renderDataCellContent()' of the class CButtonColumn
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    public function renderDataCellContent($row, $data) {
        $tr = array();
        ob_start();
        foreach ($this->buttons as $id => $button) {
            if(!empty($button['options'])){
                foreach ($button['options'] as $k => $v) {
                    //                if($k != 'class' && $k != 'data-modal-url' && $k != 'id' && $k != 'title' )
                    //                if ($k != 'class' && $k != 'data-modal-url' && $k != 'id' && $k != 'title' && $k != 'data-multiple-role')
                    //                $button['options'][$k] = $this->evaluateExpression($button['options'][$k], array('row' => $row, 'data' => $data));
                    if (!is_array($v) && !is_callable($v)) {
                        if (strpos($v, '$data->') === false) {
                            continue;
                        }
                    }
                    $datum = $this->evaluateExpression($button['options'][$k], array('data' => $data, 'row' => $row));
                    $button['options'][$k] = empty($datum) ? "" : $datum;
                }
            }


            $this->renderButton($id, $button, $row, $data);
            $tr['{' . $id . '}'] = ob_get_contents();
            ob_clean();
        }
        ob_end_clean();
        echo "<div class='btn-group'>" . strtr($this->template, $tr) . "</div>";
    }

}
