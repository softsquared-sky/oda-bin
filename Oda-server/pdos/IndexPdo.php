<?php

//READ
	function test()
	{
		$pdo = pdoSqlConnect();
		$query = "SELECT * FROM TEST_TB;";

		$st = $pdo->prepare($query);
		//    $st->execute([$param,$param]);
		$st->execute();
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;

		return $res;
	}

//READ
	function testDetail($testNo)
	{
		$pdo = pdoSqlConnect();
		$query = "SELECT * FROM TEST_TB WHERE no = ?;";

		$st = $pdo->prepare($query);
		$st->execute([$testNo]);
		//    $st->execute();
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;

		return $res[0];
	}


	function testPost($name)
	{
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO TEST_TB (name) VALUES (?);";

		$st = $pdo->prepare($query);
		$st->execute([$name]);

		$st = null;
		$pdo = null;

	}

	function CheckId($id)
	{
		$pdo = pdoSqlConnect();
		$query = "SELECT EXISTS(SELECT * FROM User WHERE id= ?) AS exist;";
		$st = $pdo->prepare($query);
		//    $st->execute([$param,$param]);
		$st->execute([$id]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;

		return intval($res[0]["exist"]);
	}

	function signup($id, $pw, $ad, $business)
	{
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO User(id, pw, business,address)
					SELECT ?,?,?,? FROM DUAL 
					WHERE NOT EXISTS(
					SELECT * FROM User 
					WHERE id = ? );";

		$st = $pdo->prepare($query);
		if ($st->execute([$id, $pw, $business, $ad, $id])) {
			$res = $id;
			$st = null;
			$pdo = null;
			return $res;
		} else {
			$res = null;
			return $res;
		}

	}

	function login($id, $pw)
	{
		$pdo = pdoSqlConnect();
		$query = "SELECT EXISTS (SELECT * FROM User WHERE id = ? AND pw = ?) AS exist;";

		$st = $pdo->prepare($query);
		$st->execute([$id, $pw]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;
		return intval($res[0]["exist"]);
	}

	function isValidJWToken($id, $pw)
	{
		$pdo = pdoSqlConnect();
		$query = "SELECT EXISTS (SELECT * FROM User WHERE id = ? AND pw = ?) AS exist;";

		$st = $pdo->prepare($query);
		$st->execute([$id, $pw]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;
		return intval($res[0]["exist"]);
	}
	// CREATE
	//    function addMaintenance($message){
	//        $pdo = pdoSqlConnect();
	//        $query = "INSERT INTO MAINTENANCE (MESSAGE) VALUES (?);";
	//
	//        $st = $pdo->prepare($query);
	//        $st->execute([$message]);
	//
	//        $st = null;
	//        $pdo = null;
	//
	//    }


	// UPDATE
	//    function updateMaintenanceStatus($message, $status, $no){
	//        $pdo = pdoSqlConnect();
	//        $query = "UPDATE MAINTENANCE
	//                        SET MESSAGE = ?,
	//                            STATUS  = ?
	//                        WHERE NO = ?";
	//
	//        $st = $pdo->prepare($query);
	//        $st->execute([$message, $status, $no]);
	//        $st = null;
	//        $pdo = null;
	//    }

	// RETURN BOOLEAN
	//    function isRedundantEmail($email){
	//        $pdo = pdoSqlConnect();
	//        $query = "SELECT EXISTS(SELECT * FROM USER_TB WHERE EMAIL= ?) AS exist;";
	//
	//
	//        $st = $pdo->prepare($query);
	//        //    $st->execute([$param,$param]);
	//        $st->execute([$email]);
	//        $st->setFetchMode(PDO::FETCH_ASSOC);
	//        $res = $st->fetchAll();
	//
	//        $st=null;$pdo = null;
	//
	//        return intval($res[0]["exist"]);
	//
	//    }
