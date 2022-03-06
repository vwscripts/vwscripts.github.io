<?php
	ini_set("display_errors", 0);

	if (isset($_GET["nicks"])) {
		$j = json_decode(file_get_contents("https://api.vimeworld.ru/user/name/" . $_GET["nicks"] . "?token=DBlJt12QS6FrFTNHQcb9HlwI8ptVw5R"), true);
		foreach ($j as $user) {
			$ids[] = $user["id"];
		}
		echo(json_encode(["ids" => implode(",", $ids)]));
		return true;
	}
	
	if (isset($_POST["lg"])) {
		foreach (explode(",", $_POST["ids"]) as $id) {
			$j = json_decode(file_get_contents("https://api.vimeworld.ru/user/" . $id . "/matches?count=1&token=DBlJt12QS6FrFTNHQcb9HlwI8ptVw5R"), true);
			$res[0][$j["user"]["username"]] = $j["matches"][0]["game"] . " (<a href='https://vime.top/matches#" . $j["matches"][0]["id"] . "' target='_blank'>" . $j["matches"][0]["id"] . "</a>)";
			$res[1][$j["user"]["username"]] = $j["matches"][0]["date"];
		}
		echo (json_encode($res));
		return true;
	}
	
	$j = json_decode(file_get_contents("https://api.vimeworld.ru/user/session/" . $_POST["ids"] . "?token=DBlJt12QS6FrFTNHQcb9HlwI8ptVw5R"), true);
	if (empty($j)) {
		echo "err";
		return false;
	}
	foreach ($j as $user) $res[$user["username"]] = (!$user["online"]["value"] ? "offline" : $user["online"]["game"]);
	echo (json_encode($res));
?>