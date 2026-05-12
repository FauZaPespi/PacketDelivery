<?php
namespace Fauza\Template\Models;
class Paquet
{
    public string $numeroPostal;
    public string $nomDestinataire;
    public string $prenomDestinataire;
    public string $adresseDestinataire;
    public float $latitudeAdresse;
    public float $longitudeAdresse;
    public string $dateLivraison;
    public int $ordreRouteLivraison;
    public string $statutLivraison;
    public int $id_route;
    public int $id_employe_createur;
    public int $id_employe_livreur;

    function __construct(string $numeroPostal, string $nomDestinataire, string $prenomDestinataire, float $adresseDestinataire, float $latitudeAdresse, string $longitudeAdresse, string $dateLivraison, string $ordreRouteLivraison, string $statutLivraison, int $id_route, int $id_employe_createur, int $id_employe_livreur)
    {
        $this->numeroPostal = $numeroPostal;
        $this->nomDestinataire = $nomDestinataire;
        $this->prenomDestinataire = $prenomDestinataire;
        $this->adresseDestinataire = $adresseDestinataire;
        $this->latitudeAdresse = $latitudeAdresse;
        $this->longitudeAdresse = $longitudeAdresse;
        $this->dateLivraison = $dateLivraison;
        $this->ordreRouteLivraison = $ordreRouteLivraison;
        $this->statutLivraison = $statutLivraison;
        $this->id_route = $id_route;
        $this->id_employe_createur = $id_employe_createur;
        $this->id_employe_livreur = $id_employe_livreur;
    }
}
?>
