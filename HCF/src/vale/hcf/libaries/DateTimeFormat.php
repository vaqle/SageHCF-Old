<?php

namespace vale\hcf\libaries;

class DateTimeFormat{

    public 	function secstotime($totalSeconds) {
		$startTotalSeconds = $totalSeconds;
		$hours = floor($totalSeconds / 3600);
		$totalSeconds %= 3600;
		$minutes = floor($totalSeconds / 60);
		$seconds = $startTotalSeconds - ($minutes * 60);
		return sprintf("%02d",$hours) . ":" . sprintf("%02d",$minutes) . ":" .sprintf("%.1f", $seconds);
	}
}