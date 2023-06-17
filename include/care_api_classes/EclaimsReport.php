<?php


class EclaimsReport
{
    public function decode_json($json_data, $status)
    {
        $reasons = json_decode($json_data, true);
        $reason = '';

        foreach ($reasons as $data){
            if($status == 'RETURN'){
                $reason .= $data['deficiency'] . ', ';
            }else{ 
                $reason .= $data . ', '; 
            }
        }

        $result = substr(trim($reason), 0, -1); 
        return $result;
    }

    public function calculateTotal($amount_one, $amount_two){
        $total = $amount_one + $amount_two;
        return $total;
    }
}
