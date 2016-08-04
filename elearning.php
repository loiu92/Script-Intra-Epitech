<?php
$lien_elearning = "https://intra.epitech.eu" . "/e-learning/?format=json";
$lien_autologin = 'https://intra.epitech.eu/' . $argv[1];  // Mettez ici votre lien d'auto-login
$script_dir = getcwd();

$return = login_intra_json($lien_autologin, $lien_elearning);  // Fais une requête qui se connecte à l'intra.
$intra = json_decode($return);					// Decode JSON le resultat de la 1ère requete.


for ($nb_semester=0; $nb_semester < 4; $nb_semester++) {
	$modules = list_modules_semester($intra, $nb_semester);
	init_directory($script_dir);
	init_directory_semester($nb_semester, $script_dir);
	create_dir_modules($intra, $nb_semester, $modules, $script_dir);

	foreach ($intra[$nb_semester]->modules as $working_module) {
			chdir($script_dir . '/Modules/Semestre' . $nb_semester . '/' . $working_module->slug);
			foreach ($working_module->classes as $classes) {
				chdir($classes->slug);
				foreach ($classes->steps as $steps) {
					$path_cookie = 'cookies_connexion.txt';
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
				}
			};
	}
}

function login_intra_json($lien_autologin, $lien_elearning)
{
	$path_cookie = 'cookies_connexion.txt';
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

function create_dir_modules($intra, $nb_semester, $modules, $script_dir)
{
	foreach ($modules as $key) {
		mkdir($script_dir . '/Modules/Semestre' . $nb_semester . '/'. $key);
	}
}

function list_modules_semester($intra, $nb_semester)
{
	$i = 0;
	foreach ($intra[$nb_semester]->modules as $key) {
			$modules_semester[$i] = $key->slug;
			$i = $i + 1;
	}
	return $modules_semester;
}

?>
