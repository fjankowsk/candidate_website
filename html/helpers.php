<?php

function get_sql_query($pointing, $beam_start, $beam_end, $sort, $limit, $offset) {
    // construct sql query
    $sql = "SELECT * FROM candidates";

    if ( $pointing != null ) {
        $sql_pointing = "pointing = " . $pointing;
    } else {
        $sql_pointing = null;
    }

    if ( $beam_start != null ) {
        $sql_beam_start = "beam >= " . $beam_start;
    } else {
        $sql_beam_start = null;
    }

    if ( $beam_end != null ) {
        $sql_beam_end = "beam <= " . $beam_end;
    } else {
        $sql_beam_end = null;
    }

    $where_used = FALSE;
    foreach ( array($sql_pointing, $sql_beam_start, $sql_beam_end) as $var ) {
        if ( $var != null ) {
            if ( !$where_used ) {
                $sql .= " WHERE " . $var;
                $where_used = TRUE;
            } else {
                $sql .= " AND " . $var;
            }
        }
    }

    $sql .= " ORDER BY " . $sort . " DESC LIMIT " . $limit . " OFFSET " . $offset;
    return $sql;
    }
    
    ?>