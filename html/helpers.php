<?php

function refValues($arr){
    // pass values by reference
    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
    {
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
    return $arr;
}

function get_sql_result($conn, $pointing, $beam_start, $beam_end, $sort, $limit, $offset) {
    // construct sql query
    $sql = "SELECT * FROM spscandidate";

    $stack = array();

    if ( $pointing != null ) {
        $sql_pointing = "pointing = ?";
        array_push($stack, array($pointing => "i"));
    } else {
        $sql_pointing = null;
    }

    if ( $beam_start != null ) {
        $sql_beam_start = "beam >= ?";
        array_push($stack, array($beam_start => "i"));
    } else {
        $sql_beam_start = null;
    }

    if ( $beam_end != null ) {
        $sql_beam_end = "beam <= ?";
        array_push($stack, array($beam_end => "i"));
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

    $sql .= " ORDER BY ? DESC LIMIT ? OFFSET ?";
    array_push($stack, array($sort => "s"));
    array_push($stack, array($limit => "i"));
    array_push($stack, array($offset => "i"));

    // assemble types string
    $types = "";
    foreach ( $stack as $var ) {
        foreach ( $var as $key => $val ){
            $types .= $val;
        }
    }

    // assemble params array
    $params = array($types);
    foreach ( $stack as $var ) {
        foreach ( $var as $key => $val ){
            array_push($params, $key);
        }
    }

    //echo "<p>SQL query: " . $sql . "</p>\n";
    //echo "<p>Type string: " . $types . "</p>\n";
    //foreach ( $params as $var ) {
    //    echo "<p>" . $var . "</p>\n";
    //}

    $stmt = $conn->prepare($sql);

    call_user_func_array(array($stmt, "bind_param"), refValues($params));
    $stmt->execute();

    $result = $stmt->get_result();

    return $result;
}

?>