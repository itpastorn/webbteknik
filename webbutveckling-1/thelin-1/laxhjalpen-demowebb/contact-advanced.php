<?php
$args = array(
    'umail'   => FILTER_VALIDATE_EMAIL,
    'uname'    => array(
        'filter'    => FILTER_SANITIZE_STRING,
        'flags'     => FILTER_FLAG_STRIP_LOW,
        'options'   => array('min_range' => 1,
        'max_range' => 10)
    ),
    'mmessage'     => FILTER_UNSAFE_RAW,
    'get_in_touch' => FILTER_VALIDATE_BOOLEAN,
    'mcategory'   => array(
        'filter' => FILTER_CALLBACK,
        'options'  => function($val)
        {
            $category_valid = array('complaint','suggestion','other');
            return in_array($val, $category_valid) ? $val : FALSE;},
    ),
);

echo "<pre>";
echo "Unfiltered POST (as long as no default filter has been enabled
in php.ini):\n";
var_dump($_POST);

/* test existence of one input */
if (!(empty($_POST)) && !filter_has_var(INPUT_POST, 'umail')) {
	die("missing input");
}
$res = filter_input_array(INPUT_POST, $args);

echo "Filtered POST (res):\n";
var_dump($res);

if (!$res['mmessage'] || strlen($res['mmessage']) < 25) {
	echo "Invalid or missing mmessage\n";
} else {
	$in_clean_nl      = str_replace(
	    array("\r\n","\r", "\n"),
        array("@NEWLINE@","@NEWLINE@","@NEWLINE@"),
        $res['mmessage']
    );
	$in_clean_nl      = filter_var(
	    $in_clean_nl, FILTER_SANITIZE_STRING,
        FILTER_FLAG_STRIP_LOW
    );
	$res['mmmessage'] = str_replace(
	    "@NEWLINE@", "\r\n",
        $still_unsafe['mmessage']
    );
}
echo "With new line only, no <32 (res):\n";
var_dump($res);
echo "</pre>";
