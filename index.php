<html>
   <head>
      <script src="http://code.jquery.com/jquery-latest.js"></script>
      <script src='https://code.responsivevoice.org/responsivevoice.js'></script>
      <script type="text/javascript">
		var nicksLatestStatus = {};
		var nicksLatestMatch = {};
		var nicksEntered = "";
		var ids = "";

		function isJson(str) {
		    try {
		        JSON.parse(str);
		    } catch (e) {
		        return false;
		    }
		    return true;
		}

		function nicksToId() {
		    var nicks = document.getElementById("nicks").value;
		    if (nicks !== nicksEntered) {
		        $.get("spec.php?nicks=" + nicks, function(html) {
		            if (isJson(html)) {
		            	res = JSON.parse(html);
		                ids = res["ids"];
		            }
		        });
		        nicksEntered = nicks;
		    }
		}

		function startMon() {
			nicksToId();
		    $.ajax({
		        type: "post",
		        url: "spec.php",
		        data: {
		            "ids": ids,
		        },
		        cache: false,
		        success: function(html) {
		            if (isJson(html)) {
		                res = JSON.parse(html);
		                Object.keys(res).forEach(function(obj) {
		                    nick = obj;
		                    game = res[obj];
		                    if (nicksLatestStatus[nick] !== game) {
		                        $("#results").append(new Date().toLocaleTimeString() + " > " + nick + " > " + game + "<br>");
		                        responsiveVoice.speak(nick.toLowerCase() + " at " + game.toLowerCase());
		                        nicksLatestStatus[nick] = game;
		                    }
		                });
		            }
		        }
		    });
		    t = setTimeout(startMon, 1000);
		}

		function checkLatestGames() {
			nicksToId();
		    $.ajax({
		        type: "post",
		        url: "spec.php",
		        data: {
		            "ids": ids,
		            "lg": "true"
		        },
		        cache: false,
		        success: function(html) {
		            if (isJson(html)) {
		                res = JSON.parse(html);
		                Object.keys(res[0]).forEach(function(obj) {
		                    nick = obj;
		                    match = res[0][obj];
		                    if (!nicksLatestMatch[nick]) nicksLatestMatch[nick] = match;
		                    if (nicksLatestMatch[nick] !== match) {
		                        $("#results").append(new Date().toLocaleTimeString() + " > " + nick + " > new match ended " + (Math.floor((new Date()).getTime() / 1000) - res[1][nick]) + " sec ago: " + match + "<br>");
		                        responsiveVoice.speak(nick.toLowerCase() + " ended the game on " + match.toLowerCase().split(" ")[0]);
		                        nicksLatestMatch[nick] = match;
		                    }
		                });
		            }
		        }
		    });
		    t2 = setTimeout(checkLatestGames, 4000);
		}

		function stopMon() {
		    clearTimeout(t);
		    clearTimeout(t2);
		}
      </script>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Мониторинг</title>
   </head>
   <body>
     Ники: <input id="nicks" type="text" maxlength="255" placeholder="Hocico,Lalisa,Okssi">
     <input type="submit" id="submitFormData" onclick="startMon(); checkLatestGames();" value="Начать мониторинг" >
     <input type="submit" id="submitFormData" onclick="stopMon();" value="Остановить мониторинг" >
      <div id="results"></div>
   </body>
</html>