<?php

class Relationship{

    public $id;
    public $idColumnName;
    public $relTableName;
    public $tableName;

    public function __construct($idColumnName, $relTableName, $tableName){
        $this->idColumnName = $idColumnName;
        $this->relTableName = $relTableName;
        $this->tableName = $tableName;
    }

}