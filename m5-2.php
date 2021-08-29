<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission5-2</title>
    </head>
    <body>
<?php
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
?>
<form action="" method="post">
            <!-- 投稿欄 -->
            <?php
            //編集投稿番号が入力された時
            if(!empty($_POST["edit"]) && !empty($_POST["pass2"])){
                //番号とパスワードが一致しているかどうか
                $sql = 'SELECT * FROM mfive';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach($results as $row){
                    //一致していたら編集フォーム
                    if($row['id'] == $_POST["edit"] && $row['pass'] == $_POST["pass2"]){
                        $editName = $row['name'];
                        $editCom = $row['comment'];
                        $edit = $row['id'];
                        echo "<input type='text' name='str' value='".$editName."'><br>";
                        echo "<input type='text' name='comments' value='".$editCom."'><br>";
                        echo "<input type='hidden' name='editNum' value='".$edit."'>";
                    }elseif($row['id'] == $_POST["edit"] && $row['pass'] != $_POST["pass2"]){
                        echo "パスワードが一致しません<br>";
                        echo "<input type='text' name='str' placeholder='名前'><br>";
                        echo "<input type='text' name='comments' placeholder='コメント'><br>";
                        echo "<input type='hidden' name='editNum'>";
                    }
                }
            }
            //編集対象番号が入力されていない時
            else{
                echo "<input type='text' name='str' placeholder='名前'><br>";
                echo "<input type='text' name='comments' placeholder='コメント'><br>";
                echo "<input type='hidden' name='editNum'>";
            }
            ?>
            <input type="password" name="pass" placeholder="パスワード">
            <input type="submit" name="submit"><br>
            <br>
            <!-- 削除欄 -->
            <input type="number" name="number" placeholder="削除対象番号"><br>
            <input type="password" name="pass1" placeholder="パスワード">
            <input type="submit" name="delete" value="削除">
            
            <br>
            <br>
            <!-- 編集欄 -->
            <input type="number" name="edit" placeholder="編集対象番号"><br>
            <input type="password" name="pass2" placeholder="パスワード">
            <input type="submit" name="editsub" value="編集">
        </form>
        <?php
        //投稿された時
        if(!empty($_POST["str"]) && !empty($_POST["comments"]) && empty($_POST["editNum"]) && !empty($_POST["pass"])){
            //テーブルがなかったら作成する
            $sql = "CREATE TABLE IF NOT EXISTS mfive"
            ."("
            ."id INT AUTO_INCREMENT PRIMARY KEY,"
            ."name char(32),"
            ."comment TEXT,"
            ."date datetime,"
            ."pass char(32)"
            .");";
            $stmt = $pdo->query($sql);
            //データ入力
            $sql2 = $pdo -> prepare("INSERT INTO mfive (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
            $sql2 -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql2 -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql2 -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql2 -> bindParam(':pass', $pass, PDO::PARAM_STR);
            $name = $_POST["str"];
            $comment = $_POST["comments"];
            $date = date("Y-m-d H:i:s");
            $pass = $_POST["pass"];
            $sql2 -> execute();
            //データを表示する
            $sql3 = 'SELECT * FROM mfive';
            $stmt2 = $pdo->query($sql3);
            $results = $stmt2->fetchAll();
            foreach($results as $row){
                echo $row['id'].' ';
                echo $row['name'].' ';
                echo $row['comment'].' ';
                echo $row['date'].'<br>';
                echo "<hr>";
            }
        }elseif(!empty($_POST["str"]) && !empty($_POST["comments"]) && empty($_POST["editNum"]) && empty($_POST["pass"])){
            echo "パスワードを入力してください";
            }
        //削除欄から送信された時
        if(!empty($_POST["number"]) && !empty($_POST["pass1"])){
            //番号とパスワードが一致しているかどうか
            $sql = 'SELECT*FROM mfive';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach($results as $row){
                //一致している時
                if($row['id'] == $_POST["number"] && $row['pass'] == $_POST["pass1"]){
                    //指定された投稿を削除
                    $id = $_POST["number"];
                    $pass1 = $_POST["pass1"];
                    $sql2 = 'delete from mfive where id=:id and pass=:pass1';
                    $stmt2 = $pdo->prepare($sql2);
                    $stmt2->bindParam(':id',$id,PDO::PARAM_INT);
                    $stmt2->bindParam(':pass1',$pass1,PDO::PARAM_STR);
                    $stmt2->execute();
            //データを表示する
                    $sql3 = 'SELECT*FROM mfive';
                    $stmt3 = $pdo->query($sql3);
                    $results2 = $stmt3->fetchAll();
                    foreach($results2 as $row2){
                        echo $row2['id'].' ';
                        echo $row2['name'].' ';
                        echo $row2['comment'].' ';
                        echo $row2['date'].'<br>';
                        echo "<hr>";
                    }
                }elseif($row['id'] == $_POST["number"] && $row['pass'] != $_POST["pass1"]){
                    echo "パスワードが一致しません。<br>";
                }
            }
        }
        
        //編集フォームから送信された時
        if(!empty($_POST["editNum"]) && !empty($_POST["str"]) && !empty($_POST["comments"]) && !empty($_POST["pass"])){
            $id = $_POST["editNum"];//変更する投稿番号
            $name = $_POST["str"];
            $comment = $_POST["comments"];
            $date = date("Y-m-d H:i:s");
            $pass = $_POST["pass"];
            //指定された番号の投稿内容を編集する
            $sql = 'UPDATE mfive SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name',$name,PDO::PARAM_STR);
            $stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
            $stmt->bindParam(':id',$id,PDO::PARAM_INT);
            $stmt->bindParam(':date',$date,PDO::PARAM_STR);
            $stmt->bindParam(':pass',$pass,PDO::PARAM_STR);
            $stmt->execute();
            //データを表示する
            $sql2 = 'SELECT*FROM mfive';
            $stmt2 = $pdo->query($sql2);
            $results = $stmt2->fetchAll();
            foreach($results as $row){
                echo $row['id'].' ';
                echo $row['name'].' ';
                echo $row['comment'].' ';
                echo $row['date'].'<br>';
                echo "<hr>";
            }
        }
        ?>
    </body>
</html>