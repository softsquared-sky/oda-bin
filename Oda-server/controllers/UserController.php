<?php
	require 'function.php';

	const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

	$res = (Object)Array();
	header('Content-Type: json');
	$req = json_decode(file_get_contents("php://input"));
	try {
		addAccessLogs($accessLogs, $req);
		switch ($handler) {
			case "login":
				http_response_code(200);
				$id = $req->id;
				$pw = $req->pw;

				if (login($id, $pw)) {
					//페이로드에 맞게 다시 설정 요함
					$jwt = getJWToken($id, $pw, JWT_SECRET_KEY);
					$res->result->jwt = $jwt;
					$res->isSuccess = TRUE;
					$res->code = 1200;
					$res->message = "로그인 성공";
				} else {
					$res->isSuccess = FALSE;
					$res->code = 1210;
					$res->message = "아이디 비밀번호 틀림";
				}
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;

			case "createBasket":
				$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
				if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
					$res->isSuccess = FALSE;
					$res->code = 201;
					$res->message = "유효하지 않은 토큰입니다";
					echo json_encode($res, JSON_NUMERIC_CHECK);
					addErrorLogs($errorLogs, $res, $req);
					return;
				}
				$data = getDataByJWToken($jwt, JWT_SECRET_KEY);
				$id = $data->id;
				$pNum = $req->pNum;
				$pName = checkStock($pNum);
				$type = "장바구니";
				if ($pName == null) {
					$res->isSuccess = FALSE;
					$res->code = 850;
					$res->message = "재고가 없습니다";
				} else {
					$res->pName = setBasket($pNum, $pName, $id, $type);
					$res->isSuccess = TRUE;
					$res->code = 800;
					$res->message = "장바구니 등록";
				}
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;

			case "getBasket":
				$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
				if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
					$res->isSuccess = FALSE;
					$res->code = 201;
					$res->message = "유효하지 않은 토큰입니다";
					echo json_encode($res, JSON_NUMERIC_CHECK);
					addErrorLogs($errorLogs, $res, $req);
					return;
				}
				$data = getDataByJWToken($jwt, JWT_SECRET_KEY);
				$id = $data->id;
				$res->basketList = getBasket($id);
				if ($res->basketList != null) {
					$res->isSuccess = TRUE;
					$res->code = 900;
					$res->message = "장바구니 조회";
				} else {
					$res = (Object)Array();
					$res->isSuccess = TRUE;
					$res->code = 950;
					$res->message = "장바구니에 담은 물품이 없습니다";
				}
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;

			case "directOrder":

				$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
				if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
					$res->isSuccess = FALSE;
					$res->code = 201;
					$res->message = "유효하지 않은 토큰입니다";
					echo json_encode($res, JSON_NUMERIC_CHECK);
					addErrorLogs($errorLogs, $res, $req);
					return;
				}
				$data = getDataByJWToken($jwt, JWT_SECRET_KEY);
				$id = $data->id;
				$rows = (array)$req->pNumList;
				$k = sizeof($rows);
				for ($i = 0; $i < $k; $i++) {
					$rows[$i]->id = $id;
					$num = $rows[$i]->pNum;
					$ck = checkBasket($id, $num);
					if ($ck == 1) {
						unset($rows[$i]);
						continue;
					}
					$rows[$i]->pName = getProductName($num);
					if ($rows[$i]->pName == null) {
						$res->isSuccess = FALSE;
						$res->code = 1310;
						$res->message = "존재하지 않은 pNum입니다";
						echo json_encode($res, JSON_NUMERIC_CHECK);
						return;
					}
					$rows[$i]->type = "바로주문";
				}
				if ($rows == null) {
					$res->isSuccess = TRUE;
					$res->code = 1320;
					$res->message = "등록되어 있는 상품들입니다";
					echo json_encode($res, JSON_NUMERIC_CHECK);
					return;
				}
				$data = setDirect($rows);
				$res->isSuccess = TRUE;
				$res->code = 1300;
				$res->message = "바로주문 등록";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;

//				$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//				if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
//					$res->isSuccess = FALSE;
//					$res->code = 201;
//					$res->message = "유효하지 않은 토큰입니다";
//					echo json_encode($res, JSON_NUMERIC_CHECK);
//					addErrorLogs($errorLogs, $res, $req);
//					return;
//				}
//				$data = getDataByJWToken($jwt, JWT_SECRET_KEY);
//				$id = $data->id;
//				$pNum = $req->pNum;
//				$pName = checkStock($pNum);
//				$type = "바로주문";
//				if ($pName == null) {
//					$res->isSuccess = FALSE;
//					$res->code = 1350;
//					$res->message = "재고가 없습니다";
//				} else {
//					$res->pName = setBasket($pNum, $pName, $id, $type);
//					$res->isSuccess = TRUE;
//					$res->code = 1300;
//					$res->message = "바로주문 등록";
//				}
//				echo json_encode($res, JSON_NUMERIC_CHECK);
//				break;

			case "payment":
//				print_r(($req->paylist)[0]);
//				$ar = ($req->paylist)[0];
//				echo $ar->pNum;
//				$i = 0;
//				while ($req->paylist[$i]) {
//					print_r($req->paylist[$i]);
//					$i++;
//				}
				$jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
				if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
					$res->isSuccess = FALSE;
					$res->code = 201;
					$res->message = "유효하지 않은 토큰입니다";
					echo json_encode($res, JSON_NUMERIC_CHECK);
					addErrorLogs($errorLogs, $res, $req);
					return;
				}
				$data = getDataByJWToken($jwt, JWT_SECRET_KEY);
				$id = $data->id;
				$rows = (array)$req->pNumList;
				$k = sizeof($rows);
				for ($i = 0; $i < $k; $i++) {
					$rows[$i]->id = $id;
					$num = $rows[$i]->pNum;
					$amount = $rows[$i]->amount;
					$ck = getStock($num, $amount);
				}

				break;
		}
	} catch (\Exception $e) {
		return getSQLErrorException($errorLogs, $e, $req);
	}
