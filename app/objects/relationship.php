<?php

class Relationship{

    // Properties of class
    public $id;
    public $idColumnName;
    public $relTableName;
    public $tableName;

    // Constructor
    public function __construct($idColumnName, $relTableName, $tableName){
        $this->idColumnName = $idColumnName;
        $this->relTableName = $relTableName;
        $this->tableName = $tableName;
    }

}