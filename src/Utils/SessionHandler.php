<?php
namespace Fauza\Template\Utils;

/**
* Project: Session Handler
* Description: Basic session handler tool. It is strongly recommended to use this script if you work with sessions.
* @author: Mellowize Back-End Services, (edited by Palms member)
* @date: 07.10.2024
* @version: 1.0
*/
class SessionHandler
{
    static function initSession(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Save one data in the actual session.
     *
     * @param string $dataName The name of the data.
     * @param mixed $dataContent The content of the data.
     */
    static function SaveInSession(string $dataName, $dataContent): void
    {
        $_SESSION[$dataName] = $dataContent;
    }

    /**
     * Delete one or more datas from the actual session.
     *
     * @param string ...$dataNames The name(s) of the data(s) to remove.
     */
    static function DeleteFromSession(string ...$dataNames): void
    {
        foreach ($dataNames as $dataName) {
            unset($_SESSION[$dataName]);
        }
    }

    /**
     * Get one data in the actual session.
     *
     * @param mixed $dataName The name of the data to get.
     *
     * @return mixed The value of the data which was in parameter.
     */
    static function GetDataFromSession($dataName): mixed
    {
        if (array_key_exists($dataName, $_SESSION)) {
            return json_encode([$dataName => $_SESSION[$dataName]], JSON_PRETTY_PRINT);
        } else {
            return null;
        }
    }

    /**
     * Get all the datas in the actual session.
     *
     * @return array The array that contains all of the datas in the session.
     */
    static function GetAllSessionData(): array
    {
        $datas = [];
        foreach ($_SESSION as $dataName => $dataValue) {
            $datas[$dataName] = $dataValue;
        }
        return $datas;
    }

    /**
     * Get a raw session value (unlike GetDataFromSession which returns a JSON string).
     *
     * @param string $dataName The name of the data to get.
     * @return mixed The stored value, or null if not set.
     */
    static function GetRawDataFromSession(string $dataName): mixed
    {
        return $_SESSION[$dataName] ?? null;
    }

    /**
     * Fully destroy the active session: clear $_SESSION, expire the session
     * cookie, and call session_destroy().
     */
    static function DestroySession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            return;
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $p['path'],
                $p['domain'],
                $p['secure'],
                $p['httponly']
            );
        }
        session_destroy();
    }

    /**
     * Regenerate the session id. Call after a successful login to prevent
     * session fixation.
     */
    static function RegenerateId(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Whether a user is currently authenticated in the session.
     */
    static function IsLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Lazily mint a per-session CSRF token. Same token is returned for the
     * lifetime of the session.
     */
    static function CsrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    /**
     * Constant-time check of a submitted CSRF token against the session token.
     */
    static function VerifyCsrf(?string $token): bool
    {
        return is_string($token)
            && !empty($_SESSION['_csrf'])
            && hash_equals($_SESSION['_csrf'], $token);
    }
}