<?php

namespace App\Model;

class UserManager extends AbstractManager
{
    public const TABLE = 'user';
    //Methode pour récupérer un utilisateur par son mail
    public function selectOneByEmail(string $email): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE email=:email");
        $statement->bindValue('email', $email, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }

    //Ajout d'un utilisateur
    public function insert(array $credentials): int
    {
        $query = "INSERT INTO "  . static::TABLE . "(email, password,pseudo, firstname, lastname) 
        VALUES (:email, :password, :pseudo, :firstname, :lastname)";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':email', $credentials['email']);
        //Je n'oublie pas de crypter le mot de passe
        $statement->bindValue(':password', password_hash($credentials['password'], PASSWORD_DEFAULT));
        $statement->bindValue(':firstname', $credentials['firstname']);
        $statement->bindValue(':lastname', $credentials['lastname']);
        $statement->bindValue(':pseudo', $credentials['pseudo']);
        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }
}
