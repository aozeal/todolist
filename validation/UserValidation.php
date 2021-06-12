<?php

class UserValidation{
    public const USER_ID_MIN_LENGTH = 6;
    public const USER_ID_MAX_LENGTH = 250; #255文字だがここでは250としておく
    public const PASSWORD_MIN_LENGTH = 6;
    public const PASSWORD_MAX_LENGTH = 50; #255文字だがここでは50としておく
    public const NAME_MAX_LENGTH = 250; #255文字だがここでは250としておく
    public const DETAIL_MAX_LENGTH = 1000;

    public $data;
    public $error_msgs = array();
    
    private $id;
    private $name;
    private $detail;
    private $password1;
    private $password2;

    private $login_id;
    private $login_password;

    public function setId($id){
        $this->id = $id;
    }
    public function setName($name){
        $this->name = $name;
    }
    public function setDetail($detail){
        $this->detail = $detail;
    }
    public function setPassword1($password1){
        $this->password1 = $password1;
    }
    public function setPassword2($password2){
        $this->password2 = $password2;
    }

    public function getValidData(){
        return $this->data;
    }


    public function setLoginId($id){
        $this->login_id = $id;
    }
    public function setLoginPassword($password){
        $this->login_password = $password;
    }
    public function setData($data){
        $this->data = $data;
    }

    public function checkSignup(){

        /*
        if (!ctype_alnum($this->id)){
            $this->error_msgs[] = 'ユーザーIDは半角英数にしてください。';
        }
        */
        if (mb_strlen($this->id) < UserValidation::USER_ID_MIN_LENGTH){
            $this->error_msgs[] = 'ユーザーIDは6文字以上にしてください。';
        }
        if (mb_strlen($this->id) > UserValidation::USER_ID_MAX_LENGTH){
            $this->error_msgs[] = 'ユーザーIDが250文字を超えています。';
        }
        if (empty($this->name)){
			$this->error_msgs[] = 'ユーザー名が空です。';
		}
        if (mb_strlen($this->name) > UserValidation::NAME_MAX_LENGTH){
            $this->error_msgs[] = 'ユーザー名は6〜250文字にしてください。';
        }
        if ($this->password1 !== $this->password2){
            $this->error_msgs[] = 'パスワードが一致していません。';
        }
        if (mb_strlen($this->password1) < UserValidation::PASSWORD_MIN_LENGTH){
            $this->error_msgs[] = 'パスワードは6〜50文字にしてください。';
        }
        if (mb_strlen($this->password1) > UserValidation::PASSWORD_MAX_LENGTH){
            $this->error_msgs[] = 'パスワードは6〜50文字にしてください。';
        }
        if (mb_strlen($this->detail) > UserValidation::DETAIL_MAX_LENGTH){
			$this->error_msgs[] = '詳細が1000文字を超えています。';
		}

        if (count($this->error_msgs) > 0){
			return false;
		}

        //データを整形する
        $this->data['id'] = $this->id;
        $this->data['name'] = $this->name;
        $this->data['detail'] = $this->detail;
        if ($this->detail === ""){
            $this->data['detail'] = null;
        }
        $this->data['icon_path'] = null;

        $this->data['encrypted_password'] = password_hash($this->password1, PASSWORD_DEFAULT);

        return true;
    }

    public function getErrorMessages(){
        return $this->error_msgs;
    }

    private function isUserIdExist($id){
        $dbh = new PDO(DSN, USERNAME, PASSWORD);
        $stmt = $dbh->prepare("SELECT * FROM users WHERE id=:id;");
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result === false){
            return false;
        }

        return true;
    }

    public function checkLogin(){
        #echo var_dump($this->data);

        if (!password_verify($this->login_password, $this->data['encrypted_password'])){
            $this->error_msgs[] = 'ログイン情報が一致しません。';
        }

        if (count($this->error_msgs) > 0){
            return false;
        }

        return true;
    }

    
}

?>
