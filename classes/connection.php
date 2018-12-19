<?php


class Connection 
{
 private $errors ;   
 private $mysqli; 
 
function __construct()
{ 


// echo "NOS";
# Запуск сессии
// session_start();
# Служит для отладки, показывает все ошибки, предупреждения и т.д.
// error_reporting(E_ALL);

// локальный MySQL

    $db_host = 'localhost';
	$db_username = 'mysql';
	$db_password = 'mysql';
	$db_name = 'gloss1';
	$db_charset = 'utf8'; 

/*
// локальная Хероку    
    $db_host = 'localhost';
	$db_username = 'mysql';
	$db_password = 'mysql';
	$db_name = 'heroku_846065d530579e0';
	$db_charset = 'utf8';
*/
// Реальная хероку
/*
    $db_host = 'eu-cdbr-west-02.cleardb.net';
	$db_username = 'b0f439327ec632';
	$db_password = 'bf8363b2';
	$db_name = 'heroku_846065d530579e0';
	$db_charset = 'utf8';
*/
  
    
//    echo "<p> 2__Before  Connection";
    
	// $is_connected = @mysql_connect($db_host, $db_username, $db_password);
// !    $is_connected = mysql_connect($db_host, $db_username, $db_password);
//    echo "<p> After MySQL Connection. is_connected=$is_connected";
    
   $mysqli = new mysqli($db_host, $db_username, $db_password, $db_name); // подключаемся к базе MySQL   

//  echo "<p> After DB Connection. ";

 if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
 }
    
    
  if (!$mysqli->set_charset($db_charset)) {
    die('Error : set_charset '. $mysqli->connect_error);  
  }
    
    $charr=$mysqli->character_set_name();
  //  echo "<p>$charr";
  //  echo "<p>MOS1";
  // printf("Текущий набор символов: %s\n", $mysqli->character_set_name());  

 

$this->mysqli=$mysqli; 
 
 
}

function GetCount ($tableName, $condition)
{
    /* строка из таблицы */
  $sql = "SELECT COUNT(*) FROM $tableName WHERE $condition";  
  $result = $this->mysqli->query($sql);

 if (!$result)  {
     die("Error : $sql");
 }
  
  $ret = $result->fetch_row();  
  $result->free();  
 return $ret[0]; 
} 


function GetRow ($tableName, $condition)
{
    /* строка из таблицы */
  $row = null;
  $sql = "SELECT * FROM $tableName WHERE $condition";
  $result = $this->mysqli->query($sql);

 if (!$result)  {
     die("Error : $sql");
 }
  
  $row = $result->fetch_assoc();
  $result->free();
 return $row; 
} 


function Proc ($ProcName, $aParms)
{
    $strParms=$this->sParms($aParms);

    $sql = "CALL $ProcName($strParms)";
    $result = $this->mysqli->query($sql);
     if (!$result)  {
     die("Error : $sql");
 }
    return 1;                            

}

function vFunc($ProcName, $aInParms)
{
 // когда оказалось что от хранимых функций один геморрой  
 // я переделал их в процедуры c возвращаемым параметром по имени ret 
 //
     
     if  (count( $aInParms)>0)
         $strParms=$this->sParms($aInParms).",";
     else
         $strParms="";
     

    $strParms.="@ret";      
     
     
    $sql = "CALL $ProcName($strParms)";
    $result = $this->mysqli->query($sql);
     if (!$result) 
         {     
            die("Error : $sql")  ; 
         }
     
         
    $sql1 = "SELECT @ret AS Res";
    $result1 = $this->mysqli->query($sql1);
    if (!$result1) 
        {     
                die("Error :$sql1 after $sql"); 
        }; 
    $row = $result1->fetch_assoc();
     
    
    
    return $row[Res];  
     
}

function Func ($funcName, $aParms)
{
 
 
 /* вызов sql функции */   
 
 
 $strParms=$this->sParms($aParms);
 $sql = "SELECT $funcName($strParms) AS Res";
  $result = $this->mysqli->query($sql);

 if (!$result)  {
     die("Error : $sql");
 }
  
  $row = $result->fetch_assoc();
  $result->free();
 return $row[Res]; 
   
}    

function GetColumn ($tableName, $idFieldName, $valueFiledName, $whereStr=null )
{
 // формируем массив со значениями данного поля по всей таблице. в принципе можно будет фильтр добавить если будет надо
        $ret = array();
       
        $sql = "SELECT $idFieldName, $valueFiledName FROM $tableName";
        
        if ($whereStr != null)
        {
            $sql = $sql.(" WHERE ".$whereStr);
          //  echo "<p>GetColumn = $sql";   
        }    
                  
        $result = $this->mysqli->query($sql);

              if (!$result)  {
                                die("Error : $sql");
                                                        }
			
        
            while ( $row =  $result->fetch_assoc() )
			{
                 $key=$row[$idFieldName];
                 $value=$row[$valueFiledName];
                 
                 $ret[$key]=$value;
			
             } 

        return $ret;

}    


function GetOnlyColumn ($tableName, $valueFiledName, $whereStr=null )
{
 // формируем массив со значениями данного поля по всей таблице. в принципе можно будет фильтр добавить если будет надо
        $ret = array();
       
        $sql = "SELECT $valueFiledName FROM $tableName";
        
        if ($whereStr != null)
        {
            $sql = $sql.(" WHERE ".$whereStr);
          //  echo "<p>GetColumn = $sql";   
        }    
                  
        $result = $this->mysqli->query($sql);

              if (!$result)  {
                                die("Error : $sql");
                                                        }
			
        
            while ( $row =  $result->fetch_assoc() )
			{
                 $value=$row[$valueFiledName];
                 
                 $ret[]=$value;
			
             } 

        return $ret;

}    




function GetTable ($tableName, $aField, $whereStr=null, $orderStr=null, $groupStr=null)
{
 
 $ret=array();
 
 $selectStr=$aField[0];
   $n= Count($aField);
  
  for($i=1; $i<$n; $i++) {
    $selectStr.=",";
    $selectStr.=$aField[$i];
  } 
    $sql = "SELECT $selectStr FROM $tableName";
    if ($whereStr != null)
        {
            $sql = $sql.(" WHERE ".$whereStr);
          //  echo "<p>GetColumn = $sql";   
        }    

    if ($groupStr != null)
        {
            $sql = $sql.(" GROUP BY ".$groupStr);
          //  echo "<p>GetColumn = $sql";   
        }    
        
    if ($orderStr != null)
        {
            $sql = $sql.(" ORDER BY  ".$orderStr);
          //  echo "<p>GetColumn = $sql";   
        }    

    // echo "<p>GetTable sql=$sql";
    
    $result = $this->mysqli->query($sql);

    if (!$result)  {
     die("Error : $sql");
       }
         
			
			while ( $row = $result->fetch_assoc() )
			{
                 
                 $ret[]=$row;
			
             } 

       return $ret; 
        
  }  

function UpdateTable ($tableName,  $arr, $whereStr=null)
{
  // массив arr имеет структуру: индекс - имя поля, значение - значение  
   $sql="UPDATE $tableName SET ";  
   $lFirst=1;  
     foreach($arr as $indx => $val)
     {
       if ($lFirst==0) 
       {
         $sql.=","  ;
       }    
       
       $lquote = is_string($val);

       if ($lquote)        {
                $sql.=(" $indx='"."$val'");
       }
       else         {
                $sql.=(" $indx=$val");
       }
       if ($lFirst==1) 
       {
           $lFirst=0;
       }    
    
     }

          if ($whereStr != null)
        {
            $sql = $sql.(" WHERE ".$whereStr);
          //  echo "<p>GetColumn = $sql";   
        }    
        $result = $this->mysqli->query($sql);

        
      if (!$result)  {
                die("Error : $sql");
                     }

       return $result;
     } 

  
  
  
 function sParms($aParms)
{
    // функция возвращает строку параметров. через запятую. с проверкой типа и апострофами если нужно.
    $ret = "";
    $n = count($aParms);
    
      for($i=0; $i<$n; $i++) { 
       
       if ($i>0)  $ret.=",";
       $parm=$aParms[$i];
       $lquote = is_string($parm);
       if ($lquote) $ret=$ret."'";
        $ret= $ret."$parm";     
        if ($lquote) $ret=$ret."'";        
 } 


    return $ret;
    
}    
  

function GetUserId()
   {
       return 1;
  
   } 

function GetRootEmail()
   {
       return "andrey.noskoff@gmail.com";
   } 

   
}


 
 ?>