<?php

namespace App\Controller;

use App\Model\UserManager;

class UserController extends AbstractController
{
    public function login(): string
    {
        $errors = [];
        //Vérifie si des champs ont des erreurs
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $credentials = array_map('trim', $_POST);
            if ($credentials['email'] === '' || !filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'veuillez saisir une adresse email valide';
            }
            $email = htmlentities($credentials['email']);
            $password = $credentials['password'];
            if ($password === '') {
                $errors[] = 'Veuillez saisir un mot de passe';
            }
            //Si je n'ai pas d'erreur
            if (empty($errors)) {
                //Je récupère l'user dans la BDD
                $userManager = new UserManager();
                $user = $userManager->selectOneByEmail($email);
                //Je vérifie son mot de passe
                if ($user && password_verify($password, $user['password'])) {
                    //Si tout est OK, je configure la session afin de me rappeler de l'utilisateur
                    $_SESSION['user_id'] = $user['id'];
                    header('location: /');
                    exit();
                } else {
                    $errors[] = "L'utilisateur n'existe pas.";
                }
            }
        }
        return $this->twig->render('User/login.html.twig');
    }

    public function logout()
    {
        //Je supprime la clé de session, comme cela impossible de revenir dessus
        unset($_SESSION['user_id']);
        header('location: /');
    }

    public function register(): string
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $credentials = array_map('trim', $_POST);
            if (!$this->validateEntry($credentials, 'firstname')) {
                $errors[] = 'Veuillez remplir votre prénom';
            }
            if (!$this->validateEntry($credentials, 'lastname')) {
                $errors[] = 'Veuillez remplir votre nom';
            }
            if (!$this->validateEntry($credentials, 'email')) {
                $errors[] = 'Veuillez remplir votre email';
            }
            if (!$this->validateEntry($credentials, 'pseudo')) {
                $errors[] = 'Veuillez remplir votre pseudo';
            }
            if (!$this->validateEntry($credentials, 'password')) {
                $errors[] = 'Veuillez remplir votre mot de passe';
            }
            if (empty($errors)) {
                $credentials['firstname'] = htmlspecialchars($credentials['firstname']);
                $credentials['lastname'] = htmlspecialchars($credentials['lastname']);
                $credentials['email'] = htmlspecialchars($credentials['email']);
                $credentials['pseudo'] = htmlspecialchars($credentials['pseudo']);
                $userManager = new UserManager();
                //J'enregistre l'utilisateur en base de données
                if ($userManager->insert($credentials)) {
                    //et je fais une auto connexion de cet utilisateur
                    return $this->login();
                }
            }
        }
        return $this->twig->render('User/register.html.twig');
    }

    private function validateEntry(array $credentials, string $field): bool
    {
        return !empty($credentials[$field]) && $credentials[$field] !== '';
    }
}
