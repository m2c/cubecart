
(1) confirmed.inc.php

- Copy this into this cubecart folder   /storename/includes/content

example : http://webonline.com.my/includes/content


(2) Webcash (folder)

- copy the whole folder into    /storename/module/gateway
- edit file transfer.inc.php (in this folder)
	- Line 117 - edit merchant ID (to be given upon successful Webcash merchant registration)
	- Line 125 - edit your return url (usually http://xxx.com/confirmed.php)

For Testing ... change Line 117 and 168 value according to our API.

Testing Server
Line 117 : Merchant ID : 80000155
Line 168 : Webcash gateway url    http://test.webcash.my/wcgatewayinit.php


(3) Admin/Webcash (Folder)

- copy Webcash sub-folder (in admin folder) into  /storename/admin/modules/gateway
- Login into cubecart admin. Navigate to :

> Modules > Gateway

The enable WEBCASH as default.


-end-


