<?php


class DB
{
    public PDO|null $pdo;

    public function __construct(
        $connectName = '',
        $charset = 'utf8'

    )
    {
        global $cfg;

        if(empty($connectName)){
            $connectName = 0;
        }
        $con = (object) $cfg->connects[$_SERVER['SERVER_NAME']][$connectName];

        $dsn = "mysql:host=$con->Host;dbname=$con->Name;charset=$charset";
        $this->opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => FALSE
        ];

        try {
            $this->pdo = new PDO($dsn, $con->User, $con->Pass, $this->opt);
        } catch (PDOException $ex) {
            die('dbError');
        }

    }

    public function qwe($sql, $args = NULL): bool|PDOStatement
    {
        if (!$args) {
           return self::query($sql);
        }
        return self::execute($sql,$args);
    }

    private function execute(string $sql, array $args): bool|PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($args);

        } catch (PDOException $ex) {

            $log_text = self::prepLog($ex->getTraceAsString(), $sql , $ex->getMessage());
            self::writelog('sql_error', $log_text);
            return false;
        }
        return $stmt ?? false;
    }

    private function query($sql): bool|PDOStatement
    {
        try {
           $result = $this->pdo->query($sql);

        } catch (PDOException $ex) {

            $log_text = self::prepLog($ex->getTraceAsString(), $sql , $ex->getMessage());
            self::writelog('sql_error', $log_text);
            return false;

        }
        return $result ?? false;
    }

    public function pHolders(array $list): string
    {
        return rtrim(str_repeat('?, ', count($list)), ', ') ;
    }

    private function prepLog(string $trace, string $sql, string $error) : string
    {
        return date("Y-m-d H:i:s")."\t".$error."\t".$trace."\r\n".$sql."\r\n";
    }

    private function writelog($typelog, $log_text)
    {
        $log = fopen($_SERVER['DOCUMENT_ROOT'].'/../logs/'.$typelog.'.txt','a+');
        fwrite($log, "$log_text\r\n");
        fclose($log);
    }


    public function __destruct() {
        $pdo = null;
    }
}