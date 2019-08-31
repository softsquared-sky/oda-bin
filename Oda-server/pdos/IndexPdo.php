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

	function checkId($id)
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

	function signUp($id, $pw, $ad, $business)
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

	function search($pName,$turn)
	{
		$pdo = pdoSqlConnect();
		$query = "SELECT p.pNum,p.pName,p.odaPrice,i.imageUrl from Product as p inner join ProductImage as i on p.pNum = i.pNum where (p.pName like ?  and i.type = 'main' and i.turn = 1);";
		$st = $pdo->prepare($query);
		$st->execute(["%$pName%"]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		$st = null;
		$query = "INSERT INTO Word(word) VALUES (?);";
		$st = $pdo->prepare($query);
		$st->execute(["$pName"]);
		$st = null;
		$pdo = null;
		return $res;
	}

	function viewProduct($pNum){
		$res = Array();
		$pdo = pdoSqlConnect();
		$query = "SELECT p.pNum,p.pName,p.odaPrice p from Product as p where p.pNum =?;";
		$st = $pdo->prepare($query);
		$st->execute([$pNum]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res["basicContent"] = $st->fetchAll();
		$st = null;
		$query = "SELECT imageUrl,turn from ProductImage where pNum = ? and type = 'main' order by turn;";
		$st = $pdo->prepare($query);
		$st->execute([$pNum]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res["imageResult"] = Array();
		$res["imageResult"] = $st->fetchAll();
		$st = null;
		$pdo = null;
		return $res;
	}

	function viewProductDetail($pNum){
		$pdo = pdoSqlConnect();
		$query = "SELECT p.qpp,p.storeMethod,p.origin from ProductDetail as p where p.pNum =?;";
		$st = $pdo->prepare($query);
		$st->execute([$pNum]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res["detailContent"] = $st->fetchAll();
		$st = null;
		$query = "SELECT imageUrl,turn from ProductImage where pNum = ? and type = 'detail' order by turn;";
		$st = $pdo->prepare($query);
		$st->execute([$pNum]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res["imageResult"] = Array();
		$res["imageResult"] = $st->fetchAll();
		$st = null;
		$pdo = null;
		return $res;
	}

	function viewProductReview($pNum){
		$pdo = pdoSqlConnect();
		$query = "select id,review,reviewDate,reviewImage from Review where pNum = ?;";
		$st = $pdo->prepare($query);
		$st->execute([$pNum]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		$st = null;
		$pdo = null;
		return $res;
	}
	function putReview($id,$pNum,$review,$ig){
		$rday = date("Y-m-d");
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO Review(pNum,id, review, reviewDate,reviewImage)
					SELECT ?,?,?,?,? FROM DUAL
					WHERE EXISTS(
					SELECT id FROM Pay
					WHERE id = ? and pNum = ? )
                    and NOT EXISTS(SELECT id FROM Review
					WHERE id = ? and pNum = ?);";
		$st = $pdo->prepare($query);
		if($st->execute([$pNum,$id,$review,$rday,$ig,$id,$pNum,$id,$pNum])){
			$res = $rday;
			$st = null;
			$pdo = null;
			return $res;
		}
		else{
			$st = null;
			$pdo = null;
			$res = null;
			return $res;
		}
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
