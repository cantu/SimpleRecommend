<?php

$raw_array = array(5,2,4,6,1,3);
echo "\n";

/*
 * 插入排序法思路：将要排序的元素插入到已经 假定排序号的数组的指定位置。
 * 打牌取牌的思路
 */
function InsertSort( $array){
	if( !is_array( $array) or count( $array ) == 0){
		echo 'input array is not avalid';
		return null;
	}
	echo "--------------------------------------------\n";
	$array_str = implode(',' ,  $array);
	echo"raw array: ".$array_str."\n";
	echo "Insert sorte\n";	
	$compare_times = 0;
	for( $j=1; $j<count( $array) ; $j++){
		$key = $array[$j];
		for( $i = $j - 1;  $i>=0; $i-- ){
			$compare_times ++;
			if( $array[$i] > $key ){
				$array[$i+1] = $array[$i];
				$array[$i] = $key;
			} 
		$array_str = implode( ',', $array);
		echo "[$compare_times],  [key]: $key [sorted]: $array_str \n";
		}

	}
	return $array;
}

/*
 * 冒泡法
 */
function BubbleSorte( $array ){
	if( !is_array( $array) or count($array) == 0 ){
		print "Error, input array is not avalide";
		return null;
	}
	echo "--------------------------------------------\n";
	$array_str = implode(',' ,  $array);
	echo"raw array: ".$array_str."\n";
	echo "Bubble sorte\n";
	$compare_times= 0;
	for ($i=0; $i<count($array); $i++){
		for( $j=1; $j<count($array)-$i; $j++){
			$compare_times++;
			#数组按升序排列
			if( $array[$j] <  $array[$j-1]){
				$temp = $array[$j];
				$array[$j] = $array[$j-1];
				$array[$j-1] = $temp;
			}
		$array_str = implode(',', $array);
		echo"[$compare_times],  [key]: $array[$i],  array: $array_str \n";
		}
	}	
	return $array;
}


function MergeSort(){
	
}

function QuickSort( $array, $left, $right, $done ) {
	if( !is_array( $array ) or count( $array ) == 0 ){
		print"Error, input array is not validate";
		return null;
	}
	echo "--------------------------------------------\n";
	echo "Quick sorte\n";
	
	$i=$left;
	$j=$right;
	$k = $array[0];
	$r = $array[$j];
	
	for( $i = 0; $i<count($array)-1; $i++){
		if( $array[$i] > $k){
			break;
		}
	}
	for( $j=count($array)-1; $j>0; $j-- ){
		if( $array[$j] < $r){
			break;
		}
	}
	if( $i< $j ){
		$temp = $array[$i];
		$array[$i] = $array[$j];
		$array[$j] = $temp;
	}else{
		$temp = $array[0];
		$array[0] = $array[$j];
		$arrray[$j] = $temp;
	}
	$array_str = implode(',', $array);
	echo" $array[$i],  array: $array_str \n";
	$done++;
	while( ($right = count($array) -1 - $done) > 0){
		QuickSort($array, 0, count($array)-1-$done, $done );
	}
	
}

InsertSort( $raw_array) ;
BubbleSorte($raw_array);
//QuickSort($raw_array, 0, count($raw_array)-1, 0);
	
