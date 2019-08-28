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

	function Search($pName)
	{
		$pdo = pdoSqlConnect();
		$query = "SELECT p.pNum,p.pName,p.price,i.imageUrl from Product as p inner join ProductImage as i on p.pNum = i.pNum where (p.pName like ? and i.turn = 1);";
		$st = $pdo->prepare($query);
		$st->execute(["%$pName%"]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		$st = null;
		$pdo = null;
		return $res;
	}

	function ViewProduct($pNum){
		$pdo = pdoSqlConnect();
		$query = "SELECT p.pNum,p.pName,p.price,i.imageUrl from Product as p inner join ProductImage as i on p.pNum = i.pNum where (p.pNum = ? and i.turn = 1);";
		$st = $pdo->prepare($query);
		$st->execute([$pNum]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		$st = null;
		$pdo = null;
		return $res;
	}

	function ViewProductDetail($pNum){
		$pdo = pdoSqlConnect();
		$query = "SELECT p.pNum,p.qpp,p.storeMethod,p.origin,i.imageUrl from ProductDetail as p inner join ProductImage as i on p.pNum = i.pNum where p.pNum = ? order by i.turn;";
		$st = $pdo->prepare($query);
		$st->execute([$pNum]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		$st = null;
		$pdo = null;
		return $res;
	}

	function ViewProductReview($pNum){
		$pdo = pdoSqlConnect();
		$query = "select r.id,r.review,r.reviewDate from Review as r where pNum = ?;";
		$st = $pdo->prepare($query);
		$st->execute([$pNum]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		$st = null;
		$pdo = null;
		return $res;
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
