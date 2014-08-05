<?php

require '../vendor/autoload.php';
require __DIR__.'/models/FBComment.php';
require __DIR__.'/controllers/CommentController.php';
require __DIR__.'/models/FBReply.php';
require __DIR__.'/tasks/getreplies.php';
require __DIR__.'/tasks/getcomments.php';


Resque::enqueue('durrr', 'GetReplies');
Resque::enqueue('durrr', 'GetComments');

?>


