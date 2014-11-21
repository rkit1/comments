<?php
namespace Comments;
class Session {
    /**
     * @var String
     */
    private $sid;
    /**
     * @var MyPDO
     */
    private $db;
    /**
     * @var Int
     */
    public $user;
    /**
     * @var Int
     */
    private $ttl;
    private $IsAdmin = null;

    function __construct() {}

    /**
     * @param $user Int
     * @param $remember Bool
     * @param $db \PDO
     * @return Session
     */
    public static function NewSession($user, $remember, $db) {
        $t = new Session();
        $t->user = $user;
        $t->db = $db;
        // 30 days or 1 hour
        if($remember) $t->ttl = 2592000; else $t->ttl = 3600;
        // session
        $st = $db->prepare('SELECT idSessions FROM Sessions WHERE idSessions = ?');
        do {
            $t->sid = uniqid();
            $st->execute(array($t->sid));
        } while ($st->rowCount() == 1);

        $st = $db->prepare('INSERT INTO Sessions (idSessions, User, TimeToLive, LastActivity) VALUES (?,?,?, NOW())');
        $st->execute(array($t->sid, $t->user, $t->ttl));

        //$db->exec('CALL Cleanup_Sessions');

        $t->TouchCookie();
        return $t;
    }

    /**
     * @return Bool
     */
    public function IsConfirmed(){
        $r = $this->db->q('SELECT True FROM Users WHERE idUsers = ? AND ConfirmKey IS NULL', array($this->user));
        return $r->rowCount() == 1;
    }

    /**
     * @return Bool
     */
    public function IsAdmin(){
        if (is_null($this->IsAdmin)){
            $res = $this->db->q('SELECT RoleName FROM Users JOIN Roles ON Role = idRoles
                                 WHERE idUsers = ?'
                               , array($this->user)) -> fetch();
            $this->IsAdmin = $res && $res[0] == 'Admin';
        }
        return $this->IsAdmin;
    }

    /**
     * @param $db \PDO
     * @param $touch Bool
     * @return Session
     */
    public static function CheckSession($db, $touch = true){
        if(!isset($_COOKIE['commentsUser'])) return null;
        $st = $db->prepare('SELECT User, TimeToLive FROM Sessions
            WHERE ADDTIME(LastActivity, SEC_TO_TIME(TimeToLive)) >= NOW()
            AND idSessions = ?');
        $st->execute(array($_COOKIE['commentsUser']));
        if (!$res = $st->fetch()) {
            setcookie('commentsUser', '', 0, '/');
            return null;
        }

        $t = new Session();
        $t->sid = $_COOKIE['commentsUser'];
        $t->user = $res[0];
        $t->ttl = $res[1];
        $t->db = $db;
        if ($touch) $t->Touch();
        return $t;
    }

    function TouchCookie(){
        setcookie('commentsUser', $this->sid, time() + $this->ttl, '/');
    }

    public function Touch(){
        $st = $this->db->prepare('UPDATE Sessions SET LastActivity = NOW() WHERE idSessions = ?');
        $st->execute(array($this->sid));
        $this->touchCookie();
    }

    public function Logout(){
        $this->db->q('DELETE FROM Sessions WHERE idSessions = ?', array($this->sid));
    }
}