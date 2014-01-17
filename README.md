# Email Delivery

Command Line Tool For Emails Delivery.

## Options

-db, -database - SQLite Database File (Required). Default delivery.sqlite
-e, -emails - Comma Separated Receivers Emails List
-ef, -emails-file - Read Line By Line Receivers Emails List File (Watch emails.txt)
-m, -message - Delivery Message (Not Html)
-mf, -messages-file - Delivery Messages List File In PHP Array Manner (Watch messages.php)
-start - Starts Delivery In Queue Manner (If you not Clear Queue, then old (not sent before) messages will be sended). Flag Only. Default false
-cq, --clear-queue - Clear Delivery Queue (Will Be Executed Before Adding New Emails In Queue). Flag Only. Default false
-count - Count Addresses In One Email. Default 5
-pause - Delay Between Emails Sending (In Seconds). Default 3
-as, --add-senders - Add To Senders List From File In PHP Array Manner (Watch senders.php). Will Be Added Only New Senders.

Options -e (-ef) and -m (-mf) Should Be Specified When You Want Add Emails To Delivery Queue

## Example usage

```
> php delivery.php -as senders.php
```
add senders to senders list (in database) from senders.php

```
> php delivery.php -db delivery_new.sqlite -ef emails.txt -mf messages.php -cq -count 2 -pause 2 -start
```
set database delivery_new.sqlite adding new emails from combo emails.txt & messages.php files.
clear delivery queue before.
set count receivers in one email to 2.
set delay between sending emails to 2 seconds.
and finally start delivery.

```
> php delivery.php -start
```
just start delivery. useful when delivery was interrupted.