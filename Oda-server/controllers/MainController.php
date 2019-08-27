<?php
	require 'function.php';

	const JWT_SECRET_KEY = "HELLO_HELLO_HELLO_HELO_HELLO_HELLO_HELLO_HELLO_HELO_HELO_HELLO_HELLO_HELLO_HELLO_HELLO_HEO_HELLO_HELLO_HELLO_HELLO_HELLO_HELO_";

	$res = (Object)Array();
	header('Content-Type: json');
	$req = json_decode(file_get_contents("php://input"));
	try {
		addAccessLogs($accessLogs, $req);
		switch ($handler) {
			/*
			 * API No. 0
			 * API Name : JWT 유효성 검사 테스트 API
			 * 마지막 수정 날짜 : 19.04.25
			 */
			case "validateJwt":
				// jwt 유효성 검사
				if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
					$res->isSuccess = FALSE;
					$res->code = 201;
					$res->message = "유효하지 않은 토큰입니다";
					echo json_encode($res, JSON_NUMERIC_CHECK);
					addErrorLogs($errorLogs, $res, $req);
					return;
				}

				http_response_code(200);
				$res->isSuccess = TRUE;
				$res->code = 100;
				$res->message = "테스트 성공";

				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;
			/*
			 * API No. 0
			 * API Name : JWT 생성 테스트 API
			 * 마지막 수정 날짜 : 19.04.25
			 */
			case "createJwt":
				// jwt 유효성 검사
				if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
					$res->isSuccess = FALSE;
					$res->code = 201;
					$res->message = "유효하지 않은 토큰입니다";
					echo json_encode($res, JSON_NUMERIC_CHECK);
					addErrorLogs($errorLogs, $res, $req);
					return;
				}
				http_response_code(200);

				//페이로드에 맞게 다시 설정 요함
				$jwt = getJWToken($userId, $userPw, $loginType, $accessToken, $refreshToken, JWT_SECRET_KEY);
				$res->result->jwt = $jwt;
				$res->isSuccess = TRUE;
				$res->code = 100;
				$res->message = "테스트 성공";
				echo json_encode($res, JSON_NUMERIC_CHECK);
				break;

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
		}
	} catch (\Exception $e) {
		return getSQLErrorException($errorLogs, $e, $req);
	}
