<?php

// Helper functions
function select($sql, $conn, $params = null) {
  try {

    $stmt = $conn->prepare($sql);
    if ($params) {
      $stmt->execute($params);
    } else {
      $stmt->execute();
    }
    $res = $stmt->fetch();
    return $res;

  }
  catch(PDOException $e)
  {
    error_log($sql . " - " . $e->getMessage());
  }

}

function selectAll($sql, $conn, $params = null) {
  try {

    $stmt = $conn->prepare($sql);
    if ($params) {
      $stmt->execute($params);
    } else {
      $stmt->execute();
    }
    $res = $stmt->fetchAll();
    return $res;

  }
  catch(PDOException $e)
  {
    error_log($sql . " - " . $e->getMessage());
  }

}

?>
