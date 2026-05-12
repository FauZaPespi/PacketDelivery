<?php
namespace Fauza\Template\Models;
class Employe
{
    public int $id;
    public string $nom;
    public string $prenom;
    public string $email;
    public string $password;
    public bool $role; // true for admin, false for livreur

    function __construct(int $id, string $nom, string $prenom, string $email, string $password, bool $role)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }
}
?>