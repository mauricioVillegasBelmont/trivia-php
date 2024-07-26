$urls = array(
    "/^\/$/" => "/site/page/default",
    "/^\/registration$/" => "/site/page/player_registration",

    "/^\/trivia(?:\/|\w+|\d+)*$/" => "/site/trivia/game_manager",
	"/^\/get_trivia$/" => "/site/trivia/get_trivia",
	"/^\/save_trivia$/" => "/site/trivia/save_trivia",


    "/^\/login$/" => "/panel/user",
    "/^\/logout$/" => "/panel/user",
    "/^\/dashboard$/" => "/panel/dashboard/home",
);
