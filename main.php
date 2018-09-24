<?php

const MINIMUM_COUNT_OF_ARGUMENTS = 2;

print "Inotify start...\n";

$file_name = $argv[1];

if (!file_exists($file_name)) {
    exit("File not exits.\n");
}

$inotify_file_descriptor = inotify_init();

while (true) {
    $watch_descriptor = inotify_add_watch($inotify_file_descriptor, $file_name, IN_MODIFY | IN_ONESHOT);

    if (!$watch_descriptor) {
        exit("Directory not exist.\n");
    }

    if ($events = inotify_read($inotify_file_descriptor)) {
        $fd = fopen($file_name, 'r');
        $stat = fstat($fd);
        printf("Block size %s, blocks count %d\n", $stat['blksize'], $stat['blocks']);
        $fs = fseek($fd, -10, SEEK_END);
        if ($fs === -1) {
            exit("fseek error\n");
        }
        $data = fread($fd, 20);
        if (!$data) {
            exit("fgets error\n");
        }
        print $data . PHP_EOL;

        foreach ($events as $index => $event) {
            printf("Event index %d, mask %d, file name %s\n", $index, $event['mask'], $event['name'] ?: 'NO_FILE_NAME');
        }

        fclose($fd);
    }
}

inotify_rm_watch($inotify_file_descriptor, $watch_descriptor);
fclose($inotify_file_descriptor);
