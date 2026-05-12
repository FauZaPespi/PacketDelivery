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


}