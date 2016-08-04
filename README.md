#########################################################
#########################################################
##                                                     ##
##                      Documentation                  ##
##                                                     ##
#########################################################
#########################################################

Pour utiliser ces scripts, vous devez obtenier votre lien d'autologin dans la partie Administration de l'Intra d'Epitech.

Pour lancer les scripts, il faut appeler le script avec en paramètre, le lien d'autologin sans la partie de l'url de l'intra.
    ./register_modules.php auth-2g4p3b34v5b2g0f17217z5fze622dg544c3166e0

Pour s'inscrire à un module, vous devez rajouter register?format=json à la fin de l'url du module.

    "https://intra.epitech.eu/module/2014/G-EPI-022/PAR-0-1/" devient "https://intra.epitech.eu/module/2014/G-EPI-022/PAR-0-1/register?format=json"



Pour se désinscrire, c'est la même chose avec unregister :

    "https://intra.epitech.eu/module/2014/G-EPI-022/PAR-0-1/" devient "https://intra.epitech.eu/module/2014/G-EPI-022/PAR-0-1/unregister?format=json"
