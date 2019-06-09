<?php
        //cpu stat
        $prevVal = shell_exec("cat /proc/stat");
        $prevArr = explode(' ',trim($prevVal));
        $prevTotal = $prevArr[2] + $prevArr[3] + $prevArr[4] + $prevArr[5];
        $prevIdle = $prevArr[5];
        usleep(0.15 * 1000000);
        $val = shell_exec("cat /proc/stat");
        $arr = explode(' ', trim($val));
        $total = $arr[2] + $arr[3] + $arr[4] + $arr[5];
        $idle = $arr[5];
        $intervalTotal = intval($total - $prevTotal);
        $stat['cpu'] =  intval(100 * (($intervalTotal - ($idle - $prevIdle)) / $intervalTotal));
        $cpu_result = shell_exec("cat /proc/cpuinfo | grep cpu\ cores");
        $stat['cpu_cores'] = strstr($cpu_result, "\n", true);
        $stat['cpu_cores'] = str_replace("cpu cores    : ", "", $stat['cpu_cores']);
        //memory stat
        $stat['mem_percent'] = round(shell_exec("free | grep Mem | awk '{print $3/$2 * 100.0}'"), 2);
        $mem_result = shell_exec("cat /proc/meminfo | grep MemTotal");
        $stat['mem_total'] = round(preg_replace("#[^0-9]+(?:\.[0-9]*)?#", "", $mem_result) / 1024 / 1024, 3);
        $mem_result = shell_exec("cat /proc/meminfo | grep MemFree");
        $stat['mem_free'] = round(preg_replace("#[^0-9]+(?:\.[0-9]*)?#", "", $mem_result) / 1024 / 1024, 3);
        $stat['mem_used'] = $stat['mem_total'] - $stat['mem_free'];
        //hdd stat
        $stat['hdd_free'] = round(disk_free_space("/") / 1024 / 1024 / 1024, 2);
        $stat['hdd_total'] = round(disk_total_space("/") / 1024 / 1024/ 1024, 2);
        $stat['hdd_used'] = $stat['hdd_total'] - $stat['hdd_free'];
        $stat['hdd_percent'] = round(sprintf('%.2f',($stat['hdd_used'] / $stat['hdd_total']) * 100), 2);
        //network stat
        $stat['network_rx'] = round(trim(file_get_contents("/sys/class/net/eth0/statistics/rx_bytes")) / 1024/ 1024/ 1024, 2);
        $stat['network_tx'] = round(trim(file_get_contents("/sys/class/net/eth0/statistics/tx_bytes")) / 1024/ 1024/ 1024, 2);
        //output headers
        header('Content-type: text/json');
        header('Content-type: application/json');
        //output data by json
        echo    
        "{\"cpu\": " . $stat['cpu'] . ", \"cpu_cores\": \"" . $stat['cpu_cores'] . "\"" . //cpu stats
        ", \"mem_percent\": " . $stat['mem_percent'] . ", \"mem_total\":" . $stat['mem_total'] . ", \"mem_used\":" . $stat['mem_used'] . ", \"mem_free\":" . $stat['mem_free'] . //mem stats
        ", \"hdd_free\":" . $stat['hdd_free'] . ", \"hdd_total\":" . $stat['hdd_total'] . ", \"hdd_used\":" . $stat['hdd_used'] . ", \"hdd_percent\":" . $stat['hdd_percent'] . ", " . //hdd stats
        "\"network_rx\":" . $stat['network_rx'] . ", \"network_tx\":" . $stat['network_tx'] . //network stats
        "}";
?>