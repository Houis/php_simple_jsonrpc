<?php
class jsonRPCClient{
    private $debug;
    private $url;
    //请求id
    private $id;

    /**
     * 构造函数
     * @param [type]  $url   [description]
     * @param boolean $debug [description]
     */
    public function __construct($url,$debug=false){
        //server URL
        $this->url = $url;
        //debug state
        empty($debug)?$this->debug = false:$this->debug = true;
        //message id
        $this->id = 1;
    }


    /**
     * 魔术方法，jsonrpc通讯转发
     * @param  [type] $method [description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function __call($method,$params){
        //检验request信息
        if(!is_scalar($method)){
            throw new Exception("Method name has no scalar value");
        }

        if(is_array($params)){
            $params = array_values($params);    
        }else{
            throw new Exception("Param must be given as array");
        }

        //拼装成一个request请求
        $request = array(
            'method'=>$method,
            'params'=>$params,
            'id'=>$this->id,
        );
        $request = json_encode($request);
        $this->debug && $this->debug.='***** Request *****'."\n".$request."\n".'***** End Of request *****'."\n\n";
                
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        $response = curl_exec($ch);
        $response = json_decode($response,true);

        if ($this->debug) {
            echo nl2br(($this->debug));
        }
        
        if ($response['id'] != $this->id) {
            throw new Exception('Incorrect response id (request id: '.$this->id.', response id: '.$response['id'].')');
        }
        if (!is_null($response['error'])) {
            throw new Exception('Request error: '.$response['error']);
        }
        return $response['result'];

        
    }
}
?>