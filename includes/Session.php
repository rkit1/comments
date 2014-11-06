<?php
class Session {
    /**
     * @var String
     */
    private $sid;
    /**
     * @var PDO
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

    function __construct() {}

    /**
     * @param $user Int
     * @param $remember Bool
     * @param $db PDO
     * @return Session
     */
    public static function NewSession($user, $remember, $db) {
        $t = new Session();
        $t->user = $user;
        // 30 days or 1 hour
        if($remember) $t->ttl = 2592000; else $t->ttl = 3600;

        // session
        $st = $db->prepare('SELECT TRUE FROM Sessions WHERE idSessions = ?');
        do {
            $t->sid = uniqid();
            $st->execute(array($t->sid));
        } while ($st->columnCount() == 1);

        $st = $db->prepare('INSERT INTO Sessions (idSessions, User, TimeToLive) VALUES (?,?,?)');
        $st->execute(array($t->sid, $t->user, $t->ttl));

        $db->exec('CALL Cleanup_Sessions');

        $t->touchCookie();
        return $t;
    }

    function touchCookie(){
        setcookie('commentsUser', $this->sid, time() + $this->ttl, '/');
    }

    /**
     * @param $db PDO
     * @return Session
     */
    public static function CheckSession($db){
        if(!isset($_COOKIE['commentsUser'])) return null;
        $st = $db->prepare('SELECT User, TimeToLive FROM Sessions
            WHERE ADDTIME(LastActivity, SEC_TO_TIME(TimeToLive)) >= NOW()
            AND idSessions = ?');
        $st->execute($_COOKIE['commentsUser']);
        if (!$res = $st->fetch()) return null;

        $t = new Session();
        $t->sid = $_COOKIE['commentsUser'];
        $t->user = $res[0];
        $t->ttl = $res[1];
        $t->db = $db;
        return $t;
    }

    public function Touch(){
        $st = $this->db->prepare('UPDATE Sessions SET LastActivity = NOW() WHERE idSessions = ?');
        $st->execute($this->sid);
        $this->touchCookie();
    }

    public function Logout(){
        $st = $this->db->prepare('DELETE FROM Sessions WHERE idSessions = ?');
        $st->execute($this->sid);
    }
}