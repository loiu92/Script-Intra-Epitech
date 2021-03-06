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
$lien_autologin = 'https://intra.epitech.eu/' . $argv[1];

$lien_intra_json = 'https://intra.epitech.eu' . '?format=json';
$module = '';          // Mettez un mot clé concernant le module, la casse n'est pas prise en compte, exemple : 'robotique' pour le module 'B1 - Robotique'


$return = login_intra_json($lien_autologin, $lien_intra_json);  // Fais une requête qui se connecte à l'intra.
$return = parse_epitech_webservice($return);                    // Parse la 1ère ligne.
$intra = json_decode($return);					// Decode JSON le resultat de la 1ère requete.
$lien_module = found_word($intra, $module);			// Cherche une correspondance dans les noms de modules avec $module.
if ($lien_module === -1)
{
	return -1;
}
$lien_module = "https://intra.epitech.eu$lien_module" . "register?format=json";
register_module($lien_autologin, $lien_module);			// Fais une requête qui inscris le compte au module.
mail_robotique($module, $intra);				// Envoie un mail de succès d'inscription à l'email du compte.

function login_intra_json($lien_autologin, $lien_intra_json)
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
	curl_setopt($curl, CURLOPT_URL, $lien_intra_json);
	curl_setopt($curl, CURLOPT_POSTFIELDS, data_json);
	$return = curl_exec($curl);
	curl_close($curl);
	return $return;
}
function parse_epitech_webservice($return)
{
	$nb = strpos($return, "{");
	return substr($return, $nb);
}
function found_word($intra, $module)
{
	$i = 0;
	while ($i <= 100)
	{
		if (stripos($intra->board->modules[$i]->title, $module) === FALSE)
		{
			$i = $i + 1;
		}
		else
		{
			return $intra->board->modules[$i]->title_link;
		}
	}
	echo "$module non trouvé dans les modules à inscrire\n";
	return -1;
}
function register_module($lien_autologin, $lien_module)
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
        curl_setopt($curl, CURLOPT_URL, $lien_module);
        curl_setopt($curl, CURLOPT_POSTFIELDS, data_json);
        $return = curl_exec($curl);
        curl_close($curl);
        return $return;
}
function mail_robotique ($module, $intra)
{
	$to = $intra->infos->internal_email;
	$subject = "Correspondance $module";
	$message = "Bonjour,\n\nSi tu recois ce mail,\nCela veut dire qu'il y a une correspondance avec  le mot $module, et que ce script t'a sûrement inscris au module $module.\nN'hésite pas à visiter https://intra.epitech.eu";
	$headers = "From: bot@loiu92.com\n";
	mail($to, $subject, $message, $headers);
}
?>
