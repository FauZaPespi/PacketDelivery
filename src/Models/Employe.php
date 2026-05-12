<?php
namespace Fauza\Template\Models;
class Employe
{
    public int $id;
    public string $nom;
    public string $prenom;
    public string $email;
    public string $password;
    public bool $estLivreur;

    function __construct(int $id, string $nom, string $prenom, string $email, string $password, bool $estLivreur)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->password = $password;
        $this->estLivreur = $estLivreur;
    }
}
?>
