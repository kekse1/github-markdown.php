<?php

//
// Copyright (c) Sebastian Kucharczyk <kuchen@kekse.biz>
// v0.3.2
//
// Will first fetch your .md markdown document,
// then uses the GitHub API to render it as HTML.
//
// It's an example for using the cURL library for HTTP requests,
// used here in combination with some GitHub API.
//

//
namespace kekse;

//
const DEFAULT_TIMEOUT = 30;
const DEFAULT_FAMILY = 0;

//
if(!extension_loaded('curl'))
{
	die('No cURL module loaded!');
}
else
{
	require_once(__DIR__ . '/github-markdown.inc.php');
}

//
if(!defined('TIMEOUT')) define('TIMEOUT', DEFAULT_TIMEOUT);
if(!defined('FAMILY')) define('FAMILY', DEFAULT_FAMILY);

//
function getMarkdownHTML(... $_args)
{
	return \kekse\github\getMarkdownHTML(... $_args);
}

//
function renderHeaders($_headers)
{
	$result = [];
	
	if(array_is_list($_headers))
	{
		$count = count($_headers);
		
		for($i = 0, $j = 0; $i < $count; ++$i)
		{
			if(is_string($_headers[$i]) && $_headers[$i] !== '')
			{
				$result[$j++] = $_headers[$i];
			}
		}
	}
	else foreach($_headers as $key => $value)
	{
		if(is_string($value))
		{
			$result[] = trim($key) . ': ' . trim($value);
		}
	}

	return $result;
}

function parseHeaders($_headers)
{
	if(!array_is_list($_headers))
	{
		return $_headers;
	}
	
	$count = count($_headers);
	$result = []; $item;

	for($i = 0; $i < $count; ++$i)
	{
		if(!is_string($_headers[$i]) || $_headers[$i] === '')
		{
			continue;
		}
		
		$item = explode(':', $_headers[$i], 2);
		
		if(count($item) < 2)
		{
			continue;
		}

		$item[0] = trim($item[0]);
		$item[1] = trim($item[1]);

		$result[$item[0]] = $item[1];
	}
	
	return $result;
}

function extractFromHeaders($_headers, $_subject)
{
	if(array_is_list($_headers))
	{
		$_headers = parseHeaders($_headers);
	}

	$_subject = strtolower($_subject);
	
	foreach($_headers as $key => $value)
	{
		if(strtolower($key) === $_subject)
		{
			return $value;
		}
	}
	
	return null;
}

function httpRequest($_url, $_method = 'GET', $_headers = null, $_data = null, $_timeout = TIMEOUT, $_family = FAMILY)
{
	//
	$_method = strtoupper($_method);

	//
	$curl = curl_init();

	curl_setopt($curl, CURLOPT_URL, $_url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_method);
	
	if($_method === 'POST')
	{
		if(is_array($_data))
		{
			$_data = json_encode($_data);
		}
		else if(! is_string($_data))
		{
			die('Invalid $_data argument (neither Array nor String)');
		}

		curl_setopt($curl, CURLOPT_POSTFIELDS, $_data);
	}

	if(is_array($_headers))
	{
		$_headers = renderHeaders($_headers);

		curl_setopt($curl, CURLOPT_HTTPHEADER, $_headers);

		$userAgent = extractFromHeaders($_headers, 'user-agent');

		if($userAgent !== null)
		{
			curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
		}
	}

	if(is_int($_family)) switch($_family)
	{
		case 4:
			curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			break;
		case 6:
			curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V6);
			break;
	}

	if(is_int($_timeout) && $_timeout >= 0)
		curl_setopt($curl, CURLOPT_TIMEOUT, $_timeout);
	//curl_setopt($curl, CURLOPT_HEADER, true);

	//
	$response = curl_exec($curl);
	$error = curl_error($curl);

	//
	curl_close($curl);

	//
	if($error)
	{
		die('Error in cURL request: ' . $error);
	}
	else if(curl_errno($curl))
	{
		die('Error in cURL request: (' . curl_errno($curl) . ') ' . curl_error($curl));
	}

	return $response;
}

//
namespace kekse\github;

//
const API = 'https://api.github.com/markdown';
const API_VERSION = '2022-11-28';
const RAW = 'https://raw.githubusercontent.com/%{user}/%{repository}/master/';
const AGENT = 'https://github.com/kekse1/';

//
function generateDocumentURL($_repository, $_path = 'README.md', $_user = USER)
{
	$result = str_replace('%{repository}', $_repository, RAW);
	$result = str_replace('%{user}', $_user, $result);
	if($result[strlen($result) - 1] !== '/') $result .= '/';
	if(is_string($_path)) $result .= $_path;
	return $result;
}

function renderMarkdown($_document, $_repository, $_user = USER)
{
	$data = array('text' => $_document);
	$headers = array(
		'Accept' => 'application/vnd.github+json',
		'Authorization' => 'Bearer ' . TOKEN,
		'X-GitHub-Api-Version' => API_VERSION,
		'User-Agent' => AGENT
	);

	$data = array(
		'text' => $_document,
		'mode' => MODE
	);

	if(is_string($_repository) && is_string($_user))
	{
		$data['context'] = $_user . '/' . $_repository;
	}

	return \kekse\httpRequest(API, 'POST', $headers, $data);
}

function getMarkdownDocument($_repository, $_path = 'README.md', $_user = USER)
{
	$url = generateDocumentURL($_repository, $_path, $_user);
	return \kekse\httpRequest($url);
}

function getMarkdownHTML($_repository, $_path = 'README.md', $_user = USER)
{
	$document = getMarkdownDocument($_repository, $_path, $_user);
	return renderMarkdown($document, $_repository, $_user);
}

?>
