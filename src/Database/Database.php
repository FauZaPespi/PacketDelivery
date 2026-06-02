<?php
namespace Fauza\Template\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
class Database
{
    private \PDO $pdo;

    function __construct()
    {
        $host = getenv('MARIADB_HOST') ?: 'localhost';
        $port = getenv('MARIADB_PORT') ?: '3306';
        $dbname = getenv('MARIADB_DATABASE') ?: 'packet_delivery';
        $user = getenv('MARIADB_USER') ?: 'root';
        $password = getenv('MARIADB_PASSWORD') ?: '';

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        try {
            $this->pdo = new \PDO($dsn, $user, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    private static ?Database $instance = null;

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): \PDO
    {
        return $this->pdo;
    }


    public function getAllEmployes(): array
    {
        $stmt = $this->getConnection()->query("SELECT * FROM Employe");
        $employes = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $employes[] = new \Fauza\Template\Models\Employe(
                (int)$row['id_employe'],
                $row['nom'],
                $row['prenom'],
                $row['email'],
                $row['motDePasse'],
                (bool)$row['estLivreur']
            );
        }
        return $employes;
    }

    public function getEmployeByEmail(string $email): ?\Fauza\Template\Models\Employe
    {
        $stmt = $this->getConnection()->prepare("SELECT * FROM Employe WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            return new \Fauza\Template\Models\Employe(
                (int)$row['id_employe'],
                $row['nom'],
                $row['prenom'],
                $row['email'],
                $row['motDePasse'],
                (bool)$row['estLivreur']
            );
        }
        return null;
    }

    public function loginCheck(string $email, string $password): ?\Fauza\Template\Models\Employe
    {
        $employe = $this->getEmployeByEmail($email);
        if ($employe && password_verify($password, $employe->password)) {
            return $employe;
        }
        return null;
    }

    // ========== PAQUETS ==========

    public function getAllPaquets(): array
    {
        $stmt = $this->getConnection()->query("
            SELECT * FROM Paquet
            ORDER BY dateLivraison DESC, numeroPostal ASC
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function searchPaquets(string $query): array
    {
        $stmt = $this->getConnection()->prepare("
            SELECT * FROM Paquet
            WHERE numeroPostal LIKE :query
            ORDER BY dateLivraison DESC, numeroPostal ASC
        ");
        $stmt->execute(['query' => '%' . $query . '%']);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPaquetById(string $id): ?array
    {
        $stmt = $this->getConnection()->prepare("SELECT * FROM Paquet WHERE numeroPostal = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public function createPaquet(array $data): bool
    {
        $stmt = $this->getConnection()->prepare("
            INSERT INTO Paquet (
                numeroPostal, nomDestinataire, prenomDestinataire,
                adresseDestinataire, latitudeAdresse, longitudeAdresse,
                dateLivraison, id_employe_createur, id_employe_livreur, statutLivraison
            ) VALUES (
                :numeroPostal, :nomDestinataire, :prenomDestinataire,
                :adresseDestinataire, :latitudeAdresse, :longitudeAdresse,
                :dateLivraison, :id_employe_createur, :id_employe_livreur, 'Pas encore livré'
            )
        ");
        return $stmt->execute($data);
    }

    public function updatePaquet(string $id, array $data): bool
    {
        $data['numeroPostal'] = $id;
        $stmt = $this->getConnection()->prepare("
            UPDATE Paquet SET
                numeroPostal = :numeroPostal,
                nomDestinataire = :nomDestinataire,
                prenomDestinataire = :prenomDestinataire,
                adresseDestinataire = :adresseDestinataire,
                latitudeAdresse = :latitudeAdresse,
                longitudeAdresse = :longitudeAdresse,
                dateLivraison = :dateLivraison,
                id_employe_livreur = :id_employe_livreur
            WHERE numeroPostal = :numeroPostal
        ");
        return $stmt->execute($data);
    }

    public function deletePaquet(string $id): bool
    {
        $stmt = $this->getConnection()->prepare("DELETE FROM Paquet WHERE numeroPostal = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function canDeletePaquet(string $id): bool
    {
        $paquet = $this->getPaquetById($id);
        if (!$paquet) {
            return false;
        }
        // Impossible de supprimer si attribué à un livreur et date du jour
        if ($paquet['id_employe_livreur'] && $paquet['dateLivraison'] == date('Y-m-d')) {
            return false;
        }
        return true;
    }

    public function canModifyPaquet(string $id): bool
    {
        $paquet = $this->getPaquetById($id);
        if (!$paquet) {
            return false;
        }
        // Impossible de modifier si déjà livré
        if ($paquet['statutLivraison'] === 'Livré') {
            return false;
        }
        return true;
    }

    public function marquerLivraison(string $idPaquet, int $idLivreur): bool
    {
        // Vérifier d'abord si le paquet peut être marqué comme livré
        $paquet = $this->getPaquetById($idPaquet);
        if (!$paquet || $paquet['statutLivraison'] === 'Livré') {
            return false;
        }

        // Si le paquet n'a pas encore de route, en créer une
        if (!$paquet['id_route'] && $paquet['dateLivraison'] == date('Y-m-d')) {
            $this->creerRoutePourLivreur($idLivreur, $paquet['dateLivraison']);
            // Récupérer la route créée
            $route = $this->getConnection()->prepare("
                SELECT id_route FROM RouteLivraison
                WHERE id_employe_livreur = :livreur_id AND dateRoute = :date
            ");
            $route->execute(['livreur_id' => $idLivreur, 'date' => $paquet['dateLivraison']]);
            $idRoute = $route->fetchColumn();
            $idRoute = $idRoute ? (int)$idRoute : null;
        } else {
            $idRoute = $paquet['id_route'] ?? null;
        }

        // Mettre à jour le paquet comme livré
        $stmt = $this->getConnection()->prepare("
            UPDATE Paquet SET
                statutLivraison = 'Livré',
                date_heure_livraison = NOW(),
                id_route = :id_route
            WHERE numeroPostal = :id_paquet
        ");
        return $stmt->execute([
            'id_paquet' => $idPaquet,
            'id_route' => $idRoute
        ]);
    }

    private function creerRoutePourLivreur(int $livreurId, string $date): void
    {
        // Vérifier si une route existe déjà pour ce livreur et cette date
        $existing = $this->getConnection()->prepare("
            SELECT id_route FROM RouteLivraison
            WHERE id_employe_livreur = :livreur_id AND dateRoute = :date
        ");
        $existing->execute(['livreur_id' => $livreurId, 'date' => $date]);
        if ($existing->fetchColumn()) {
            return;
        }

        // Créer la route
        $stmt = $this->getConnection()->prepare("
            INSERT INTO RouteLivraison (id_employe_livreur, dateRoute)
            VALUES (:livreur_id, :date)
        ");
        $stmt->execute(['livreur_id' => $livreurId, 'date' => $date]);
    }

    public function marquerEnCoursDeLivraison(string $idPaquet): bool
    {
        $stmt = $this->getConnection()->prepare("
            UPDATE Paquet SET statutLivraison = 'En cours de livraison' WHERE numeroPostal = :id
        ");
        return $stmt->execute(['id' => $idPaquet]);
    }

    public function updatePackageOrder(string $idPaquet, int $ordre, int $livreurId, string $date): bool
    {
        $this->creerRoutePourLivreur($livreurId, $date);

        $routeStmt = $this->getConnection()->prepare("
            SELECT id_route FROM RouteLivraison
            WHERE id_employe_livreur = :livreur_id AND dateRoute = :date
        ");
        $routeStmt->execute(['livreur_id' => $livreurId, 'date' => $date]);
        $idRoute = (int)$routeStmt->fetchColumn();

        $stmt = $this->getConnection()->prepare("
            UPDATE Paquet SET ordreRouteLivraison = :ordre, id_route = :id_route
            WHERE numeroPostal = :id
              AND id_employe_livreur = :livreur_id
              AND dateLivraison = :date
        ");
        $ok = $stmt->execute([
            'ordre'      => $ordre,
            'id_route'   => $idRoute,
            'id'         => $idPaquet,
            'livreur_id' => $livreurId,
            'date'       => $date,
        ]);

        if (!$ok) return false;

        // Si tous les colis du livreur pour cette date ont un ordre → "En cours de livraison"
        $pending = $this->getConnection()->prepare("
            SELECT COUNT(*) FROM Paquet
            WHERE id_employe_livreur = :livreur_id
              AND dateLivraison = :date
              AND ordreRouteLivraison IS NULL
        ");
        $pending->execute(['livreur_id' => $livreurId, 'date' => $date]);

        if ((int)$pending->fetchColumn() === 0) {
            foreach ($this->getPaquetsByLivreur($livreurId, $date) as $p) {
                if ($p['statutLivraison'] === 'Pas encore livré') {
                    $this->marquerEnCoursDeLivraison($p['numeroPostal']);
                }
            }
        }

        return true;
    }

    public function getPaquetsEnCoursOuNonLivres(string $date): array
    {
        $stmt = $this->getConnection()->prepare("
            SELECT * FROM Paquet
            WHERE dateLivraison = :date
              AND statutLivraison IN ('Pas encore livré', 'En cours de livraison')
            ORDER BY numeroPostal ASC
        ");
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ========== LIVREURS ==========

    public function getAllLivreurs(): array
    {
        $stmt = $this->getConnection()->prepare("
            SELECT * FROM Employe
            WHERE estLivreur = 1
            ORDER BY nom ASC, prenom ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function searchLivreurs(string $query): array
    {
        $stmt = $this->getConnection()->prepare("
            SELECT * FROM Employe
            WHERE estLivreur = 1 AND (nom LIKE :query OR prenom LIKE :query)
            ORDER BY nom ASC, prenom ASC
        ");
        $stmt->execute(['query' => '%' . $query . '%']);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPaquetsByLivreur(int $livreurId, string $date): array
    {
        $stmt = $this->getConnection()->prepare("
            SELECT * FROM Paquet
            WHERE id_employe_livreur = :livreur_id
              AND dateLivraison = :date
        ");
        $stmt->execute(['livreur_id' => $livreurId, 'date' => $date]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getLivreursDisponibles(string $date): array
    {
        $stmt = $this->getConnection()->prepare("
            SELECT e.*,
                   COUNT(p.id_paquet) as nb_paquets
            FROM Employe e
            LEFT JOIN Paquet p ON e.id_employe = p.id_employe_livreur
                AND p.dateLivraison = :date
            WHERE e.estLivreur = 1
            GROUP BY e.id_employe
            HAVING nb_paquets < 10
            ORDER BY e.nom ASC, e.prenom ASC
        ");
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getEmployeById(int $id): ?array
    {
        $stmt = $this->getConnection()->prepare("SELECT * FROM Employe WHERE id_employe = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getNextNumeroPostal(): string
    {
        $stmt = $this->getConnection()->query("
            SELECT MAX(CAST(SUBSTRING(numeroPostal, 5) AS UNSIGNED)) as last_numero
            FROM Paquet
            WHERE numeroPostal LIKE 'PKG-%'
        ");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $nextNum = ($row['last_numero'] ?? 1200) + 1;
        return 'PKG-' . $nextNum . '-CH';
    }
}