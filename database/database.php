<?php

include_once "connect.php";

class Database
{
    private $conn;

    public function __construct()
    {
        $this->conn = Connect::getConnection();
    }

    public function create($table, $array)
    {
        try {
            if (!$this->tableExists($table)) {
                throw new Exception("Tabela $table não existe!");
            }

            //INSERT INTO table (field 1 , ..., field n) values (?,  ... , ?)
            $fields = array_keys($array);
            $values = array_values($array);

            $placeholder = "";
            $valuesPlaceholders = "";

            $placeholder = str_repeat("?, ", count($fields) - 1) . "?";
            $valuesPlaceholders = implode(", ", $fields);

            $sql = "INSERT INTO $table ($valuesPlaceholders) VALUES ($placeholder)";

            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute($values);

            if ($result) {
                return [
                    'success' => true,
                    'id' => $this->conn->lastInsertId(),
                    'message' => 'Registro criado com sucesso'
                ];
            } else {
                return [
                    'success' => true,
                    'message' => 'Erro ao criar o registro'
                ];
            }

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no banco: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

    }

    //Nao fazia ideia de fazer um read pra Join igual os outros entao deixei simples assim mesmo
    public function readCustom($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'count' => $stmt->rowCount()
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no banco: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }


    public function read($table, $condition = [], $limit = null, $offset = null)
    {
        try {
            if (!$this->tableExists($table)) {
                throw new Exception("Tabela $table não existe!");
            }

            //SELECT * from $table
            // WHERE $field1 = ? AND ... AND $fieldN = ?
            // LIMIT = $limit OFFSET = $offset
            $sql = "SELECT * from $table";
            $param = [];
            if (!empty($condition)) {
                $where_condition = [];
                foreach ($condition as $field => $value) {
                    $where_condition[] = "$field = ?";
                    $param[] = $value;
                }
                $sql .= " WHERE " . implode(" AND ", $where_condition);
            }

            if ($limit !== null) {
                $sql .= " LIMIT $limit";
                if ($offset !== null) {
                    $sql .= " OFFSET $offset";
                }
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($param);
            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'count' => $stmt->rowCount()
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no banco: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function update($table, $data, $condition = [])
    {
        try {
            if (!$this->tableExists($table)) {
                throw new Exception("Tabela $table não existe!");
            }

            if (!$data) {
                throw new Exception("Sem dados");
            }

            //UPDATE $table SET $data1 = ?, ... , $dataN = ?
            // WHERE $condition1 = ? AND ... AND $conditionN = ?

            $sql = "UPDATE $table SET ";
            $setFields = [];
            $params = [];

            foreach ($data as $key => $value) {
                $setFields[] = "$key = ?";
                $params[] = $value;
            }

            $sql .= implode(", ", $setFields);

            if (!empty($condition)) {
                $whereCondition = [];
                $whereParams = [];
                foreach ($condition as $key => $value) {
                    $whereCondition[] = "$key = ?";
                    $whereParams[] = $value;
                }

                $sql .= " WHERE " . implode(" AND ", $whereCondition);
                $params = array_merge($params, $whereParams);

            }
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute($params);
            return [
                "success" => true,
                "affected_rows" => $stmt->rowCount(),
                "message" => $stmt->rowCount() > 0 ? 'Registro(s) alterado(s) com sucesso!' : 'Nenhum registro alterado'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no banco: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function delete($table, $conditions = [])
    {
        try {
            if (!$this->tableExists($table)) {
                throw new Exception("Tabela $table não existe!");
            }

            $sql = "DELETE FROM $table";

            $param = [];

            if (!empty($conditions)) {
                $where_conditions = [];
                foreach ($conditions as $field => $value) {
                    $where_conditions[] = "$field = ?";
                    $param[] = $value;
                }

                $sql .= " WHERE " . implode(" AND ", $where_conditions);
            }

            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute($param);

            return [
                'success' => true,
                'affected_rows' => $stmt->rowCount(),
                'message' => $stmt->rowCount() > 0 ? 'Registro(s) deletados(s) com sucesso!' : 'Nenhum registro foi deletado'
            ];

        } catch (PDOException $e) {
            return [
                "success" => false,
                'message' => 'Erro no banco: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function deleteCustom($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return [
                'success' => true,
                'affected_rows' => $stmt->rowCount(),
                'message' => $stmt->rowCount() > 0 ? 'Registro(s) deletados(s) com sucesso!' : 'Nenhum registro foi deletado'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no banco: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function tableExists($table)
    {
        try {
            $stmt = $this->conn->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}

?>