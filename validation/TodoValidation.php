<?php

class TodoValidation{
    public const TITLE_MAX_LENGTH = 250; #255文字だがここでは250としておく
    public const DETAIL_MAX_LENGTH = 1000;
    public $data;
    public $error_msgs = array();
    
    public function setData($data){
        $this->data = $data;
    }

    public function getData(){
        return $this->data;
    }

    public function check(){
        $title = $this->data['title'];
        $detail = $this->data['detail'];
        $deadline_at = $this->data['deadline_at'];

        if (empty($title)){
			$this->error_msgs[] = 'タイトルが空です。';
		}
        if (mb_strlen($title) > TodoValidation::TITLE_MAX_LENGTH){
            $this->error_msgs[] = 'タイトルが250文字を超えています。';
        }
		if (mb_strlen($detail) > TodoValidation::DETAIL_MAX_LENGTH){
			$this->error_msgs[] = '詳細が1000文字を超えています。';
		}

        $date_valid = false;
        if (is_null($deadline_at) || $deadline_at === ""){
            $date_valid = true;
        }
        if ($deadline_at === date("Y-m-d H:i:s", strtotime($deadline_at))){
            $date_valid = true;
        }
        if ($deadline_at === date("Y-m-d", strtotime($deadline_at))){
            $date_valid = true;
        }
        if (!$date_valid){
            $this->error_msgs[] = '期日の書き方が正しくありません。';
        }


        if (count($this->error_msgs) > 0){
			return false;
		}

        //データを整形する
        if ($detail === ""){
            $this->data['detail'] = null;
        }
        if ($deadline_at === ""){
            $this->data['deadline_at'] = null;
        }

        return true;
    }

    public function getErrorMessages(){
        return $this->error_msgs;
    }

}

?>
