<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1-3</title>
</head>
<body>
    <?php
    // DB接続設定
    $dsn = "mysql:dbname=データベース名;host=localhost";
    $user = "ユーザー名";
    $password = "パスワード";
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    // テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS mission5_3" //ここでテーブル名を変える
    ." ("
    . "id char(32),"
    . "DBname char(32)," 
    . "DBcom TEXT,"
    . "DBdate char(32),"
    . "DBpass char(32)"
    .");";
    $stmt = $pdo->query($sql);


    $x = "";
    $v = "";
    $w = "";
    $err = false;
    if ($_SERVER ['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['submit3'])){
            $edit_num = $_POST["edit_num"];
            $pass_edi = $_POST["pass3"];
            // 変更部分 ファイル開く
            $sql = 'SELECT * FROM mission5_3';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            $flag_edi_message = 0;
            // 入力された番号とパスワードの一致を検証
            foreach ($results as $row){
                if ($edit_num==$row["id"] && $pass_edi==$row["DBpass"]){
                    $v = $row["DBname"];
                    $w = $row["DBcom"];
                    $x = $edit_num;
                    $edi_message = "コメントNo.".$x."を編集して送信してください。";
                    $flag_edi_message = 1;
                }
            }
        }
    }
    ?>  

    <h3>おすすめのミュージシャンや楽曲を教えてください</h3>
    ※パスワードはこちらから見えてしまうので普段使うものは避けてください。<br><br>
    <div style="color:red;">
        <?php 
            if(isset($_POST['submit3'])){
                if($flag_edi_message){
                    echo $edi_message; 
                } else{
                    echo "コメント番号またはパスワードが正しくありません。";
                }
            }
        ?>
    </div>
    <form action="" method="post">
        <!--インプット欄-->
        コメント投稿<br>
        <input type="text" placeholder="名前" name="str_name" value="<?php echo $v; ?>">
        <input type="text" placeholder="コメント" name="str_com" value="<?php echo $w; ?>">
        <input type="hidden" placeholder="編集中" name="editing_num" value="<?php echo $x; ?>">
        <input type="password" name="pass1" placeholder="パスワード">
        <input type="submit" name="submit1"><br>
        コメント削除<br>
        <input type="number" placeholder="コメント番号(半角数字)" name="del">
        <input type="password" name="pass2" placeholder="パスワード">
        <input type="submit" name="submit2" value="削除"><br>
        コメント編集<br>
        <input type="number" placeholder="コメント番号(半角数字)" name="edit_num"> 
        <input type="password" name="pass3" placeholder="パスワード">
        <input type="submit" name="submit3" value="編集">
        <!--<input type="text" name="edit_com" value="編集コメント">-->
        
    </form>
    <?php
    if (isset($_POST['submit1'])||isset($_POST['submit2'])){
    $name = $_POST["str_name"];
    $com = $_POST["str_com"];
    $del = $_POST["del"];
    $edit_num = $_POST["edit_num"];
    $pass_pos = $_POST["pass1"];
    $pass_del = $_POST["pass2"];
    $pass_edi = $_POST["pass3"];
    $editing_num = $_POST["editing_num"];
    }
    
    // コメントの追加処理、最初のif文で送信ボタンが押されたか判定    
    if(isset($_POST['submit1'])){
        if ($name!="" && $com!=""){
            $sql = 'SELECT * FROM mission5_3';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            $date = date("Y/m/d H:i:s");
            
            // 新規投稿（編集番号が空）DBに書き込む 
            // idで管理できるため、数字が大きいときもここで処理
            if ($editing_num=="" || $editing_num>$results[count($results)-1]["id"]){
                $sql = $pdo -> prepare("INSERT INTO mission5_3 (id, DBname, DBcom, DBdate, DBpass) VALUES (:id, :DBname, :DBcom, :DBdate, :DBpass)");
                $sql -> bindParam(':DBname', $DBnew_name, PDO::PARAM_STR);
                $sql -> bindParam(':DBcom', $DBnew_com, PDO::PARAM_STR);
                $sql -> bindParam(':DBdate', $DBnew_date, PDO::PARAM_STR);
                $sql -> bindParam(':DBpass', $DBnew_pass, PDO::PARAM_STR);
                $sql -> bindParam(':id', $id, PDO::PARAM_INT);
                $id = count($results)+1;
                $DBnew_name = $name;
                $DBnew_com = $com; 
                $DBnew_date = $date;
                $DBnew_pass = $pass_pos;
                $sql -> execute();
                echo $name.", ".$com." を投稿しました。<br><br>";
            }
            // 置き換え可能な時
            else{
                $id = $editing_num;
                $DBnew_name = $name;
                $DBnew_com = $com; 
                $DBnew_date = $date;
                $DBnew_pass = $pass_pos;
                $sql = 'UPDATE mission5_3 SET DBname=:DBname,DBcom=:DBcom, DBdate=:DBdate, DBpass=:DBpass WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt -> bindParam(':DBname', $DBnew_name, PDO::PARAM_STR);
                $stmt -> bindParam(':DBcom', $DBnew_com, PDO::PARAM_STR);
                $stmt -> bindParam(':DBdate', $DBnew_date, PDO::PARAM_STR);
                $stmt -> bindParam(':DBpass', $DBnew_pass, PDO::PARAM_STR);
                $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                echo "コメントNo.".$id."を編集しました。<br><br>";
            }
        }   
            // 削除
    }else if(isset($_POST['submit2'])){
        if ($del!="" && $pass_del!=""){
            $sql = 'SELECT * FROM mission5_3';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            $i = 1;
            $flag = 0;
            foreach ($results as $row){
                if ($del!=$row["id"] || $pass_del!=$row["DBpass"]){
                    $id = $i;
                    $DBnew_name = $row["DBname"];
                    $DBnew_com = $row["DBcom"]; 
                    $DBnew_date = $row["DBdate"];
                    $DBnew_pass = $row["DBpass"];
                    $sql = 'UPDATE mission5_3 SET DBname=:DBname,DBcom=:DBcom, DBdate=:DBdate, DBpass=:DBpass WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt -> bindParam(':DBname', $DBnew_name, PDO::PARAM_STR);
                    $stmt -> bindParam(':DBcom', $DBnew_com, PDO::PARAM_STR);
                    $stmt -> bindParam(':DBdate', $DBnew_date, PDO::PARAM_STR);
                    $stmt -> bindParam(':DBpass', $DBnew_pass, PDO::PARAM_STR);
                    $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                }else{
                    $flag = 1;
                }
                $i++;
            }
            // 削除操作してたら最後の行を消す
            if ($flag==1){
                $id = $results[count($results)-1]["id"];
                $sql = 'delete from mission5_3 where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                echo "コメントNo.".$row["id"]."を削除しました。<br><br>";
            }
            else{
                echo "コメント番号またはパスワードが正しくありません。<br><br>";
            }
        }
    }
    // 順番に出力
    $sql = 'SELECT * FROM mission5_3';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
        echo "No.".$row['id'].'<br>';
        echo "Name   :".$row['DBname'].'<br>';
        echo "Comment :".$row['DBcom'].'<br>';
        echo "Date   :".$row['DBdate'].'<br><br>';
    }
    
    ?>
    
</body>
        
        
        