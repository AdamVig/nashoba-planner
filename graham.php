<?php

require "helpers.php";
date_default_timezone_set ("America/New_York");

function genYear()
{

    $currentDate = new DateTime("2014-08-27");
    $finalDate = new DateTime("2015-06-18");
    $dayCount = 0;
    $arrayPlace = 0;
    $fullSchedule = [];

        while ($currentDate<=$finalDate) {

            //Not weekend
            if (($currentDate->format('w') != 6) && ($currentDate->format('w') != 0)) {

                //Is school
                if (isSchool($currentDate) === true) {

                    $fullSchedule[$arrayPlace] = [
                        "_id" => getDay($dayCount,"normal", $currentDate)[0][0],
                        "title" => getDay($dayCount,"normal", $currentDate)[0][1]
                    ];

                    $dayCount++;
                    $arrayPlace++;

                //No school
                } else {

                    $fullSchedule[$arrayPlace] = [
                        "date" => $currentDate->format('Y-m-d'),
                        "title" => isSchool($currentDate)
                    ];
                    $arrayPlace++;
                }
            }

            //Go to the next day
            $currentDate->modify("+1 day");

        }

    return $fullSchedule;
}

echo genYear();

function getDay($dayNum, $type, $date)
{
    $classMinutes = [0,0,0,0,0,0,0];
    $periods = ["A","B","C","D","E","F","G"];
    $offset = ($dayNum % 7);
    if ($type == "normal") {
        for ($i=0; $i<7; $i++) {
            $schedule[$i] = $periods[($offset+$i)%7].(($dayNum % 8)+1);
            $classMinutes[($offset+$i)%7]+=46;
            if ($i == 4) {
                $schedule[$i] = $periods[($offset+$i)%7].(($dayNum % 8)+1)."L";
                $classMinutes[($offset+$i)%7]+=23;
            }
        }
    }
    if ($type == "half") {

    }
    if ($type == "activity") {
        for ($i=0; $i<8; $i++) {
            if ($i == 2) {
                $schedule[2] = "Activity Period";
            }
            else {
                $schedule[$i] = $periods[($offset+$i)%7].(($dayNum % 8)+1);
                $classMinutes[($offset+$i)%7]+=38;
                if ($i == 4) {
                    $schedule[$i] = $periods[($offset+$i)%7].(($dayNum % 8)+1)."L";
                    $classMinutes[($offset+$i)%7]+=31;
                }
            }
        }
    }
    $output = [[$date->format('Y-m-d'),$schedule[0]],$classMinutes];
    return $output;
}

function storeSchedule($schedule)
{
    $db = new mysqli('localhost', 'admin', 'admin', 'main');
    $query = "INSERT INTO schedule (Date,Schedule) VALUES ";
    $first = true;
    foreach ($schedule as $day) {
        //Add comma first or not
        if ($first) {
            $first = false;
        } else {
            $query .= ",";
        }
        //Format: ('2014-04-14','A1')
        $query .= "('".$day['date']."','".$db->escape_string($day['title'])."')";
    }
    $query .= ';';
    $db->query($query);
} 
