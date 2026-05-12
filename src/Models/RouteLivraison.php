<?php
namespace Fauza\Template\Models;
class RouteLivraison
{
    public int $id_route;
    public string $dateRoute;
    public int $id_employe_livreur;

    function __construct(int $id_route, string $dateRoute, int $id_employe_livreur)
    {
        $this->id_route = $id_route;
        $this->dateRoute = $dateRoute;
        $this->id_employe_livreur = $id_employe_livreur;
    }
}
?>
