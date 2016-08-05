#!/usr/bin/php
<?php
if (!isset($argv[1]))
	{
		echo "Veuillez mettre votre lien d'autologin\n";
		return -1;
	}
else if ($argv[1] == "-h")
{
	echo "Usage: ./" . $argv[0] . " autologin_token (ex: auth-1a2b3c4d5e) [directory for files]\n";
	return 0;
}

$lien_elearning = "https://intra.epitech.eu" . "/e-learning/?format=json";
$lien_autologin = 'https://intra.epitech.eu/' . $argv[1];
$script_dir = getcwd();

$return = login_intra_json($lien_autologin, $lien_elearning, $script_dir);
$intra = json_decode($return);

foreach ($intra as $semester) {
	$nb_semester = $semester->semester;
	init_directory($script_dir);
	init_directory_semester($nb_semester, $script_dir);

	foreach ($intra[$nb_semester]->modules as $working_module) {
		mkdir($script_dir . '/Modules/Semestre' . $nb_semester . '/' . $working_module->slug);
			foreach ($working_module->classes as $classes) {
				chdir($script_dir . '/Modules/Semestre' . $nb_semester . '/' . $working_module->slug);
				mkdir($classes->slug);
				chdir($classes->slug);
				foreach ($classes->steps as $steps) {
						get_file($steps, $script_dir, $lien_autologin);
				}
			};
	}
}

function login_intra_json($lien_autologin, $lien_elearning, $script_dir)
{
	$path_cookie = $script_dir . '/cookies_connexion.txt';
	if (!file_exists(realpath($path_cookie))) touch($path_cookie);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $lien_autologin);
	curl_setopt($curl, CURLOPT_COOKIESESSION, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, false);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_COOKIEJAR, realpath($path_cookie));
	$return = curl_exec($curl);
	$data_json = array();
	curl_setopt($curl, CURLOPT_URL, $lien_elearning);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
	$return = curl_exec($curl);
	curl_close($curl);
	return $return;
}
function init_directory($script_dir)
{
	mkdir($script_dir . '/Modules');
}

function init_directory_semester($nb_semester, $script_dir)
{
	mkdir($script_dir . '/Modules/Semestre' . $nb_semester);
}

function get_file($steps, $script_dir, $lien_autologin)
{
	$path_cookie = $script_dir . '/cookies_connexion.txt';
	if (!file_exists(realpath($path_cookie))) touch($path_cookie);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $lien_autologin);
	curl_setopt($curl, CURLOPT_COOKIESESSION, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, false);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_COOKIEJAR, realpath($path_cookie));
	$return = curl_exec($curl);
	$data_json = array();
	curl_setopt($curl, CURLOPT_URL, $steps->step->fullpath);
	curl_setopt($curl, CURLOPT_POST, false);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
	$content = curl_exec($curl);
	curl_close($curl);
	$name_content = substr($steps->step->fullpath, strripos($steps->step->fullpath, "/") + 1);
	file_put_contents($name_content, $content);
	echo $name_content . "\n";
}

?>
