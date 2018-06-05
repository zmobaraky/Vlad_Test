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

function findKey($array, $keySearch) {
     // check if it's even an array
    if (!is_array($array)) return false;

    // key exists
    if (array_key_exists($keySearch, $array)) return true;

    // key isn't in this array, go deeper
    foreach($array as $key => $val)
    {
        // return true if it's found
        if (findKey($val, $keySearch)) return true;
    }

    return false;
}


ob_start(); // turn on output buffering
include('template.tmpl');
//include('extra.tmpl');
$res = ob_get_contents(); // get the contents of the output buffer
ob_end_clean(); //  clean (erase) the output buffer and turn off output buffering

$match_start_char = [' ','{{','{{#each','{{#unless','{{else}}'];
$match_end_char = [' ','}}','{{/each}}','{{/unless}}','{{/unless}}'];
$arr =  preg_split("/({{\/each}}|{{#each|{{#unless|{{else}}|{{\/unless}}|}}|{{)/", $res, -1, PREG_SPLIT_DELIM_CAPTURE);
$output = "";
$start_stack = [];
$foreach_var = [];   
$error_syntax = false;
//print_r($arr);echo "<br><br>";

for ( $key= 0 ; $key < count($arr) ; $key++ )
{
	$arr2 = $arr[$key];
	if($output != "") 	$output .= " ";
	$key_search = array_search($arr2, $match_start_char);

	if($key_search != null){if($key == 24)echo"1111";
		switch($key_search)
		{
			case 1://{{
				if($arr[$key+2] == "}}"){
					$output .= create_var($start_stack, $foreach_var, $arr, $key);
					$key = $key+2;
				}	
				else  
					$error_syntax = true;
				break;
			case 2://{{#each
				if($arr[$key+2] == "}}"){
					$output .= 'foreach ( $'.trim($arr[$key + 1]).' as $'.trim($arr[$key + 1]).'s){';
					array_push($foreach_var,trim($arr[$key + 1]));
					$key = $key+2;
					array_push($start_stack,$arr2);
				}					
				else  
					$error_syntax = true;
				break;
			case 3://{{#unless
				if($arr[$key+2] == "}}"){
					array_push($start_stack,$arr2);
					$var = create_var($start_stack, $foreach_var, $arr, $key);
					$output .= 'if ( !('.$var.')){';
					$key = $key+2;
				}					
				else  
					$error_syntax = true;
				break;
			case 4://{{else}}
				if($start_stack[count($start_stack)-1] == "{{#unless"){
					$output .= '}else{';
				}
				else  
					$error_syntax = true;
				break;

			default://var and strings
		  		if(count($start_stack)>0)
					$output .= create_var($start_stack, $foreach_var, $arr, $key);
				else
					$output .= $arr[$key];
				break;
		}
	}
	else
	{
		$key_search = array_search($arr2, $match_end_char);
		if($key_search != null)
		{ 
			if(array_pop($start_stack) == $match_start_char[$key_search])
			{
				$key++;$output .= "}";
			}
			else{
				$error_syntax = true;
				$output .= $arr[$key];
			}
		}
  		else{
  			
			$output .= "echo \"".$arr[$key]."\";";
  		}
	}
}

echo $output;



/*****************  create variables *******************/
function create_var($start_stack, $foreach_var, $arr, $key)
{
	$output = "";
	if(count($start_stack)>0 && $start_stack[count($start_stack)-1] == '{{#each')//foreach
	{
		$flag_for = false;
		foreach($foreach_var as $each_var)
		{
			eval(" global \$".$each_var."; \$var = \$".$each_var.";");
			if(findKey($var, $arr[$key+1]))
			{
				$output = "echo $".$each_var."s[".$arr[$key+1]."];";
				$flag_for = true;
				break;
			}
		}
		if(!$flag_for)
			$output = "echo $".$arr[$key+1].";";


	}
	else if(substr(trim($arr[$key+1]), 0, 1 ) === "@")
	{
		switch(trim($arr[$key+1]))
		{
			case '@last'://it should check if it is in a loop {foreach} by checking $start_stack
				if(count($foreach_var)>0)
				{
					$output = "$".$foreach_var[count($foreach_var)-1]."s === end($".$foreach_var[count($foreach_var)-1].")";
				}
				break;
			default:
				$output = "echo ".$arr[$key+1];
				break;
		}
	}
	else
		{//echo"-----"
			$output = "echo $".$arr[$key+1].";";
		}
	return $output;
}
?>