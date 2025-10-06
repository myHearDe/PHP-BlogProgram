<?php
/**
 * PHP专业博客程序 - 数据库连接文件
 */

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    /**
     * 构造函数
     */
    public function __construct() {
        $this->host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
    }

    /**
     * 建立数据库连接
     * @return PDO|false 数据库连接对象或false
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        } catch(PDOException $exception) {
            echo "数据库连接错误: " . $exception->getMessage();
        }

        return $this->conn;
    }

    /**
     * 辅助方法：绑定参数并指定类型
     * @param PDOStatement $stmt PDO语句对象
     * @param array $params 查询参数
     */
    private function bindParams($stmt, $params) {
        foreach ($params as $key => $value) {
            $paramType = PDO::PARAM_STR;
            if (is_int($value)) {
                $paramType = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $paramType = PDO::PARAM_BOOL;
            } elseif (is_null($value)) {
                $paramType = PDO::PARAM_NULL;
            }
            
            // 检查参数是索引数组还是关联数组
            if (is_numeric($key)) {
                // 索引数组（从1开始）
                $stmt->bindValue($key + 1, $value, $paramType);
            } else {
                // 关联数组
                $stmt->bindValue($key, $value, $paramType);
            }
        }
    }

    /**
     * 执行查询并返回所有结果
     * @param string $query SQL查询语句
     * @param array $params 查询参数
     * @return array 查询结果
     */
    public function query($query, $params = array()) {
        $stmt = $this->conn->prepare($query);
        $this->bindParams($stmt, $params);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 执行查询并返回单条结果
     * @param string $query SQL查询语句
     * @param array $params 查询参数
     * @return array|null 查询结果或null
     */
    public function queryOne($query, $params = array()) {
        $stmt = $this->conn->prepare($query);
        $this->bindParams($stmt, $params);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result === false ? null : $result;
    }

    /**
     * 执行插入操作
     * @param string $query SQL插入语句
     * @param array $params 查询参数
     * @return int 插入的ID
     */
    public function insert($query, $params = array()) {
        $stmt = $this->conn->prepare($query);
        $this->bindParams($stmt, $params);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    /**
     * 执行更新操作
     * @param string $query SQL更新语句
     * @param array $params 查询参数
     * @return int 受影响的行数
     */
    public function update($query, $params = array()) {
        $stmt = $this->conn->prepare($query);
        $this->bindParams($stmt, $params);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * 执行删除操作
     * @param string $query SQL删除语句
     * @param array $params 查询参数
     * @return int 受影响的行数
     */
    public function delete($query, $params = array()) {
        $stmt = $this->conn->prepare($query);
        $this->bindParams($stmt, $params);
        $stmt->execute();
        return $stmt->rowCount();
    }
}