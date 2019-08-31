<?php
	require 'function.php';

	const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

	$res = (Object)Array();
	header('Content-Type: json');
	$req = json_decode(file_get_contents("php://input"));
	try {
		addAccessLogs($accessLogs, $req);
		switch ($handler) {
			case "index":
				echo "API Server";
				break;
			case "ACCESS_LOGS":
				//            header('content-type text/html charset=utf-8');
				header('Content-Type: text/html; charset=UTF-8');
				getLogs("./logs/access.log");
				break;
			case "ERROR_LOGS":
				//            header('content-type text/html charset=utf-8');
				header('Content-Type: text/html; charset=UTF-8');
				getLogs("./logs/errors.log");
				break;
			/*
			 * API No. 0
			 * API Name : 테스트 API
			 * 마지막 수정 날짜 : 19.04.29
			 */
			case "id":
				$id = $_GET["id"];
				http_response_code(200);
				if (checkId($id) == 1) {
					$res->isSuccess = TRUE;
					$res->code = 100;
					$res->message = "중복된ID존재";

				} else {
					$res->isSuccess = FALSE;
					$res->code = 150;
					$res->message = "중복된 ID없음";

				}
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;

			case "signup":
				http_response_code(200);
				$id = str_replace(' ', '', trim($req->id));
				$pw = $req->pw;
				$ad = $req->address;
				$bs = $req->type;
				$spe = preg_match("/[\!\@\#\$\%\^\&\*\,\.\?\+\=\-\_]/u", $id);
				$ckid = preg_match("/^[a-z0-9]{4,10}$/", $id);
				$ckpw = preg_match("/^[0-9a-z]{5,15}$/", $pw);
				if (empty($id) || empty($pw) || empty($ad) || empty($bs)) {
					$res->isSuccess = false;
					$res->code = 00;
					$res->message = "공백이 입력됐습니다";
					http_response_code(200);
				} else if ($spe == 1) {
					$res->isSuccess = false;
					$res->code = 10;
					$res->message = "id에 특수문자가 입력됬습니다";
					http_response_code(200);
				} else if ($ckid != 1) {
					$res->isSuccess = false;
					$res->code = 20;
					$res->message = "id는 4자 이상 10자 이하 영소문자/숫자 허용으로 만들어주세요";
				} else if ($ckpw != 1) {
					$res->isSuccess = false;
					$res->code = 30;
					$res->message = "pw는 5자 이상 15자 이하의 숫자와 소문자 조합으로 만들어주세요";
				} else {
					$bs = getBusiness($bs);
					if ($bs == false) {
						$res->isSuccess = false;
						$res->code = 20;
						$res->message = "요식업이 잘못 입력됐습니다";
						http_response_code(200);
						echo json_encode($res, JSON_NUMERIC_CHECK);
						break;
					}
					$res->id = signUp($id, $pw, $ad, $bs);
					$res->isSuccess = true;
					$res->code = 200;
					$res->message = "회원가입성공";
					http_response_code(200);
				}
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;

		}
	} catch (\Exception $e) {
		return getSQLErrorException($errorLogs, $e, $req);
	}
