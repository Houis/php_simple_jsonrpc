<?php
class jsonRPCServer{
    /**
     * 处理一个request类，这个类绑定了一些请求参数
     * @param  [type] $object [description]
     * @return [type]         [description]
     */
    public static function handle($object){
        //判断是否一个rpc json请求
        if($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] != 'application/json'){
            return false;
        }
        //reads the input data
        $request = json_decode(file_get_contents('php://input'),true);
        // print_r($request);return ;
        //执行请求类中的接口
        try{
            //
            $result = @call_user_func([$object,$request['method']],$request['params']);

            // echo 'adasd';return 1;
            if($result){
                $response = array('id'=>$request['id'],'result'=>$result,'error'=>NULL);
            }else{
                $response = array('id'=>$request['id'],'result'=>NULL,'error'=>'unknow method or incorrect parameters');
            }
        }catch(Exception $e){
            $response = array('id'=>$request['id'],'result'=>NULL,'error'=>$e->getMessage());
        }

        if(!empty($request['id'])){
            header('content-type:text/javascript');
            echo json_encode($response);
        }

        return true;
    }
}
?>