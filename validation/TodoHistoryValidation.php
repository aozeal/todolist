<?php

class TodoHistoryValidation{
    public $error_msgs = array();
    public $target_date;
    
    public function setTargetDate($date){
        $this->target_date = $date;
    }

    public function getTargetDate(){
        return $this->target_date;
    }

    public function check(){

        $date_valid = false;
        if (is_null($this->target_date) || $this->target_date === ""){
            $date_valid = true;
        }
        if ($this->target_date === date("Y-m-d H:i:s", strtotime($this->target_date))){
            $date_valid = true;
        }
        if ($this->target_date === date("Y-m-d", strtotime($this->target_date))){
            $date_valid = true;
        }

        if (!$date_valid){
            $this->error_msgs[] = '日時の書き方が正しくありません。';

            $now = new DateTime();
            $this->target_date = $now->format('Y-m-d H:i:s');

			return false;
        }

        if (is_null($this->target_date) || $this->target_date === ""){
            $now = new DateTime('Asia/Tokyo');
            $this->target_date = $now->format('Y-m-d H:i:s');
        }
        
        return true;
    }

    public function getErrorMessages(){
        return $this->error_msgs;
    }

}

?>
