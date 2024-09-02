<?php
$json = file_get_contents("php://input");
$data = json_decode($json);

$db = $data->dbName;
$command = $data->command;

if ($command === ".tables;") {
  $command = "SELECT name FROM sqlite_master WHERE type='table'";
  echo $command . "\n";
}


try {
  $pdo = new PDO('sqlite:' . $db);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // クエリ実行
  $stmt = $pdo->query($command);

  if ($stmt) {
    // 結果がある場合、表示
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
      echo json_encode($results, JSON_PRETTY_PRINT);
    } else {
      echo "クエリが成功しましたが、結果はありません。\n";
    }
  } else {
    echo "クエリの実行に失敗しました。\n";
  }
} catch (PDOException $e) {
  echo "エラー: " . $e->getMessage();
}

// var_dump($stmt);
