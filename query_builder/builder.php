<?php

abstract class AbstractQueryBuilder {
    abstract function get_query();
}

abstract class AbstractQueryDirector {
    abstract function __construct( AbstractQueryBuilder $builder );
    abstract function build_query();
    abstract function get_query();
}

class SelectQuery {
    private $query = NULL;
    private $anchor_table = 'packets';
    private $join_tables = [];
    private $params = [];
    private $limit = NULL;
    private $order = NULL;
    private $columns = NULL;
    
    function __construct() {
    }
    
    function set_tables( $tables = NULL ) {
        if( $tables === NULL )
            {
            //WCD - handle error
            }
    }
    
    function set_params( $params = NULL ) {
    
    }
    
    function set_limit( $limit = NULL ) {
    
    }
    
    function set_order( $order ) {
    
    }
    
    function set_columns( $columns ) {
        if( $columns === NULL )
            {
            $this->columns = '*';
            }
    }
    
    function format_query() {
        $this->query = '"""SELECT ' . $this->columns . ' ';
        $this->query .= 'FROM ' . $this->anchor_table;
        $this->query .= '"""';
    }
    
    public function __toString() {
        return $this->query;
    }
}

class SelectQueryBuilder extends AbstractQueryBuilder {
    private $query = NULL;
    
    function __construct() {
        $this->query = new SelectQuery();
    }
    
    function set_tables( $tables = NULL ) {
        $this->query->set_tables( $tables );
    }
    
    function set_params( $params = NULL ) {
        $this->query->set_params( $params );
    }
    
    function set_limit( $limit = NULL ) {
        $this->query->set_limit( $limit );
    }
    
    function set_order( $order = NULL ) {
        $this->query->set_order( $order );
    }
    
    function set_columns( $columns = NULL ) {
        $this->query->set_columns( $columns );
    }
    
    function format_query() {
        $this->query->format_query();
    }
    
    function get_query() {
        return $this->query;
    }
}

class SelectQueryDirector extends AbstractQueryDirector {
    private $builder = NULL;
    
    public function __construct( AbstractQueryBuilder $builder ) {
        $this->builder = $builder;
    }
    
    public function build_query( /* json structure from GET query */) {
        //set_tables()
        //etc.
        $this->builder->set_columns();
        $this->builder->format_query();
    }
    
    public function get_query() {
        return $this->builder->get_query();
    }

}

/* Test Execution */
$query_builder = new SelectQueryBuilder();
$query_director = new SelectQueryDirector( $query_builder );
$query_director->build_query( /* json structure */ );
$query = $query_director->get_query();
echo $query;






?>