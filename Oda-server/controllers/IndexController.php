<?php
	require 'function.php';

	const JWT_SECRET_KEY = "HELLO_HELLO_HELLO_HELO_HELLO_HELLO_HELLO_HELLO_HELO_HELO_HELLO_HELLO_HELLO_HELLO_HELLO_HEO_HELLO_HELLO_HELLO_HELLO_HELLO_HELO_";

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
			case "test":
				http_response_code(200);
				$res->result = test();
				$res->isSuccess = TRUE;
				$res->code = 100;
				$res->message = "테스트 성공";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;
			/*
			 * API No. 0
			 * API Name : 테스트 Path Variable API
			 * 마지막 수정 날짜 : 19.04.29
			 */
			case "testDetail":
				http_response_code(200);
				$res->result = testDetail($vars["testNo"]);
				$res->isSuccess = TRUE;
				$res->code = 100;
				$res->message = "테스트 성공";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;
			/*
			 * API No. 0
			 * API Name : 테스트 Body & Insert API
			 * 마지막 수정 날짜 : 19.04.29
			 */
			case "testPost":
				http_response_code(200);
				$res->result = testPost($req->name);
				$res->isSuccess = TRUE;
				$res->code = 100;
				$res->message = "테스트 성공";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;

			case "id":
				$id = $req->id;
				http_response_code(200);
				if (CheckId($id) == 1) {
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
				$bs = $req->business;
				$spe = preg_match("/[\!\@\#\$\%\^\&\*\,\.\?\+\=\-\_]/u", $id);
				$sbs = preg_match("/[\!\@\#\$\%\^\&\*\,\.\?\+\=\-\_]/u", $bs);
				if (empty($id) || empty($pw) || empty($ad) || empty($bs)) {
					$res->isSuccess = false;
					$res->code = 00;
					$res->message = "공백이 입력됬습니다";
					http_response_code(200);
				} else if ($spe == 1 || $sbs == 1) {
					$res->isSuccess = false;
					$res->code = 10;
					$res->message = "id나 직종에 특수문자가 입력됬습니다";
					http_response_code(200);
				} else {
					$res->id = Signup($id, $pw, $ad, $bs);
					$res->isSuccess = true;
					$res->code = 200;
					$res->message = "회원가입성공";
					http_response_code(200);
				}
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;

			case "searchProduct":
				$pName = $req->pName;
				if (empty($pName)) {
					$res->isSuccess = false;
					$res->code = 00;
					$res->message = "공백이 입력됬습니다";
					http_response_code(200);
				} else {
					$res->result = Array();
					$res->result = Search($pName);
					if ($res->result != null) {
						$res->isSuccess = true;
						$res->code = 300;
						$res->message = "검색결과";
						http_response_code(200);
					} else {
						$res = (Object)Array();
						$res->isSuccess = FALSE;
						$res->code = 350;
						$res->message = "검색결과 없음";
						http_response_code(200);
					}
				}
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;


			case "viewProduct":
				$pNum = $req->pNum;
				if (empty($pNum)) {
					$res->isSuccess = false;
					$res->code = 00;
					$res->message = "공백이 입력됬습니다";
					http_response_code(200);
				} else {
					$res->result = Array();
					$res->result = ViewProduct($pNum);
					if ($res->result != null) {
						$res->isSuccess = true;
						$res->code = 400;
						$res->message = "상품기본페이지";
						http_response_code(200);
					} else {
						$res = (Object)Array();
						$res->isSuccess = FALSE;
						$res->code = 450;
						$res->message = "DB오류";
						http_response_code(200);
					}
				}
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;

			case "viewProductDetail":
				$pNum = $req->pNum;
				if (empty($pNum)) {
					$res->isSuccess = false;
					$res->code = 00;
					$res->message = "공백이 입력됬습니다";
					http_response_code(200);
				} else {
					$res->result = Array();
					$res->result = ViewProductDetail($pNum);
					if ($res->result != null) {
						$res->isSuccess = true;
						$res->code = 500;
						$res->message = "상품상세페이지";
						http_response_code(200);
					} else {
						$res = (Object)Array();
						$res->isSuccess = FALSE;
						$res->code = 550;
						$res->message = "DB오류";
						http_response_code(200);
					}
				}
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;

			case "viewProductReview":
				$pNum = $req->pNum;
				if (empty($pNum)) {
					$res->isSuccess = false;
					$res->code = 00;
					$res->message = "공백이 입력됬습니다";
					http_response_code(200);
				} else {
					$res->result = Array();
					$res->result = ViewProductReview($pNum);
					if ($res->result != null) {
						$res->isSuccess = true;
						$res->code = 600;
						$res->message = "상품후기목록";
						http_response_code(200);
					} else {
						$res = (Object)Array();
						$res->isSuccess = true;
						$res->code = 610;
						$res->message = "상품후기가없습니다";
						http_response_code(200);
					}
				}
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;
		}
	} catch (\Exception $e) {
		return getSQLErrorException($errorLogs, $e, $req);
	}
