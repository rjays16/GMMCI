<?php


class class_eclaims_report
{
    public function decode_json($json_data)
    {
        $reasons = json_decode($json_data, true);
        $reason = '';
        foreach ($reasons as $key => $data){
            $reason .= $data['deficiency'] . ', ';
        }
        $result = substr($reason, 0, -1);
        return $result;
    }
    public function calculateTotal($amount_one, $amount_two){
        $total = $amount_one + $amount_two;
        return $total;
    }
}
