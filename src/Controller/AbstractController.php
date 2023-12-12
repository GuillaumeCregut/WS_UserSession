<?php

namespace App\Controller;

use App\Model\UserManager;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

abstract class AbstractController
{
    protected Environment $twig;
    //L'utilisateur sera disponible pour tous les controleurs si il existe
    protected array | false $user;

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        $loader = new FilesystemLoader(APP_VIEW_PATH);
        $this->twig = new Environment(
            $loader,
            [
                'cache' => false,
                'debug' => (ENV === 'dev'),
            ]
        );
        $this->twig->addExtension(new DebugExtension());
        //Je récupère l'utilisateur dans la base de données si il existe
        //Pour cela, j'utilise l'opérateur ternaire
        $userManager = new UserManager();
        /* Si la session existe, alors je vais chercher dans la base de données, sinon je retourne false */
        $this->user = isset($_SESSION['user_id']) ? $userManager->selectOneById($_SESSION['user_id']) : false;
        //Maintenant, tous les templates twig auront accès directement à user,
        //sans devoir le préciser dans chaque controleur.
        $this->twig->addGlobal('user', $this->user);
    }
}
