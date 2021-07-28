<?php

require './vendor/autoload.php';

$redis = new Predis\Client();
$t1 = 0;
$t2 = 0;

//trying to get redis cache
$cachedEntry = $redis->get('mis_scan');

//checking if redis cache is empty or not by the key "mis_scan"
if ($cachedEntry) {
  echo "display the result from the cahce <br/>";
  $t1 = microtime(true) * 1000;
  echo $cachedEntry;
  $t2 = microtime(true) * 1000;
  echo "Time Taken: " . round($t2 - $t1, 4);
  exit();
} else {
  echo "connect with database & aslo update the cache <br/>";
  $t1 = microtime(true) * 1000;
  $conn = mysqli_connect("localhost:3306", "root", "Pass1234", "test");
  $sql = "select * from mis_scan limit 10000";
  //getting data from mysql
  $result = $conn->query($sql);
  $temp = "";
  while ($row = $result->fetch_assoc()) {
    echo $row['doc_id'] . "&nbsp;&nbsp;&nbsp;" . $row['doc_file'];
    echo '<br/>';

    $temp .= $row['doc_id'] . "&nbsp;&nbsp;&nbsp;" . $row['doc_file'] . "<br/>";
  }
  $t2 = microtime(true) * 1000;
  echo "Time Taken: " . round($t2 - $t1, 4);
  //setting mysql data to redis
  $redis->set("mis_scan", $temp);
  //setting expiry time for redis key to fetch the update data again from mysql
  $redis->expire('mis_scan', 60);
  exit();
}
