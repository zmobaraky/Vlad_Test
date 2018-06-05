<?php

$Name  = "Your name goes here";
$Stuff = [
  [
    Thing => "roses",
    Desc  => "red"
  ],
  [
    Thing => "violets",
    Desc  => "blue"
  ],
  [
    Thing => "you",
    Desc  => "able to solve this"
  ],
  [
    Thing => "we",
    Desc  => "interested in you"
  ]
];

//extra
echo "Hey "; echo $Name; echo ", here's a slightly better formatted poem for you: "; foreach ( $Stuff as $Stuffs){ echo " "; echo $Stuffs[Thing]; echo " are "; echo $Stuffs[Desc]; echo ""; if ( !($Stuffs === end($Stuff))){ echo ","; }else{ echo "!"; } }


//template
echo "Hey "; echo $Name; echo ", here's a poem for you: "; foreach ( $Stuff as $Stuffs){ echo " "; echo $Stuffs[Thing]; echo " are "; echo $Stuffs[Desc]; echo " "; }
?>