<?php
/**
 * ButtonColumn
 *
 * @author Alvin Quinones
 * @copyright Copyright &copy; Segworks Technologies Corporation 2012
 * @package application.widgets
 * 
 */

Yii::import('bootstrap.widgets.TbButtonGroupColumn');

/**
 * ButtonColumn is an extension on the Twitter Bootstrap for Yii
 * implementation of the zii widget CButtonColumn. Instead of plain icons,
 * the widget uses actual <button> elements that redirect to the given
 * url.
 *
 */
class ButtonColumn extends TbButtonGroupColumn {

    public $buttonSize = 'sm';

    /**
     * Overrides the renderButton method
     */
    protected function renderButton($id, $button, $row, $data) {
        if(isset($button['visible'])) {
            if($button['visible'] instanceof Closure || is_array($button['visible']) || is_string($button['visible'])) {
                if(!$this->evaluateExpression($button['visible'], array('row'=>$row, 'data'=>$data)))
                    return;
            } else {
                if(!$button['visible'])
                    return;
            }
        }

        $label = isset($button['label']) ? $button['label'] : $id;
        $url = isset($button['url']) ? $this->evaluateExpression($button['url'], array('data'=>$data, 'row'=>$row)) : '#';
        $options = isset($button['options']) ? $button['options'] : array();

        $class = array('btn');
        if (!empty($options['class'])) {
            $class = array_unique(
                array_merge(
                    $class,
                    explode(' ', trim($options['class']))
                )
            );
        } else {
            $class[] = 'btn-default';
        }

        if ($this->buttonSize) {
            $class[] = 'btn-' . $this->buttonSize;
        }

        if (!empty($button['context'])) {
            $class[] = 'btn-' . $button['context'];
        } elseif (!empty($button['type'])) {
            $class[] = 'btn-' . $button['type'];
        } else {
            $class[] = 'btn-default';
        }

        $options['class'] = implode(' ', $class);
        if (strpos($options['class'], 'btn-') === false) {
            $options['class'] .= ' btn-default';
        }

        if (!isset($options['title']))
            $options['title'] = $label;

        if (!isset($options['rel']))
            $options['rel'] = 'tooltip';


        /**
         * Runtime evaluation of values for data- attributes
         */

        // Bulk set
        if (!empty($options['data'])) {
            $dataSet = $options['data'];
            if (is_callable($options['data'])) {
                $dataSet = $this->evaluateExpression($dataSet, array('data'=>$data, 'row'=>$row));
            }

            foreach ($dataSet as $key => $value) {
                if (is_callable($value)) {
                    $value = $this->evaluateExpression($value, array('data'=>$data, 'row'=>$row));
                }
                $options['data-' . $key] = $value;
            }
            unset($options['data']);
        }

        if (!isset($options['data-toggle'])) {
            $options['data-toggle'] = 'tooltip';
            $options['data-container'] = 'body';
        }

        // Individual setting
        foreach ($options as $key => $value) {
            if (preg_match("/^data\-/i", $key)) {
                if (is_callable($value)) {
                    $options[$key] = $this->evaluateExpression($value, array('data'=>$data, 'row'=>$row));
                }
            }
        }

        if (!empty($options['hidden']) && is_callable($options['hidden'])) {
            $options['hidden'] = $this->evaluateExpression($options['hidden'], array('data'=>$data, 'row'=>$row));
        }

        if (isset($button['icon']))
        {
            if (strpos($button['icon'], 'icon-') === false && strpos($button['icon'], 'fa-') === false)
                $button['icon'] = 'fa fa-'.implode(' fa-', explode(' ', $button['icon']));
            else{
                // $button['icon']  = str_replace('icon-' , "fa fa-" , $button['icon']);
            }

            echo CHtml::link('<i class="'.$button['icon'].'"></i>', $url, $options);
        }
        else if (isset($button['imageUrl']) && is_string($button['imageUrl']))
            echo CHtml::link(CHtml::image($button['imageUrl'], $label), $url, $options);
        else
            echo CHtml::link($label, $url, $options);
    }
    
    
//    /**
//     * Renders the data cell content.
//     * This method renders the view, update and delete buttons in the data cell.
//     * @param integer $row the row number (zero-based)
//     * @param mixed $data the data associated with the row
//     */
//    public function getDataCellContent($row)
//    {
//        return  CHtml::tag('div', ['class' => 'btn-toolbar', 'style' => 'margin:0'],
//            CHtml::tag('div', ['class' => 'btn-group', 'style' => 'white-space:nowrap'],
//            parent::getDataCellContent($row)
//        ));
//    }
}