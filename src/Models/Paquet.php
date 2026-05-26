<?php
namespace Fauza\Template\Models;
class Paquet
{
    public ?int $id_paquet;
    public string $numeroPostal;
    public string $nomDestinataire;
    public string $prenomDestinataire;
    public string $adresseDestinataire;
    public float $latitudeAdresse;
    public float $longitudeAdresse;
    public string $dateLivraison;
    public ?int $ordreRoute;
    public string $statutLivraison;
    public ?int $id_route;
    public int $id_employe_createur;
    public ?int $id_employe_livreur;
    public ?string $dateHeureLivraison;

    function __construct(?int $id_paquet, string $numeroPostal, string $nomDestinataire, string $prenomDestinataire, float $latitudeAdresse, float $longitudeAdresse, string $adresseDestinataire, string $dateLivraison, ?int $ordreRoute, string $statutLivraison, ?int $id_route, int $id_employe_createur, ?int $id_employe_livreur, ?string $dateHeureLivraison = null)
    {
        $this->id_paquet = $id_paquet;
        $this->numeroPostal = $numeroPostal;
        $this->nomDestinataire = $nomDestinataire;
        $this->prenomDestinataire = $prenomDestinataire;
        $this->adresseDestinataire = $adresseDestinataire;
        $this->latitudeAdresse = $latitudeAdresse;
        $this->longitudeAdresse = $longitudeAdresse;
        $this->dateLivraison = $dateLivraison;
        $this->ordreRoute = $ordreRoute;
        $this->statutLivraison = $statutLivraison;
        $this->id_route = $id_route;
        $this->id_employe_createur = $id_employe_createur;
        $this->id_employe_livreur = $id_employe_livreur;
        $this->dateHeureLivraison = $dateHeureLivraison;
    }
}
?>
