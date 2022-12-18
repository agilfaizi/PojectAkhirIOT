<?php
function buka_koneksi_mysql(){
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $db = "pjakhir_iot";

    $conn = new mysqli($dbhost, $dbuser, $dbpass, $db) or die ("Connect Failed : %s\n". $conn -> error);
    return $conn;
}
function tutup_koneksi_mysql($conn){
    $conn -> close();
}

function insert_data($suhu)
{
    if(!is_numeric($suhu)) return false;
    $conn = buka_koneksi_mysql();
    $skr=date("Y-m-d H:i:s");
    $sql = "INSERT INTO suhu(jam,suhu) VALUES ('$skr', $suhu)";
    if ($conn->query($sql) === TRUE){
        echo " --> [$skr] record berhasil disimpan";
    } else{
        echo "Error : " . $sql . "<br> . $conn->error";
    }
    $conn->close();
}

// Main Program

$host   = "riset.revolusi-it.com";
$port     = 1883;
$username = "";
$password = "";
$topic = "iot/G.231.19.0203";

require("phpMQTT.php");
$mqtt = new bluerhinos\phpMQTT($host, $port, "G.231.19.0203-2425".rand());

buka_koneksi_mysql();
if (!$mqtt->connect(true,NULL,$username,$password)) {
    exit(1);
}

$topics[$topic] = array ("qos"=>0, "function"=>"procmsg");
$mqtt->subscribe($topics,0);

while($mqtt->proc()){

}

$mqtt->close();
function procmsg($topic, $msg){
    $skr = date("d-m-Y H:i:s");
    echo "\r\n $skr : [$topic] : $msg";
    insert_data($msg);
}
?>