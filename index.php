<?php
//test update
    require ('phpQuery.php');

    $host = '127.0.0.1';
    $db   = 'elco';
    $user = 'root';
    $pass = '536801';
    $charset = 'utf8';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = array(PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES   => false);
    $pdo = new PDO($dsn,$user,$pass,$opt);
    $db = dbase_open('c:\temp\ord.dbf', 0);
    $record_numbers = dbase_numrecords($db);
    for ($i = 1; $i <= $record_numbers; $i++) {
        $row = array_change_key_case(dbase_get_record_with_names($db, $i), CASE_UPPER);
        echo $row['CODE'];

   // foreach ($rows as $row) {
        $url = 'https://www.domko.ru/item/' . $row['CODE'];
        $s1 = $pdo->prepare('SELECT id FROM originalDescriptions where url = ?');
        $s1->execute(array($url)); //Тут передеалть что бы определять когда последний раз обновлялось
        if (!$s1->fetch()) {
            $file = file_get_contents($url);//test string for git
            $s1 = $pdo->prepare('INSERT INTO originalDescriptions (supplier_id,url,content,lastParsed) VALUES (?,?,?,?)');
            $s1->execute(array(2, $url, $file, date("Y-m-d H:i:s")));

/*            $doc = phpQuery::newDocumentHTML($file);//,$charset = 'utf-8');
            $title = pq($doc['.item-page_name'])->text();
            echo $title;
            if (strlen($title) > 0) {
                //$description = pq($doc['.item-page_description'])->html();
                $description = trim(pq($doc['.item-page_description'])->text());
                if (mb_strlen($description) > 0) {
                    $lastPoint = mb_strrpos($description, '.', 200);
                    if (!$lastPoint) $lastPoint = mb_strlen($description);
                    if ($lastPoint > 245) {
                        $description = mb_substr($description, 0, 245) . '...';
                    } else {
                        $description = mb_substr($description, 0, $lastPoint);
                    }
                }
                $searchTitle = preg_replace('/[^а-яёА-ЯЁa-zA-Z0-9]/', '', $title);
                $stmtu = $pdo->prepare('insert into goods (title,searchTitle,supplierId,shortText,supplierGoodId) VALUES (?,?,?,?,?)');
                $stmtu->execute(array($title, $searchTitle, 2, $description,$row));
                $goodId = $pdo->lastInsertId();
                $imgs = $doc['.item_page-img_block img'];
                $flag = true;
                foreach ($imgs as $img) {
                    $name = pq($img)->attr('src');
                    $file = file_get_contents($name);
                    $len = mb_strlen($file);
                    $stms = $pdo->prepare('SELECT id FROM images WHERE originalName = ? AND length = ?');
                    $stms->execute(array($name, $len));
                    $data = $stms->fetch();
                    if (!$data) {
                        //file_put_contents("test.jpg",$file);
                        $stmu = $pdo->prepare('INSERT INTO images (content,originalName,length) VALUES (?,?,?)');
                        $stmu->execute(array($file, $name, $len));
                        $data['id'] = $pdo->lastInsertId();
                    }
                    if ($flag) {
                        $flag = false;
                        $stmu = $pdo->prepare('UPDATE goods SET mainPictureId = ? WHERE id = ?');
                        $stmu->execute(array($data['id'], $goodId));
                    }
                    $stmn = $pdo->prepare('SELECT id FROM imagesToGoods WHERE goodId = ? AND imageId = ?');
                    $stmn->execute(array($goodId, $data['id']));
                    if (!$stmn->fetch()) {
                        $stmu = $pdo->prepare('INSERT INTO imagesToGoods (goodId,imageId) VALUES(?,?)');
                        $stmu->execute(array($goodId, $data['id']));
                    }
                }
            }
*/
        }
    }
?>