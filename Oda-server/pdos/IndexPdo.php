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

	function search($pName, $turn)
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

	function viewProduct($pNum)
	{
		$res = Array();
		$pdo = pdoSqlConnect();
		$query = "SELECT p.pNum,p.pName,p.odaPrice  from Product as p where p.pNum =?;";
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

	function viewProductDetail($pNum)
	{
		$pdo = pdoSqlConnect();
		$query = "SELECT p.qpp,p.storeMethod,p.origin from ProductDetail as p where p.pNum =?;";
		$st = $pdo->prepare($query);
		$st->execute([$pNum]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll()[0];
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

	function viewProductReview($pNum)
	{
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

	function putReview($id, $pNum, $review, $ig, $title)
	{
		$rday = date("Y-m-d");
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO Review(pNum,id, review, reviewDate,reviewImage,title)
					SELECT ?,?,?,?,?,? FROM DUAL
					WHERE EXISTS(
					SELECT id FROM Pay
					WHERE id = ? and pNum = ? )
                    and NOT EXISTS(SELECT id FROM Review
					WHERE id = ? and pNum = ?);";
		$st = $pdo->prepare($query);
		if ($st->execute([$pNum, $id, $review, $rday, $ig, $title, $id, $pNum, $id, $pNum])) {
			$res = $rday;
			$st = null;
			$pdo = null;
			return $res;
		} else {
			$st = null;
			$pdo = null;
			$res = null;
			return $res;
		}
	}

	function checkPay($id, $pNum)
	{
		$pdo = pdoSqlConnect();
		$query = "SELECT EXISTS(SELECT id,pNum FROM Pay WHERE id= ? and pNum =?) AND NOT EXISTS(SELECT id,pNum FROM Review where id = ? and pNum = ?) AS exist;";
		$st = $pdo->prepare($query);
		$st->execute([$id, $pNum, $id, $pNum]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		$st = null;
		$pdo = null;

		return intval($res[0]["exist"]);
	}

	function checkStock($pNum)
	{
		$pdo = pdoSqlConnect();
		$query = "SELECT d.pNum,p.pName FROM ProductDetail as d inner join Product as p on p.pNum = d.pNum WHERE d.pNum =? and d.stock > 0;";
		$st = $pdo->prepare($query);
		$st->execute([$pNum]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetch();
		if ($res != null) return $res['pName'];
		$st = null;
		$pdo = null;
		return $res;

	}

	function setBasket($pNum, $pName, $id, $type)
	{
		$pdo = pdoSqlConnect();
		$query = "INSERT INTO Basket(id,pNum,pName, type)
					SELECT ?,?,?,? FROM DUAL
					WHERE NOT EXISTS(SELECT id FROM Basket
					WHERE id = ? and pNum = ? and type = ?);";
		$st = $pdo->prepare($query);
		if ($st->execute([$id, $pNum, $pName, $type, $id, $pNum, $type])) {
			$res = $pName;
			$st = null;
			$pdo = null;
			return $res;
		} else {
			$st = null;
			$pdo = null;
			$res = null;
			return $res;
		}
	}

	function getBasket($id)
	{
		$pdo = pdoSqlConnect();
		$query = "SELECT n.odaPrice, n.pNum, n.pName, n.stock, i.imageUrl,n.type
					from (SELECT tmp.odaPrice, d.stock, tmp.pNum, tmp.pName, tmp.type
      					from (SELECT p.odaPrice, p.pNum, p.pName, b.type
            					from Product as p
                     			join Basket as b on b.pNum = p.pNum
            					where b.id = ?) tmp
                			inner join ProductDetail as d on tmp.pNum = d.pNum) n
         				inner join ProductImage as i on i.pNum = n.pNum
					where i.type = 'main'
  					and i.turn = 1 order by type;";
		$st = $pdo->prepare($query);
		$st->execute([$id]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();
		return $res;

	}
	function getProductName($pNum){
		$pdo = pdoSqlConnect();
		$query = "SELECT pName FROM Product where pNum =?;";
		$st = $pdo->prepare($query);
		$st->execute([$pNum]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetch();
		if ($res != null) return $res['pName'];
		$st = null;
		$pdo = null;
		return $res;
	}

	function setDirect($rows){
		$pdo = pdoSqlConnect();
		$insert_values =array();
		foreach($rows as $d){
			$d = (array)$d;
			$question_marks[] = '(' .placeHolders('?', sizeof($d)) .')';
			$insert_values = array_merge($insert_values, array_values($d));
			$datafields = array_keys($d);
		}
		$sql = "INSERT INTO Basket (" . implode(",", $datafields ) . ") VALUES " . implode(',', $question_marks);

		$stmt = $pdo->prepare ($sql);
		$stmt->execute($insert_values);
	}
	function checkBasket($id,$pNum){
		$pdo = pdoSqlConnect();
		$query = "SELECT EXISTS(SELECT * FROM Basket WHERE id= ? and  pNum =?) AS exist;";


		$st = $pdo->prepare($query);
		//    $st->execute([$param,$param]);
		$st->execute([$id,$pNum]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchAll();

		$st = null;
		$pdo = null;

		return intval($res[0]["exist"]);

	}
	function getStock($pNum,$amount)
	{
		$pdo = pdoSqlConnect();
		$query = "SELECT EXISTS(SELECT d.pNum,p.pName FROM ProductDetail as d inner join Product as p on p.pNum = d.pNum WHERE d.pNum =? and d.stock > ?) AS exist;";
		$st = $pdo->prepare($query);
		$st->execute([$pNum,$amount]);
		$st->setFetchMode(PDO::FETCH_ASSOC);
		$res = $st->fetchALL();
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
