<?php
namespace TM;

class Auth extends DB
{
    /**
     * セッションキー名
     *
     * @var string
     */
    private $session_name = '';

    /**
     * return 型
     *
     * @var string
     */
    private $format = 'json';

    /**
     * コンストラクター
     *
     * @param string セッション名
     * @return void
     */
    public function __construct(string $format='json')
    {
        parent::__construct();
        $this->format = $format;
        $this->session_name = KEY_NAME['SESSION'];
    }

    /**
     * ログイン実行
     *
     * @param string アカウント
     * @param string パスワード
     * @return bool セッション
     */
    public function loginExec(string $account, string $passwd, $request_url=null)
    {
        unset($_SESSION[$this->session_name]);
        unset($_SESSION['msg']);
        $result = [
            'result' => 0,
            'msg' => 'Login Error',
            'id' => null,
            'name' => null,
            'auth' => null,
            'request_url' => $request_url
        ];
        $_SESSION[$this->session_name] = [];
        $sql = 'SELECT * FROM `account` WHERE `account` = ? AND `del` = 0';
        $user_obj = $this->query($sql, [$account]);
        if ($user_obj && password_verify($passwd, $user_obj[0]->passwd)){
            session_regenerate_id(true);
            $_SESSION[$this->session_name]['token'] = $this->generateToken();
            $_SESSION[$this->session_name]['account'] = $account;
            $result['result'] = 1;
            $result['msg'] = 'Login Success';
            $result['id'] = $user_obj[0]->id;
            $result['name'] = $user_obj[0]->name;
            $result['auth'] = $user_obj[0]->auth;
            $result['icon'] = $user_obj[0]->icon;

        }
        return $this->format == 'json' ? json_encode($result, JSON_UNESCAPED_UNICODE):$result;
    }

    /**
     * ログイン承認チェック
     *
     * @return bool 検証結果
     */
    public function checkExec()
    {
        $result = [
            'result' => 0,
            'msg' => 'Auth Error',
            'id' => null,
            'name' => null,
            'auth' => null,
        ];
        if(isset($_SESSION[$this->session_name]['token']) && $_SESSION[$this->session_name]['token'] == $this->generateToken()){
            session_regenerate_id(true);
            $_SESSION[$this->session_name]['token'] = $this->generateToken();
            $sql = 'SELECT * FROM `account` WHERE `account` = ? AND `del` = 0';
            $user_obj = $this->query($sql, [$_SESSION[$this->session_name]['account']]);
            if(!empty($user_obj)){
                $result['result'] = 1;
                $result['msg'] = 'Login Success';
                $result['id'] = $user_obj[0]->id;
                $result['name'] = $user_obj[0]->name;
                $result['icon'] = $user_obj[0]->icon;
                $result['auth'] = $user_obj[0]->auth;
            }
        }
        return $this->format == 'json' ? json_encode($result, JSON_UNESCAPED_UNICODE):$result;
    }

     /**
     * CSRFトークンの生成
     *
     * @return string トークン
     */
    private function generateToken():string
    {
        return hash('sha256', session_id());
    }

    /**
    * logout
    *
    */
    public function logout():void
    {
        unset($_SESSION[ 'msg' ]);
        unset($_SESSION[$this->session_name]);
    }

    // *****************************************
    // ワンタイムトークンの生成
    // *****************************************
    public function createToken()
    {
    	$ipad = hash( 'sha256', getenv('REMOTE_ADDR') );
    	$time = hash( 'md5', time() );
    	$rand = hash( 'md5', mt_rand() );
    	return hash( 'sha256', $ipad.$time.$rand );
    }

    // *****************************************
    // トークンの半券を取得
    // *****************************************
    public function getHarfToken()
    {
    	$original_token = $this->createToken();
    	$_SESSION['HarfToken'] = substr( $original_token, 10 );
    	$_SESSION['OriginalToken'] = $original_token;
    	return substr( $original_token, 0, 10 );
    }

    // *****************************************
    // トークンの照合
    // *****************************************
    public function checkToken( $harf_token )
    {
    	return strcmp( $_SESSION['OriginalToken'], $harf_token.$_SESSION['HarfToken'] );
    }

}
