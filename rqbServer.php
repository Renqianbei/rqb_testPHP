

<?php
        
    class  UserAPI{
        private $db;
        // Constructor - open DB connection 构造 连接服务器
        function __construct(){
            $db_host="rqb.local";                                           //连接的服务器地址
            $db_user="rqb";                                                  //连接数据库的用户名
            $db_psw="123";                                                  //连接数据库的密码
            $db_name="rqbServer";                                           //连接的数据库名称
            $this->db = new mysqli($db_host,$db_user,$db_psw,$db_name);
//            $this->db->autocommit(true);
        }
        
        function _destruct(){
            
            $this->db->close(); //关闭服务器
            
        }
        
        //下面这一坨 都是仿制了一个把状态码转成html信息 的方法
        /*如果你不理解为什么我们不要这个，那是因为这是一个遵守HTTP协议的web服务器，当你发送一个相应你可以制定一个包含错误码和详细描述的头。有标准错误码可以用，这些方法不过是用起来更方便。
         */
        // Helper method to send a HTTP response code/message
        function sendResponse($status = 200, $body = '', $content_type = 'json')
        {
            $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->getStatusCodeMessage($status);
            //PHP header() 函数
            header($status_header);
            header('Content-type: ' . $content_type); //设置http返回header
            
            
            echo $body;//这才是真正的返回的data
        }
        
        function getStatusCodeMessage($status)
        {
            // these could be stored in a .ini file and loaded
            // via parse_ini_file()... however, this will suffice
            // for an example
            $codes = Array(
                           100 => 'Continue',
                           101 => 'Switching Protocols',
                           200 => 'OK',
                           201 => 'Created',
                           202 => 'Accepted',
                           203 => 'Non-Authoritative Information',
                           204 => 'No Content',
                           205 => 'Reset Content',
                           206 => 'Partial Content',
                           300 => 'Multiple Choices',
                           301 => 'Moved Permanently',
                           302 => 'Found',
                           303 => 'See Other',
                           304 => 'Not Modified',
                           305 => 'Use Proxy',
                           306 => '(Unused)',
                           307 => 'Temporary Redirect',
                           400 => 'Bad Request',
                           401 => 'Unauthorized',
                           402 => 'Payment Required',
                           403 => 'Forbidden',
                           404 => 'Not Found',
                           405 => 'Method Not Allowed',
                           406 => 'Not Acceptable',
                           407 => 'Proxy Authentication Required',
                           408 => 'Request Timeout',
                           409 => 'Conflict',
                           410 => 'Gone',
                           411 => 'Length Required',
                           412 => 'Precondition Failed',
                           413 => 'Request Entity Too Large',
                           414 => 'Request-URI Too Long',
                           415 => 'Unsupported Media Type',
                           416 => 'Requested Range Not Satisfiable',
                           417 => 'Expectation Failed',
                           500 => 'Internal Server Error',
                           501 => 'Not Implemented',
                           502 => 'Bad Gateway',
                           503 => 'Service Unavailable',
                           504 => 'Gateway Timeout',
                           505 => 'HTTP Version Not Supported'
                           );
            
            return (isset($codes[$status])) ? $codes[$status] : '';
        }
        
         function readData(){
            
//            isset是一个用于检测变量是否已经设置了的PHP函数。我们这里用它来确保所有需要的POST参数都发送了。
             
             if($_SERVER['REQUEST_METHOD'] == 'GET') {
                 $name = $_GET["name"];
                 $result = $this->selectkeywithValue("name",$name);
                 $this->sendResponse(200,json_encode($result));
                 return true;
             }
             
             if(isset($_POST["insert"]) == 1){
                $name = $_POST["name"];
                $age = $_POST["age"];
                $description = $_POST["description"];
                $this->insertIntoDB($name,$age,$description);
                $this->sendResponse(200,json_encode('insert success '));
                 return true;
                }
            else  if (isset($_POST["name"])){//判断POST请求中的参数是否有值
                $postname = $_POST["name"];
                
                $result = $this->selectkeywithValue('name',$postname);
                if (count($result)==0){
                   $this->sendResponse(400,json_encode('not Find name'));
                    return false;
                }

               $this->sendResponse(200, json_encode($result));
                return true;
            }
            
             
           $this->sendResponse(400,'parameterFail');
            return false;
    }
        
        
        function selectkeywithValue($key,$value){ //根据对应key的值来查找users表中的数据
            $selectKey = $key;
            $bindName = $value;
            // Print all codes in database //            注意我们没有自己把传进来的变量放到SQL语句中，而是使用bind_param方法。这是更安全的方法，否则你可能使自己易受SQL injection的攻击。
            $sql = 'select id , name,age,description from users where '.$selectKey.'=?';
            //准备查询语句
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s",$bindName); /*
                                               bind_param(“sss”, firstname,lastname, $email);
                                               1:该函数绑定了 SQL 的参数，且告诉数据库参数的值。 “sss” 参数列处理其余参数的数据类型。s 字符告诉数据库该参数为字符串。
                                               参数有以下四种类型:
                                               i - integer（整型）
                                               d - double（双精度浮点型）
                                               s - string（字符串）
                                               b - BLOB（布尔值）
                                               每个参数都需要指定类型。
                                               通过告诉数据库参数的数据类型，可以降低 SQL 注入的风险。
                                               */
            $stmt->execute();//执行
            $stmt->bind_result($id,$name,$age,$description);//绑定结果
            $result = array();
            while ($stmt->fetch()){//查询结果
                $obj = array ("name" => $name,
                              "id" => $id,
                              "description" => $description);
                array_push($result,$obj);
            }
            $stmt->close();
            return  $result;
        }
    
        
        function insertIntoDB($name ,$age,$description){ //插入数据
            $stmt = $this->db->prepare("INSERT INTO USERS (name,age,description) VALUES (?,?,?)");
            $stmt->bind_param("sis",$name,$age,$description);
            $stmt->execute();
            $stmt->close();
        }
   
}
    
    $api = new UserAPI;
    $api->readData();
    
?>

