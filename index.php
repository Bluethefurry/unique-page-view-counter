<?php

// mysql
$mysqli = new mysqli("127.0.0.1", "user", "password", "database");

// Oh no! A connect_errno exists so the connection attempt failed!
if ($mysqli->connect_errno) {
    echo "500 Interal Server Error ( DB Connection Failure )";
	exit();
}
function defineCount($mysqli){
    $ip = getUserIpAddr(); // get the current users IP

	$sql = "SELECT * FROM visitors WHERE ip = '$ip'";
	if ($result = $mysqli->query($sql)) {
        if($result->num_rows > 0) {
            $row = $result->fetch_array(MYSQLI_ASSOC); 
            $sql = "SELECT * FROM visitors"; 
            if ($result2 = $mysqli->query($sql)) { // get our visitor count
                global $VISITOR_NUM, $VISITOR_TOTAL;
                $VISITOR_TOTAL = $result2->num_rows;
                $VISITOR_NUM = $row["id"]; // get the id of our current visitor
            }
        } else { // visitor never visited before, create new entry
            $sql = "INSERT INTO `visitors`(`ip`) VALUES ('$ip')";
            if ($mysqli->query($sql)) {
                global $VISITOR_NUM, $VISITOR_TOTAL;
                $VISITOR_NUM = $mysqli->insert_id; // visitor num and visitor total are identical as they are the latest visitor
                $VISITOR_TOTAL = $mysqli->insert_id;
            }
        }
    }
    $mysqli->close(); //tie up loose ends
}

function getUserIpAddr(){ // ip address getter, thanks SO!
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
};


function ordinal($number) { // ordinal numbers, thanks SO, again!
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
    return $number. 'th';
    else
    return $number. $ends[$number % 10];
}

defineCount($mysqli); // pass the mysql connection onto defineCount function, so we have mysql features
$VIEWER_NUM_STR = ordinal($VISITOR_NUM); // for our "you are visitor number x" string 

// for total views you use $VISITOR_TOTAL 

?>
