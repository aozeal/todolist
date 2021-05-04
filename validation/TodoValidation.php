<?php

class TodoValidation{
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

        if (empty($title)){
			$this->error_msgs[] = 'タイトルが空です。';
		}
		if (empty($detail)){
			$this->error_msgs[] = '詳細が空です。';
		}

        if (count($this->error_msgs) > 0){
			return false;
		}

        return true;
    }

    public function getErrorMessages(){
        return $this->error_msgs;
    }

}

?>
