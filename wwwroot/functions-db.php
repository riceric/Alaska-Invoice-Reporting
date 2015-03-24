<?php 
/**
 *  Database parameters
 */
###### db-config.ini ######
#db_driver=mysql
#db_user=root
#db_password=

#[dsn]
#host=localhost
#port=3306
#dbname=ak_invoices

#[db_options]
#PDO::MYSQL_ATTR_INIT_COMMAND=set names utf8

#[db_attributes]
#ATTR_ERRMODE=ERRMODE_EXCEPTION
############

class Database {
    private static $link = null ;

    private static function getLink ( ) {
        if ( self :: $link ) {
            return self :: $link ;
        }

        $ini = "db-config.ini" ;
        $parse = parse_ini_file ( $ini , true ) ;

        $driver = $parse [ "db_driver" ] ;
        $dsn = "${driver}:" ;
        $user = $parse [ "db_user" ] ;
        $password = $parse [ "db_password" ] ;
        $options = $parse [ "db_options" ] ;
        $attributes = $parse [ "db_attributes" ] ;

        foreach ( $parse [ "dsn" ] as $k => $v ) {
            $dsn .= "${k}=${v};" ;
        }
		
		try {
			self :: $link = new PDO ( $dsn, $user, $password, $options ) ;

			foreach ( $attributes as $k => $v ) {
				self :: $link -> setAttribute ( constant ( "PDO::{$k}" )
					, constant ( "PDO::{$v}" ) ) ;
			}

			return self :: $link ;
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}			
    }

    public static function __callStatic ( $name, $args ) {
        $callback = array ( self :: getLink ( ), $name ) ;
        return call_user_func_array ( $callback , $args ) ;
    }
} 

/**
 * Register user for session login
 */
function dbUserExists($unm)
{
	$result = false;
	$sql = "SELECT username FROM _account WHERE username = '$unm'";
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$count = $stmt -> rowCount();
		if ($count > 0) {
			$result = true; //Username already taken
		}
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	return $result;
}

/**
 * Register user for session login
 */
function dbRegisterNewUser($unm,$md5pwd,$salt,$name,$email)
{
	$result = 0;
	$sql = "SELECT username FROM _account WHERE username = '$unm'";
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$result = $stmt -> rowCount();
		//echo "$sql : $result record(s)";
		if ($result > 0) {
			return -1; //Username already taken
		}
		else 
		{
			$sql = "INSERT INTO _account (username,password,salt,full_name,email) VALUES(:username, :password, :salt, :full_name, :email)";
			$params = array(":username"=>$unm, 
						":password"=>$md5pwd, 
						":salt"=>$salt, 
						":full_name"=>$name, 
						":email"=>$email);
			$stmt = Database :: prepare ( $sql );
			$stmt->execute($params);
			return 1;
		}
		$stmt->closeCursor ( ) ;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	return $result;
}

/**
 * Validate user (admin or data entry) for session login
 */
function dbCheckAuth($unm,$pwd)
{
	$result = 0;
	$sql = "SELECT username,password,salt FROM _account WHERE username = '$unm'";
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		if ($stmt -> rowCount() > 0) 
		{
			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$hash = $data[0]['salt'].$pwd.$data[0]['salt'];
			$md5pwd = MD5($hash);
			
			$sql = "SELECT username FROM _account WHERE password = '$md5pwd'";
			$stmt = Database :: prepare ( $sql );
			$stmt->execute();
			$result = $stmt -> rowCount();
		}
		else { $result = 0; }
		//echo "$sql : $result record(s)";
		$stmt->closeCursor ( ) ;
		
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	return $result;
}

/**
 * Validate user (admin or data entry) for session login
 */
function dbCheckAuthLevel($unm)
{
	$result = 0;
	$sql = "SELECT level FROM _account WHERE username = '$unm'";
	try {
		$stmt = Database :: prepare ( $sql );
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor () ;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	return $result[0]['level'];
}
?>