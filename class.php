<?php

require_once("Log.php");

class Db
{
    var $dsn;
    protected $pdo;

    function __construct(){
        date_default_timezone_set('Asia/Tokyo');
        $this->dsn = 'sqlite:db';
        $this->pdo = new PDO($this->dsn);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }

    function dropDB(){
        try{
            $this->pdo->query("DROP TABLE casts");
            $this->pdo->query("DROP TABLE members");
            $this->pdo->query("DROP TABLE matches");
            $this->pdo->query("VACUUM");
        }catch(PDOException $e){
            $this->log($e->getMessage());
        }
    }

    function setupDB(){
        try{
            $this->pdo->query("CREATE TABLE casts(name)");
            $this->pdo->query("CREATE TABLE members(mail)");
            $this->pdo->query("CREATE TABLE matches(name,mail)");
        }catch(PDOException $e){
            $this->log($e->getMessage());
        }
    }

    function addCast($name){
        try{
            $q = "SELECT COUNT(*) FROM casts WHERE name=?";
            $stmt = $this->pdo->prepare($q);
            $stmt->execute([$name]);
            $count = $stmt->fetch(PDO::FETCH_NUM);
            $this->log($q);
            $this->log($count);
            if($count[0] != 0){
                return 0;
            }

            $q = "INSERT INTO casts(name) VALUES(?)";
            $stmt = $this->pdo->prepare($q);
            $stmt->execute([$name]);
            $this->log($q);
            $this->log($stmt->rowCount());
        }catch(PDOException $e){
            $this->log($e->getMessage());
        }
        return $stmt->rowCount();
    }

    function removeCast($name){
        try{
            $q = "DELETE FROM casts WHERE name=?";
            $stmt = $this->pdo->prepare($q);
            $stmt->execute([$name]);
            $q = "DELETE FROM matches WHERE name=?";
            $stmt = $this->pdo->prepare($q);
            $stmt->execute([$name]);
        }catch(PDOException $e){
            $this->log($e->getMessage());
        }
        $this->log($q);
        $this->log($stmt->rowCount());
        return $stmt->rowCount();
    }

    function addMember($mail){
        try{
            $q = "SELECT COUNT(*) FROM members WHERE mail=?";
            $stmt = $this->pdo->prepare($q);
            $stmt->execute([$mail]);
            $count = $stmt->fetch(PDO::FETCH_NUM);
            $this->log($q);
            $this->log($count);
            if($count[0] != 0){
                return 0;
            }

            $q = "INSERT INTO members VALUES(?)";
            $stmt = $this->pdo->prepare($q);
            $stmt->execute([$mail]);
        }catch(PDOException $e){
            $this->log($e->getMessage());
        }
        $this->log($q);
        $this->log($stmt->rowCount());
        return $stmt->rowCount();
    }

    function removeMember($mail){
        try{
            $q = "DELETE FROM matches WHERE mail=?";
            $stmt = $this->pdo->prepare($q);
            $stmt->execute([$mail]);
            $q = "DELETE FROM members WHERE mail=?";
            $stmt = $this->pdo->prepare($q);
            $stmt->execute([$mail]);
        }catch(PDOException $e){
            $this->log($e->getMessage());
        }
        $this->log($q);
        $this->log($stmt->rowCount());
        return $stmt->rowCount();
    }

    function addMatch($name,$mail){
        try{
            $q = "SELECT COUNT(*) FROM matches WHERE name=? AND mail=?";
            $stmt = $this->pdo->prepare($q);
            $stmt->execute([$name,$mail]);
            $count = $stmt->fetch(PDO::FETCH_NUM);
            $this->log($q);
            $this->log($count);
            if($count[0] != 0){
                return 0;
            }

            $q = "INSERT INTO matches VALUES(?,?)";
            $stmt = $this->pdo->prepare($q);
            $stmt->execute([$name,$mail]);
        }catch(PDOException $e){
            $this->log($e->getMessage());
        }
        $this->log($q);
        $this->log($stmt->rowCount());
        return $stmt->rowCount();
    }

    function removeMatch($name,$mail){
        try{
            $q = "DELETE FROM matches WHERE name=? AND mail=?";
            $stmt = $this->pdo->prepare($q);
            $stmt->execute([$name,$mail]);
        }catch(PDOException $e){
            $this->log($e->getMessage());
        }
        $this->log($q);
        $count = $stmt->rowCount();
        return $count;
    }

    function listMembers(){
        $members = [];
        try{
            $q = "SELECT mail FROM members ORDER BY mail";
            $this->log($q);
            $stmt = $this->pdo->prepare($q);
            $stmt->execute();
            while($member = $stmt->fetch(PDO::FETCH_NUM)){
                array_push($members,$member[0]);
            }
        }catch(PDOException $e){
            $this->log($e->getMessage());
        }
        $this->log($members);
        return $members;
    }

    function listCasts(){
        $casts = [];
        try{
            $stmt = $this->pdo->prepare("SELECT name FROM casts ORDER BY name");
            $stmt->execute();
            while($cast = $stmt->fetch(PDO::FETCH_NUM)){
                array_push($casts,$cast[0]);
            }
        }catch(PDOException $e){
            $this->log($e->getMessage());
        }
        $this->log($casts);
        return $casts;
    }

    /*
     * 一覧の配列を指定すると一致するメンバ一覧を出力
     */
    function listMembersByCastName($casts){
        $members = [];

        $cast_str = "";
        foreach($casts as $cast){
            //$cast_str = $cast.",".$cast_str;
            $cast_str = "'".$cast."',".$cast_str;
        }
        $cast_str = substr($cast_str,0,-1); // 最後の1文字を除去

        try{
            $q = "SELECT DISTINCT mail FROM matches WHERE name IN(".$cast_str.")";
            //$q = "SELECT * FROM matches";
            $this->log($q);
            $stmt = $this->pdo->query($q);
            while($member = $stmt->fetch(PDO::FETCH_NUM)){
                $this->log($member);
                array_push($members,$member[0]);
            }
        }catch(PDOException $e){
            $this->log($e->getMessage());
        }
        $this->log($members);
        return $members;
    }

    /*
     * 一覧の配列を指定すると一致するメンバ一覧を出力
     */
    function listCastsByMember(array $members){
        $this->log("listCastsByMember");
        $this->log($members);
        $casts = [];

        $member_str = "";
        foreach($members as $member){
            $member_str = "'".$member."',".$member_str;
        }
        $member_str = substr($member_str,0,-1); // 最後の1文字を除去

        try{
            $q = "SELECT name FROM matches WHERE mail IN(".$member_str.")";
            $this->log($q);
            $stmt = $this->pdo->query($q);
            while($cast = $stmt->fetch(PDO::FETCH_NUM)){
                $this->log($cast);
                array_push($casts,$cast[0]);
            }
        }catch(PDOException $e){
            $this->log($e->getMessage());
        }
        $this->log($casts);
        return $casts;
    }

    function listMatches(){
        $matches = [];
        try{
            $stmt = $this->pdo->prepare("SELECT * FROM matches");
            $stmt->execute();
            while($match = $stmt->fetch(PDO::FETCH_ASSOC)){
                array_push($matches,$match);
            }
        }catch(PDOException $e){
            $this->log($e->getMessage());
        }
        return $matches;
    }

    function log($message)
    {
        $logconf = ['mode'=>0777,'timeFormat'=>'%Y/%m/%d %H:%M:%S'];
        $log = Log::singleton('file', 'log/app.log', '', $logconf);
        $log->debug($message);
    }

}