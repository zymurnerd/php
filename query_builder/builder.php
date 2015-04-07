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
    private $params = NULL;
    private $limit = NULL;
    private $order = NULL;
    private $columns = NULL;

    function __construct() {
    }

    function set_join_tables( $tables = NULL ) {
        if( !isset( $tables ) )
            {
            return NULL;
            }
        if( !is_array( $tables ) )
        {
            $tables = explode( " ", $tables );
        }
        $this->join_tables = array_values( array_diff( $tables, [ "packets" ] ) );
    }

    function set_params( $params = NULL ) {
        if( empty( $params ) ) {
            $this->params = NULL;
            return;
        }
        elseif( !is_array( $params ) )
        {
            $this->params = explode( " ", $params );
        }
        else {
            $this->params = $params;
        }
        foreach( $this->params as $key => $param ) {
            $pattern = '~(?P<key>[^\d\s]+)=(?P<value>[^\']+)~';
            preg_match($pattern, $param, $matches);
            if( isset( $matches['key'] ) && isset( $matches['value'] ) ) {
                $this->params[$key] = trim($matches['key'] ) . '=\'' . trim($matches['value'] ) . '\'';
            }
            
        }
    }

    function set_limit( $limit = NULL ) {

    }

    function set_order( $order ) {

    }

    function set_columns( $columns = NULL) {
        // if an empty string or null is provided get all columns
        if( empty($columns))
            {
            $this->columns = '*';
            }
        // if an array of columns is provided, link them with a comma
        elseif( is_array( $columns ) ) {
            $this->columns = implode( ', ', $columns );
        }
        else {
            $this->columns = $columns;
        }
    }

    function format_query() {
        $this->query = 'SELECT ' . $this->columns . ' ';
        $this->query .= 'FROM ' . $this->anchor_table . ' ';
        foreach( $this->join_tables as $table )
        {
            $this->query .= 'JOIN ' . $table . ' ON packets.row_id = ' . $table . '.packet_row_id ';
        }
        if( !empty( $this->params ) )
        {
            $this->query .= 'WHERE ';
            foreach( $this->params as $param ) {
                $this->query .=  $param . ' AND ';
            }
            $this->query = trim($this->query, 'AND ');
            $this->query = trim($this->query);
        }
        
        
        $this->query = trim($this->query);
        $this->query .= ';';
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

    function set_join_tables( $tables = NULL ) {
        $this->query->set_join_tables( $tables );
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

    public function build_query( $json_data = NULL ) {
        if( !isset( $json_data ) )
        {
            return NULL;
        }
        if( !array_key_exists( "columns", $json_data ) )
        {
            $json_data["columns"] = NULL;
        }
        if( !array_key_exists( "params", $json_data ) )
        {
            $json_data["params"] = NULL;
        }
        if( !array_key_exists( "tables", $json_data ) )
        {
            $json_data["tables"] = NULL;
        }
        $this->builder->set_columns( $json_data[ "columns" ] );
        $this->builder->set_params( $json_data[ "params" ] );
        $this->builder->set_join_tables( $json_data[ "tables" ] );
        $this->builder->format_query();
    }

    public function get_query() {
        return $this->builder->get_query();
    }

}







?>