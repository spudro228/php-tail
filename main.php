
<?php

print "Inotify start...\n";

//var_dump($constans);exit;
$file_name = __DIR__ . '/test.txt';
$second_file_name = __DIR__ . '/test2.txt';

if (!file_exists($file_name)) {
    exit("File not exits.\n");
}

$inotify_file_descriptor = inotify_init();


while (true) {

    $watch_descriptor = inotify_add_watch($inotify_file_descriptor, $file_name, IN_MODIFY | IN_ONESHOT);
    $watch_descriptor2 = inotify_add_watch($inotify_file_descriptor, $second_file_name, IN_MODIFY | IN_ONESHOT);
    if ($watch_descriptor === false) {
        exit("Directory not exist.\n");
    }

    if ($events = inotify_read($inotify_file_descriptor)) {
        foreach ($events as $index => $event) {
            printf("Event index %d, mask %d, file name %s\n", $index, $event['mask'], $event['name'] ?: 'NO_FILE_NAME');
        }
    }
}

inotify_rm_watch($inotify_file_descriptor, $watch_descriptor);
inotify_rm_watch($inotify_file_descriptor, $watch_descriptor2);
fclose($inotify_file_descriptor);

//printf("inotify_queue_len %d\n", inotify_queue_len($inotify_file_descriptor));
