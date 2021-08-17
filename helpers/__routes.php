<?php


/**
 * function to handle incoming requests
 */
function __handleRoutes()
{

	global $db;

	/* uri and method */
	$uri = $_SERVER['REQUEST_URI'];
	$method = $_SERVER['REQUEST_METHOD'];
	$domain = 'http://' . $_SERVER['HTTP_HOST'];

	/*
	| POST /shorten
	*/
	if ($method == 'POST' && $uri == '/shorten') {

		if (isset($POST['link'])) {
			
			$link = $POST['link'];
			$link_id = substr(md5($link . time()), 0, 10);

			/* store in the database */
			$db->insert('links', [
				'link_id'       => $link_id,
				'original_link' => $link,
				'short_link'    => $domain . '/' . $link_id,
			])->submit();

			/* get link object and return to client */
			$row = $db->select('links', ['link_id', 'short_link'])
						->where(['link_id' => $link_id])
						->submit()[0];

			if ($row) {
				header('Content-Type: application/json');
				echo json_encode([
					'status'     => 'OK',
					'link_id'    => $row['link_id'],
					'short_link' => $row['short_link'],
				], JSON_UNESCAPED_SLASHES);
			}
			return;
		}

		header('Content-Type: application/json');
		echo json_encode([
			'status'  => '404',
			'message' => 'Link is required',
		], JSON_UNESCAPED_SLASHES);
		return;
	}


	/*
	| GET /{link_id}
	*/
	if ($method == 'GET' && @preg_match('/^(\/(?<link_id>[a-fA-F0-9]{10}))$/i', $uri, $match)) {
		
		$link_id = $match['link_id'];

		/* search the database for the given id */
		$links = $db->select('links', ['id', 'original_link', 'visits'])
			->where(['link_id' => $link_id])
			->submit();


		/* if link was found */
		if (!is_null($links) && isset($links[0])) {

			$lid = $links[0]['id'];
			$visits = intval($links[0]['visits']);
			$visits = $visits + 1;

			/* first we increment the visits. */
			$db->update('links', ['visits' => "{$visits}"])->where(['id' => $lid])->submit();

			/* redirect */
			header('Location: ' . $links[0]['original_link']);
		}

		/* if not found! */
		echo json_encode([
			'status' => '404',
			'message' => 'Link was not found!',
		]);
		return;
	}


	/**
	 * else, show welcome page
	 */
	// echo __DIR__;
	return include_once($_SERVER['DOCUMENT_ROOT'] . '/pages/welcome.php');
}